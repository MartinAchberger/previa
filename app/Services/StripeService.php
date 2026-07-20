<?php

namespace App\Services;

use App\Models\Order;
use Stripe\Checkout\Session;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $client;

    public function __construct()
    {
        $this->client = new StripeClient(config('services.stripe.secret'));
    }

    public function createCheckoutSession(Order $order): Session
    {
        $lineItems = [];
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => trim(($item->product_code ? "n° {$item->product_code} · " : '') . $item->product_name),
                        'description' => trim(($item->product_line_label ? "{$item->product_line_label} · " : '') . ($item->product_volume ?? '')),
                    ],
                    'unit_amount' => (int) round(((float) $item->unit_price) * 100),
                ],
                'quantity' => (int) $item->qty,
            ];
        }

        if (((float) $order->shipping) > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => ['name' => 'Doprava - kuriér'],
                    'unit_amount' => (int) round(((float) $order->shipping) * 100),
                ],
                'quantity' => 1,
            ];
        }

        return $this->client->checkout->sessions->create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'customer_email' => $order->customer_email,
            'client_reference_id' => $order->order_number,
            'metadata' => [
                'order_number' => $order->order_number,
                'order_id' => (string) $order->id,
            ],
            'success_url' => route('stripe.success', ['orderNumber' => $order->order_number]) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.cancel', ['orderNumber' => $order->order_number]),
            'locale' => 'sk',
        ]);
    }

    public function retrieveSession(string $sessionId): Session
    {
        return $this->client->checkout->sessions->retrieve($sessionId);
    }

    /**
     * Full refund of the order's payment. Requires the payment intent stored
     * at payment time. Stripe refund creation is idempotent per intent for
     * full refunds — a second call fails with "charge already refunded".
     */
    public function refundOrder(Order $order): \Stripe\Refund
    {
        if (empty($order->stripe_payment_intent)) {
            throw new \RuntimeException('Objednávka nemá uložený Stripe payment intent - refund treba spraviť ručne v Stripe dashboarde.');
        }

        return $this->client->refunds->create([
            'payment_intent' => $order->stripe_payment_intent,
            'metadata' => ['order_number' => $order->order_number],
        ]);
    }
}
