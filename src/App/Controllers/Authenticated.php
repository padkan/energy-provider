<?php

namespace App\Controllers;

use Core\BaseController;


/**
 * [Class Authenticated]
 */
abstract class Authenticated extends BaseController {
    protected function before() {
        $this->requiredLogin();
    }
}