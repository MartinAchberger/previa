<?php

namespace App\Orchid\Screens;

use App\Models\Order;
use App\Orchid\Layouts\OrderListLayout;
use Orchid\Screen\Screen;

class OrderListScreen extends Screen
{
    public function permission(): ?iterable { return ['platform.eshop.orders']; }

    public function name(): string { return 'Objednávky'; }

    public function description(): string { return 'B2C aj B2B objednávky.'; }

    public function query(): array
    {
        return [
            'orders' => Order::filters()->defaultSort('id', 'desc')->paginate(30),
        ];
    }

    public function commandBar(): array { return []; }

    public function layout(): array { return [OrderListLayout::class]; }
}
