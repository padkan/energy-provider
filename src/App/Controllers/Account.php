<?php

namespace App\Controllers;

use App\Models\User;
use Core\BaseController;

/**
 * [Class Account]
 */
class Account extends BaseController {

    /**
     * @return void
     */
    public function validateEmailAction() {
        $is_valid = ! User::emailExists($_GET['email'], $_GET['ignore_id'] ?? null);
        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }

}