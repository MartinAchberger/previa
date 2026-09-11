<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class CartController extends Controller
{
    public function show(): View
    {
        return view('pages.cart', [
            'freeShippingFrom' => \App\Http\Controllers\CheckoutController::FREE_SHIPPING_FROM,
            'minShippingCost'  => \App\Http\Controllers\CheckoutController::minShippingCost(),
        ]);
    }
}
