<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class B2bUser extends Model implements AuthenticatableContract
{
    use Authenticatable, AsSource, Filterable;

    protected $fillable = [
        'email', 'password', 'contact_name', 'salon_name', 'phone',
        'ico', 'vat_id', 'address', 'city', 'zip', 'country',
        'discount_pct', 'tier', 'status', 'notes', 'approved_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'discount_pct' => 'integer',
        'approved_at' => 'datetime',
    ];

    protected $allowedFilters = [
        'email' => Like::class,
        'salon_name' => Like::class,
        'contact_name' => Like::class,
        'status' => Where::class,
    ];

    protected $allowedSorts = ['id', 'salon_name', 'email', 'status', 'discount_pct', 'created_at'];

    public const STATUSES = [
        'pending'  => 'Čaká na schválenie',
        'active'   => 'Aktívny',
        'disabled' => 'Pozastavený',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'b2b_user_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function priceFor(Product $product): float
    {
        if ($this->discount_pct <= 0) return (float) $product->price;
        return round($product->price * (1 - $this->discount_pct / 100), 2);
    }
}
