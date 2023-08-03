<?php

namespace App\Integrations\ElectricityManagementSystem\Handlers\Interfaces;

/**
 * Interface for Electricity management systems ICalculateByType
 */
interface ICalculateByType {
    /**
     * @return string|mixed|null
     */
    public function getAnuallCosts();
}