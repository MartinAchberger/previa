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

        // "Začnite svoju cestu" = curated picks (admin: "Výber na úvodnej stránke"),
        // topped up with the first regular-size products if fewer than 4 are flagged.
        $topProducts = Product::query()
            ->where('published', true)
            ->where('featured', true)
            ->when(!$isB2b, fn ($q) => $q->where('b2b_only', false))
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        if ($topProducts->count() < 4) {
            $fill = Product::dedupeSizeVariants(
                Product::query()
                    ->where('published', true)
                    ->when(!$isB2b, fn ($q) => $q->where('b2b_only', false))
                    ->whereNotIn('id', $topProducts->pluck('id'))
                    ->orderBy('sort_order')
                    ->get()
            )->take(4 - $topProducts->count());
            $topProducts = $topProducts->concat($fill);
        }

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
