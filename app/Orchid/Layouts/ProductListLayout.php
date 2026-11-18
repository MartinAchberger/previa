<?php

namespace App\Orchid\Layouts;

use App\Models\Product;
use App\Orchid\Screens\StockListScreen;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProductListLayout extends Table
{
    public $target = 'products';

    protected function columns(): array
    {
        return [
            TD::make('sort_order', '#')->sort()->width('60px'),
            TD::make('code', 'n°')->width('60px'),
            TD::make('name', 'Produkt')->sort()->filter(TD::FILTER_TEXT)->render(fn (Product $p) =>
                Link::make($p->name)->route('platform.products.edit', $p->id)
            ),
            TD::make('line_label', 'Línia')->sort()->filter(TD::FILTER_TEXT),
            TD::make('complex', 'Komplex')->width('90px'),
            TD::make('volume', 'Objem')->width('90px'),
            TD::make('price', 'Cena')->sort()->render(fn (Product $p) => $p->price_formatted),
            TD::make('stock', 'Sklad')->width('110px')->render(fn (Product $p) => self::stockCell($p)),
            TD::make('published', 'Pub.')->render(fn (Product $p) => $p->published ? '✓' : '-'),
            TD::make('actions', '')->alignRight()->render(fn (Product $p) =>
                Link::make('Edit')->route('platform.products.edit', $p->id)->icon('bs.pencil')
            ),
        ];
    }

    /** Single item: its level. Shades: total pieces, plus how many shades are sold out. */
    private static function stockCell(Product $p): string
    {
        if (!$p->hasShades()) {
            $stock = $p->stock === null ? null : (int) $p->stock;
            return $stock === null ? '—' : StockListLayout::badge(StockListScreen::stateOf($stock)) . ' ' . $stock;
        }
        $levels = collect($p->shades)->filter(fn ($s) => is_array($s) && isset($s['stock']))->map(fn ($s) => (int) $s['stock']);
        if ($levels->isEmpty()) {
            return '—';
        }
        $out = $levels->filter(fn ($v) => $v <= 0)->count();
        return $levels->sum() . ' ks' . ($out ? ' <span class="badge bg-danger">' . $out . ' vypred.</span>' : '');
    }
}
