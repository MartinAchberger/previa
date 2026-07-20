<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class CartController extends Controller
{
    public function show(): View
    {
        return view('pages.cart');
    }
}
