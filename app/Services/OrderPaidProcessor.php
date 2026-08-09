<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Models\Order;
use App\Services\SuperFaktura\SuperFakturaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Invoicing side effects for an order that just became paid. Shared by the
 * admin order screen and the Foxlog webhook (COD delivered → paid).
 * The caller is responsible for setting payment_status/paid_at beforehand.
 */
class OrderPaidProcessor
{
    public function __construct(private SuperFakturaService $sf) {}

    /**
     * Ensure a paid regular invoice exists + email it. Returns a human-readable
     * summary (used for admin toasts). Throws on SuperFaktura failure — callers
     * decide how to surface it.
     */
    public function process(Order $order): string
    {
        if ($order->hasProforma() && !$order->hasInvoice()) {
            $this->sf->convertProformaToInvoice($order);
            $this->sf->markInvoicePaid($order->refresh());
            return 'Vystavená ostrá faktúra a zaznamenaná platba.' . $this->emailOutcome($order->refresh());
        }

        if ($order->hasInvoice()) {
            $this->sf->markInvoicePaid($order->refresh());
            return 'Platba zaznamenaná v SuperFaktúre.';
        }

        // No document yet (typicky dobierka) → vystaviť faktúru teraz.
        $this->sf->issueForOrder($order);
        $this->sf->markInvoicePaid($order->refresh());
        return 'Vystavená faktúra a zaznamenaná platba.' . $this->emailOutcome($order->refresh());
    }

    /** Email failures are logged, never thrown — the payment itself is already recorded. */
    private function emailOutcome(Order $order): string
    {
        if (!$order->activeDocumentPdfPath()) {
            return ' E-mail zákazníkovi NEODIŠIEL — chýba PDF faktúry.';
        }

        try {
            Mail::to($order->customer_email)->send(new InvoiceMail($order));
            $order->forceFill(['invoice_sent_at' => now()])->save();
            return ' E-mail odoslaný zákazníkovi.';
        } catch (Throwable $e) {
            Log::error('Invoice email failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            return ' E-mail zákazníkovi zlyhal — pozri log.';
        }
    }
}
