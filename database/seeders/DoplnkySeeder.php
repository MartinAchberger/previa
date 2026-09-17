<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductLine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Kolekcia „Doplnky“ – finálny zoznam od klientky (10. 9. 2026). Nahrádza všetky
 * doterajšie doplnky (staré tašky a vzorkovníky sa mažú a nahodia nanovo).
 * 5 položiek len pre salóny, 4 (kefy a hrebene) aj pre verejnosť.
 */
class DoplnkySeeder extends Seeder
{
    public function run(): void
    {
        $line = ProductLine::where('slug', 'doplnky')->first();
        if (!$line) {
            $this->command?->error('DoplnkySeeder: línia „doplnky“ neexistuje – spusti najprv ProductLineSeeder.');
            return;
        }
        $curls = ProductLine::where('slug', 'curl-friends')->value('id');

        Product::withoutGlobalScopes()->where('line_id', $line->id)->delete();

        // [name, volume (veľkosť), price, b2b_only, variant_group, subtitle, description, extra_line_ids]
        $items = [
            ['Plátená taška',            'S',  7,  true,  'platena-taska',       'Bavlnená taška · béžová · veľkosť S', null, []],
            ['Plátená taška',            'M',  9,  true,  'platena-taska',       'Bavlnená taška · béžová · veľkosť M', null, []],
            ['Darčeková taška Premium',  'L',  9,  true,  null,                  'Darčeková taška · veľkosť L', null, []],
            ['Darčeková taška EKO',      'S',  2,  true,  'darcekova-taska-eko', 'Ekologická darčeková taška · veľkosť S', null, []],
            ['Darčeková taška EKO',      'M',  4,  true,  'darcekova-taska-eko', 'Ekologická darčeková taška · veľkosť M', null, []],
            ['Head Spa Miska',           'S',  5,  true,  'head-spa-miska',      'Miska na head spa rituál · veľkosť S', null, []],
            ['Head Spa Miska',           'L',  7,  true,  'head-spa-miska',      'Miska na head spa rituál · veľkosť L', null, []],
            ['Vzorkovník Earth Powder Infusion', null, 55, true, null,           'Vzorkovník odtieňov Earth Powder Infusion', null, []],
            ['Vzorkovník Earth',         null, 45, true,  null,                  'Vzorkovník odtieňov Earth Professional Color', null, []],
            ['Vzorkovník Virtuos',       null, 45, true,  null,                  'Vzorkovník odtieňov Virtuos Professional Color', null, []],
            ['Virtuos Kefa',             null, 15, false, null,                  'Rozčesávacia kefa',
             'Ľahká rozčesávacia kefa s flexibilnou konštrukciou, ktorá prechádza vlasmi plynulo a s minimálnym odporom. Pomáha rozčesať uzlíky bez zbytočného ťahania a je vhodná na mokré aj suché vlasy. Otvorený dizajn zároveň umožňuje lepšie prúdenie vzduchu pri fénovaní a robí z nej univerzálnu kefu na každodenné použitie s možnosťou masáže vlasovej pokožky jemným stláčaním.', []],
            ['Luscious Curls Kefa',      null, 19, false, null,                  'Kefa pre kučeravé a vlnité vlasy',
             'Kefa navrhnutá pre kučeravé a vlnité vlasy, ktorá pomáha s jemným rozčesávaním a zároveň podporuje prirodzený tvar kučier. Flexibilná otvorená konštrukcia sa prispôsobuje vlasom a umožňuje jednoduchšie rozdelenie jednotlivých prameňov bez zbytočného ťahania. Ideálna na styling, definovanie kučier aj rovnomerné zapracovanie stylingových produktov.', [$curls]],
            ['Luscious Curls Hrebeň',    null, 15, false, null,                  'Širokozubý hrebeň',
             'Širokozubý hrebeň je ideálnou voľbou na šetrné rozčesávanie mokrých aj suchých vlasov. Veľké rozostupy medzi zubami pomáhajú minimalizovať ťahanie a mechanické namáhanie vlasov, preto je vhodný aj pre husté, vlnité či kučeravé vlasy. Praktický pomocník pri každodennej starostlivosti aj pri aplikácii masiek a kondicionérov.', [$curls]],
            ['Treatment Hrebeň',         null, 11, false, null,                  'Bambusový širokozubý hrebeň',
             'Minimalistický širokozubý hrebeň z bambusu určený na jemné a komfortné rozčesávanie vlasov. Široké zuby sú vhodné najmä pre dlhé, husté, vlnité a kučeravé vlasy a pomáhajú rozčesávať bez zbytočného narúšania ich prirodzenej textúry. Elegantný prírodný materiál z neho robí praktický aj estetický doplnok každodennej haircare rutiny.', []],
        ];

        $code = (int) Product::withoutGlobalScopes()->get()->max(fn ($p) => (int) $p->code);
        $sort = (int) Product::withoutGlobalScopes()->max('sort_order');

        foreach ($items as [$name, $size, $price, $b2bOnly, $group, $subtitle, $description, $extra]) {
            $slug = Str::slug($name . ' ' . ($size ?? ''));
            $image = file_exists(public_path('products/' . $slug . '.png')) ? '/products/' . $slug . '.png' : null;

            Product::withoutGlobalScopes()->updateOrCreate(['slug' => $slug], [
                'line_id'        => $line->id,
                'extra_line_ids' => array_values(array_filter($extra)),
                'code'           => str_pad((string) ++$code, 3, '0', STR_PAD_LEFT),
                'sku'            => str_pad((string) $code, 3, '0', STR_PAD_LEFT), // warehouse SKU = catalog code (Foxlog pairs on their side)
                'variant_group'  => $group,
                'name'           => $name,
                'subtitle'       => $subtitle,
                'line_label'     => $line->name,
                'complex'        => null,
                'volume'         => $size,
                'price'          => $price,
                'kind'           => 'sachet',
                'tone'           => '#a89e88',
                'image_path'     => $image,
                'description'    => $description,
                'sort_order'     => ++$sort,
                'published'      => true,
                'b2b_only'       => $b2bOnly,
            ]);
        }
    }
}
