<?php

namespace App\Billing;

use App\Entity\Currency;
use App\Entity\Subscription;
use App\Service\Billing\TaxesCalculator;

class Cart
{
    private array $subscriptions;

    public function addSubscription(Subscription $subscription)
    {
        $productSubscriptionCartId = $subscription->getProduct()->getId() . $subscription->getId();
        $this->subscriptions[$productSubscriptionCartId] = $subscription;
    }

    public function getSubscriptions(): ?array
    {
        return $this->subscriptions;
    }

    public function getTotalWithTaxes(): float
    {
        $total = 0;

        if (null == $this->subscriptions) {
            return 0;
        }

        foreach ($this->subscriptions as $subscription) {
            $total += $subscription->getProduct()->getPrice();
        }

        $total = TaxesCalculator::calculatePriceWithTaxes($total);

        return $total;
    }

    public function getTotalWithoutTaxes(): float
    {
        $total = 0;

        if (null === $this->subscriptions) {
            return 0;
        }

        foreach ($this->subscriptions as $subscription) {
            $total += $subscription->getProduct()->getPrice();
        }

        return $total;
    }

    public function getTaxes(): float
    {
        return TaxesCalculator::calculateTax($this->getTotalWithoutTaxes());
    }

    public function getCurrency(): Currency
    {
        $product = $this->subscriptions[array_key_first($this->subscriptions)];

        return $product['product']->getCurrency();
    }
}
