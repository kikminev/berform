<?php

namespace App\Controller\Admin\Billing;

use App\Billing\Cart;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Repository\CurrencyRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\TransactionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Gedmo\Translator\TranslationInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Mail\Message;
use App\Mail\Sender;
use Symfony\Contracts\Translation\TranslatorInterface;

class CartController extends AbstractController
{
    private $session;
    private Cart $cart;
    private EntityManagerInterface $entityManager;

    public function __construct(SessionInterface $session, Cart $cart, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->cart = $cart;
        $this->entityManager = $entityManager;
    }

    public function view(CurrencyRepository $currencyRepository, ProductRepository $productRepository)
    {
        /** @var Cart $cart */
        $cart = $this->session->get('cart');
        if (null == $cart) {
            $cart = new Cart();
        }

        $subscriptions = $cart->getSubscriptions();
        $productsBySubscriptionId = [];
        if (null != $subscriptions) {
            foreach ($subscriptions as $subscriptionId => $productId) {
                $productsBySubscriptionId[$subscriptionId] = $productRepository->find($productId);
            }
        }

        return $this->render(
            'Admin/Billing/cart_view.html.twig',
            [
                'cartCurrency' => $cart->getCurrency()->getSystemCode(),
                'subscriptions' => $subscriptions,
                'productsBySubscriptionId' => $productsBySubscriptionId,
                'taxAmount' => $cart->getTaxes(),
                'totalAmountWithTaxes' => $cart->getTotalWithTaxes(),
            ]
        );
    }

    public function success()
    {
        return $this->render(
            'Admin/Billing/success.html.twig'
        );
    }


    public function cancelled()
    {
        return $this->render(
            'Admin/Billing/cancelled.html.twig'
        );
    }

    public function addSubscription(
        Product $product,
        Subscription $subscription,
        CurrencyRepository $currencyRepository
    ) {
        /** @var Cart $cart */
        $cart = $this->session->get('cart');
        if (null === $cart) {
            $currency = $currencyRepository->findOneBy(['systemCode' => 'GBP']);
            $cart = new Cart();
            $cart->setCurrency($currency);
        }
        $cart->attachSubscriptionToNewProduct($subscription, $product);

        $this->session->set('cart', $cart);

        return $this->redirectToRoute('admin_billing_cart_view');
    }

