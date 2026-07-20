<?php

namespace App\Orchid\Layouts;

use App\Models\Order;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class OrderListLayout extends Table
{
    public $target = 'orders';

    protected function columns(): array
    {
        return [
            TD::make('order_number', 'Číslo')->sort()->filter(TD::FILTER_TEXT)->render(fn (Order $o) =>
                Link::make($o->order_number)->route('platform.orders.view', $o->id)
            ),
            TD::make('order_type', 'Typ')->sort()->render(fn (Order $o) => strtoupper($o->order_type)),
            TD::make('customer_name', 'Zákazník')->filter(TD::FILTER_TEXT)->render(fn (Order $o) =>
                $o->company_name ?: $o->customer_name
            ),
            TD::make('customer_email', 'Email')->filter(TD::FILTER_TEXT),
            TD::make('total', 'Suma')->sort()->render(fn (Order $o) => $o->totalFormatted()),
            TD::make('status', 'Stav')->sort()->render(fn (Order $o) => $o->statusLabel()),
            TD::make('payment_method', 'Platba')->render(fn (Order $o) => $o->paymentLabel()),
            TD::make('created_at', 'Vytvorená')->sort()->render(fn (Order $o) => $o->created_at?->format('j.n.Y · H:i')),
            TD::make('actions', '')->alignRight()->render(fn (Order $o) =>
                Link::make('Detail')->route('platform.orders.view', $o->id)->icon('bs.eye')
            ),
        ];
    }
}
