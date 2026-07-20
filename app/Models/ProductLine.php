<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class ProductLine extends Model
{
    use AsSource, Filterable;

    protected $fillable = [
        'code', 'slug', 'name', 'eyebrow', 'description', 'complex', 'sort_order', 'published',
    ];

    protected $casts = [
        'published' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $allowedFilters = [
        'name' => Like::class,
        'published' => Where::class,
    ];

    protected $allowedSorts = ['id', 'name', 'sort_order', 'created_at'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'line_id')->orderBy('sort_order');
    }
}
