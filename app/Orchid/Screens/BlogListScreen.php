<?php

namespace App\Orchid\Screens;

use App\Models\BlogArticle;
use App\Orchid\Layouts\BlogListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;

class BlogListScreen extends Screen
{
    public function name(): string { return 'Blog'; }

    public function description(): string { return 'Články, klinické zistenia, listy z laboratória.'; }

    public function permission(): ?iterable
    {
        return ['platform.eshop.catalog'];
    }

    public function query(): array
    {
        return [
            'articles' => BlogArticle::filters()->defaultSort('published_at', 'desc')->paginate(30),
        ];
    }

    public function commandBar(): array
    {
        return [
            Link::make('Nový článok')->icon('bs.plus')->route('platform.blog.create'),
        ];
    }

    public function layout(): array { return [BlogListLayout::class]; }
}
