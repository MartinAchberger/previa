<?php

namespace App\Orchid\Screens;

use App\Models\ProductLine;
use App\Orchid\Layouts\ProductLineListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class ProductLineListScreen extends Screen
{
    public function name(): string { return 'Línie produktov'; }

    public function description(): string { return 'Hlavné rady - Restructure, Hydra, Brillance, ...'; }

    public function permission(): ?iterable
    {
        return ['platform.eshop.catalog'];
    }

    public function query(): array
    {
        return [
            'lines' => ProductLine::filters()->defaultSort('sort_order')->paginate(20),
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Nová línia')->icon('bs.plus')->route('platform.lines.create'),
        ];
    }

    public function layout(): array { return [ProductLineListLayout::class]; }
}
