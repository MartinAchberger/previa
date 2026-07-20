<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data migration: makes the server DB match the July 2026 client feedback
     * (PH WEB-2 PDF) — catalog upsert, variant groups, variant photo fallback
     * and the new short line descriptions.
     */
    public function up(): void
    {
        // 1) Ensure the full 84-product catalog exists (idempotent upsert).
        if (class_exists(\Database\Seeders\CatalogSync2026Seeder::class)) {
            (new \Database\Seeders\CatalogSync2026Seeder())->run();
        }

        // 2) Variant groups — size variants of one product share a group
        //    (rendered as the size switcher on the PDP). Keyed by product code.
        $groups = [
            '01' => 'ak-shampoo', '02' => 'ak-mask',
            '05' => 'pr-shampoo', '06' => 'pr-mask',
            '08' => 'ps-post-mask',
            '09' => 'dm-shampoo', '10' => 'dm-conditioner', '11' => 'dm-mask',
            '12' => 'sp-shampoo', '13' => 'sp-conditioner', '14' => 'sp-mask',
            '15' => 'ib-shampoo', '17' => 'rej-shampoo',
            '44' => 'ak-shampoo', '45' => 'ak-mask',
            '46' => 'pr-shampoo', '47' => 'pr-shampoo',
            '48' => 'pr-mask', '49' => 'pr-mask',
            '52' => 'ps-post-mask',
            '53' => 'dm-shampoo', '54' => 'dm-shampoo',
            '55' => 'dm-conditioner', '56' => 'dm-conditioner',
            '57' => 'dm-mask',
            '61' => 'sp-shampoo', '62' => 'sp-conditioner', '63' => 'sp-mask',
            '64' => 'ib-shampoo',
            '65' => 'rej-shampoo', '66' => 'rej-shampoo',
        ];
        foreach ($groups as $code => $group) {
            DB::table('products')->where('code', $code)
                ->whereNull('variant_group')
                ->update(['variant_group' => $group]);
        }

        // 3) Sizes without their own photo reuse the photo of a sibling variant.
        $variants = DB::table('products')
            ->whereNotNull('variant_group')->where('variant_group', '!=', '')
            ->get(['id', 'variant_group', 'image_path'])
            ->groupBy('variant_group');
        foreach ($variants as $items) {
            $src = $items->first(fn ($p) => !empty($p->image_path));
            if (!$src) {
                continue;
            }
            DB::table('products')
                ->whereIn('id', $items->filter(fn ($p) => empty($p->image_path))->pluck('id'))
                ->update(['image_path' => $src->image_path]);
        }

        // 4) All peroxide strengths share the one peroxide photo.
        DB::table('products')->where('name', 'like', 'Peroxid%')
            ->whereNull('image_path')
            ->update(['image_path' => '/products/pro-peroxide.jpg']);

        // 5) Short line descriptions per the PH WEB-2 feedback PDF.
        $descriptions = [
            'argan-keratin'  => 'Pomáha obnoviť poškodené vlasy, redukovať lámavosť a zanecháva ich silnejšie, hladšie a lesklejšie.',
            'pure-repair'    => 'Obnovuje štruktúru vlasov narušenú farbením, zosvetľovaním alebo tepelnou úpravou a pomáha predchádzať ďalšiemu poškodeniu.',
            'pure-straight'  => 'Uľahčuje úpravu vlasov, redukuje krepatenie a pomáha udržať vlasy hladké počas celého dňa.',
            'deep-moisture'  => 'Dodáva vlasom hydratáciu, hebkosť a pružnosť bez zaťaženia. Ideálna voľba pre suché vlasy bez života.',
            'smooth-perfect' => 'Pomáha skrotiť nepoddajné vlasy, znižuje krepatenie a zanecháva vlasy hladšie a výrazne lesklejšie.',
            'ice-blonde'     => 'Neutralizuje žlté tóny a pomáha udržať blond vlasy žiarivé a zdravé.',
            'rejuvenating'   => 'Podporuje zdravú pokožku hlavy a vytvára ideálne podmienky pre silnejšie a vitálnejšie vlasy.',
            'style-finish'   => 'Od tepelnej ochrany až po finálnu fixáciu. Všetko pre styling ako zo salónu.',
        ];
        foreach ($descriptions as $slug => $text) {
            DB::table('product_lines')->where('slug', $slug)->update(['description' => $text]);
        }
    }

    public function down(): void
    {
        // Data migration — nothing to reverse.
    }
};
