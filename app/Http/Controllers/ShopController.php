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
        $searchTerm = trim((string) $request->query('q', ''));

        // "previa-pro" is a virtual collection/type for salons: the whole professional range.
        $proFilter = $isB2b && ($activeLine === Product::PRO_FILTER || $activeType === Product::PRO_FILTER);
        if (!$isB2b && ($activeLine === Product::PRO_FILTER || $activeType === Product::PRO_FILTER)) {
            $activeLine = $activeLine === Product::PRO_FILTER ? null : $activeLine;
            $activeType = $activeType === Product::PRO_FILTER ? null : $activeType;
        }

        $query = Product::query()->where('published', true);
        if (!$isB2b) {
            $query->where('b2b_only', false);
        }
        if ($searchTerm !== '') {
            $query->search($searchTerm);
        }

        match ($activeSort) {
            'price-asc'  => $query->orderBy('price', 'asc'),
            'price-desc' => $query->orderBy('price', 'desc'),
            default      => $query->orderBy('sort_order'),
        };

        $activeLineModel = $activeLine ? $allLines->firstWhere('slug', $activeLine) : null;
        if ($activeLineModel) {
            $query->inLine($activeLineModel->id);
        }

        $products = Product::dedupeSizeVariants($query->get());

        if ($proFilter) {
            $products = $products->filter(fn ($p) => $p->isPro())->values();
        } elseif ($activeType) {
            $products = $products->filter(fn ($p) => $p->type === $activeType)->values();
        }

        $allProducts = Product::dedupeSizeVariants(
            Product::where('published', true)
                ->when(!$isB2b, fn ($q) => $q->where('b2b_only', false))
                ->orderBy('sort_order')
                ->get()
        );
        $totalProducts = $allProducts->count();

        $typeCounts = $allProducts->groupBy(fn ($p) => $p->type)->map->count()->all();
        // A product counts towards its primary line and every extra collection it is listed in.
        $lineCounts = [];
        foreach ($allProducts as $p) {
            foreach ($p->lineIds() as $id) {
                $lineCounts[$id] = ($lineCounts[$id] ?? 0) + 1;
            }
        }
        $proCount = $isB2b ? $allProducts->filter(fn ($p) => $p->isPro())->count() : 0;

        return view('pages.shop', compact(
            'lines', 'products', 'totalProducts',
            'activeLine', 'activeLineModel', 'activeType', 'activeSort', 'searchTerm', 'proFilter', 'proCount',
            'typeCounts', 'lineCounts'
        ));
    }
}
