<?php

namespace App\Controller\Admin\Billing;

use App\Repository\Payment\SubscriptionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Billing\SubscriptionHandler;

class BillingController extends AbstractController
{
    /**
     * @param SubscriptionHandler $subscriptionHandler
     * @param SubscriptionRepository $subscriptionRepository
     * @return Response
     */
    public function listPayments(
        SubscriptionHandler $subscriptionHandler,
        SubscriptionRepository $subscriptionRepository
    ): Response {
        $subscriptions = $subscriptionRepository->findBy(['user' => $this->getUser()]);

        return $this->render(
            'Admin/Billing/payment_list.html.twig',
            ['subscriptions' => $subscriptionHandler->markUpgradeable($subscriptions)]
        );
    }
}
