<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductLine;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $isB2b = auth('b2b')->check();

        $allLines = ProductLine::query()
            ->where('published', true)
            ->orderBy('sort_order')
            ->get();

        // Línie skryté z verejných filtrov:
        // - profesionálne farbenie a trvalá (earth/virtuos/waving) sú len pre salóny
        $hiddenLineSlugs = [];
        if (!$isB2b) {
            $hiddenLineSlugs = ['earth-professional-color', 'virtuos-professional-color', 'waving-system'];
        }
        $lines = $allLines->whereNotIn('slug', $hiddenLineSlugs)->values();

        $activeLine = $request->query('line');
        $activeType = $request->query('type');
        $activeSort = $request->query('sort');

        $query = Product::query()->where('published', true);
        if (!$isB2b) {
            $query->where('b2b_only', false);
        }

        match ($activeSort) {
            'price-asc'  => $query->orderBy('price', 'asc'),
            'price-desc' => $query->orderBy('price', 'desc'),
            default      => $query->orderBy('sort_order'),
        };

        $activeLineModel = $activeLine ? $allLines->firstWhere('slug', $activeLine) : null;
        if ($activeLineModel) {
            $query->where('line_id', $activeLineModel->id);
        }

        $products = $query->get();

        if ($activeType) {
            $products = $products->filter(fn ($p) => $p->type === $activeType)->values();
        }

        $allProducts = Product::where('published', true)
            ->when(!$isB2b, fn ($q) => $q->where('b2b_only', false))
            ->get();
        $totalProducts = $allProducts->count();

        $typeCounts = $allProducts->groupBy(fn ($p) => $p->type)->map->count();

        return view('pages.shop', compact(
            'lines', 'products', 'totalProducts',
            'activeLine', 'activeLineModel', 'activeType', 'activeSort',
            'typeCounts'
        ));
    }
}
