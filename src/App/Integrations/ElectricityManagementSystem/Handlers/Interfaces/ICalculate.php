<?php

namespace App\Integrations\ElectricityManagementSystem\Handlers\Interfaces;

/**
 * Interface for Electricity management systems calculate
 */
interface ICalculate {
    /**
     * @return string|mixed|null
     */
    public function getBaseCosts();
    
}