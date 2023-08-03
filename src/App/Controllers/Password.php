<?php

namespace App\Controllers;

use App\Models\User;
use Core\BaseController;
use Core\BaseView;


/**
 * [Class Password]
 */
class Password extends BaseController {

    public function forgotAction() {
        BaseView::renderTemplate('Password/forgot.html');
    }

    public function requestResetAction() {
        User::sendPasswordReset($_POST['email']);

        BaseView::renderTemplate('Password/reset_requested.html');
    }

    public function resetAction() {
        $token = $this->routeParams['token'];
        $this->getUserOrExit($token);
        $user = User::findByPasswordReset($token);
            BaseView::renderTemplate('Password/new.html', [
                'token' => $token
            ]);
    }

    public function resetPasswordAction() {
        $token = $_POST['token'];
        $this->getUserOrExit($token);
        $user = User::findByPasswordReset($token);
        if ($user->resetPassword($_POST['password'])) {
            BaseView::renderTemplate('Password/reset_success.html');
        } else {
            BaseView::renderTemplate('Password/new.html', [
                'token' => $token,
                'user' => $user
            ]);
        }
    }

    /**
     * @param string $token
     * 
     * @return User|void
     */
    protected function getUserOrExit(string $token) {
        $user = User::findByPasswordReset($token);
        if ($user) {
            return $user;
        } else {
            BaseView::renderTemplate('Password/token_expired.html');
            exit;
        }
    }
}