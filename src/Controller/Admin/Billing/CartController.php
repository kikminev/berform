<?php

namespace App\Controller\Admin\Billing;

use App\Billing\Cart;
use App\Entity\Product;
use App\Entity\Subscription;
use App\Repository\CurrencyRepository;
use App\Repository\ProductRepository;
use Exception;
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
            foreach ($subscriptions as $subscriptionId => $productId) {
                $productsBySubscriptionId[$subscriptionId] = $productRepository->find($productId);
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

        $cart->attachSubscriptionToNewProduct($subscription, $product);

        $this->session->set('cart', $cart);

        return $this->redirectToRoute('admin_billing_cart_view');
    }

    public function checkout(): JsonResponse
    {
        Stripe::setApiKey('sk_test_51I1XREEhLqLirlZw3vmtBlPk3MtGW7TtxckCDmXFWAnAJtcYzpDJ8D0J55wIFUqSUHnOoQKhbzcqnhl2tfy3SaC9006ExulQVP');

        try {
            $checkout_session = \Stripe\Checkout\Session::create([
                'success_url' => 'https://example.com/success.html?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'https://example.com/canceled.html',
                'payment_method_types' => ['card'],
                'mode' => 'subscription',
                'line_items' => [[
                    'price' => 'price_1IIGJPEhLqLirlZwTw1kvykg',
                    'quantity' => 1,
                ]],
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }


        return new JsonResponse(['sessionId' => $checkout_session['id']]);
    }
}
