<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductLine;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $isB2b = auth('b2b')->check();

        $topProducts = Product::query()
            ->where('published', true)
            ->when(!$isB2b, fn ($q) => $q->where('b2b_only', false))
            ->orderBy('price', 'desc')
            ->limit(4)
            ->get();

        // "Naše línie" na homepage = len vlasové línie (bez profi, doplnkov, sun a leave-in)
        $lines = ProductLine::query()
            ->where('published', true)
            ->whereNotIn('slug', ['earth-professional-color', 'virtuos-professional-color', 'waving-system', 'doplnky'])
            ->orderBy('sort_order')
            ->get();

        $totalProducts = Product::where('published', true)
            ->when(!$isB2b, fn ($q) => $q->where('b2b_only', false))
            ->count();

        return view('pages.home', [
            'topProducts'    => $topProducts,
            'lines'          => $lines,
            'totalProducts'  => $totalProducts,
        ]);
    }
}
