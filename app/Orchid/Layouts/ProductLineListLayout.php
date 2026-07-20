<?php

namespace App\Orchid\Layouts;

use App\Models\ProductLine;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProductLineListLayout extends Table
{
    public $target = 'lines';

    protected function columns(): array
    {
        return [
            TD::make('sort_order', 'Poradie')->sort()->width('80px'),
            TD::make('code', 'Kód')->width('80px'),
            TD::make('name', 'Línia')->sort()->filter(TD::FILTER_TEXT)->render(fn (ProductLine $l) =>
                Link::make($l->name)->route('platform.lines.edit', $l->id)
            ),
            TD::make('eyebrow', 'Popis'),
            TD::make('complex', 'Komplex')->width('100px'),
            TD::make('products_count', 'Produkty')->render(fn (ProductLine $l) => $l->products()->count()),
            TD::make('published', 'Publikované')->render(fn (ProductLine $l) => $l->published ? '✓' : '-'),
            TD::make('actions', '')->alignRight()->render(fn (ProductLine $l) =>
                Link::make('Edit')->route('platform.lines.edit', $l->id)->icon('bs.pencil')
            ),
        ];
    }
}
