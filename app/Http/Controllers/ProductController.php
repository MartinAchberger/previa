<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Scopes\B2bVisibilityScope;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    public function show(string $slug): View
    {
        $isB2b = auth('b2b')->check();

        $product = Product::with('line')
            ->where('slug', $slug)
            ->where('published', true)
            ->when(!$isB2b, fn ($q) => $q->where('b2b_only', false))
            ->first();

        if (!$product) {
            throw new NotFoundHttpException();
        }

        // Size variants (e.g. 250 ml + 1 l). Loaded WITHOUT the b2b visibility
        // scope so a guest still sees a salon-only large size as a locked option
        // ("dostupné pre salóny"), rather than it silently disappearing.
        $variants = $product->variant_group
            ? Product::withoutGlobalScope(B2bVisibilityScope::class)
                ->where('variant_group', $product->variant_group)
                ->where('published', true)
                ->orderBy('price')
                ->get()
            : new Collection();

        $crossSell = Product::query()
            ->where('published', true)
            ->when(!$isB2b, fn ($q) => $q->where('b2b_only', false))
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('pages.pdp', compact('product', 'variants', 'crossSell'));
    }
}
