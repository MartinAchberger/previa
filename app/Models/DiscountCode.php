<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class DiscountCode extends Model
{
    use AsSource, Filterable;

    /** Upper bound for the percent — keeps card totals above zero (Stripe). */
    public const MAX_PERCENT = 90;

    protected $fillable = ['name', 'code', 'percent', 'active'];

    protected $casts = [
        'percent' => 'integer',
        'active'  => 'boolean',
    ];

    protected $allowedFilters = [
        'name'   => Like::class,
        'code'   => Like::class,
        'active' => Where::class,
    ];

    protected $allowedSorts = ['id', 'name', 'code', 'percent', 'active', 'created_at'];

    /** Codes are case-insensitive: always stored and looked up uppercase. */
    public static function normalize(?string $code): string
    {
        return mb_strtoupper(trim((string) $code));
    }

    public function setCodeAttribute(?string $value): void
    {
        $this->attributes['code'] = self::normalize($value);
    }

    /** Active code matching the customer's input, or null. */
    public static function findUsable(?string $code): ?self
    {
        $code = self::normalize($code);
        if ($code === '') {
            return null;
        }
        return self::where('code', $code)->where('active', true)->where('percent', '>', 0)->first();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'discount_code', 'code');
    }
}
