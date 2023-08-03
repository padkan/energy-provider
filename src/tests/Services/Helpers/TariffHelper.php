<?php
namespace tests\Services\Helpers;

Class TariffHelper {

    /**
     * data provider for Tariffs
     * @return string
     */
    public static function dataProviderForGetTariffs() :string {
        return '[
            {
              "name": "Product A",
              "type": 1,
              "baseCost": 5,
              "additionalKwhCost": 22
            },
            {
              "name": "Product B",
              "type": 2,
              "includedKwh": 4000,
              "baseCost": 800,
              "additionalKwhCost": 30
            }
          ]';
    }
    
}