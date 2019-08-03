<?php

namespace App\Controller\Admin\Billing;

use App\Document\Payment\Product;
use App\Document\Payment\Subscription;
use App\Repository\DomainRepository;
use App\Repository\Payment\PaymentRepository;
use App\Repository\Payment\SubscriptionRepository;
use App\Repository\SiteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\Billing\SubscriptionHandler;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends AbstractController
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function view()
    {
        die('ok');
    }

    public function addProduct(Product $product, Subscription $subscription)
    {
        // todo: verification
        $cart = $this->session->get('cart');
        $cart[] = ['product' => $product, 'subscription' => $subscription];

        $this->session->set('cart', $cart);

        $this->redirectToRoute('admin_billing_cart_view');
    }
}
