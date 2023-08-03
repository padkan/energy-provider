<?php

namespace App\Integrations\ElectricityManagementSystem\Handlers\Interfaces;

/**
 * Interface for Electricity management systems tariff
 */
interface ITariff {
    /**
     * @return string|mixed|null
     */
    public function getTariffList();
}