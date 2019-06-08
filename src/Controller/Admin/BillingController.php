<?php

namespace App\Controller\Admin;

use App\Repository\DomainRepository;
use App\Repository\Payment\PaymentRepository;
use App\Repository\SiteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BillingController extends AbstractController
{
    /**
     * @param SiteRepository $siteRepository
     * @param DomainRepository $domainRepository
     * @return Response
     * @throws \Doctrine\ODM\MongoDB\MongoDBException
     */
    public function listPayments(PaymentRepository $paymentRepository)
    {
        return $this->render(
            'Admin/payment/payment_list.html.twig',
            array(
            )
        );
    }
}
