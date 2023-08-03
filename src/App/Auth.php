<?php

namespace App;

use App\Models\User;
use App\Models\RememberedLogin;

class Auth {
    /**
     * login
     * @param User $user
     * @param bool $rememberMe
     * 
     * @return void
     */
    public static function login(User $user, bool $rememberMe = false) :void {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user->id;
        if ($rememberMe) {
            if ($user->rememberLogin()) {
                setcookie('remember_me', $user->rememberToken, $user->expiryTimestamp, '/');
            }
        }
    }

    /**
     * logout
     * @return void
     */
    public static function logout() :void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $param = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $param['path'],
                $param['domain'],
                $param['secure'],
                $param['httponly']
            );
        }
        session_destroy();
        // remove remember login
        static::forgetRememberLogin();
    }

    /**
     * remember request
     * @return void
     */
    public static function rememberRequestedPage() :void {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI']; 
    }

    /**
     * @return string
     */
    public static function getReturnToPage() :string {
        return $_SESSION['return_to'] ?? '/';
    }


    /**
     * @return null | User
     */
    public static function getUser() {
        if (isset($_SESSION['user_id'])) {
            return User::findById($_SESSION['user_id']);
        } else {

        }
    }

    protected static function loginFromRememberCookie() {
        $cookie = $_COOKIE['remember_me'] ?? false;
        if ($cookie) {
            $rememberLogin = RememberedLogin::findByToken($cookie);
            if ($rememberLogin && ! $rememberLogin->hasExpired()) {
                $user = $rememberLogin->getUser();
                static::login($user, false);
                return $user;
            }
        }
    }

    protected static function forgetRememberLogin() {
        $cookie = $_COOKIE['remember_me'] ?? false;
        if ($cookie) {
            $rememberLogin = RememberedLogin::findByToken($cookie);
            if ($rememberLogin) {
                $rememberLogin->delete($rememberLogin->token_hash);
                setcookie('remember_me', '', time() - 3600);
            }  
        }
        
    }
}