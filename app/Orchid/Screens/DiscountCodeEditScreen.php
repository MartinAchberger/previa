<?php

namespace App\Orchid\Screens;

use App\Models\DiscountCode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class DiscountCodeEditScreen extends Screen
{
    public ?DiscountCode $discountCode = null;

    public function permission(): ?iterable { return ['platform.eshop.catalog']; }
    public bool $exists = false;

    public function query(DiscountCode $discountCode): array
    {
        $this->discountCode = $discountCode;
        $this->exists = $discountCode->exists;
        return ['discount' => $discountCode];
    }

    public function name(): string
    {
        return $this->exists ? 'Zľavový kód: ' . $this->discountCode->code : 'Nový zľavový kód';
    }

    public function commandBar(): array
    {
        $cmds = [Button::make('Uložiť')->method('save')->icon('bs.check')];
        if ($this->exists) {
            $cmds[] = Button::make('Zmazať')->method('remove')->icon('bs.trash')->confirm('Naozaj zmazať zľavový kód? Staré objednávky si kód ponechajú.');
        }
        return $cmds;
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('discount.name')->title('Názov')->required()->maxlength(150)->help('Interný názov, napr. "Black Friday 2026". Zákazník ho uvidí po zadaní kódu.'),
                Input::make('discount.code')->title('Kód')->required()->maxlength(40)->help('Čo zákazník zadá v pokladni. Veľkosť písmen nehrá rolu.'),
                Input::make('discount.percent')->type('number')->min(1)->max(DiscountCode::MAX_PERCENT)->title('Zľava (%)')->required()->help('1–' . DiscountCode::MAX_PERCENT . ' %. Pripočíta sa k akciovej cene produktu.'),
                CheckBox::make('discount.active')->title('Aktívny')->placeholder('Kód je možné použiť')->sendTrueOrFalse()->value($this->discountCode->active ?? true),
            ]),
        ];
    }

    public function save(DiscountCode $discountCode, Request $request)
    {
        $request->merge(['discount' => array_merge($request->input('discount', []), [
            'code' => DiscountCode::normalize($request->input('discount.code')),
        ])]);

        $data = $request->validate([
            'discount.name'    => 'required|string|max:150',
            'discount.code'    => ['required', 'string', 'max:40', 'regex:/^[A-Z0-9_-]+$/u', Rule::unique('discount_codes', 'code')->ignore($discountCode->id)],
            'discount.percent' => 'required|integer|min:1|max:' . DiscountCode::MAX_PERCENT,
            'discount.active'  => 'boolean',
        ], [
            'discount.code.regex'  => 'Kód môže obsahovať len písmená bez diakritiky, číslice, pomlčku a podčiarkovník.',
            'discount.code.unique' => 'Tento kód už existuje.',
        ])['discount'];

        $discountCode->fill($data)->save();
        Toast::info('Uložené');
        return redirect()->route('platform.discount-codes');
    }

    public function remove(DiscountCode $discountCode)
    {
        $discountCode->delete();
        Toast::info('Zmazané');
        return redirect()->route('platform.discount-codes');
    }
}
