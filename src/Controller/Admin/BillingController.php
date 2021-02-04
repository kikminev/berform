<?php

namespace App\Controller\Admin;

use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BillingController extends AbstractController
{
    public function listPayments(SubscriptionRepository $subscriptionRepository):Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $subscriptions = $subscriptionRepository->findBy(['user' => $this->getUser()]);

        return $this->render(
            'admin/Payment/payment_list.html.twig',
            ['subscriptions' => $subscriptions]
        );
    }

    public function upgrade():void
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
    }
}