    public function checkout(Request $request, OrderRepository $orderRepository): JsonResponse
    {
        $stripeSecretKey = $this->getParameter('stripe_secret_key');
        Stripe::setApiKey($stripeSecretKey);

        $priceId = $request->get('priceId');
        $defaultUKTaxRate = 'txr_1IMbPYEhLqLirlZwOSYtNkHI';

        try {

            $order = $orderRepository->findOneBy(['userCustomer' => $this->getUser(), 'status' => Order::STATUS_CART]);

            if(null === $order) {
                $order = new Order();
                $order->setUserCustomer($this->getUser());
                $order->setCreatedAt(new DateTime());
                $order->setUpdatedAt(new DateTime());
                $order->setStatus(Order::STATUS_CART);

                $cart = $this->session->get('cart');
                $order->setTotalAmount($cart->getTotalWithTaxes());
            }

            $this->entityManager->persist($order);
            $this->entityManager->flush();

            $checkout_session = \Stripe\Checkout\Session::create([
                'success_url' => $this->generateUrl('admin_billing_cart_success',
                    ['session_id' => '{CHECKOUT_SESSION_ID}'],
                    UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('admin_billing_cart_cancelled',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL),
                'payment_method_types' => ['card'],
                'customer_email' => $this->getUser()->getUsername(),
                'mode' => 'subscription',
                'metadata' => ['orderNumber' => $order->getId()],
                'line_items' => [
                    [
                        'price' => $priceId,
                        'quantity' => 1,
                        'tax_rates' => [$defaultUKTaxRate],
                    ],
                ],
            ]);

            $order->setStripeId($checkout_session['id']);
            $this->entityManager->flush();


        } catch (Exception $e) {
            return new JsonResponse([
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], 400);
        }

        return new JsonResponse(['sessionId' => $checkout_session['id']]);
    }

    public function stripeWebhookEndpoint(Request $request, OrderRepository $orderRepository, TransactionRepository  $transactionRepository, Sender $sender, TranslatorInterface  $translator)
    {
        $stripeSecretKey = $this->getParameter('stripe_secret_key');
        $webhookSecret = $this->getParameter('stripe_webhook_secret_key');

        \Stripe\Stripe::setApiKey(
            $stripeSecretKey
        );


        $object = json_decode($request->getContent());

        if(!isset($object->type)) {
            throw new Exception('Webhook: type should be set');
        }

        if(!isset($object->id)) {
            throw new Exception('Webhook: id should be set');
        }

        $type = $object->type;

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->headers->get('stripe-signature'),
                $webhookSecret
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()]);
        }

        switch ($type) {
            case 'invoice.paid':
                if(!isset($object->data->object->hosted_invoice_url)) {
                    throw new Exception('Webhook: hosted_invoice_url should be set');
                }

                if(!isset($object->data->object->hosted_invoice_url)) {
                    throw new Exception('Webhook: hosted_invoice_url should be set');
                }

                if(!isset($object->data->object->invoice_pdf)) {
                    throw new Exception('Webhook: invoice_pdf should be set');
                }

                if(!isset($object->data->object->paid)) {
                    throw new Exception('Webhook: paid should be set');
                }

                if(!isset($object->data->object->subscription)) {
                    throw new Exception('Webhook: subscription should be set');
                }

                if(!isset($object->data->object->customer_email)) {
                    throw new Exception('Webhook: customer_email should be set');
                }

                $transaction = $transactionRepository->findOneBy(['subscription' => $object->data->object->subscription]);
                if(null === $transaction) {
                    throw new Exception('Webhook: Transaction was not found by subscription id');
                }

                $transaction->setStatus(Transaction::STATUS_COMPLETED);
                $transaction->setInvoicePdf($object->data->object->invoice_pdf);
                $this->entityManager->flush();


                $message = $this->render('Mail/Payment/successful_payment.html.twig', ['invoice_pdf' => $object->data->object->invoice_pdf])->getContent();
                $messageTEst = new Message($stripeSecretKey = $this->getParameter('system_no_reply_email'), $object->data->object->customer_email, $message, $translator->trans('landing_sitec_successful_payment_email_subject'));
                $sender->send($messageTEst);

                break;
            case 'checkout.session.completed':
                if(!isset($object->data->object->metadata->orderNumber)) {
                    throw new Exception('Webhook: no order number provided');
                }

                if(!isset($object->data->object->subscription)) {
                    throw new Exception('Webhook: no subscription number provided');
                }

                $orderNumber = $object->data->object->metadata->orderNumber;
                $order = $orderRepository->find($orderNumber);
                if(null === $order) {
                    throw new Exception('Webhook: order was not found by webhok order number');
                }

                $order->setStatus(Order::STATUS_COMPLETED);

                $transaction = new Transaction();
                $transaction->setCreatedAt(new DateTime());
                $transaction->setUpdatedAt(new DateTime());
                $transaction->setSubscription($object->data->object->subscription);
                $transaction->setOrder($order);
                $transaction->setStatus(Transaction::STATUS_PENDING);
                $this->entityManager->persist($transaction);

                $this->entityManager->flush();

                break;
            case 'invoice.payment_failed':
                // The payment failed or the customer does not have a valid payment method.
                // The subscription becomes past_due. Notify your customer and send them to the
                // customer portal to update their payment information.
                break;
            // ... handle other event types
            default:
                // Unhandled event type
        }

        return new JsonResponse('ok');
    }
}
