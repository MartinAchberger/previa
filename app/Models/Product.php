<?php

namespace App\Models;

use App\Models\Scopes\B2bVisibilityScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class Product extends Model
{
    use AsSource, Filterable;

    public const VAT_RATE = 0.23;

    protected static function booted(): void
    {
        static::addGlobalScope(new B2bVisibilityScope);
    }

    protected $fillable = [
        'line_id', 'code', 'sku', 'stock', 'variant_group', 'slug', 'name', 'subtitle', 'line_label',
        'complex', 'volume', 'price', 'discount_percent', 'badge', 'kind', 'tone', 'cap',
        'image_path', 'description', 'ingredients', 'results', 'compatibility', 'protocol',
        'fragrance', 'shades', 'sort_order', 'published', 'b2b_only',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percent' => 'integer',
        'stock' => 'integer',
        'published' => 'boolean',
        'b2b_only' => 'boolean',
        'sort_order' => 'integer',
        'ingredients' => 'array',
        'results' => 'array',
        'compatibility' => 'array',
        'protocol' => 'array',
        'fragrance' => 'array',
        'shades' => 'array',
    ];

    public function hasShades(): bool
    {
        return !empty($this->shades) && is_array($this->shades);
    }

    public function findShade(string $code): ?array
    {
        if (!$this->hasShades()) return null;
        foreach ($this->shades as $shade) {
            if (($shade['code'] ?? null) === $code) return $shade;
        }
        return null;
    }

    public function shadesGrouped(): array
    {
        if (!$this->hasShades()) return [];

        $hasExplicitGroup = false;
        foreach ($this->shades as $s) {
            if (!empty($s['group'])) { $hasExplicitGroup = true; break; }
        }

        $grouped = [];

        if ($hasExplicitGroup) {
            foreach ($this->shades as $shade) {
                $g = trim((string) ($shade['group'] ?? ''));
                if ($g === '') $g = 'Ostatné';
                $grouped[$g][] = $shade;
            }
            return $grouped;
        }

        foreach ($this->shades as $shade) {
            $code = (string) ($shade['code'] ?? '');
            if ($code === '') continue;
            $prefix = explode('.', ltrim($code, '.'))[0];
            $level = is_numeric($prefix) ? (int) $prefix : 0;
            $grouped[$level][] = $shade;
        }

        ksort($grouped);
        return $grouped;
    }

    protected $allowedFilters = [
        'name' => Like::class,
        'line_label' => Like::class,
        'complex' => Like::class,
        'published' => Where::class,
    ];

    protected $allowedSorts = ['id', 'name', 'price', 'sort_order', 'created_at'];

    public function line(): BelongsTo
    {
        return $this->belongsTo(ProductLine::class, 'line_id');
    }

    public function getPriceFormattedAttribute(): string
    {
        return '€' . number_format($this->price, 2, ',', ' ');
    }

    /** Per-product sale is active. */
    public function hasDiscount(): bool
    {
        return (int) $this->discount_percent > 0;
    }

    /** Retail price after the product's own sale discount (what everyone pays). */
    public function salePrice(): float
    {
        return $this->discountedPrice((float) $this->price);
    }

    /** Apply the product's sale discount to any base price (e.g. a shade price). */
    public function discountedPrice(float $base): float
    {
        $pct = (int) $this->discount_percent;
        return $pct > 0 ? round($base * (1 - $pct / 100), 2) : round($base, 2);
    }

    public function getPriceNetAttribute(): float
    {
        return round($this->price / (1 + self::VAT_RATE), 2);
    }

    public function getPriceVatAttribute(): float
    {
        return round($this->price - $this->price_net, 2);
    }

    public function getPriceNetFormattedAttribute(): string
    {
        return '€' . number_format($this->price_net, 2, ',', ' ');
    }

    /**
     * Listing dedup: size variants of one product (shared variant_group) show as
     * a single card — the classic size (lowest sort_order). The other sizes stay
     * reachable via the PDP size switcher.
     */
    public static function dedupeSizeVariants(\Illuminate\Support\Collection $products): \Illuminate\Support\Collection
    {
        $primary = $products->filter(fn ($p) => $p->variant_group)
            ->groupBy('variant_group')
            ->map(fn ($group) => $group->sortBy([['sort_order', 'asc'], ['id', 'asc']])->first()->id);

        return $products
            ->filter(fn ($p) => !$p->variant_group || $primary[$p->variant_group] === $p->id)
            ->values();
    }

    /** Stock is tracked only when the Foxlog sync filled it — null means "not tracked, sell freely". */
    public function isOutOfStock(): bool
    {
        return $this->stock !== null && (int) $this->stock <= 0;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }
        $value = $this->image_path;
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '/')) {
            return $value;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($value);
    }

    public function getTypeAttribute(): string
    {
        $s = mb_strtolower(($this->subtitle ?? '') . ' ' . ($this->name ?? ''));
        if (mb_strtolower($this->line_label ?? '') === 'sada' || preg_match('/\b(kit|sada)\b/u', $s)) return 'kity';
        if (preg_match('/\b(farba|farbenie|color|peroxid|oxid|bleach|blond|gloss|melír|toner)\b/u', $s)) return 'farba';
        if (str_contains($s, 'šampón') || str_contains($s, 'shampoo')) return 'sampon';
        if (str_contains($s, 'maska') || str_contains($s, 'mask')) return 'maska';
        if (str_contains($s, 'kondicionér') || str_contains($s, 'conditioner')) return 'kondicioner';
        if (str_contains($s, 'olej') || str_contains($s, 'sérum') || str_contains($s, 'oil') || str_contains($s, 'serum')) return 'olej-serum';
        return 'styling';
    }

    public static function typeLabels(): array
    {
        return [
            'sampon'      => 'Šampón',
            'maska'       => 'Maska',
            'kondicioner' => 'Kondicionér',
            'olej-serum'  => 'Olej a sérum',
            'styling'     => 'Styling',
            'farba'       => 'Farba',
            'kity'        => 'Kity',
        ];
    }
}
