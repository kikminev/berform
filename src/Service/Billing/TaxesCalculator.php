<?php

namespace App\Service\Billing;

class TaxesCalculator
{
    // todo: this works until you have 10 000 EUR annual turnover
    public static function calculateTax($price)
    {
        return round((0.2 * $price), 2);
    }

    public static function calculatePriceWithTaxes(float $price): float
    {
        return $price + self::calculateTax($price);
    }
}