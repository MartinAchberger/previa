<?php

namespace App\Orchid\Screens;

use App\Models\BlogArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Quill;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class BlogEditScreen extends Screen
{
    public ?BlogArticle $article = null;

    public function permission(): ?iterable { return ['platform.eshop.catalog']; }
    public bool $exists = false;

    public function query(BlogArticle $article): array
    {
        $this->article = $article;
        $this->exists = $article->exists;
        return ['article' => $article];
    }

    public function name(): string
    {
        return $this->exists ? 'Článok: ' . $this->article->title : 'Nový článok';
    }

    public function commandBar(): array
    {
        $cmds = [Button::make('Uložiť')->method('save')->icon('bs.check')];
        if ($this->exists) {
            $cmds[] = Button::make('Zmazať')->method('remove')->icon('bs.trash')->confirm('Naozaj zmazať?');
        }
        return $cmds;
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('article.title')->title('Nadpis')->required()->maxlength(255),
                Input::make('article.category')->title('Kategória')->required()->help('Veda, Diagnostika, Filozofia, ...'),
                Input::make('article.read_time')->title('Čas čítania')->help('napr. "8 min"'),
                Cropper::make('article.image_path')
                    ->title('Titulný obrázok (upload)')
                    ->targetRelativeUrl()
                    ->help('Nahraj vlastný obrázok. Ak je prázdne, použije sa URL z poľa nižšie.')
                    ->width(1400)
                    ->height(800),
                Input::make('article.cover_url')->title('URL titulného obrázka (fallback)')->help('Použije sa len ak nie je nahraný vlastný obrázok'),
                TextArea::make('article.excerpt')->title('Krátky popis (excerpt)')->rows(3)->required(),
                Quill::make('article.body')->title('Telo článku'),
                DateTimer::make('article.published_at')->title('Dátum publikácie')->format('Y-m-d'),
                CheckBox::make('article.featured')->title('Hlavný článok')->placeholder('Featured (zobrazí sa na blog hero)')->sendTrueOrFalse()->value($this->article->featured ?? false),
                CheckBox::make('article.published')->title('Publikované')->placeholder('Zobraziť na webe')->sendTrueOrFalse()->value($this->article->published ?? true),
            ]),
        ];
    }

    public function save(BlogArticle $article, Request $request)
    {
        $data = $request->input('article');
        // Slug is auto-generated from the title; kept stable on edit.
        if (empty($article->slug)) {
            $baseSlug = Str::slug($data['title'] ?? '') ?: 'clanok';
            $slug = $baseSlug;
            $i = 2;
            while (BlogArticle::where('slug', $slug)->where('id', '!=', $article->id ?? 0)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }
            $data['slug'] = $slug;
        } else {
            unset($data['slug']);
        }
        $article->fill($data)->save();
        Toast::info('Uložené');
        return redirect()->route('platform.blog');
    }

    public function remove(BlogArticle $article)
    {
        $article->delete();
        Toast::info('Zmazané');
        return redirect()->route('platform.blog');
    }
}
