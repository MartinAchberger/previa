<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Screen\AsSource;

class BlogArticle extends Model
{
    use AsSource, Filterable;

    protected $fillable = [
        'slug', 'title', 'category', 'read_time', 'cover_url', 'image_path',
        'excerpt', 'body', 'featured', 'published_at', 'published',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'published' => 'boolean',
        'published_at' => 'date',
    ];

    protected $allowedFilters = [
        'title' => Like::class,
        'category' => Like::class,
        'featured' => Where::class,
        'published' => Where::class,
    ];

    protected $allowedSorts = ['id', 'title', 'published_at', 'created_at'];

    public function getMetaAttribute(): string
    {
        $parts = array_filter([$this->category, $this->read_time]);
        return implode(' · ', $parts);
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            return $this->resolveImageUrl($this->image_path);
        }
        return $this->cover_url ?: null;
    }

    private function resolveImageUrl(string $value): string
    {
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://') || str_starts_with($value, '/')) {
            return $value;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($value);
    }
}
