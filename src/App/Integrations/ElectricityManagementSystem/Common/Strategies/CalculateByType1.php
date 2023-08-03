<?php
namespace App\Integrations\ElectricityManagementSystem\Common\Strategies;

use App\Integrations\ElectricityManagementSystem\Handlers\Interfaces\ICalculateByType;
use App\Models\Product;

class CalculateByType1 implements ICalculateByType {

    public $baseCost;
    public $additionalKwhCost;
    public $consumption;

    public function __construct($product, int $consumption) {
        $this->baseCosts = $product['baseCost'];
        $this->additionalKwhCost = $product['additionalKwhCost'];
        $this->consumption = $consumption;
    }

    public function getAnuallCosts() {
        $baseCostForAnuall = $this->baseCosts * 12;
        // per 22 cent/kWh 
        return ($this->consumption * $this->additionalKwhCost)/100 + $baseCostForAnuall;
    }

}