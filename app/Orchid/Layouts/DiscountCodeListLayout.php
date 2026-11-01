<?php

namespace App\Orchid\Layouts;

use App\Models\DiscountCode;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class DiscountCodeListLayout extends Table
{
    public $target = 'codes';

    protected function columns(): array
    {
        return [
            TD::make('name', 'Názov')->sort()->filter(TD::FILTER_TEXT)->render(fn (DiscountCode $c) =>
                Link::make($c->name)->route('platform.discount-codes.edit', $c->id)
            ),
            TD::make('code', 'Kód')->sort()->filter(TD::FILTER_TEXT)->render(fn (DiscountCode $c) => '<code>' . e($c->code) . '</code>'),
            TD::make('percent', 'Zľava')->sort()->width('100px')->render(fn (DiscountCode $c) => '−' . $c->percent . ' %'),
            TD::make('orders_count', 'Použitý')->width('100px')->render(fn (DiscountCode $c) => $c->orders_count . '×'),
            TD::make('active', 'Aktívny')->sort()->width('100px')->render(fn (DiscountCode $c) => $c->active ? '✓' : '-'),
            TD::make('actions', '')->alignRight()->render(fn (DiscountCode $c) =>
                Link::make('Edit')->route('platform.discount-codes.edit', $c->id)->icon('bs.pencil')
            ),
        ];
    }
}
