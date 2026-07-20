<?php

namespace App\Http\Controllers;

use App\Mail\AdminNewOrderMail;
use App\Mail\InvoiceMail;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Services\StripeService;
use App\Services\SuperFaktura\SuperFakturaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Stripe\Webhook;
use Throwable;

class StripeController extends Controller
{
    public function success(Request $request, string $orderNumber, StripeService $stripe): RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('checkout.show')->withErrors(['payment' => 'Chýba session_id v návrate zo Stripe.']);
        }

        try {
            $session = $stripe->retrieveSession($sessionId);
        } catch (Throwable $e) {
            Log::error('Stripe session retrieve failed', ['order' => $orderNumber, 'error' => $e->getMessage()]);
            return redirect()->route('checkout.show')->withErrors(['payment' => 'Nepodarilo sa overiť platbu zo Stripe.']);
        }

        if (($session->payment_status ?? null) !== 'paid') {
            return redirect()->route('checkout.show')->withErrors(['payment' => 'Platba zatiaľ nie je potvrdená. Skúste neskôr alebo kontaktujte podporu.']);
        }

        // Bind the Stripe session to THIS order. Without this an attacker could
        // replay any paid session_id against a guessable order number to flip it
        // to paid and reach its signed confirmation URL. We verify both the order
        // reference and the charged amount.
        $sessionOrderNumber = $session->metadata->order_number ?? $session->client_reference_id ?? null;
        $expectedAmount = (int) round(((float) $order->total) * 100);
        if ($sessionOrderNumber !== $order->order_number || (int) ($session->amount_total ?? -1) !== $expectedAmount) {
            Log::warning('Stripe success: session does not match order', [
                'order' => $orderNumber,
                'session_order' => $sessionOrderNumber,
                'session_amount' => $session->amount_total ?? null,
                'expected_amount' => $expectedAmount,
            ]);
            return redirect()->route('checkout.show')->withErrors(['payment' => 'Platbu sa nepodarilo priradiť k objednávke.']);
        }

        if ($this->markPaidOnce($order)) {
            $this->afterPaid($order->refresh(), $session);
        }

        return redirect(URL::temporarySignedRoute(
            'order.confirmation',
            now()->addDays(30),
            ['orderNumber' => $order->order_number],
        ));
    }

    public function webhook(Request $request): JsonResponse
    {
        $secret = config('services.stripe.webhook_secret');
        if (!$secret) {
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                $request->header('Stripe-Signature', ''),
                $secret,
            );
        } catch (Throwable $e) {
            Log::warning('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type !== 'checkout.session.completed') {
            return response()->json(['received' => true]);
        }

        $session = $event->data->object;
        $orderNumber = $session->metadata->order_number ?? $session->client_reference_id ?? null;
        if (!$orderNumber) {
            return response()->json(['received' => true]);
        }

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            Log::warning('Stripe webhook: order not found', ['order_number' => $orderNumber]);
            return response()->json(['received' => true]);
        }

        if (($session->payment_status ?? null) !== 'paid') {
            return response()->json(['received' => true]);
        }

        $expectedAmount = (int) round(((float) $order->total) * 100);
        if ((int) ($session->amount_total ?? -1) !== $expectedAmount) {
            Log::warning('Stripe webhook: amount mismatch', [
                'order' => $orderNumber,
                'session_amount' => $session->amount_total ?? null,
                'expected_amount' => $expectedAmount,
            ]);
            return response()->json(['received' => true]);
        }

        if ($this->markPaidOnce($order)) {
            $this->afterPaid($order->refresh(), $session);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Atomically transition the order to paid. Returns true only for the single
     * request that actually performs the transition, so invoice issuance + email
     * happen exactly once even when the browser return and the webhook race.
     */
    private function markPaidOnce(Order $order): bool
    {
        $affected = Order::where('id', $order->id)
            ->where('payment_status', '!=', 'paid')
            ->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'status' => DB::raw("CASE WHEN status = 'new' THEN 'confirmed' ELSE status END"),
            ]);

        return $affected > 0;
    }

    public function cancel(string $orderNumber): RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return redirect()->route('checkout.show')
            ->with('warning', 'Platba bola zrušená. Objednávka ' . $order->order_number . ' čaká na úhradu - môžeš to skúsiť znova alebo zmeniť spôsob platby.');
    }

    /**
     * Runs exactly once per order (guarded by markPaidOnce): stores the payment
     * intent for later refunds, queues the confirmation + admin emails, then
     * issues the invoice.
     */
    private function afterPaid(Order $order, object $session): void
    {
        $intent = $session->payment_intent ?? null;
        if ($intent) {
            $order->forceFill(['stripe_payment_intent' => is_string($intent) ? $intent : ($intent->id ?? null)])->save();
        }

        try {
            Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
            Mail::to(config('mail.admin_address'))->send(new AdminNewOrderMail($order));
        } catch (Throwable $e) {
            Log::error('Order emails failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
        }

        $this->issueInvoice($order, $session->id ?? null);

        // Card is now paid → push the order to the fulfilment warehouse.
        app(\App\Services\Foxlog\FoxlogService::class)->sendOrder($order);
    }

    private function issueInvoice(Order $order, ?string $stripeSessionId = null): void
    {
        $sf = app(SuperFakturaService::class);

        try {
            $sf->issueForOrder($order);
        } catch (Throwable $e) {
            Log::error('SuperFaktura issue failed (after Stripe)', [
                'order' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
            $sf->recordError($order, $e);
            return;
        }

        try {
            $sf->markInvoicePaid($order->refresh(), $stripeSessionId);
        } catch (Throwable $e) {
            Log::warning('SuperFaktura mark paid failed', [
                'order' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }

        $path = $order->activeDocumentPdfPath();
        if (!$path) return;

        try {
            Mail::to($order->customer_email)->send(new InvoiceMail($order));
            $order->forceFill(['invoice_sent_at' => now()])->save();
        } catch (Throwable $e) {
            Log::error('Invoice email failed', [
                'order' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
