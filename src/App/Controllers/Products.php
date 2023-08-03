<?php

namespace App\Controllers;

use App\Models\Product;
use Core\BaseController;
use Core\BaseView;
use App\Integrations\ElectricityManagementSystem\Handlers\Impl\ElectricityProvider1\ElectricityProvider1Handler;
use App\Services\Tariff\ServiceProvider1;

/**
 * [Class Products]
 */
class Products extends BaseController {
   

    protected $serviceProvider;
    protected $product;

    public function __construct() {
        $this->serviceProvider = new ServiceProvider1(new \GuzzleHttp\Client());
        $this->product = new Product([]);
    }
    /**
     * index product
     * 
     * @return void
     */
    public function indexAction() :void {

        $electricityProvider1Handler = new ElectricityProvider1Handler($this->serviceProvider, $this->product);
        $products = $electricityProvider1Handler->getAllTariffsNoApi();
        //TODO 
        //$products = $electricityProvider1Handler->getTariffList();
        BaseView::renderTemplate('Product\index.html', ['products' => $products]);
    }


    public function queryAction() :void {
        $items = [];
        $electricityProvider1Handler = new ElectricityProvider1Handler($this->serviceProvider, $this->product);
        if ($electricityProvider1Handler && !empty($_POST['consumption'])) {
            $items  = $electricityProvider1Handler->getTariffListbasedOnConsumption($_POST['consumption']);
        }
        BaseView::renderTemplate('Product\query.html', ['items' => $items ]);
    }
    /**
     * add post
     * 
     * @return void
     */
    public function addAction() :void {
        echo "add new";
    }

    protected function before() {
        parent::before();
    }

    protected function after () {
        parent::after();
    }

}