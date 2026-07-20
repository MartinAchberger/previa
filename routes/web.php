<?php

use App\Http\Controllers\B2b\AuthController as B2bAuthController;
use App\Http\Controllers\B2b\BulkOrderController as B2bBulkOrderController;
use App\Http\Controllers\B2b\DashboardController as B2bDashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FoxlogWebhookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HairQuizController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/eshop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/produkt/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::view('/filozofia', 'pages.filozofia')->name('philosophy.show');
Route::get('/diagnostika', [HairQuizController::class, 'show'])->name('quiz.show');
Route::post('/diagnostika', [HairQuizController::class, 'result'])
    ->middleware('throttle:20,1')
    ->name('quiz.result');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Cart + checkout
Route::get('/kosik', [CartController::class, 'show'])->name('cart.show');
Route::get('/pokladna', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/objednat', [CheckoutController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('checkout.store');
Route::get('/objednavka/{orderNumber}', [CheckoutController::class, 'confirmation'])
    ->middleware('signed')
    ->name('order.confirmation');

// Foxlog fulfilment webhooks (warehouse → us). Auth via X-Foxlog-Token header.
Route::post('/api/foxlog/stock', [FoxlogWebhookController::class, 'stock'])->name('foxlog.stock');
Route::post('/api/foxlog/order-status', [FoxlogWebhookController::class, 'orderStatus'])->name('foxlog.order-status');

// Stripe payment return URLs
Route::get('/platba/{orderNumber}/success', [StripeController::class, 'success'])->name('stripe.success');
Route::get('/platba/{orderNumber}/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');

// B2B
Route::prefix('b2b')->name('b2b.')->group(function () {
    Route::middleware('guest:b2b')->group(function () {
        Route::get('/login', [B2bAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [B2bAuthController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login.submit');
        Route::get('/registracia', [B2bAuthController::class, 'showRegister'])->name('register');
        Route::post('/registracia', [B2bAuthController::class, 'register'])
            ->middleware('throttle:3,10')
            ->name('register.submit');
    });

    Route::post('/logout', [B2bAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:b2b')->group(function () {
        Route::get('/dashboard', [B2bDashboardController::class, 'index'])->name('dashboard');
        Route::get('/objednavky', [B2bDashboardController::class, 'orders'])->name('orders');
        Route::get('/objednavky/{orderNumber}', [B2bDashboardController::class, 'orderDetail'])->name('order.detail');
        Route::get('/profil', [B2bDashboardController::class, 'profile'])->name('profile');
        Route::post('/profil', [B2bDashboardController::class, 'profileUpdate'])->name('profile.update');
        Route::get('/farby', [B2bBulkOrderController::class, 'show'])->name('colors');
    });
});
