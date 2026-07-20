<?php

declare(strict_types=1);

use App\Orchid\Screens\B2bUserEditScreen;
use App\Orchid\Screens\B2bUserListScreen;
use App\Orchid\Screens\BlogEditScreen;
use App\Orchid\Screens\BlogListScreen;
use App\Models\Order;
use App\Orchid\Screens\OrderListScreen;
use App\Orchid\Screens\OrderViewScreen;
use App\Services\SuperFaktura\SuperFakturaService;
use Illuminate\Support\Facades\Storage;
use App\Orchid\Screens\PlatformScreen;
use App\Orchid\Screens\ProductEditScreen;
use App\Orchid\Screens\ProductLineEditScreen;
use App\Orchid\Screens\ProductLineListScreen;
use App\Orchid\Screens\ProductListScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', PlatformScreen::class)
    ->name('platform.main');

// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

// Orders
Route::screen('orders', OrderListScreen::class)
    ->name('platform.orders')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.index')->push('Objednávky'));

Route::screen('orders/{order}/view', OrderViewScreen::class)
    ->name('platform.orders.view')
    ->breadcrumbs(fn (Trail $trail, $order) => $trail->parent('platform.orders')->push($order->order_number));

Route::get('orders/{order}/invoice/{kind}', function (Order $order, string $kind) {
    abort_unless(optional(auth()->user())->hasAccess('platform.eshop.orders'), 403);
    abort_unless(in_array($kind, ['proforma', 'invoice'], true), 404);
    $path = $kind === 'invoice' ? $order->invoice_pdf_path : $order->proforma_pdf_path;
    abort_unless($path && Storage::disk(SuperFakturaService::PDF_DISK)->exists($path), 404);
    $filename = ($kind === 'invoice' ? ($order->invoice_number ?: $order->order_number) : ($order->proforma_number ?: $order->order_number)) . '.pdf';
    return Storage::disk(SuperFakturaService::PDF_DISK)->download($path, $filename);
})->name('platform.orders.invoice.download');

// B2B users
Route::screen('b2b-users', B2bUserListScreen::class)
    ->name('platform.b2b-users')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.index')->push('B2B salóny'));

Route::screen('b2b-users/create', B2bUserEditScreen::class)
    ->name('platform.b2b-users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.b2b-users')->push('Nový salón'));

Route::screen('b2b-users/{b2b_user}/edit', B2bUserEditScreen::class)
    ->name('platform.b2b-users.edit')
    ->breadcrumbs(fn (Trail $trail, $b2b_user) => $trail->parent('platform.b2b-users')->push($b2b_user->salon_name));

// Catalog
Route::screen('products', ProductListScreen::class)
    ->name('platform.products')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.index')->push('Produkty'));

Route::screen('products/create', ProductEditScreen::class)
    ->name('platform.products.create')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.products')->push('Nový produkt'));

Route::screen('products/{product}/edit', ProductEditScreen::class)
    ->name('platform.products.edit')
    ->breadcrumbs(fn (Trail $trail, $product) => $trail->parent('platform.products')->push($product->name));

Route::screen('lines', ProductLineListScreen::class)
    ->name('platform.lines')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.index')->push('Línie'));

Route::screen('lines/create', ProductLineEditScreen::class)
    ->name('platform.lines.create')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.lines')->push('Nová línia'));

Route::screen('lines/{line}/edit', ProductLineEditScreen::class)
    ->name('platform.lines.edit')
    ->breadcrumbs(fn (Trail $trail, $line) => $trail->parent('platform.lines')->push($line->name));

// Blog
Route::screen('blog', BlogListScreen::class)
    ->name('platform.blog')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.index')->push('Blog'));

Route::screen('blog/create', BlogEditScreen::class)
    ->name('platform.blog.create')
    ->breadcrumbs(fn (Trail $trail) => $trail->parent('platform.blog')->push('Nový článok'));

Route::screen('blog/{article}/edit', BlogEditScreen::class)
    ->name('platform.blog.edit')
    ->breadcrumbs(fn (Trail $trail, $article) => $trail->parent('platform.blog')->push($article->title));
