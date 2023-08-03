<?php

namespace App\Controllers;

use App\Auth;
use App\Models\User;
use Core\BaseView;
use App\Flash;


/**
 * [Class Profile]
 */
class Profile extends Authenticated {
    protected function before() {
        parent::before();
        $this->user = Auth::getUser();
    }

    public function showAction() {
        BaseView::renderTemplate('Profile/show.html',[
            'user' => $this->user
        ]);
    }

    public function editAction() {
        BaseView::renderTemplate('Profile/edit.html',[
            'user' => $this->user
        ]); 
    }
    public function updateAction() {
        if ($this->user->updateProfile($_POST)) {

            Flash::addMessage('Changes saved');

            $this->redirect('/profile/show');

        } else {

            View::renderTemplate('Profile/edit.html', [
                'user' => $this->user
            ]);

        }
    }
}
