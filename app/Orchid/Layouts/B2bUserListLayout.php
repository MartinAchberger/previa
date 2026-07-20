<?php

namespace App\Orchid\Layouts;

use App\Models\B2bUser;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class B2bUserListLayout extends Table
{
    public $target = 'users';

    protected function columns(): array
    {
        return [
            TD::make('id', 'ID')->sort()->width('60px'),
            TD::make('salon_name', 'Salón')->sort()->filter(TD::FILTER_TEXT)->render(fn (B2bUser $u) =>
                Link::make($u->salon_name)->route('platform.b2b-users.edit', $u->id)
            ),
            TD::make('contact_name', 'Kontakt')->filter(TD::FILTER_TEXT),
            TD::make('email', 'Email')->filter(TD::FILTER_TEXT),
            TD::make('discount_pct', 'Zľava')->sort()->render(fn (B2bUser $u) => '−' . $u->discount_pct . ' %'),
            TD::make('orders_count', 'Objednávky')->render(fn (B2bUser $u) => $u->orders_count ?? $u->orders()->count()),
            TD::make('status', 'Stav')->sort()->render(function (B2bUser $u) {
                $colors = ['pending' => '#a16207', 'active' => '#047857', 'disabled' => '#b91c1c'];
                $color = $colors[$u->status] ?? '#6b6b66';
                return '<span style="color:'.$color.';font-weight:500">' . (B2bUser::STATUSES[$u->status] ?? $u->status) . '</span>';
            }),
            TD::make('created_at', 'Registrácia')->render(fn (B2bUser $u) => $u->created_at?->format('j.n.Y')),
            TD::make('actions', '')->alignRight()->render(fn (B2bUser $u) =>
                Link::make('Edit')->route('platform.b2b-users.edit', $u->id)->icon('bs.pencil')
            ),
        ];
    }
}
