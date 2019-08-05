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
        return $this->taxesCalculator->calculateTax($price);
    }

    public function showPriceWithTaxes(float $price, string $currency)
    {
        $calculatedPrice = $this->taxesCalculator->calculatePriceWithTaxes($price);

        return 
    }
}
