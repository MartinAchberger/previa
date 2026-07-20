<?php

namespace App\Http\Controllers;

use App\Models\BlogArticle;
use Illuminate\Contracts\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $featured = BlogArticle::query()
            ->where('published', true)
            ->where('featured', true)
            ->orderByDesc('published_at')
            ->first();

        $articles = BlogArticle::query()
            ->where('published', true)
            ->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))
            ->orderByDesc('published_at')
            ->limit(6)
            ->get();

        return view('pages.blog', compact('featured', 'articles'));
    }

    public function show(string $slug): View
    {
        $article = BlogArticle::query()
            ->where('slug', $slug)
            ->where('published', true)
            ->firstOrFail();

        $related = BlogArticle::query()
            ->where('published', true)
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('pages.blog-show', compact('article', 'related'));
    }
}
