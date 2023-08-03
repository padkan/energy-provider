<?php
namespace App\Integrations\ElectricityManagementSystem\Common\Strategies;

use App\Integrations\ElectricityManagementSystem\Handlers\Interfaces\ICalculateByType;
use App\Integrations\ElectricityManagementSystem\Common\Constants\ElectricityManagementConstant;
use App\Models\Product;

class CalculateByType2 implements ICalculateByType {

    public $baseCost;
    public $additionalKwhCost;
    public $consumption;
    public $includedKwh;

    public function __construct($product, int $consumption) {
        $this->baseCosts = $product['baseCost'];
        $this->additionalKwhCost = $product['additionalKwhCost'];
        $this->consumption = $consumption;
        $this->includedKwh = $product['includedKwh'];
    }

    public function getAnuallCosts() {
        $baseCostForAnuall = $this->baseCosts;
        if ($this->consumption <= $this->includedKwh) {
            return $baseCostForAnuall ;
        } else {
            $consumption = $this->consumption - $this->includedKwh;
            // per 30 cent/kWh 
            return ($consumption * $this->additionalKwhCost)/100  + $baseCostForAnuall;
        }

    }

}