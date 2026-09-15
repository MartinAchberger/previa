<?php

namespace App\Http\Controllers;

use App\Mail\OrderShippedMail;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Support\AdminNotifier;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Inbound webhooks from Foxlog (warehouse → us).
 * Auth: shared secret in the `X-Foxlog-Token` header (or `?token=`), compared to
 * config('services.foxlog.webhook_secret').
 */
class FoxlogWebhookController extends Controller
{
    /** Map Foxlog statuses to our internal order statuses. Unknown → left unchanged. */
    private const STATUS_MAP = [
        'reservation' => 'new',
        'processing'  => 'confirmed',
        'confirmed'   => 'confirmed',
        'shipped'     => 'shipped',
        'sent'        => 'shipped',
        'delivered'   => 'delivered',
        'cancelled'   => 'cancelled',
        'canceled'    => 'cancelled',
    ];

    /**
     * Stock levels. Body: { "SKU-001": 42, "SKU-002": 0, ... }
     */
    public function stock(Request $request): JsonResponse
    {
        if (($resp = $this->authorizeRequest($request)) !== null) {
            return $resp;
        }

        $payload = $request->json()->all();
        if (!is_array($payload)) {
            return response()->json(['error' => 'Invalid payload'], 422);
        }

        $threshold = (int) config('services.foxlog.low_stock_threshold', 3);
        $updated = 0;
        $unknown = [];
        $lowNow = [];
        foreach ($payload as $sku => $level) {
            if (!is_scalar($sku) || !is_numeric($level)) {
                continue;
            }
            $level = (int) $level;
            $products = Product::withoutGlobalScopes()->where('sku', (string) $sku)->get(['id', 'name', 'volume', 'stock', 'published']);
            if ($products->isEmpty()) {
                $unknown[] = (string) $sku;
                continue;
            }
            $updated++;
            foreach ($products as $product) {
                // Alert once, when the level crosses the threshold downwards (not on every sync).
                $wasLow = $product->stock !== null && (int) $product->stock <= $threshold;
                if ($product->published && $level <= $threshold && !$wasLow) {
                    $lowNow[] = trim($product->name . ' ' . ($product->volume ?: '')) . ' (SKU ' . $sku . '): ' . ($level <= 0 ? 'vypredané' : $level . ' ks');
                }
            }
            Product::withoutGlobalScopes()->where('sku', (string) $sku)->update(['stock' => $level]);
        }

        if (!empty($lowNow)) {
            AdminNotifier::alert(
                count($lowNow) === 1 ? 'Nízky stav skladu: ' . $lowNow[0] : 'Nízky stav skladu (' . count($lowNow) . ' produktov)',
                'Sklad hlási, že tieto produkty klesli na ' . $threshold . ' ks alebo menej. Vypredané produkty sa v eshope zobrazujú ako nedostupné.',
                ['Produkty' => implode("\n", $lowNow)],
                route('platform.products'),
            );
        }

        return response()->json(['updated' => $updated, 'unknown_skus' => $unknown]);
    }

    /**
     * Order status + tracking. Body:
     * { "PH-26-0042": { "status": "shipped", "tracking_number": "…", "tracking_link": "…" }, ... }
     */
    public function orderStatus(Request $request): JsonResponse
    {
        if (($resp = $this->authorizeRequest($request)) !== null) {
            return $resp;
        }

        $payload = $request->json()->all();
        if (!is_array($payload)) {
            return response()->json(['error' => 'Invalid payload'], 422);
        }

        $updated = 0;
        $unknown = [];
        foreach ($payload as $reference => $info) {
            if (!is_array($info)) {
                continue;
            }
            $order = Order::where('order_number', (string) $reference)->first();
            if (!$order) {
                $unknown[] = (string) $reference;
                continue;
            }

            $rawStatus = isset($info['status']) ? mb_strtolower(trim((string) $info['status'])) : null;
            $wasShipped = $order->status === 'shipped';

            $fill = [];
            if ($rawStatus) {
                $fill['foxlog_status'] = $rawStatus;
                if (isset(self::STATUS_MAP[$rawStatus])) {
                    $fill['status'] = self::STATUS_MAP[$rawStatus];
                }
            }
            if (array_key_exists('tracking_number', $info)) {
                $fill['tracking_number'] = $info['tracking_number'] ?: null;
            }
            if (array_key_exists('tracking_link', $info)) {
                $fill['tracking_link'] = $info['tracking_link'] ?: null;
            }

            $order->forceFill($fill)->save();
            $updated++;

            // Notify the customer once, when the order first becomes "shipped".
            if (($fill['status'] ?? null) === 'shipped' && !$wasShipped && $order->tracking_number) {
                try {
                    Mail::to($order->customer_email)->send(new OrderShippedMail($order));
                } catch (Throwable $e) {
                    Log::error('Shipped email failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
                }
            }

            // COD delivered = money collected by the courier → record the payment
            // and issue the paid invoice (same flow the admin runs manually).
            if (($fill['status'] ?? null) === 'delivered' && $order->payment_method === 'cod') {
                $claimed = Order::whereKey($order->id)
                    ->where('payment_status', 'unpaid')
                    ->update(['payment_status' => 'paid', 'paid_at' => now()]);
                if ($claimed) {
                    try {
                        app(\App\Services\OrderPaidProcessor::class)->process($order->refresh());
                    } catch (Throwable $e) {
                        Log::error('COD delivered → invoice failed', ['order' => $order->order_number, 'error' => $e->getMessage()]);
                        app(\App\Services\SuperFaktura\SuperFakturaService::class)->recordError($order, $e);
                    }
                }
            }
        }

        return response()->json(['updated' => $updated, 'unknown_references' => $unknown]);
    }

    /**
     * Returns a 401 response if the shared secret is missing/wrong, otherwise null.
     */
    private function authorizeRequest(Request $request): ?JsonResponse
    {
        $secret = config('services.foxlog.webhook_secret');
        if (!$secret) {
            Log::error('Foxlog webhook secret not configured');
            return response()->json(['error' => 'Webhook not configured'], 500);
        }

        $provided = $request->header('X-Foxlog-Token') ?: $request->query('token', '');
        if (!is_string($provided) || !hash_equals((string) $secret, $provided)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return null;
    }
}
