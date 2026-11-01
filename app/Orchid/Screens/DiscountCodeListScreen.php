<?php

namespace App\Orchid\Screens;

use App\Models\DiscountCode;
use App\Orchid\Layouts\DiscountCodeListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class DiscountCodeListScreen extends Screen
{
    public function name(): string { return 'Zľavové kódy'; }

    public function description(): string { return 'Kódy, ktoré zákazník zadá v pokladni. Neplatia pre B2B salóny.'; }

    public function permission(): ?iterable
    {
        return ['platform.eshop.catalog'];
    }

    public function query(): array
    {
        return [
            'codes' => DiscountCode::withCount('orders')->filters()->defaultSort('created_at', 'desc')->paginate(30),
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Nový kód')->icon('bs.plus')->route('platform.discount-codes.create'),
        ];
    }

    public function layout(): array { return [DiscountCodeListLayout::class]; }
}
