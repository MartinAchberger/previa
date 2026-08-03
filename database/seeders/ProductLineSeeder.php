<?php

namespace Database\Seeders;

use App\Models\ProductLine;
use Illuminate\Database\Seeder;

class ProductLineSeeder extends Seeder
{
    public function run(): void
    {
        // Línie podľa PREVIA CENNÍK 2026 + finálne SK texty (august 2026).
        // Pozn.: žiadny delete() — products.line_id má nullOnDelete, mazanie
        // línií by odpojilo produkty. updateOrCreate podľa slug je idempotentné.
        $lines = [
            ['code' => '01', 'slug' => 'reconstruct',                'name' => 'Reconstruct',
             'eyebrow' => 'Obnova a rekonštrukcia',
             'description' => 'Obnovuje poškodené vlasové vlákno, zvyšuje jeho pevnosť a pružnosť a navracia vlasom zdravý vzhľad po chemickom aj tepelnom poškodení.'],
            ['code' => '02', 'slug' => 'keeping-after-color',        'name' => 'Keeping After Color',
             'eyebrow' => 'Ochrana farby',
             'description' => 'Predlžuje intenzitu farby, uzatvára vlasové vlákno a pomáha udržať farbené vlasy žiarivé, lesklé a chránené.'],
            ['code' => '03', 'slug' => 'energising',                 'name' => 'Energising',
             'eyebrow' => 'Energia pre vlasy',
             'description' => 'Stimuluje pokožku hlavy, posilňuje vlasové korienky a podporuje prirodzene silnejšie a zdravšie vlasy.'],
            ['code' => '04', 'slug' => 'regrowth',                   'name' => 'Regrowth',
             'eyebrow' => 'Proti vypadávaniu vlasov',
             'description' => 'Cielená starostlivosť navrhnutá na redukciu vypadávania vlasov a podporu prirodzeného rastového cyklu.'],
            ['code' => '05', 'slug' => 'purifying',                  'name' => 'Purifying',
             'eyebrow' => 'Hĺbkové čistenie',
             'description' => 'Odstraňuje prebytočný maz, nečistoty a zvyšky stylingových produktov bez narušenia prirodzenej rovnováhy pokožky.'],
            ['code' => '06', 'slug' => 'dry-dandruff',               'name' => 'Dry Dandruff',
             'eyebrow' => 'Suché lupiny',
             'description' => 'Hydratuje pokožku hlavy, redukuje výskyt suchých lupín a prináša okamžitý pocit komfortu.'],
            ['code' => '07', 'slug' => 'oily-dandruff',              'name' => 'Oily Dandruff',
             'eyebrow' => 'Mastné lupiny',
             'description' => 'Reguluje tvorbu mazu, pomáha eliminovať mastné lupiny a obnovuje zdravé prostredie pokožky hlavy.'],
            ['code' => '08', 'slug' => 'calming',                    'name' => 'Calming',
             'eyebrow' => 'Citlivá pokožka hlavy',
             'description' => 'Upokojuje podráždenú pokožku, zmierňuje začervenanie a obnovuje jej prirodzenú rovnováhu.'],
            ['code' => '09', 'slug' => 'rebalancing',                'name' => 'Rebalancing',
             'eyebrow' => 'Rovnováha pokožky',
             'description' => 'Normalizuje tvorbu mazu a pomáha udržať pokožku hlavy sviežu, zdravú a vyváženú.'],
            ['code' => '10', 'slug' => 'hair-and-scalp',             'name' => 'Hair and Scalp',
             'eyebrow' => 'Komplexná starostlivosť',
             'description' => 'Každodenná starostlivosť o vlasy aj pokožku hlavy pre dlhodobo zdravý a prirodzene krásny vzhľad.'],
            ['code' => '11', 'slug' => 'taming',                     'name' => 'Taming',
             'eyebrow' => 'Vyhladenie vlasov',
             'description' => 'Kontroluje krepatenie, uhladzuje vlasové vlákno a zanecháva vlasy hebké, lesklé a ľahko upraviteľné.'],
            ['code' => '12', 'slug' => 'curl-friends',               'name' => 'Curl Friends',
             'eyebrow' => 'Pre kučeravé vlasy',
             'description' => 'Hydratuje, zvýrazňuje prirodzený tvar kučier a pomáha eliminovať krepatenie bez zaťaženia vlasov.'],
            ['code' => '13', 'slug' => 'bodifying',                  'name' => 'Bodifying',
             'eyebrow' => 'Objem a hustota',
             'description' => 'Dodáva jemným vlasom viditeľný objem, vzdušnosť a dlhotrvajúcu plnosť už od korienkov.'],
            ['code' => '14', 'slug' => 'blonde',                     'name' => 'Blonde',
             'eyebrow' => 'Starostlivosť o blond vlasy',
             'description' => 'Neutralizuje nežiaduce žlté tóny a udržiava blond, sivé aj melírované vlasy žiarivé a plné lesku.'],
            ['code' => '15', 'slug' => 'styling-and-basics',         'name' => 'Styling and Basics',
             'eyebrow' => 'Styling pre každý deň',
             'description' => 'Profesionálne produkty na ochranu, styling a finálnu úpravu vlasov s prirodzeným a dlhotrvajúcim výsledkom.'],
            ['code' => '16', 'slug' => 'man',                        'name' => 'Man',
             'eyebrow' => 'Starostlivosť pre mužov',
             'description' => 'Komplexná starostlivosť o vlasy a pokožku hlavy vytvorená pre potreby modernej mužskej rutiny.'],
            // Profi rady a doplnky — finálne texty zatiaľ nedodané.
            ['code' => '17', 'slug' => 'earth-professional-color',   'name' => 'Earth Professional Color',
             'eyebrow' => 'Profesionálna farba',
             'description' => 'Profesionálny farbiaci systém pre salóny.'],
            ['code' => '18', 'slug' => 'virtuos-professional-color', 'name' => 'Virtuos Professional Color',
             'eyebrow' => 'Profesionálna farba',
             'description' => 'Profesionálny farbiaci systém pre salóny.'],
            ['code' => '19', 'slug' => 'waving-system',              'name' => 'Waving System',
             'eyebrow' => 'Trvalá ondulácia',
             'description' => 'Profesionálny systém pre trvalú onduláciu.'],
            ['code' => '20', 'slug' => 'doplnky',                    'name' => 'Doplnky',
             'eyebrow' => 'Príslušenstvo',
             'description' => 'Doplnky a príslušenstvo k starostlivosti o vlasy.'],
        ];

        foreach ($lines as $i => $row) {
            $row['sort_order'] = $i + 1;
            $row['published'] = true;
            ProductLine::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}
