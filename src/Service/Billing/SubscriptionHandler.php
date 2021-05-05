<?php

namespace App\Service\Billing;

use App\Document\Payment\Subscription;
use App\Entity\Product;
use App\Repository\ProductRepository;

class SubscriptionHandler
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * This only marks plans as upgradeable. It could be extended with more complicated logic.
     *
     * @param array $subscriptions
     * @return array
     */
    public function markUpgradeable(array $subscriptions): array
    {
        $upgradeable = [];
        $upgradeableTo = false;
        $paidHostingProduct = $this->productRepository->findOneBySystemCode(Product::PRODUCT_TYPE_HOSTING);

        /** @var Subscription $subscription */
        foreach ($subscriptions as $subscription) {

            if (Product::PRODUCT_TYPE_FREE_HOSTING === $subscription->getProduct()->getSystemCode()) {
                $upgradeableTo = $paidHostingProduct;
            }

            $upgradeable[] = [
                'upgradeable' => !empty($upgradeableTo),
                'subscription' => $subscription,
                'upgradeTo' => $upgradeableTo,
            ];
        }

        return $upgradeable;
    }
}
