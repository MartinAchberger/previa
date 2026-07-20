<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;
use Orchid\Support\Color;

class PlatformProvider extends OrchidServiceProvider
{
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);
    }

    public function menu(): array
    {
        return [
            Menu::make('Objednávky')
                ->icon('bs.bag-check')
                ->route('platform.orders')
                ->title('Predaj')
                ->badge(fn () => \App\Models\Order::whereIn('status', ['new'])->count() ?: null),

            Menu::make('B2B salóny')
                ->icon('bs.building')
                ->route('platform.b2b-users')
                ->badge(fn () => \App\Models\B2bUser::where('status', 'pending')->count() ?: null, Color::DARK)
                ->divider(),

            Menu::make('Produkty')
                ->icon('bs.box-seam')
                ->route('platform.products')
                ->title('Katalóg'),

            Menu::make('Línie')
                ->icon('bs.collection')
                ->route('platform.lines'),

            Menu::make('Blog')
                ->icon('bs.newspaper')
                ->route('platform.blog')
                ->title('Obsah')
                ->divider(),

            Menu::make(__('Users'))
                ->icon('bs.shield-lock')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Access Controls')),

            Menu::make(__('Roles'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),

            Menu::make('Documentation')
                ->title('Docs')
                ->icon('bs.box-arrow-up-right')
                ->url('https://orchid.software/en/docs')
                ->target('_blank'),

            Menu::make('Changelog')
                ->icon('bs.box-arrow-up-right')
                ->url('https://github.com/orchidsoftware/platform/blob/master/CHANGELOG.md')
                ->target('_blank')
                ->badge(fn () => Dashboard::version(), Color::DARK),
        ];
    }

    public function permissions(): array
    {
        return [
            ItemPermission::group('Eshop')
                ->addPermission('platform.eshop.orders', 'Objednávky a faktúry')
                ->addPermission('platform.eshop.catalog', 'Produkty, línie, blog')
                ->addPermission('platform.eshop.b2b', 'B2B salóny'),
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}
