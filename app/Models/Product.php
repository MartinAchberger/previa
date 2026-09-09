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
        'line_id', 'extra_line_ids', 'code', 'sku', 'stock', 'variant_group', 'slug', 'name', 'subtitle', 'line_label',
        'complex', 'volume', 'price', 'discount_percent', 'badge', 'kind', 'tone', 'cap',
        'image_path', 'description', 'for_whom', 'expect', 'usage', 'pro_sections',
        'fragrance', 'shades', 'sort_order', 'published', 'featured', 'b2b_only',
    ];

    /** Collections that make up the professional (salon-only) range. */
    public const PRO_LINE_SLUGS = ['earth-professional-color', 'virtuos-professional-color', 'waving-system'];

    /** Virtual shop filter value (line and type) that selects the whole PREVIA PRO range. */
    public const PRO_FILTER = 'previa-pro';

    protected $casts = [
        'price' => 'decimal:2',
        'discount_percent' => 'integer',
        'stock' => 'integer',
        'published' => 'boolean',
        'featured' => 'boolean',
        'b2b_only' => 'boolean',
        'sort_order' => 'integer',
        'extra_line_ids' => 'array',
        'for_whom' => 'array',
        'expect' => 'array',
        'pro_sections' => 'array',
        'fragrance' => 'array',
        'shades' => 'array',
    ];

    /** Extra collections are stored as a clean list of integer line ids (never the primary line). */
    public function setExtraLineIdsAttribute($value): void
    {
        $ids = collect(is_array($value) ? $value : (is_string($value) ? json_decode($value, true) : []))
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0 && $v !== (int) $this->line_id)
            ->unique()
            ->values()
            ->all();

        $this->attributes['extra_line_ids'] = $ids ? json_encode($ids) : null;
    }

    /** Every collection the product is listed in: the primary line + extra lines. */
    public function lineIds(): array
    {
        $ids = array_map('intval', $this->extra_line_ids ?? []);
        if ($this->line_id) {
            array_unshift($ids, (int) $this->line_id);
        }
        return array_values(array_unique($ids));
    }

    /** Products listed in a collection — as their primary line or as an extra one. */
    public function scopeInLine(\Illuminate\Database\Eloquent\Builder $query, int $lineId): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where(fn ($q) => $q
            ->where('line_id', $lineId)
            ->orWhereJsonContains('extra_line_ids', $lineId));
    }

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
     * a single card — the classic retail size (see defaultSizeVariant). The other
     * sizes stay reachable via the PDP size switcher.
     */
    public static function dedupeSizeVariants(\Illuminate\Support\Collection $products): \Illuminate\Support\Collection
    {
        $primary = $products->filter(fn ($p) => $p->variant_group)
            ->groupBy('variant_group')
            ->map(fn ($group) => self::defaultSizeVariant($group)->id);

        return $products
            ->filter(fn ($p) => !$p->variant_group || $primary[$p->variant_group] === $p->id)
            ->values();
    }

    /**
     * The size shown in listings (cards, homepage, quiz): the largest size that is
     * not salon-only — i.e. the regular 340 ml bottle, not the 100 ml travel size
     * and not the 1000 ml salon pack. When every size is salon-only, the middle one.
     */
    public static function defaultSizeVariant(\Illuminate\Support\Collection $group): self
    {
        $sorted = $group
            ->sort(fn ($a, $b) => (self::volumeMl($a) <=> self::volumeMl($b)) ?: ((float) $a->price <=> (float) $b->price))
            ->values();

        $retail = $sorted->filter(fn ($p) => !$p->b2b_only);
        if ($retail->isNotEmpty()) {
            return $retail->last();
        }

        return $sorted->get(intdiv($sorted->count() - 1, 2));
    }

    /** Millilitres for a single "<n> ml" volume; 0 for multipacks, grams, sets. */
    public static function volumeMl(self $product): int
    {
        return preg_match('/^\s*(\d+)\s*ml\s*$/iu', (string) $product->volume, $m) ? (int) $m[1] : 0;
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
        $name = mb_strtolower((string) $this->name);
        $s = mb_strtolower(($this->subtitle ?? '') . ' ' . $name);
        if (mb_strtolower($this->line_label ?? '') === 'sada' || preg_match('/\b(kit|sada)\b/u', $s)) return 'kity';
        // Product form first (a "Keeping After Color Shampoo" is a shampoo, not a colour).
        if (str_contains($name, 'shampoo') || str_contains($s, 'šampón')) return 'sampon';
        if (str_contains($name, 'treatment') && str_contains($s, 'maska') || str_contains($name, 'mask')) return 'maska';
        if (str_contains($name, 'conditioner') || str_contains($s, 'kondicionér')) return 'kondicioner';
        // Professional technical range.
        if (preg_match('/bleach|melír|zosvetľ/u', $s)) return 'melir';
        if (preg_match('/peroxid|activator|aktivátor|oxidant/u', $s)) return 'peroxidy';
        if (preg_match('/\b(farba|farbenie|color|colour|toner|toning|infusion|waving|neutralizer)\b/u', $s)) return 'farba';
        if (preg_match('/\b(olej|oil|sérum|serum)\b/u', $s)) return 'olej-serum';
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
            'kity'        => 'Kity',
            'farba'       => 'Farby',
            'melir'       => 'Melíry',
            'peroxidy'    => 'Peroxidy a aktivátory',
        ];
    }

    /**
     * Part of PREVIA PRO – the professional range sold only to salons: the colour /
     * bleaching / waving lines plus stand-alone salon-only items (Basic Shampoo 1 l,
     * Scalp Protective Oil, samplers). Salon sizes of retail products (1000 ml
     * variants) are NOT pro products – they live on the retail product's page.
     */
    public function isPro(): bool
    {
        if (!$this->b2b_only) return false;
        static $proLineIds = null;
        $proLineIds ??= ProductLine::whereIn('slug', self::PRO_LINE_SLUGS)->pluck('id')->map(fn ($v) => (int) $v)->all();
        if ($this->line_id && in_array((int) $this->line_id, $proLineIds, true)) return true;
        return $this->variant_group === null;
    }

    /** Free-text product search (name, Slovak subtitle, collection). */
    public function scopeSearch(\Illuminate\Database\Eloquent\Builder $query, string $term): \Illuminate\Database\Eloquent\Builder
    {
        $like = '%' . str_replace(['%', '_'], ['\%', '\_'], trim($term)) . '%';
        return $query->where(fn ($q) => $q
            ->where('name', 'like', $like)
            ->orWhere('subtitle', 'like', $like)
            ->orWhere('line_label', 'like', $like));
    }
}
