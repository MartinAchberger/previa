<?php

namespace App\Services\Foxlog;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Outbound integration with the Foxlog fulfilment warehouse.
 * Docs: POST /shipping_order/add (create/update), /shipping_order/cancel.
 * Auth: api_token in the request body.
 */
class FoxlogService
{
    public function enabled(): bool
    {
        return (bool) config('services.foxlog.enabled') && filled(config('services.foxlog.api_token'));
    }

    /**
     * Push an order to the warehouse as `processing` (ready to pick & ship).
     * Called for COD + pay-by-invoice at checkout, and for card after payment.
     */
    public function sendOrder(Order $order): void
    {
        $this->post('/shipping_order/add', ['items' => [$this->buildItem($order)]], $order, 'processing');
    }

    /**
     * Cancel an order in the warehouse.
     */
    public function cancelOrder(Order $order): void
    {
        if (!$this->enabled()) {
            return;
        }

        try {
            $res = Http::asJson()->acceptJson()->post($this->url('/shipping_order/cancel'), [
                'api_token' => config('services.foxlog.api_token'),
                'reference' => $order->order_number,
            ]);

            if ($res->failed()) {
                throw new \RuntimeException('HTTP ' . $res->status() . ': ' . $res->body());
            }

            $order->forceFill(['foxlog_status' => 'cancelled'])->save();
        } catch (Throwable $e) {
            Log::error('Foxlog cancel failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
        }
    }

    private function post(string $path, array $payload, Order $order, string $status): void
    {
        if (!$this->enabled()) {
            return;
        }

        try {
            $res = Http::asJson()->acceptJson()->post($this->url($path), array_merge(
                ['api_token' => config('services.foxlog.api_token')],
                $payload,
            ));

            if ($res->failed()) {
                throw new \RuntimeException('HTTP ' . $res->status() . ': ' . $res->body());
            }

            $order->forceFill([
                'foxlog_status'  => $status,
                'foxlog_sent_at' => now(),
            ])->save();
        } catch (Throwable $e) {
            Log::error('Foxlog send failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
            $order->forceFill(['foxlog_status' => 'error'])->save();
        }
    }

    private function buildItem(Order $order): array
    {
        return array_filter([
            'reference'           => $order->order_number,
            'secondary_reference' => (string) $order->id,
            'order_status'        => 'processing',
            'payment_id'          => $order->payment_method === 'cod' ? 'cod' : 'bacs',
            // COD collects on delivery → send the amount. Card/invoice are not COD.
            'total'               => $order->payment_method === 'cod' ? (float) $order->total : null,
            'currency'            => 'EUR',
            'shipping_name'       => $order->customer_name,
            'shipping_contact'    => $order->customer_name,
            'shipping_address_1'  => $order->shipping_address,
            'shipping_city'       => $order->shipping_city,
            'shipping_postcode'   => $order->shipping_zip,
            'shipping_country'    => $order->shipping_country ?: 'SK',
            'shipping_telephone'  => $order->customer_phone,
            'shipping_email'      => $order->customer_email,
            'shipping_id'         => $order->shipping_carrier,   // carrier (Phase 3); null = home delivery
            'branch_id'           => $order->pickup_point_id,    // pickup point (Phase 3)
            'note'                => $order->notes,
            'sale_items'          => $this->buildSaleItems($order),
        ], fn ($v) => $v !== null && $v !== '');
    }

    private function buildSaleItems(Order $order): array
    {
        $items = [];
        foreach ($order->items as $item) {
            $product = Product::withoutGlobalScopes()->find($item->product_id);
            $sku = $product?->sku ?: $item->product_code;
            // Shade lines store the shade code in product_code — every single
            // shade is its own warehouse item, so it gets a per-shade SKU
            // derived as "<product sku>-<shade code>".
            if ($product && $item->product_code !== $product->code) {
                $shade = $product->findShade($item->product_code);
                $sku = $shade['sku'] ?? (($product->sku ?: $product->code) . '-' . $item->product_code);
            }
            $items[] = array_filter([
                'sku'      => $sku,
                'name'     => $item->product_name,
                'quantity' => (int) $item->qty,
            ], fn ($v) => $v !== null && $v !== '');
        }
        return $items;
    }

    private function url(string $path): string
    {
        return rtrim((string) config('services.foxlog.base_url'), '/') . $path;
    }
}
