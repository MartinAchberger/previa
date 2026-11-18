<?php

namespace App\Services;

use App\Models\Product;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Sellable quantity per warehouse item (SKU).
 *
 *   available = level reported by Foxlog − pieces in our orders Foxlog does not know yet
 *
 * Foxlog's number is assumed to already exclude orders it has received. So a
 * line is "reserved" (subtracted on top) while its order:
 *   - is not cancelled, and
 *   - was not pushed to Foxlog yet, or was pushed AFTER the last stock report
 *     (that report could not include it), and
 *   - is not an abandoned card payment (Stripe session expired).
 *
 * level null = stock not tracked → unlimited.
 */
class StockService
{
    /** Stripe Checkout session lifetime; an unpaid card order stops reserving after it. */
    public const CARD_SESSION_MINUTES = 31;
    private const CARD_RESERVE_MINUTES = 40;

    /** Statuses after which the warehouse has handled the order (even if pushed manually). */
    private const HANDLED_STATUSES = ['shipped', 'delivered', 'returned', 'cancelled'];

    public static function skuFor(Product $product, ?array $shade = null): string
    {
        return $shade !== null ? $product->shadeSku($shade) : (string) ($product->sku ?: $product->code);
    }

    /** [level|null, synced_at|null] as last reported for this item. */
    public static function levelOf(Product $product, ?array $shade = null): array
    {
        if ($shade !== null) {
            $level = isset($shade['stock']) ? (int) $shade['stock'] : null;
            $at = !empty($shade['stock_at']) ? Carbon::parse($shade['stock_at']) : null;
            return [$level, $at];
        }
        return [$product->stock === null ? null : (int) $product->stock, $product->stock_synced_at];
    }

    /**
     * Pieces reserved by our orders, per SKU. $syncedAt maps SKU → last stock
     * report time (null = never reported with a timestamp).
     *
     * @param  array<string, ?CarbonInterface>  $syncedAt
     * @return array<string, int>
     */
    public static function reserved(array $syncedAt, ?int $exceptOrderId = null): array
    {
        if (empty($syncedAt)) {
            return [];
        }

        $rows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('order_items.sku', array_keys($syncedAt))
            ->where('orders.status', '!=', 'cancelled')
            ->when($exceptOrderId, fn ($q) => $q->where('orders.id', '!=', $exceptOrderId))
            ->where(fn ($q) => $q
                ->where('orders.payment_method', '!=', 'card')
                ->orWhere('orders.payment_status', 'paid')
                ->orWhere('orders.created_at', '>=', now()->subMinutes(self::CARD_RESERVE_MINUTES)))
            ->where(fn ($q) => $q
                ->where(fn ($q) => $q->whereNull('orders.foxlog_sent_at')->whereNotIn('orders.status', self::HANDLED_STATUSES))
                ->orWhereNotNull('orders.foxlog_sent_at'))
            ->get(['order_items.sku', 'order_items.qty', 'orders.foxlog_sent_at']);

        $out = [];
        foreach ($rows as $row) {
            $at = $syncedAt[$row->sku] ?? null;
            // Pushed to Foxlog → reserved only if the last report predates the push.
            if ($row->foxlog_sent_at !== null && ($at === null || Carbon::parse($row->foxlog_sent_at)->lte($at))) {
                continue;
            }
            $out[$row->sku] = ($out[$row->sku] ?? 0) + (int) $row->qty;
        }
        return $out;
    }

    /** Sellable pieces of one item; null = not tracked (unlimited). */
    public static function available(Product $product, ?array $shade = null, ?int $exceptOrderId = null): ?int
    {
        [$level, $at] = self::levelOf($product, $shade);
        if ($level === null) {
            return null;
        }
        $sku = self::skuFor($product, $shade);
        $reserved = self::reserved([$sku => $at], $exceptOrderId)[$sku] ?? 0;
        return max(0, $level - $reserved);
    }

    /**
     * Every warehouse item of the given products with level, reservation and
     * availability, keyed by SKU. One query for the whole set.
     *
     * @return Collection<string, array{product: Product, shade: ?array, sku: string, level: ?int, reserved: int, available: ?int}>
     */
    public static function items(iterable $products): Collection
    {
        $items = [];
        foreach ($products as $product) {
            $shades = $product->hasShades()
                ? collect($product->shades)->filter(fn ($s) => is_array($s) && isset($s['code']))->all()
                : [null];
            foreach ($shades as $shade) {
                [$level, $at] = self::levelOf($product, $shade);
                $sku = self::skuFor($product, $shade);
                $items[] = compact('product', 'shade', 'sku', 'level', 'at');
            }
        }

        $tracked = collect($items)->whereNotNull('level');
        $reserved = self::reserved($tracked->mapWithKeys(fn ($i) => [$i['sku'] => $i['at']])->all());

        return collect($items)->map(function (array $i) use ($reserved) {
            $r = $i['level'] === null ? 0 : ($reserved[$i['sku']] ?? 0);
            unset($i['at']);
            return $i + ['reserved' => $r, 'available' => $i['level'] === null ? null : max(0, $i['level'] - $r)];
        });
    }
}
