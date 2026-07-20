<?php

namespace App\Http\Controllers\B2b;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class BulkOrderController extends Controller
{
    public function show(): View
    {
        $b2b = Auth::guard('b2b')->user();

        $colorProducts = Product::query()
            ->with('line')
            ->where('published', true)
            ->whereNotNull('shades')
            ->orderBy('sort_order')
            ->get()
            ->filter(fn ($p) => is_array($p->shades) && count($p->shades) > 0)
            ->values();

        return view('pages.b2b.bulk', compact('b2b', 'colorProducts'));
    }
}
