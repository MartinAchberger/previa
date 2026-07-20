<?php

namespace App\Orchid\Layouts;

use App\Models\BlogArticle;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class BlogListLayout extends Table
{
    public $target = 'articles';

    protected function columns(): array
    {
        return [
            TD::make('id', 'ID')->width('60px')->sort(),
            TD::make('title', 'Článok')->sort()->filter(TD::FILTER_TEXT)->render(fn (BlogArticle $a) =>
                Link::make($a->title)->route('platform.blog.edit', $a->id)
            ),
            TD::make('category', 'Kategória')->sort()->filter(TD::FILTER_TEXT),
            TD::make('read_time', 'Čítanie')->width('100px'),
            TD::make('featured', 'Hlavný')->render(fn (BlogArticle $a) => $a->featured ? '★' : '-'),
            TD::make('published_at', 'Dátum')->sort()->render(fn (BlogArticle $a) => $a->published_at?->format('j.n.Y')),
            TD::make('published', 'Pub.')->render(fn (BlogArticle $a) => $a->published ? '✓' : '-'),
            TD::make('actions', '')->alignRight()->render(fn (BlogArticle $a) =>
                Link::make('Edit')->route('platform.blog.edit', $a->id)->icon('bs.pencil')
            ),
        ];
    }
}
