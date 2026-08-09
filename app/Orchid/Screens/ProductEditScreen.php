<?php

namespace App\Orchid\Screens;

use App\Models\Product;
use App\Models\ProductLine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Matrix;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ProductEditScreen extends Screen
{
    public ?Product $product = null;

    public function permission(): ?iterable { return ['platform.eshop.catalog']; }
    public bool $exists = false;

    public function query(Product $product): array
    {
        $this->product = $product;
        $this->exists = $product->exists;
        return ['product' => $product];
    }

    public function name(): string
    {
        return $this->exists ? 'Produkt: ' . $this->product->name : 'Nový produkt';
    }

    public function commandBar(): array
    {
        $cmds = [Button::make('Uložiť')->method('save')->icon('bs.check')];
        if ($this->exists) {
            $cmds[] = Button::make('Zmazať')->method('remove')->icon('bs.trash')->confirm('Naozaj zmazať produkt?');
        }
        return $cmds;
    }

    public function layout(): array
    {
        $selfId = $this->product->id ?? 0;
        $productOptions = Product::withoutGlobalScopes()
            ->where('id', '!=', $selfId)
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($p) => [$p->id => $p->name . ' · ' . ($p->volume ?: '—')])
            ->toArray();
        $currentSiblings = $this->product->variant_group
            ? Product::withoutGlobalScopes()
                ->where('variant_group', $this->product->variant_group)
                ->where('id', '!=', $selfId)
                ->pluck('id')
                ->toArray()
            : [];

        return [
            Layout::rows([
                Select::make('product.line_id')
                    ->title('Línia')
                    ->fromModel(ProductLine::class, 'name')
                    ->empty('- bez priradenia -', ''),
                Input::make('product.name')->title('Názov')->required(),
                Input::make('product.subtitle')->title('Podtitul')->help('Slovenský preklad / krátky popis'),
                Input::make('product.line_label')->title('Štítok línie')->required()->help('napr. "Pre oslabené vlasy"'),
                Input::make('product.complex')->title('Aktívny komplex')->maxlength(80),
                Input::make('product.volume')->title('Objem')->help('napr. "250 ml"'),
                Input::make('product.sku')->title('SKU (sklad Foxlog)')->maxlength(64)->help('Skladové SKU pre fulfillment. Musí sedieť so SKU vo Foxlogu.'),
                Input::make('product.stock')->type('number')->title('Sklad (ks)')->help('Stav zásob zo skladu. Synchronizuje sa automaticky; nechaj prázdne ak sklad neriešiš.'),
                Select::make('variant_members')
                    ->title('Veľkosti toho istého produktu (voliteľné)')
                    ->options($productOptions)
                    ->multiple()
                    ->value($currentSiblings)
                    ->help('Ak má produkt viac veľkostí (napr. 100 ml, 250 ml, 1 l), vyber tu všetky ostatné veľkosti. Prepoja sa do prepínača veľkostí na stránke. Stačí ich vybrať pri jednej z veľkostí — ostatné sa doplnia automaticky.'),
                Input::make('product.price')->type('number')->step('0.01')->title('Cena (€)')->required(),
                Input::make('product.discount_percent')->type('number')->min(0)->max(90)->title('Zľava (%)')->help('Akciová zľava pre všetkých. 0 = bez zľavy. Cena sa automaticky prepočíta.'),
                Input::make('product.badge')->title('Badge')->help('Bestseller / Nové / −15 % atď.'),
                Input::make('product.tone')->type('color')->title('Hlavná farba (swatch)')->help('Farba náhradného vizuálu, keď produkt nemá nahranú fotku.'),
                Cropper::make('product.image_path')
                    ->title('Obrázok produktu (voliteľné)')
                    ->targetRelativeUrl()
                    ->help('Nahraj fotku produktu. Ak nenahraješ, použije sa generovaný SVG obal.')
                    ->width(800)
                    ->height(1000),
                TextArea::make('product.description')->title('Popis (PDP)')->rows(4),

                Matrix::make('product.shades')
                    ->title('Odtiene (pre farbiace produkty)')
                    ->help('Kód = napr. 9.42 alebo Pearl. Názov = popis odtieňa. Skupina (voliteľné) = názov rodiny (napr. NATURAL, ASH, GOLDEN). Ak je vyplnená pri aspoň jednom odtieni, zoznam sa zoskupuje po rodinách; inak po číselnom leveli (1-10). Farba = HEX hodnota swatchu (napr. #caa07a). Cena (voliteľné) = ak má odtieň inú cenu než produkt.')
                    ->columns(['code', 'name', 'group', 'color', 'price', 'sku'])
                    ->fields([
                        'color' => Input::make()->type('color'),
                    ])
                    ->addRowLabel('+ Pridať odtieň')
                    ->value($this->product->shades ?? []),

                Input::make('product.sort_order')->type('number')->title('Poradie')->value(fn () => $this->product->sort_order ?? 1),
                CheckBox::make('product.published')->title('Publikované')->placeholder('Zobraziť na webe')->sendTrueOrFalse()->value($this->product->published ?? true),
                CheckBox::make('product.b2b_only')->title('Iba pre salóny')->placeholder('Zobraziť iba prihláseným salónom (skryť pred verejnosťou)')->sendTrueOrFalse()->value($this->product->b2b_only ?? false),
            ]),
        ];
    }

    public function save(Product $product, Request $request)
    {
        $request->validate([
            'product.name'       => 'required|string|max:150',
            'product.line_label' => 'required|string|max:150',
            'product.price'      => 'required|numeric|min:0',
            'product.discount_percent' => 'nullable|integer|min:0|max:90',
            'product.complex'    => 'nullable|string|max:80',
            'product.sku'        => 'nullable|string|max:64',
            'product.stock'      => 'nullable|integer|min:0',
            'product.sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->input('product');

        // Slug is auto-generated from the name (unique-suffixed on collision) —
        // admins don't manage it manually. Keep an existing slug on edit.
        if (empty($product->slug) || empty($data['slug'] ?? null)) {
            $baseSlug = $product->slug ?: (Str::slug($data['name'] ?? '') ?: 'produkt');
            $slug = $baseSlug;
            $i = 2;
            while (Product::withoutGlobalScopes()
                ->where('slug', $slug)
                ->where('id', '!=', $product->id ?? 0)
                ->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }
            $data['slug'] = $slug;
        }

        // Code (n°) is no longer entered by hand — auto-assign the next free number
        // for new products; it survives as the order-item / invoice line reference.
        if (empty($product->code)) {
            $maxCode = (int) Product::withoutGlobalScopes()->pluck('code')->map(fn ($c) => (int) $c)->max();
            $data['code'] = (string) ($maxCode + 1);
        } else {
            unset($data['code']);
        }

        if (isset($data['shades']) && is_array($data['shades'])) {
            $data['shades'] = collect($data['shades'])
                ->filter(fn ($row) => is_array($row) && !empty($row['code']))
                ->map(fn ($row) => [
                    'code'  => trim((string) ($row['code'] ?? '')),
                    'name'  => trim((string) ($row['name'] ?? '')),
                    'group' => trim((string) ($row['group'] ?? '')),
                    'color' => $this->sanitizeHexColor($row['color'] ?? ''),
                    'price' => $row['price'] !== null && $row['price'] !== ''
                        ? (float) str_replace(',', '.', (string) $row['price'])
                        : null,
                ])
                ->values()
                ->all();
            if (empty($data['shades'])) {
                $data['shades'] = null;
            }
        }

        $oldGroup = $product->variant_group;
        $product->fill($data)->save();

        // Size variants: admin selects every other size of the same product. All
        // selected products + this one share one variant_group (handles 2, 3 or
        // more sizes). Deselected products are unlinked from the old group.
        $selectedIds = array_values(array_filter(array_map('intval', (array) $request->input('variant_members', []))));
        if (!empty($selectedIds)) {
            $memberIds = array_unique(array_merge([$product->id], $selectedIds));
            $groupKey = Product::withoutGlobalScopes()
                ->whereIn('id', $memberIds)
                ->whereNotNull('variant_group')
                ->value('variant_group') ?: ('vg-' . $product->id);

            Product::withoutGlobalScopes()->whereIn('id', $memberIds)->update(['variant_group' => $groupKey]);

            if ($oldGroup && $oldGroup !== $groupKey) {
                Product::withoutGlobalScopes()->where('variant_group', $oldGroup)
                    ->whereNotIn('id', $memberIds)->update(['variant_group' => null]);
            }
            // Also unlink previous members of THIS group that the admin removed.
            Product::withoutGlobalScopes()->where('variant_group', $groupKey)
                ->whereNotIn('id', $memberIds)->update(['variant_group' => null]);
        } else {
            $product->forceFill(['variant_group' => null])->save();
            // If unlinking leaves a single lonely sibling, clear it too.
            if ($oldGroup) {
                $remaining = Product::withoutGlobalScopes()->where('variant_group', $oldGroup)->get();
                if ($remaining->count() < 2) {
                    Product::withoutGlobalScopes()->where('variant_group', $oldGroup)->update(['variant_group' => null]);
                }
            }
        }

        Toast::info('Uložené');
        return redirect()->route('platform.products');
    }

    public function remove(Product $product)
    {
        $product->delete();
        Toast::info('Zmazané');
        return redirect()->route('platform.products');
    }

    /**
     * Shade colours are rendered into CSS (style="background:…") and into the
     * cart drawer markup, so only accept a strict #rgb / #rrggbb hex value;
     * anything else is dropped to null (falls back to the default swatch).
     */
    private function sanitizeHexColor($value): ?string
    {
        $v = trim((string) $value);

        return preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $v) ? $v : null;
    }
}
