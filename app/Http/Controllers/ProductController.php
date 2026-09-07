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

        // "Doplňuje sa s týmito": the rest of the product's own routine first
        // (same collection, regular sizes), topped up with random picks.
        $visible = fn ($q) => $q
            ->where('published', true)
            ->when(!$isB2b, fn ($q) => $q->where('b2b_only', false))
            ->where('id', '!=', $product->id)
            ->when($product->variant_group, fn ($q) => $q->where(fn ($q) => $q
                ->whereNull('variant_group')
                ->orWhere('variant_group', '!=', $product->variant_group)));

        $crossSell = $product->line_id
            ? Product::dedupeSizeVariants(
                Product::query()->tap($visible)->inLine($product->line_id)->orderBy('sort_order')->get()
            )->take(4)
            : new Collection();

        if ($crossSell->count() < 4) {
            $fill = Product::dedupeSizeVariants(
                Product::query()->tap($visible)->whereNotIn('id', $crossSell->pluck('id'))->inRandomOrder()->get()
            )->take(4 - $crossSell->count());
            $crossSell = $crossSell->concat($fill);
        }

        return view('pages.pdp', compact('product', 'variants', 'crossSell'));
    }
}
