<?php

namespace Database\Seeders;

use App\Models\ProductLine;
use Illuminate\Database\Seeder;

class ProductLineSeeder extends Seeder
{
    public function run(): void
    {
        // Línie podľa PREVIA CENNÍK 2026 + finálne SK texty kolekcií (klient, 7. 9. 2026).
        // Pozn.: žiadny delete() — products.line_id má nullOnDelete, mazanie
        // línií by odpojilo produkty. updateOrCreate podľa slug je idempotentné.
        $lines = [
            ['code' => '01', 'slug' => 'reconstruct',                'name' => 'Reconstruct',
             'eyebrow' => 'Obnova poškodených vlasov',
             'description' => 'Intenzívna starostlivosť pre oslabené a poškodené vlasy. Pomáha obnoviť ich štruktúru, pevnosť a pružnosť.'],
            ['code' => '02', 'slug' => 'keeping-after-color',        'name' => 'Keeping After Color',
             'eyebrow' => 'Ochrana farbených vlasov',
             'description' => 'Starostlivosť pre farbené vlasy, ktorá pomáha chrániť intenzitu farby, predĺžiť jej žiarivosť a zachovať lesk vlasov.'],
            ['code' => '03', 'slug' => 'energising',                 'name' => 'Energising',
             'eyebrow' => 'Energia pre vlasovú pokožku',
             'description' => 'Revitalizačná starostlivosť pre oslabené vlasy a vlasovú pokožku. Podporuje vitalitu vlasov a pomáha predchádzať ich oslabovaniu.'],
            ['code' => '04', 'slug' => 'regrowth',                   'name' => 'Regrowth',
             'eyebrow' => 'Podpora rastu vlasov',
             'description' => 'Cielená starostlivosť pri rednutí a vypadávaní vlasov. Stimuluje vlasovú pokožku a podporuje prirodzený rast silnejších vlasov.'],
            ['code' => '05', 'slug' => 'purifying',                  'name' => 'Purifying',
             'eyebrow' => 'Hĺbkové čistenie pokožky',
             'description' => 'Čistiaca a detoxikačná starostlivosť pre vlasovú pokožku. Pomáha odstraňovať nečistoty, nánosy a obnoviť jej prirodzenú rovnováhu.'],
            ['code' => '06', 'slug' => 'dry-dandruff',               'name' => 'Dry Dandruff',
             'eyebrow' => 'Starostlivosť pri suchých lupinách',
             'description' => 'Jemná starostlivosť pre suchú, šupinatú vlasovú pokožku. Pomáha redukovať lupiny, hydratovať a zmierniť pocit pnutia.'],
            ['code' => '07', 'slug' => 'oily-dandruff',              'name' => 'Oily Dandruff',
             'eyebrow' => 'Starostlivosť pri mastných lupinách',
             'description' => 'Cielená starostlivosť pre mastnú pokožku so sklonom k lupinám. Pomáha regulovať maz, redukovať lupiny a obnoviť rovnováhu.'],
            ['code' => '08', 'slug' => 'calming',                    'name' => 'Calming',
             'eyebrow' => 'Upokojenie citlivej pokožky',
             'description' => 'Jemná starostlivosť pre citlivú a podráždenú vlasovú pokožku. Pomáha zmierniť diskomfort, začervenanie a pocit svrbenia.'],
            ['code' => '09', 'slug' => 'rebalancing',                'name' => 'Rebalancing',
             'eyebrow' => 'Rovnováha mastnej pokožky',
             'description' => 'Cielená starostlivosť pre vlasovú pokožku s nadmernou tvorbou mazu. Pomáha regulovať mastenie, prečistiť pokožku a obnoviť jej prirodzenú rovnováhu.'],
            ['code' => '10', 'slug' => 'hair-and-scalp',             'name' => 'Hair and Scalp',
             'eyebrow' => 'Komplexná starostlivosť o pokožku',
             'description' => 'Doplnková starostlivosť pre zdravú a vyváženú vlasovú pokožku. Pomáha ju čistiť, exfoliovať, hydratovať a pripraviť na ďalšie kroky vlasovej rutiny.'],
            ['code' => '11', 'slug' => 'taming',                     'name' => 'Taming',
             'eyebrow' => 'Uhladenie nepoddajných vlasov',
             'description' => 'Starostlivosť pre krepovité, nepoddajné a ťažko upraviteľné vlasy. Pomáha uhladiť vlasové vlákno, kontrolovať krepovatenie a dodať vlasom hebkosť a lesk.'],
            ['code' => '12', 'slug' => 'curl-friends',               'name' => 'Curl Friends',
             'eyebrow' => 'Definícia vĺn a kučier',
             'description' => 'Starostlivosť vytvorená pre prirodzene vlnité a kučeravé vlasy. Pomáha udržať hydratáciu, zvýrazniť tvar kučier a zachovať ich pružnosť bez zaťaženia.'],
            ['code' => '13', 'slug' => 'bodifying',                  'name' => 'Bodifying',
             'eyebrow' => 'Objem pre jemné vlasy',
             'description' => 'Ľahká starostlivosť pre jemné a spľasnuté vlasy bez objemu. Pomáha vlasom dodať plnosť, vzdušnosť a prirodzený objem bez zbytočného zaťaženia.'],
            ['code' => '14', 'slug' => 'blonde',                     'name' => 'Blonde',
             'eyebrow' => 'Starostlivosť o blond vlasy',
             'description' => 'Cielená starostlivosť pre blond, zosvetľované, sivé a biele vlasy. Pomáha neutralizovať nežiaduce teplé tóny a zachovať čistý, žiarivý odtieň.'],
            ['code' => '15', 'slug' => 'styling-and-basics',         'name' => 'Styling and Basics',
             'eyebrow' => 'Styling a finálna starostlivosť',
             'description' => 'Produkty pre každodennú úpravu, ochranu a finálny vzhľad vlasov. Od objemu a textúry až po uhladenie, definíciu, lesk a dlhotrvajúcu fixáciu.'],
            ['code' => '16', 'slug' => 'man',                        'name' => 'Man',
             'eyebrow' => 'Starostlivosť a styling pre mužov',
             'description' => 'Kompletná pánska starostlivosť o vlasy a vlasovú pokožku doplnená o styling. Pre čisté, vitálne vlasy a precízny účes podľa požadovaného výsledku.'],
            // Profi rady — finálne texty zatiaľ nedodané.
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
             'eyebrow' => 'Každodenná vlasová rutina',
             'description' => 'Praktické doplnky navrhnuté pre jednoduchšiu a efektívnejšiu starostlivosť o vlasy. Pre každodennú rutinu doma aj profesionálne použitie.'],
        ];

        foreach ($lines as $i => $row) {
            $row['sort_order'] = $i + 1;
            $row['published'] = true;
            ProductLine::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}
