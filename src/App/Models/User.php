<?php

namespace App\Models;

use App\Token;
use App\Mail;
use Core\BaseView;
use PDO;

class User extends \Core\BaseModel {

    public $errors = [];
    
    public function __construct($data = []) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

    }

    /**
     * @return bool user save true and not save false return
     */
    public function save() {
        $this->validate();
        if (empty($this->errors)) {
            $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);
            $token = new Token();
            $this->activationToken = $token->getValue();
            $hashedToken = $token->getHash();
            
            $sql = 'INSERT INTO users (name, email, password_hash, is_active)
                    VALUES (:name, :email, :password_hash, :is_active)';
            
            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
            $stmt->bindValue(':is_active', 1, PDO::PARAM_INT);

            return $stmt->execute();
        }
        return false;
    }

    /**
     * validate properties
     * @return void
     */
    public function validate() :void {
        // name
        if ($this->name == '') {
            $this->errors[] = 'Name is required';
        }
        // email
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Invalid email';
        }
        if (static::emailExists($this->email, $this->id ?? null)) {
            $this->errors[] = 'The email address already exist';
        }
        // password
        if (isset($this->password)) {
            if (strlen($this->password) < 6) {
                $this->errors[] = 'please insert at least 6 charecters for password';
            }
    
            if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
                $this->errors[] = 'password needs at least one letter';
            }
    
            if (preg_match('/.*\d+.*/i', $this->password) == 0) {
                $this->errors[] = 'password needs at least one number';
            }
        }
    }

    /**
     * @param string $email
     * @param $ignore_id
     * 
     * @return bool
     */
    public static function emailExists(string $email, $ignore_id = null) :bool{
        $user = static::findByEmail($email);
        if ($user) {
            if ($user->id != $ignore_id) {
                true;
            }
        }
        return false;
    }

    /**
     * @param string $email
     * 
     * @return bool|User
     */
    public static function findByEmail(string $email) {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * @param int $id
     * 
     * @return bool|User
     */
    public static function findById(int $id) {
        $sql = 'SELECT * FROM users WHERE id = :id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * @param string $email
     * @param string $password
     * 
     * @return bool|User
     */
    public static function authenticate(string $email,string $password) {
        $user = static::findByEmail($email);
        if ($user && $user->is_active) {
            if (password_verify($password, $user->password_hash)) {
                return $user;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function rememberLogin() {
        $token = new Token();
        $hashedToken = $token->getHash();
        $this->rememberToken = $token->getValue();
        $this->expiryTimestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashedToken, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiryTimestamp), PDO::PARAM_STR);

        return $stmt->execute(); 
    }

    /**
     * @param string $email
     * 
     * @return void
     */
    public static function sendPasswordReset(string $email) {
        $user = static::findByEmail($email);
        if ($user) {
            if ($user->startPasswordReset()) {
                $user->sendPasswordResetEmail();
            }
        }
    }

    protected function startPasswordReset() {
        $token = new Token();
        $hashedToken = $token->getHash();
        $this->password_reset_token = $token->getValue();
        $expiryTimestamp = time() + 60 * 60 * 2;  // 2 hours from now
        $sql = 'UPDATE users
                SET password_reset_hash = :token_hash,
                    password_reset_expires_at = :expires_at
                WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashedToken, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiryTimestamp), PDO::PARAM_STR);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    protected function sendPasswordResetEmail() {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->password_reset_token;
        $text = BaseView::getTemplate('Password/reset_email.txt', ['url' => $url]);
        $html = BaseView::getTemplate('Password/reset_email.html', ['url' => $url]);

        Mail::send($this->email, 'Password reset', $text, $html);
    }

    /**
     * @param string $token
     * 
     * @return [type]
     */
    public static function findByPasswordReset(string $token) {
        $token = new Token($token);
        $hashedToken = $token->getHash();

        $sql = 'SELECT * FROM users WHERE password_reset_hash = :token_hash';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $hashedToken, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute();
        $user = $stmt->fetch();
        if ($user) {
            if (strtotime($user->password_reset_expires_at) > time()) {
                return $user;
            }
        }
    }

    /**
     * @param string $password
     * 
     * @return 
     */
    public function resetPassword($password) {
        $this->password = $password;
        $this->validate();
        if (empty($this->errors)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $sql = 'UPDATE users
            SET password_hash = :password_hash,
                password_reset_hash = NULL,
                password_reset_expires_at = NULL
            WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }

    protected function sendActivationEmail() {
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->password_reset_token;
        $text = BaseView::getTemplate('Signup/activation_email.txt', ['url' => $url]);
        $html = BaseView::getTemplate('Signup/activation_email.html', ['url' => $url]);

        Mail::send($this->email, 'Activation Email', $text, $html);
    }

    /**
     * Activate the user account with the specified activation token
     *
     * @param string $value Activation token from the URL
     *
     * @return void
     */
    public static function activate($value) {
        $token = new Token($value);
        $hashedToken = $token->getHash();

        $sql = 'UPDATE users
                SET is_active = 1,
                    activation_hash = null
                WHERE activation_hash = :hashed_token';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':hashed_token', $hashedToken, PDO::PARAM_STR);

        $stmt->execute();                
    }

    /**
     * @param array $data
     * 
     * @return [type]
     */
    public function updateProfile(array $data) {
        $this->name = $data['name'];
        $this->email = $data['email'];

        // Only validate and update the password if a value provided
        if ($data['password'] != '') {
            $this->password = $data['password'];
        }

        $this->validate();

        if (empty($this->errors)) {

            $sql = 'UPDATE users
                    SET name = :name,
                        email = :email';

            // Add password if it's set
            if (isset($this->password)) {
                $sql .= ', password_hash = :password_hash';
            }

            $sql .= "\nWHERE id = :id";

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

            // Add password if it's set
            if (isset($this->password)) {
                $password_hash = password_hash($this->password, PASSWORD_DEFAULT);
                $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
            }

            return $stmt->execute();
        }
        return false;
    }
}