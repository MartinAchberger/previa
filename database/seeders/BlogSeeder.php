<?php

namespace Database\Seeders;

use App\Models\BlogArticle;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        BlogArticle::query()->delete();

        $body = <<<'HTML'
<p>Pokožka hlavy a vlas majú vlastnú kyslú vrstvu - tzv. acid mantle - ktorá vzniká kombináciou potu, mazu a metabolitov kožnej mikroflóry. Jej prirodzené pH sa pohybuje v rozmedzí 4,5 až 5,5.</p>

<p>Keď použijete prípravok výrazne zásaditejší ako toto rozmedzie (napríklad bežné tuhé mydlo s pH okolo 9), kutikula vlasu - vrstva drobných šupiniek na povrchu vlákna - sa otvorí. Otvorená kutikula znamená drsnejší povrch, väčšie trenie pri česaní, krepatenie a stratu lesku. Pri opakovanom používaní sa tým vlas mechanicky oslabuje.</p>

<p>Mierne kyslé pH má opačný efekt: kutikulu privrie. Povrch vlákna sa vyhladí, lepšie odráža svetlo (subjektívne to vnímame ako lesk) a vlákna sa o seba menej zachytávajú.</p>

<h3>Prečo nie „neutrálne pH"</h3>

<p>Označenie „pH neutrálne" zvyčajne znamená pH 7. Pre niektoré aplikácie (napríklad pleťové prípravky pri citlivej pokožke) je tento údaj v poriadku. Pre vlas je však pH 7 nad horným okrajom jeho prirodzeného rozsahu, čiže z hľadiska vlasu už mierne zásadité.</p>

<p>Šampóny a kondicionéry PREVIA sú formulované v rozmedzí 4,8 až 5,5. Údaj na etikete nie je marketing - je to nastavenie produktu na biológiu vlasu.</p>

<h3>Kyslý oplach po farbení a chemickom ošetrení</h3>

<p>Po farbení, blondovaní alebo trvalej je kutikula často poškodená a otvorená. Kyslý záverečný oplach (či už domáci octový alebo profesionálny salónny produkt) ju pomáha uzavrieť a vrátiť vlasom hladký povrch.</p>

<p>Z rovnakého dôvodu profesionálne salóny po každej farbe vlasov nasledujú kyslým ošetrením - bez neho by sa farebné pigmenty vyplavovali rýchlejšie a vlas by ostal náchylný na lámanie.</p>

<h3>Čo si z toho zobrať</h3>

<p>Pri výbere starostlivosti sa oplatí pozrieť na pH (alebo aspoň na kategóriu produktu). Šampón alebo kondicionér by mal byť kyslý - t. j. pH pod 6, ideálne v rozmedzí 4,5–5,5. Ak na obale informáciu nenájdete, je to často signál, že výrobca pH ako parameter neoptimalizoval.</p>
HTML;

        $articles = [
            [
                'slug' => 'kysle-ph-vlasov',
                'title' => 'Vlasy a kyslé pH - prečo na ňom záleží',
                'category' => 'Veda',
                'read_time' => '5 min',
                'cover_url' => 'https://images.unsplash.com/photo-1522338242992-e1a54906a8da?w=1400&q=80',
                'excerpt' => 'Vlas a pokožka hlavy fungujú v úzkom kyslom rozmedzí - približne 4,5 až 5,5. Príliš zásaditý šampón otvára kutikulu a robí vlasy krepatými. Krátky pohľad na to, prečo na pH záleží.',
                'body' => $body,
                'featured' => true,
                'published_at' => '2026-04-26',
            ],
        ];

        foreach ($articles as $row) {
            $row['published'] = true;
            BlogArticle::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}
