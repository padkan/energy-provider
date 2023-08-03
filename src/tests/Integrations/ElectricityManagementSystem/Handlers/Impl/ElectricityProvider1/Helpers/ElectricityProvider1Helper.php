<?php
namespace tests\Integrations\ElectricityManagementSystem\Handlers\Impl\ElectricityProvider1\Helpers;

use App\Models\Product;

Class ElectricityProvider1Helper {

    /**
     * data provider for ElectricityProvider1
     */
    public static function dataProviderForElectricityProvider1() {
        return [
            [
              "name" => "Product A",
              "type" => 1,
              "baseCost" => 5,
              "additionalKwhCost" => 22
            ],
            [
              "name" => "Product B",
              "type" => 2,
              "includedKwh" => 4000,
              "baseCost" => 800,
              "additionalKwhCost" => 30
            ]
          ];
    }

    public static function createProduct($data = []) {
        return new Product($data);
    }
    
}