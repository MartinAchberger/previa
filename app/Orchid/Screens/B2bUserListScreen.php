<?php

namespace App\Orchid\Screens;

use App\Models\B2bUser;
use App\Orchid\Layouts\B2bUserListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class B2bUserListScreen extends Screen
{
    public function permission(): ?iterable { return ['platform.eshop.b2b']; }

    public function name(): string { return 'B2B salóny'; }

    public function description(): string { return 'Veľkoobchodní zákazníci - schvaľovanie, tier, zľavy.'; }

    public function query(): array
    {
        return [
            'users' => B2bUser::withCount('orders')->filters()->defaultSort('id', 'desc')->paginate(30),
            'pending' => B2bUser::where('status', 'pending')->count(),
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Nový salón')->icon('bs.plus')->route('platform.b2b-users.create'),
        ];
    }

    public function layout(): array { return [B2bUserListLayout::class]; }
}
