<?php

namespace App\Service\Billing;

class TaxesCalculator
{
    public function calculateTax($price) {
        return 0.2 * $price;
    }

    public function calculatePriceWithTaxes($price) {
        return $price + $this->calculateTax($price);
    }
}
