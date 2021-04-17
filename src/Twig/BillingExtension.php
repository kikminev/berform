<?php

namespace App\Twig;

use App\Service\Billing\TaxesCalculator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BillingExtension extends AbstractExtension
{
    private $taxesCalculator;

    public function __construct(TaxesCalculator $taxesCalculator)
    {
        $this->taxesCalculator = $taxesCalculator;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('showTax', [$this, 'showTax']),
            new TwigFunction('showPriceWithTaxes', [$this, 'showPriceWithTaxes']),
        ];
    }

    public function showTax(float $price)
    {
        return TaxesCalculator::calculateTax($price);
    }

    public function showPriceWithTaxes(float $price, string $currency)
    {
        $price = TaxesCalculator::calculatePriceWithTaxes($price);

        return $price . ' ' . $currency;
    }
}
