<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Repository;
use Orchid\Screen\TD;

class StockListLayout extends Table
{
    public $target = 'stock';

    protected function columns(): array
    {
        return [
            TD::make('name', 'Produkt')->render(fn (Repository $r) =>
                Link::make($r->get('name'))->route('platform.products.edit', $r->get('product_id'))
            ),
            TD::make('volume', 'Objem')->width('90px'),
            TD::make('variant', 'Odtieň'),
            TD::make('sku', 'SKU (Foxlog)'),
            TD::make('stock', 'Stav (ks)')->alignRight()->width('100px')->render(fn (Repository $r) =>
                $r->get('stock') === null ? '—' : '<strong>' . (int) $r->get('stock') . '</strong>'
            ),
            TD::make('state', '')->width('130px')->render(fn (Repository $r) => self::badge($r->get('state'))),
            TD::make('published', 'Pub.')->width('60px')->render(fn (Repository $r) => $r->get('published') ? '✓' : '-'),
        ];
    }

    public static function badge(string $state): string
    {
        [$label, $class] = match ($state) {
            'out'   => ['Vypredané', 'bg-danger'],
            'low'   => ['Nízky stav', 'bg-warning text-dark'],
            'ok'    => ['Na sklade', 'bg-success'],
            default => ['Nesledované', 'bg-secondary'],
        };
        return '<span class="badge ' . $class . '">' . $label . '</span>';
    }

    protected function textNotFound(): string { return 'Žiadne položky'; }

    protected function subNotFound(): string { return 'Pre tento filter nie sú žiadne skladové položky.'; }
}
