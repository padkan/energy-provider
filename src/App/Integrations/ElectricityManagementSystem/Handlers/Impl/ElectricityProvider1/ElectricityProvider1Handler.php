<?php

namespace App\Integrations\ElectricityManagementSystem\Handlers\Impl\ElectricityProvider1;

use App\Integrations\ElectricityManagementSystem\Handlers\Interfaces\ITariff;
use App\Integrations\ElectricityManagementSystem\Handlers\Interfaces\ICalculate;
use App\Integrations\ElectricityManagementSystem\Common\Strategies\CalculateByType1;
use App\Integrations\ElectricityManagementSystem\Common\Strategies\CalculateByType2;
use App\Services\Tariff\ServiceProvider1;
use App\Models\Product;


class ElectricityProvider1Handler implements ITariff,ICalculate {
    
    public $baseCosts;
    public $product;
    public $serviceProvider;
    
    public function __construct(ServiceProvider1 $serviceProvider, Product $product) {
        $this->serviceProvider = $serviceProvider;
        $this->product         = $product;
    }


    /**
     * get list of tariff from serviceProvider api
     * 
     * @return array
     */
    public function getTariffList() :array {
        return $this->serviceProvider->getTariffs();
    }

    /**
     * get base cose
     * 
     * @return void
     */
    public function getBaseCosts():void {
        $this->baseCosts = $this->product->baseCost;
    }

    /**
     * get AnnualPrice based on product
     * 
     * @param array $product
     * @param int $consumption user input consumption
     * @return array
     */
    public function getAnnualPriceBaseType(array $product,int $consumption) {
        if ($product['type'] === 1) {
            $calculateByType = new CalculateByType1($product, $consumption);
        } else {
            $calculateByType = new CalculateByType2($product, $consumption);
        }
        return $calculateByType->getAnuallCosts();
    }

    /**
     * get tariff list based on user consumption
     * 
     * @param int $consumption user input consumption
     * @return array
     */
    public function getTariffListbasedOnConsumption(int $consumption) :array {
        $products = $this->getAllTariffsNoApi();
        $result = [];
        //TODO changed by activated real call api method getTariffList
        foreach ($products as $product) {
            $result[] = [
                'title' => $product['title'],
                'annuallCost' => $this->getAnnualPriceBaseType($product, $consumption)
            ];
        }
        //sorting by annuallCost
        $col= array_column($result, 'annuallCost');
        array_multisort($col, SORT_DESC, $result);

        return $result;
    }

    /**
     * prepare dumy api data
     * 
     * @return array
     */
    public function getAllTariffsNoApi() :array {
        sleep(1);
        
        return [
            [
              "title" => "Product A",
              "type" => 1,
              "baseCost" => 5,
              "additionalKwhCost" => 22
            ],
            [
              "title" => "Product B",
              "type" => 2,
              "includedKwh" => 4000,
              "baseCost" => 800,
              "additionalKwhCost" => 30
            ]
        ];
    }

}