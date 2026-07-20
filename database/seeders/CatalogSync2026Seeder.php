<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductLine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * One-off catalog sync from client price list (PH WEB-2.pdf, 2026-06).
 * Idempotent: updates existing products by `code`, creates new ones by `code`,
 * ensures the Doplnky + Sun lines exist. New products get no image (the storefront
 * renders the generated SVG bottle placeholder) — real photos added later in Orchid.
 */
class CatalogSync2026Seeder extends Seeder
{
    public function run(): void
    {
        // ---- ensure extra lines -------------------------------------------------
        $doplnky = ProductLine::updateOrCreate(
            ['slug' => 'doplnky'],
            ['code' => '11', 'name' => 'Doplnky', 'eyebrow' => 'Príslušenstvo',
             'description' => 'Profesionálne príslušenstvo a doplnky pre starostlivosť aj salónnu prácu.',
             'sort_order' => 11, 'published' => true]
        );
        $sun = ProductLine::updateOrCreate(
            ['slug' => 'sun'],
            ['code' => '12', 'name' => 'Pure Sun', 'eyebrow' => 'Letná línia',
             'description' => 'Letná starostlivosť a ochrana vlasov počas slnečných dní.',
             'sort_order' => 12, 'published' => true]
        );

        $lines = ProductLine::all()->keyBy('slug');

        // ---- price / flag / move updates on existing products (by code) ---------
        // [price?, b2b_only?, volume?, line(slug)?, name?]
        $updates = [
            '01' => ['price' => 21],
            '02' => ['price' => 24],
            '03' => ['price' => 29, 'volume' => '100 ml'],
            '04' => ['price' => 29],
            '30' => ['price' => 15, 'b2b_only' => true, 'line' => 'professional-color', 'name' => 'Argan & Keratin Color'],
            '31' => ['price' => 16, 'b2b_only' => true, 'line' => 'professional-color', 'name' => 'Peroxid 20 vol. · 6%', 'volume' => '1000 ml'],
            '32' => ['price' => 33],
            '05' => ['price' => 28],
            '06' => ['price' => 28],
            '33' => ['price' => 307, 'b2b_only' => true],
            '34' => ['price' => 28],
            '35' => ['price' => 111, 'b2b_only' => true],
            '07' => ['price' => 28],
            '08' => ['price' => 28],
            '36' => ['price' => 464, 'b2b_only' => true],
            '37' => ['price' => 56, 'b2b_only' => true],
            '38' => ['price' => 297, 'b2b_only' => true],
            '09' => ['price' => 22],
            '10' => ['price' => 24],
            '11' => ['price' => 25, 'volume' => '250 ml'],
            '12' => ['price' => 22],
            '13' => ['price' => 24],
            '14' => ['price' => 25],
            '15' => ['price' => 22],
            '16' => ['price' => 31],
            '17' => ['price' => 22],
            '19' => ['price' => 30, 'line' => 'style-finish', 'name' => 'Hydrating Leave-In Detangler'],
            '20' => ['price' => 28],
            '21' => ['price' => 28],
            '22' => ['price' => 24],
            '23' => ['price' => 26],
            '24' => ['price' => 30],
            '25' => ['price' => 23],
            '26' => ['price' => 32],
            '27' => ['price' => 23],
            '28' => ['price' => 28],
            '29' => ['price' => 24],
            '39' => ['price' => 42, 'b2b_only' => true],
            '40' => ['price' => 49, 'b2b_only' => true],
            '41' => ['price' => 50, 'b2b_only' => true],
            '42' => ['price' => 16, 'b2b_only' => true],
            '43' => ['b2b_only' => true],
        ];

        foreach ($updates as $code => $data) {
            $p = Product::withoutGlobalScopes()->where('code', $code)->first();
            if (!$p) continue;
            if (isset($data['price']))     $p->price = $data['price'];
            if (isset($data['b2b_only']))  $p->b2b_only = $data['b2b_only'];
            if (isset($data['volume']))    $p->volume = $data['volume'];
            if (isset($data['name']))      $p->name = $data['name'];
            if (isset($data['line']) && isset($lines[$data['line']])) {
                $p->line_id = $lines[$data['line']]->id;
                $p->line_label = $lines[$data['line']]->eyebrow;
            }
            $p->save();
        }

        // ---- new products (by code) --------------------------------------------
        // [line, code, name, subtitle, volume, price, b2b_only?, published?]
        $b = true; // b2b_only shorthand
        $new = [
            // Argan & Keratin
            ['argan-keratin', '44', 'Šampón Argan & Keratin 1000 ml', 'Šampón pre farbené vlasy', '1000 ml', 51, $b],
            ['argan-keratin', '45', 'Maska Argan & Keratin 1000 ml', 'Maska pre farbené vlasy', '1000 ml', 61, $b],
            // Pure Repair
            ['pure-repair', '46', 'Šampón Pure Repair 100 ml', 'Šampón', '100 ml', 11],
            ['pure-repair', '47', 'Šampón Pure Repair 1000 ml', 'Šampón', '1000 ml', 56, $b],
            ['pure-repair', '48', 'Maska Pure Repair 60 ml', 'Maska', '60 ml', 11],
            ['pure-repair', '49', 'Maska Pure Repair 1000 ml', 'Maska', '1000 ml', 111, $b],
            ['pure-repair', '50', 'Pure Repair Travel Kit', 'Cestovná sada', 'Sada', 17],
            ['pure-repair', '51', 'Pure Repair Duo Kit', 'Sada', 'Sada', 42],
            // Pure Straight
            ['pure-straight', '52', 'Post-Treatment Maska 1000 ml', 'Maska', '1000 ml', 111, $b],
            // Deep Moisture
            ['deep-moisture', '53', 'Šampón Deep Moisture 100 ml', 'Šampón', '100 ml', 11],
            ['deep-moisture', '54', 'Šampón Deep Moisture 1000 ml', 'Šampón', '1000 ml', 52, $b],
            ['deep-moisture', '55', 'Kondicionér Deep Moisture 100 ml', 'Kondicionér', '100 ml', 14],
            ['deep-moisture', '56', 'Kondicionér Deep Moisture 1000 ml', 'Kondicionér', '1000 ml', 56, $b],
            ['deep-moisture', '57', 'Extra Butter Maska 1000 ml', 'Maska', '1000 ml', 66, $b],
            ['deep-moisture', '58', 'Deep Moisture Travel Kit', 'Cestovná sada', 'Sada', 19],
            ['deep-moisture', '59', 'Deep Moisture Duo Kit', 'Sada', 'Sada', 35],
            ['deep-moisture', '60', 'Deep Moisture Brand Kit Premium', 'Prémiová sada', 'Sada', 71],
            // Smooth Perfect
            ['smooth-perfect', '61', 'Šampón Smooth Perfect 1000 ml', 'Šampón', '1000 ml', 52, $b],
            ['smooth-perfect', '62', 'Kondicionér Smooth Perfect 1000 ml', 'Kondicionér', '1000 ml', 56, $b],
            ['smooth-perfect', '63', 'Maska Smooth Perfect 1000 ml', 'Maska', '1000 ml', 66, $b],
            // Ice Blonde
            ['ice-blonde', '64', 'Šampón Ice Blonde 1000 ml', 'Šampón', '1000 ml', 52, $b],
            // Rejuvenating
            ['rejuvenating', '65', 'Šampón Rejuvenating 100 ml', 'Šampón', '100 ml', 11],
            ['rejuvenating', '66', 'Šampón Rejuvenating 1000 ml', 'Šampón', '1000 ml', 52, $b],
            // Professional Color (peroxides + sample books)
            ['professional-color', '67', 'Peroxid 5 vol. · 1,5%', 'Oxidačný krém', '1000 ml', 16, $b],
            ['professional-color', '68', 'Peroxid 10 vol. · 3%', 'Oxidačný krém', '1000 ml', 16, $b],
            ['professional-color', '69', 'Peroxid 30 vol. · 9%', 'Oxidačný krém', '1000 ml', 16, $b],
            ['professional-color', '70', 'Peroxid 40 vol. · 12%', 'Oxidačný krém', '1000 ml', 16, $b],
            ['professional-color', '71', 'Vzorkovník PH Argan & Keratin', 'Vzorkovník odtieňov', '-', 35, $b],
            ['professional-color', '72', 'Vzorkovník Glow Up', 'Vzorkovník odtieňov', '-', 45, $b],
            // Doplnky (accessories)
            ['doplnky', '73', 'Štetec na farbu · široký čierny', 'Príslušenstvo', '-', 8, $b],
            ['doplnky', '74', 'Štetec na farbu · Rainbow', 'Príslušenstvo', '-', 6, $b],
            ['doplnky', '75', 'Miska na farbenie · square', 'Príslušenstvo', '-', 7, $b],
            ['doplnky', '76', 'Miska na farbenie · round', 'Príslušenstvo', '-', 7, $b],
            ['doplnky', '77', 'Široký hrebeň', 'Príslušenstvo', '-', 12],
            ['doplnky', '78', 'Plátená taška S · 19×25 cm', 'Príslušenstvo', '-', 7],
            ['doplnky', '79', 'Plátená taška M · 38×42 cm', 'Príslušenstvo', '-', 9],
            ['doplnky', '80', 'Darčeková taška Premium S', 'Príslušenstvo', '-', 4],
            ['doplnky', '81', 'Darčeková taška Premium L', 'Príslušenstvo', '-', 9],
            ['doplnky', '82', 'Darčeková taška EKO', 'Príslušenstvo', '-', 2],
            // Sun
            ['sun', '83', 'Pure Sun Kit Mariolu + Pure Sun Bag', 'Letná sada', 'Sada', 80],
            ['sun', '84', 'Virtuous Summer Kit', 'Letná sada', 'Sada', 0, false, false], // price TBD -> draft
        ];

        foreach ($new as $row) {
            [$lineSlug, $code, $name, $subtitle, $volume, $price] = $row;
            $b2b = $row[6] ?? false;
            $published = $row[7] ?? true;
            $line = $lines[$lineSlug] ?? null;
            if (!$line) continue;

            Product::withoutGlobalScopes()->updateOrCreate(
                ['code' => $code],
                [
                    'line_id'    => $line->id,
                    'line_label' => $line->eyebrow,
                    'slug'       => Str::slug($name . '-' . $code),
                    'name'       => $name,
                    'subtitle'   => $subtitle,
                    'volume'     => $volume,
                    'price'      => $price,
                    'kind'       => 'tall',
                    'tone'       => '#12110f',
                    'cap'        => '#12110f',
                    'b2b_only'   => $b2b,
                    'published'  => $published,
                    'sort_order' => (int) $code,
                ]
            );
        }

        $this->command?->info('Catalog sync done: ' . count($updates) . ' updated, ' . count($new) . ' upserted.');
    }
}
