<?php

namespace App\Http\Controllers\B2b;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * PREVIA PRO – everything a salon can buy that is not in the retail shop:
 * colour ranges with shade pickers, bleaches, peroxides & activators, technical
 * products, plus the salon (1 l) sizes of retail products.
 */
class BulkOrderController extends Controller
{
    public function show(): View
    {
        $b2b = Auth::guard('b2b')->user();

        $salonOnly = Product::query()
            ->with('line')
            ->where('published', true)
            ->where('b2b_only', true)
            ->orderBy('sort_order')
            ->get();

        $pro = $salonOnly->filter(fn ($p) => $p->isPro())->values();

        // Colour ranges with a shade picker → big "open the range" cards.
        $colorProducts = $pro->filter(fn ($p) => $p->hasShades())->values();
        $rest = $pro->reject(fn ($p) => $p->hasShades());

        $proGroups = collect([
            'farba'    => ['title' => 'Farbenie a technika', 'sub' => 'rastlinné farbenie, tónovacie roztoky, ondulácia'],
            'melir'    => ['title' => 'Melíry',              'sub' => 'zosvetľovacie prášky a pasty'],
            'peroxidy' => ['title' => 'Peroxidy a aktivátory', 'sub' => 'oxidanty pre systémy Earth a Virtuos'],
            'ostatne'  => ['title' => 'Ostatné pre salón',   'sub' => 'technická starostlivosť a vzorkovníky'],
        ])->map(function ($g, $key) use ($rest) {
            $g['products'] = $rest->filter(fn ($p) => match ($key) {
                'farba', 'melir', 'peroxidy' => $p->type === $key,
                default => !in_array($p->type, ['farba', 'melir', 'peroxidy'], true),
            })->values();
            return $g;
        })->filter(fn ($g) => $g['products']->isNotEmpty());

        // Salon sizes (1000 ml…) of retail products.
        $salonSizes = $salonOnly->reject(fn ($p) => $p->isPro())->values();

        return view('pages.b2b.previa-pro', compact('b2b', 'colorProducts', 'proGroups', 'salonSizes'));
    }
}
