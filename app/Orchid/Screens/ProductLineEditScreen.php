<?php

namespace App\Orchid\Screens;

use App\Models\ProductLine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ProductLineEditScreen extends Screen
{
    public ?ProductLine $line = null;

    public function permission(): ?iterable { return ['platform.eshop.catalog']; }
    public bool $exists = false;

    public function query(ProductLine $line): array
    {
        $this->line = $line;
        $this->exists = $line->exists;
        return ['line' => $line];
    }

    public function name(): string
    {
        return $this->exists ? 'Línia: ' . $this->line->name : 'Nová línia';
    }

    public function commandBar(): array
    {
        $cmds = [Button::make('Uložiť')->method('save')->icon('bs.check')];
        if ($this->exists) {
            $cmds[] = Button::make('Zmazať')->method('remove')->icon('bs.trash')->confirm('Naozaj zmazať líniu?');
        }
        return $cmds;
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('line.code')->title('Kód (01, 02, ...)')->required()->maxlength(8),
                Input::make('line.name')->title('Názov línie')->required(),
                Input::make('line.eyebrow')->title('Popis (eyebrow)')->required()->help('napr. "Pre oslabené vlasy"'),
                Input::make('line.complex')->title('Aktívny komplex')->maxlength(16),
                TextArea::make('line.description')->title('Popis')->rows(3),
                Input::make('line.sort_order')->type('number')->title('Poradie')->value(fn () => $this->line->sort_order ?? 1),
                CheckBox::make('line.published')->title('Publikované')->placeholder('Zobraziť na webe')->sendTrueOrFalse()->value($this->line->published ?? true),
            ]),
        ];
    }

    public function save(ProductLine $line, Request $request)
    {
        $data = $request->input('line');
        // Slug is auto-generated from the name; kept stable on edit.
        if (empty($line->slug)) {
            $baseSlug = Str::slug($data['name'] ?? '') ?: 'linia';
            $slug = $baseSlug;
            $i = 2;
            while (ProductLine::where('slug', $slug)->where('id', '!=', $line->id ?? 0)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }
            $data['slug'] = $slug;
        } else {
            unset($data['slug']);
        }
        $line->fill($data)->save();
        Toast::info('Uložené');
        return redirect()->route('platform.lines');
    }

    public function remove(ProductLine $line)
    {
        $line->delete();
        Toast::info('Zmazané');
        return redirect()->route('platform.lines');
    }
}
