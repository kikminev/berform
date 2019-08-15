<?php

namespace App\Controller\Admin\Billing;

use App\Document\Payment\Product;
use App\Document\Payment\Subscription;
use App\Repository\DomainRepository;
use App\Repository\Payment\CurrencyRepository;
use App\Repository\Payment\PaymentRepository;
use App\Repository\Payment\ProductRepository;
use App\Repository\Payment\SubscriptionRepository;
use App\Repository\SiteRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
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

    public function view(CurrencyRepository $currencyRepository, ProductRepository $productRepository)
    {
        $cart = $this->session->get('cart');

        $products = $productRepository->findAllByIds(['5d32fda5d5ef96554d3da817']);

        return $this->render(
            'Admin/Billing/cart_view.html.twig',
            ['products' => $products]
        );
    }

    public function addProduct(Product $product, Subscription $subscription)
    {
        // todo: verification
        $cart = $this->session->get('cart');
        $productSubscriptionCartId = $product->getId() . $subscription->getId();
        $cart[$productSubscriptionCartId] = ['productId' => $product->getId(), 'subscriptionId' => $subscription->getId()];

        $this->session->set('cart', $cart);

        return $this->redirectToRoute('admin_billing_cart_view');
    }
}
