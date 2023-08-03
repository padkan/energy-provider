<?php

namespace App\Models;

use App\Token;
use PDO;

class RememberedLogin extends \Core\BaseModel {

    /**
     * @param string $token
     * 
     * @return RememberedLogin
     */
    public static function findByToken(string $token) {
        $token = new Token($token);
        $tokenHash = $token->getHash();
        $sql = 'SELECT * FROM remembered_logins
                WHERE token_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $tokenHash, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * @return User
     */
    public function getUser() {
        return User::findById($this->user_id);
    }

    /**
     * @return bool
     */
    public function hasExpired() {
        return strtotime($this->expires_at) < time();
    }

    /**
     * @param string $token
     * 
     * @return void
     */
    public function delete(string $token) :void {
        $sql = 'DELETE FROM remembered_logins WHERE token_hash = :token_hash';
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $token, PDO::PARAM_STR);
        $stmt->execute();
    }
}