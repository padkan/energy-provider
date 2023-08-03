<?php

namespace App\Controllers;

use App\Models\User;
use Core\BaseController;
use Core\BaseView;

/**
 * [Class Signup]
 */
class Signup extends BaseController {
    /**
     * Show the signup page
     *
     * @return void
     */
    public function newAction() {
        BaseView::renderTemplate('Signup/new.html');
    }
    /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction() {
        $user = new User($_POST);
        if ($user->save()) {
            // TODO will fixed when email service is activated 
           // $user->sendActivationEmail();
            $this->redirect('/signup/success');
        } else {
            BaseView::renderTemplate('Signup/new.html', ['user' => $user]);
        }  
    }
    /**
     * Show the signup success page
     *
     * @return void
     */
    public function successAction() {
        BaseView::renderTemplate('Signup/success.html');
    }

    /**
     * Activate a new account
     *
     * @return void
     */
    public function activateAction() {
        User::activate($this->routeParams['token']);

        $this->redirect('/signup/activated');        
    }

    /**
     * Show the activation success page
     *
     * @return void
     */
    public function activatedAction() {
        View::renderTemplate('Signup/activated.html');
    }
}