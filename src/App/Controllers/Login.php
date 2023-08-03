<?php

namespace App\Controllers;

use App\Models\User;
use App\Auth;
use App\Flash;
use Core\BaseController;
use Core\BaseView;

/**
 * [Class Login]
 */
class Login extends BaseController {
    protected function before() {
        parent::before();
    }
    public function newAction() {
        BaseView::renderTemplate('Login/new.html');
    }

    public function createAction() {
        $user = User::authenticate($_POST['email'], $_POST['password']);

        $rememberMe = isset($_POST['remember_me']);

        if ($user) {
            Auth::login($user, $rememberMe);

            Flash::addmessage('Login successful');
            $this->redirect(Auth::getReturnToPage());
        }
        Flash::addmessage('Login unsuccessful, please try again', Flash::WARNING);
        BaseView::renderTemplate('Login/new.html', [
            'email' => $_POST['email'],
            'remember_me' => $rememberMe
        ]);
    }

    public function destroyAction() {
        Auth::logout();
        $this->redirect('/login/show-logout-message');
    }

    public function showLogoutMessageAction() {
        Flash::addmessage('Logout successful');
        $this->redirect('/');
    }
    
}