<?php

namespace App\Controller\Admin\Billing;

use App\Billing\Cart;
use App\Document\Payment\Product;
use App\Document\Payment\Subscription;
use App\Repository\CurrencyRepository;
use App\Repository\ProductRepository;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends AbstractController
{
    private $session;
    private Cart $cart;

    public function __construct(SessionInterface $session, Cart $cart)
    {
        $this->session = $session;
        $this->cart = $cart;
    }

    public function view(CurrencyRepository $currencyRepository, ProductRepository $productRepository)
    {
        /** @var Cart $cart */
        $cart = $this->session->get('cart');
        if(null == $cart) {
            $cart = new Cart();
        }

        $subscriptions = $cart->getSubscriptions();
        $productsBySubscriptionId = [];
        if(null != $subscriptions) {
            foreach ($subscriptions as $subscription) {
                $productsBySubscriptionId[$subscription->getId()] = $productRepository->find($subscription->getProduct()->getId());
            }
        }

        return $this->render(
            'Admin/Billing/cart_view.html.twig',
            [
                'subscriptions' => $subscriptions,
                'productsBySubscriptionId' => $productsBySubscriptionId,
                'taxAmount' => $cart->getTaxes(),
                'totalAmountWithTaxes' => $cart->getTotalWithTaxes()
            ]
        );
    }

    public function addSubscription(Product $product, Subscription $subscription)
    {
        /** @var Cart $cart */
        $cart = $this->session->get('cart');
        if(null == $cart) {
            $cart = new Cart();
        }

        $cart->addSubscription($subscription);

        $this->session->set('cart', $cart);

        return $this->redirectToRoute('admin_billing_cart_view');
    }

    public function checkout(): JsonResponse
    {
        Stripe::setApiKey('sk_test_51I1XREEhLqLirlZw3vmtBlPk3MtGW7TtxckCDmXFWAnAJtcYzpDJ8D0J55wIFUqSUHnOoQKhbzcqnhl2tfy3SaC9006ExulQVP');
        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => 2000,
                        'product_data' => [
                            'name' => 'Stubborn Attachments',
                            'images' => ["https://i.imgur.com/EHyR2nP.png"],
                        ],
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => 'http://berform.kik/success.html',
            'cancel_url' => 'http://berform.kik/cancel.html',
        ]);

        return new JsonResponse(['id' => $checkout_session->id]);
    }
}
