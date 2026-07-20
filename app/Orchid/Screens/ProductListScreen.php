<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use App\Orchid\Layouts\ProductListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class ProductListScreen extends Screen
{
    public function permission(): ?iterable { return ['platform.eshop.catalog']; }

    public function name(): string { return 'Produkty'; }

    public function description(): string { return 'Celý katalóg PREVIA.'; }

    public function query(): array
    {
        return [
            'products' => Product::filters()->defaultSort('sort_order')->paginate(30),
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Nový produkt')->icon('bs.plus')->route('platform.products.create'),
        ];
    }

    public function layout(): array { return [ProductListLayout::class]; }
}
