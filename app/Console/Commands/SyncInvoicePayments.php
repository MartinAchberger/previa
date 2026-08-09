<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\SuperFaktura\SuperFakturaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use SuperFaktura\ApiClient\Contract\Invoice\InvoiceStatus;
use Throwable;

/**
 * Pulls payment status from SuperFaktúra for open pay-by-invoice orders.
 * SF has no webhooks — bank pairing (Tatra banka) marks invoices paid inside
 * SF, and this scheduled sync mirrors that back onto our orders (which also
 * unblocks the salon's next pay-by-invoice order).
 */
class SyncInvoicePayments extends Command
{
    protected $signature = 'superfaktura:sync-payments';

    protected $description = 'Mark transfer orders paid when their SF invoice is paid (bank pairing)';

    public function handle(SuperFakturaService $sf): int
    {
        $orders = Order::where('payment_method', 'transfer')
            ->where('payment_status', 'unpaid')
            ->where('status', '!=', 'cancelled')
            ->whereNotNull('invoice_sf_id')
            ->get();

        $paid = 0;
        foreach ($orders as $order) {
            try {
                $status = $sf->fetchInvoiceStatus($order);
            } catch (Throwable $e) {
                Log::warning('SF payment sync failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
                continue;
            }

            if ($status === InvoiceStatus::PAID->value) {
                $order->forceFill(['payment_status' => 'paid', 'paid_at' => now()])->save();
                $paid++;
                $this->info($order->order_number . ' → paid');
            }
        }

        $this->info(sprintf('Checked %d open invoice orders, %d marked paid.', $orders->count(), $paid));
        return self::SUCCESS;
    }
}
