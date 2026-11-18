<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use App\Orchid\Layouts\StockListLayout;
use Illuminate\Support\Collection;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Repository;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;

/**
 * Read-only warehouse overview. One row per Foxlog stock item: a product
 * without shades is one item, a product with shades has one item per shade.
 * Levels come from the Foxlog stock webhook — nothing here is editable.
 */
class StockListScreen extends Screen
{
    public const FILTERS = [
        'all'       => 'Všetko',
        'out'       => 'Vypredané',
        'low'       => 'Nízky stav',
        'ok'        => 'Na sklade',
        'untracked' => 'Nesledované',
    ];

    public function permission(): ?iterable { return ['platform.eshop.catalog']; }

    public function name(): string { return 'Sklad'; }

    public function description(): string
    {
        return 'Stav zásob podľa skladu Foxlog. Nízky stav = ' . self::threshold() . ' ks a menej. Nesledované = Foxlog pre SKU ešte neposlal stav, produkt sa predáva bez obmedzenia.';
    }

    public function query(): array
    {
        $items  = self::items();
        $filter = array_key_exists((string) request('stav'), self::FILTERS) ? (string) request('stav') : 'all';
        $counts = $items->countBy('state');

        return [
            'metrics' => [
                'total'     => ['value' => number_format($items->count(), 0, ',', ' ')],
                'out'       => ['value' => (string) ($counts['out'] ?? 0)],
                'low'       => ['value' => (string) ($counts['low'] ?? 0)],
                'untracked' => ['value' => (string) ($counts['untracked'] ?? 0)],
            ],
            'stock' => $items
                ->when($filter !== 'all', fn (Collection $c) => $c->where('state', $filter))
                ->map(fn (array $row) => new Repository($row))
                ->values(),
        ];
    }

    public function commandBar(): array
    {
        $current = (string) request('stav', 'all');

        return collect(self::FILTERS)->map(fn (string $label, string $key) =>
            Link::make($label)
                ->route('platform.stock', $key === 'all' ? [] : ['stav' => $key])
                ->type($current === $key ? Color::PRIMARY : Color::DEFAULT)
        )->values()->all();
    }

    public function layout(): array
    {
        return [
            Layout::metrics([
                'Skladové položky' => 'metrics.total',
                'Vypredané'        => 'metrics.out',
                'Nízky stav'       => 'metrics.low',
                'Nesledované'      => 'metrics.untracked',
            ]),
            StockListLayout::class,
        ];
    }

    public static function threshold(): int
    {
        return (int) config('services.foxlog.low_stock_threshold', 3);
    }

    public static function stateOf(?int $stock): string
    {
        return match (true) {
            $stock === null               => 'untracked',
            $stock <= 0                   => 'out',
            $stock <= self::threshold()   => 'low',
            default                       => 'ok',
        };
    }

    /** Every stock item, most urgent first (sold out → low → in stock → untracked). */
    public static function items(): Collection
    {
        $rank = ['out' => 0, 'low' => 1, 'ok' => 2, 'untracked' => 3];

        return Product::orderBy('sort_order')->get()
            ->flatMap(function (Product $p) {
                if (!$p->hasShades()) {
                    $stock = $p->stock === null ? null : (int) $p->stock;
                    return [self::row($p, '—', $p->sku ?: $p->code, $stock)];
                }
                return collect($p->shades)
                    ->filter(fn ($s) => is_array($s) && isset($s['code']))
                    ->map(fn (array $s) => self::row(
                        $p,
                        trim($s['code'] . ' ' . ($s['name'] ?? '')),
                        $p->shadeSku($s),
                        isset($s['stock']) ? (int) $s['stock'] : null,
                    ));
            })
            ->sort(fn ($a, $b) => [$rank[$a['state']], $a['stock'] ?? PHP_INT_MAX, $a['sort']]
                              <=> [$rank[$b['state']], $b['stock'] ?? PHP_INT_MAX, $b['sort']])
            ->values();
    }

    private static function row(Product $p, string $variant, string $sku, ?int $stock): array
    {
        return [
            'product_id' => $p->id,
            'name'       => $p->name,
            'volume'     => $p->volume,
            'variant'    => $variant,
            'sku'        => $sku,
            'stock'      => $stock,
            'state'      => self::stateOf($stock),
            'published'  => (bool) $p->published,
            'sort'       => (int) $p->sort_order,
        ];
    }
}
