<?php

namespace App\Controllers;

use Core\BaseController;
use Core\BaseView;

/**
 * [Class Home]
 */
class Home extends BaseController {

    /**
     * index
     * @return void
     */
    public function indexAction() {
        // BaseView::render('Home/index.php',[
        //     'name' => 'Home',
        //     'color' => ['red', 'green', 'yellow'],
        // ]);
        BaseView::renderTemplate('Home/index.html',[
                'name' => 'Saeed',
                'colors' => ['red', 'green', 'yellow'],
            ]);
    }
}