<?php

namespace App\Service\Billing;

class TaxesCalculator
{
    public static function calculateTax($price)
    {
        return 0.2 * $price;
    }

    public static function calculatePriceWithTaxes(float $price): float
    {
        return $price + self::calculateTax($price);
    }
}
