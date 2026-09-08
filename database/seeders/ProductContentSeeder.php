<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductLine;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Finálne SK texty produktov (klient, 7. 9. 2026 – previa.docx) + štrukturálne
 * úpravy katalógu z toho istého zadania. Idempotentné: párovanie podľa názvu
 * produktu (všetky veľkosti zdieľajú rovnaký text), bez mazania/vytvárania
 * produktov okrem explicitne vyžiadaného Keeping After Color Brand Kit Premium.
 */
class ProductContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->applyStructuralChanges();

        $byName = Product::withoutGlobalScopes()->get()->groupBy('name');

        foreach (self::CONTENT as $name => $c) {
            $rows = $byName->get($name);
            if (!$rows) {
                $this->command?->warn("ProductContentSeeder: produkt „{$name}“ v katalógu neexistuje – text preskočený.");
                continue;
            }
            foreach ($rows as $product) {
                $product->forceFill([
                    'subtitle'    => $c['subtitle'],
                    'description' => $c['description'],
                    'for_whom'    => $c['for_whom'],
                    'expect'      => $c['expect'],
                    'usage'       => $c['usage'],
                ])->save();
            }
        }

        // Produkty bez dodaného textu: žiadna latinčina na webe.
        Product::withoutGlobalScopes()
            ->whereNotIn('name', array_keys(self::CONTENT))
            ->each(function (Product $p) {
                $dirty = [];
                if ($p->subtitle === 'Lorem ipsum dolor sit amet' || str_starts_with((string) $p->subtitle, 'Lorem ipsum')) {
                    $dirty['subtitle'] = null;
                }
                if (str_starts_with((string) $p->description, 'Lorem ipsum')) {
                    $dirty['description'] = null;
                }
                if ($dirty) {
                    $p->forceFill($dirty)->save();
                }
            });
    }

    private function applyStructuralChanges(): void
    {
        $lineId = fn (string $slug) => ProductLine::where('slug', $slug)->value('id');

        // „Regrowth Treatment“ → „Hair Regrowth Treatment“ (názov, slug aj fotka).
        Product::withoutGlobalScopes()->where('name', 'Regrowth Treatment')->each(function (Product $p) {
            $slug = Str::slug('Hair Regrowth Treatment ' . $p->volume);
            $image = file_exists(public_path('products/' . $slug . '.png')) ? '/products/' . $slug . '.png' : $p->image_path;
            $p->forceFill(['name' => 'Hair Regrowth Treatment', 'slug' => $slug, 'image_path' => $image])->save();
        });

        // Vymazať – požiadavka klienta (7. 9. KAC brand kit, 8. 9. všetky brand kity).
        Product::withoutGlobalScopes()->where('name', 'like', '%Brand Kit Premium%')->delete();

        // Produkty zaradené do viacerých kolekcií (ostávajú aj vo svojej pôvodnej).
        $extra = [
            'Scalp Cleanser'        => ['oily-dandruff', 'calming', 'rebalancing'],
            'Scalp Peeling'         => ['dry-dandruff', 'calming', 'rebalancing'],
            'Rebalancing Treatment' => ['oily-dandruff'],
            'Scalp Protective Oil'  => ['earth-professional-color', 'virtuos-professional-color'],
        ];
        foreach ($extra as $name => $slugs) {
            $ids = array_values(array_filter(array_map($lineId, $slugs)));
            Product::withoutGlobalScopes()->where('name', $name)->update(['extra_line_ids' => json_encode($ids)]);
        }

        // Scalp Protective Oil – len pre profesionálov.
        Product::withoutGlobalScopes()->where('name', 'Scalp Protective Oil')->update(['b2b_only' => true]);

        // Výber na úvodnej stránke („Začnite svoju cestu ku krajším vlasom“).
        $featured = [
            'reconstruct-regenerating-shampoo-340-ml',
            'reconstruct-regenerating-treatment-150-ml',
            'keeping-after-color-shampoo-340-ml',
            'blonde-silver-shampoo-340-ml',
        ];
        Product::withoutGlobalScopes()->update(['featured' => false]);
        Product::withoutGlobalScopes()->whereIn('slug', $featured)->update(['featured' => true]);
    }

    /** Názov produktu => texty. Platí pre všetky veľkosti daného produktu. */
    public const CONTENT = [
        'Reconstruct Regenerating Shampoo' => [
            'subtitle'    => 'Regeneračný šampón pre poškodené vlasy',
            'description' => 'Jemne čistí poškodené a oslabené vlasy a zároveň podporuje obnovu ich štruktúry. Pomáha zlepšiť pevnosť a pružnosť vlasového vlákna a prinavrátiť vlasom hebkosť, vitalitu a zdravší vzhľad.',
            'for_whom'    => [
                'poškodené, namáhané a lámavé vlasy',
                'suché a porézne vlasy',
                'vlasy po farbení, melírovaní alebo zosvetľovaní',
                'vlasy pravidelne vystavované tepelnému stylingu',
            ],
            'expect'      => [
                'šetrné čistenie bez zbytočného vysušovania',
                'pevnejšie a pružnejšie vlasové vlákno',
                'menej drsný a porézny pocit vlasov',
                'hebkejšie a zdravšie pôsobiace vlasy',
            ],
            'usage'       => 'Naneste Reconstruct Regenerating Shampoo na mokrú vlasovú pokožku a vlasy. Jemne vmasírujte končekmi prstov a rovnomerne rozpracujte do dĺžok, aby produkt vlasy šetrne vyčistil a pripravil poškodené vlasové vlákno na následnú regeneračnú starostlivosť. Dôkladne opláchnite a v prípade potreby aplikáciu zopakujte. Pre kompletnú obnovujúcu rutinu pokračujte Reconstruct Regenerating Conditioner alebo Reconstruct Regenerating Treatment podľa miery poškodenia vlasov.',
        ],
        'Reconstruct Regenerating Conditioner' => [
            'subtitle'    => 'Regeneračný kondicionér pre poškodené vlasy',
            'description' => 'Každodenná regeneračná starostlivosť, ktorá pomáha poškodené vlasové vlákno uhladiť, zjemniť a chrániť pred ďalším namáhaním. Uľahčuje rozčesávanie a zanecháva vlasy hebké a poddajné bez pocitu zbytočného zaťaženia.',
            'for_whom'    => [
                'poškodené a oslabené vlasy',
                'suché a drsné dĺžky',
                'vlasy so sklonom k lámaniu',
                'chemicky a tepelne namáhané vlasy',
            ],
            'expect'      => [
                'jednoduchšie rozčesávanie',
                'hladšie vlasové vlákno',
                'väčšiu hebkosť a pružnosť',
                'zdravší a upravenejší vzhľad vlasov',
            ],
            'usage'       => 'Po umytí vlasov Reconstruct Regenerating Shampoo naneste Reconstruct Regenerating Conditioner do uterákom vysušených vlasov, najmä do dĺžok a končekov. Rovnomerne rozpracujte a nechajte krátko pôsobiť, aby pomohol uhladiť poškodené vlasové vlákno, uľahčiť rozčesávanie a prinavrátiť vlasom hebkosť a pružnosť. Následne dôkladne opláchnite. Pri výrazne poškodených vlasoch pravidelne zaraďte aj Reconstruct Regenerating Treatment pre intenzívnejšiu regeneráciu.',
        ],
        'Reconstruct Regenerating Treatment' => [
            'subtitle'    => 'Intenzívna regeneračná maska',
            'description' => 'Hĺbková starostlivosť pre vlasy, ktorým bežný kondicionér nestačí. Intenzívne ošetruje poškodené vlasové vlákno, pomáha zlepšiť jeho odolnosť a pružnosť a prinavracia vlasom hebkosť a vitalitu.',
            'for_whom'    => [
                'výrazne poškodené a oslabené vlasy',
                'zosvetľované a chemicky ošetrované vlasy',
                'veľmi suché alebo porézne dĺžky',
                'vlasy so sklonom k lámaniu',
            ],
            'expect'      => [
                'intenzívnejšiu regeneráciu',
                'pevnejšie a odolnejšie vlasy',
                'hladšie a mäkšie dĺžky',
                'jednoduchšie rozčesávanie',
            ],
            'usage'       => 'Po umytí vlasov Reconstruct Regenerating Shampoo naneste Reconstruct Regenerating Treatment do uterákom vysušených vlasov, najmä do poškodených dĺžok a končekov. Rovnomerne rozpracujte a nechajte pôsobiť, aby intenzívna starostlivosť pomohla obnoviť poškodené vlasové vlákno, posilniť jeho štruktúru a prinavrátiť vlasom pružnosť, hebkosť a zdravší vzhľad. Následne dôkladne opláchnite. Používajte pravidelne ako intenzívnejšiu alternatívu ku kondicionéru.',
        ],
        'Reconstruct Biphasic Leave-in Filler Conditioner' => [
            'subtitle'    => 'Dvojfázový bezoplachový kondicionér',
            'description' => 'Ľahká bezoplachová starostlivosť pre poškodené a porézne vlasy. Pomáha uhladiť vlasové vlákno, uľahčuje rozčesávanie a dodáva vlasom okamžitú hebkosť bez zaťaženia.',
            'for_whom'    => [
                'poškodené a porézne vlasy',
                'vlasy, ktoré sa ťažko rozčesávajú',
                'suché dĺžky a končeky',
                'vlasy vyžadujúce ľahkú každodennú regeneráciu',
            ],
            'expect'      => [
                'okamžite jednoduchšie rozčesávanie',
                'hladšie a poddajnejšie vlasy',
                'menej drsný pocit v dĺžkach',
                'ľahkú starostlivosť bez oplachovania',
            ],
            'usage'       => 'Pred použitím Biphasic Leave-In Filler Conditioner dôkladne pretrepte, aby sa obe fázy spojili. Nastriekajte rovnomerne do čistých, uterákom vysušených vlasov, najmä do poškodených dĺžok a končekov. Jemne prečešte, aby sa produkt rovnomerne rozložil a pomohol vyplniť a uhladiť poškodené vlasové vlákno, uľahčiť rozčesávanie a dodať vlasom hebkosť a pružnosť. Neoplachujte a pokračujte bežným stylingom.',
        ],
        'Reconstruct Serum' => [
            'subtitle'    => 'Regeneračné sérum pre poškodené vlasy',
            'description' => 'Koncentrovaný záverečný krok regeneračnej rutiny. Pomáha ošetriť poškodené a porézne vlasové vlákno, uhladiť jeho povrch a zlepšiť celkový vzhľad vlasov.',
            'for_whom'    => [
                'poškodené a lámavé vlasy',
                'suché a namáhané končeky',
                'porézne vlasové vlákno',
                'vlasy bez hebkosti a pružnosti',
            ],
            'expect'      => [
                'uhladenejší povrch vlasov',
                'hebkejšie dĺžky a končeky',
                'zdravší vzhľad',
                'lepšiu ovládateľnosť vlasov',
            ],
            'usage'       => 'Naneste malé množstvo Reconstruct Serum do čistých, uterákom vysušených vlasov, najmä do poškodených dĺžok a končekov. Rovnomerne rozpracujte, aby sérum pomohlo vyplniť a uhladiť poškodené vlasové vlákno, zlepšiť jeho pružnosť a dodať vlasom hebkosť a zdravší vzhľad. Neoplachujte a pokračujte bežným stylingom. Pri veľmi suchých alebo poškodených končekoch môžete malé množstvo aplikovať aj do suchých vlasov. KEEPING AFTER COLOR',
        ],
        'Keeping After Color Shampoo' => [
            'subtitle'    => 'Šampón na ochranu farby',
            'description' => 'Jemne čistí farbené vlasy a pomáha chrániť intenzitu a žiarivosť ich odtieňa. Starostlivosť vytvorená tak, aby farba pôsobila sviežo a lesklo čo najdlhšie medzi návštevami salónu.',
            'for_whom'    => [
                'farbené a tónované vlasy',
                'melírované vlasy',
                'vlasy so sklonom k rýchlemu vymývaniu farby',
                'každého, kto chce predĺžiť efekt farby',
            ],
            'expect'      => [
                'dlhšie pôsobiacu žiarivosť farby',
                'menej rýchle vymývanie odtieňa',
                'väčší lesk',
                'hebké a upravené vlasy',
            ],
            'usage'       => 'Naneste Keeping After Color Shampoo na mokrú vlasovú pokožku a vlasy. Jemne vmasírujte končekmi prstov a rovnomerne rozpracujte do dĺžok, aby produkt vlasy šetrne vyčistil a pomohol chrániť intenzitu farby, obmedziť jej vymývanie a zachovať žiarivosť a lesk vlasov. Dôkladne opláchnite a v prípade potreby aplikáciu zopakujte. Pre kompletnú starostlivosť o farbené vlasy pokračujte Keeping After Color Conditioner alebo Keeping After Color Treatment podľa potrieb vlasov.',
        ],
        'Keeping After Color Conditioner' => [
            'subtitle'    => 'Kondicionér na ochranu farby',
            'description' => 'Ošetruje farbené vlasy, pomáha uhladiť vlasovú kutikulu a zachovať krásu a intenzitu odtieňa. Vlasy zostávajú hebké, lesklé a jednoduchšie sa rozčesávajú.',
            'for_whom'    => [
                'všetky typy farbených vlasov',
                'matné farbené vlasy',
                'suchšie dĺžky po farbení',
                'vlasy, ktorých farba rýchlo stráca lesk',
            ],
            'expect'      => [
                'hladšie vlasové vlákno',
                'výraznejší lesk',
                'hebkosť',
                'dlhšie krásne pôsobiacu farbu',
            ],
            'usage'       => 'Po umytí vlasov Keeping After Color Shampoo naneste Keeping After Color Conditioner do uterákom vysušených vlasov, najmä do dĺžok a končekov. Rovnomerne rozpracujte a nechajte krátko pôsobiť, aby pomohol uhladiť vlasovú kutikulu, chrániť farebné pigmenty a zachovať intenzitu a žiarivosť farby. Následne dôkladne opláchnite. Pri vlasoch, ktoré potrebujú intenzívnejšiu starostlivosť, pravidelne zaraďte aj Keeping After Color Treatment.',
        ],
        'Keeping After Color Treatment' => [
            'subtitle'    => 'Intenzívna starostlivosť pre farbené vlasy',
            'description' => 'Hĺbkovejšia starostlivosť pre farbené a chemicky ošetrené vlasy. Pomáha zachovať intenzitu odtieňa a zároveň dodáva dĺžkam hebkosť, lesk a upravenejší vzhľad.',
            'for_whom'    => [
                'farbené vlasy vyžadujúce intenzívnejšiu starostlivosť',
                'suchšie a namáhané farbené vlasy',
                'matné vlasy bez lesku',
            ],
            'expect'      => [
                'intenzívnejší lesk',
                'hebkejšie dĺžky',
                'uhladenejší vzhľad',
                'podporu dlhotrvajúcej farby',
            ],
            'usage'       => 'Po umytí vlasov Keeping After Color Shampoo naneste Keeping After Color Treatment do uterákom vysušených vlasov, najmä do dĺžok a končekov. Rovnomerne rozpracujte a nechajte pôsobiť, aby intenzívna starostlivosť pomohla uzavrieť vlasovú kutikulu, chrániť farebné pigmenty a predĺžiť intenzitu a žiarivosť farby. Následne dôkladne opláchnite. Používajte pravidelne ako intenzívnejšiu alternatívu ku Keeping After Color Conditioner. KEEPING AFTER COLOR BRAND KIT PREMIUM VYMAZAŤ ENERGISING',
        ],
        'Energising Shampoo' => [
            'subtitle'    => 'Revitalizačný šampón pre vlasovú pokožku',
            'description' => 'Osviežujúca starostlivosť pre unavenú vlasovú pokožku a oslabené vlasy. Jemne čistí a pripravuje pokožku na ďalšie kroky rutiny.',
            'for_whom'    => [
                'oslabené vlasy bez vitality',
                'unavená vlasová pokožka',
                'vlasy, ktoré pôsobia mdlo a bez energie',
                'ako preventívna starostlivosť o pokožku',
            ],
            'expect'      => [
                'sviežejší pocit vlasovej pokožky',
                'dôkladné, ale šetrné čistenie',
                'vitalizovaný pocit pri korienkoch',
                'ideálny základ ďalšej scalp care',
            ],
            'usage'       => 'Naneste Energising Shampoo na mokrú vlasovú pokožku a vlasy. Jemne vmasírujte končekmi prstov so zameraním na pokožku, aby sa produkt rovnomerne rozložil, dôkladne ju vyčistil a podporil jej vitalitu a energizujúci účinok. Nechajte krátko pôsobiť a následne dôkladne opláchnite. V prípade potreby aplikáciu zopakujte. Pre kompletnú energizujúcu starostlivosť pokračujte Energising Leave-In Lotion, ktorý sa aplikuje priamo na vlasovú pokožku a neoplachuje sa.',
        ],
        'Energising Leave-in Lotion' => [
            'subtitle'    => 'Energizujúce bezoplachové tonikum',
            'description' => 'Ľahké tonikum určené priamo na vlasovú pokožku. Podporuje jej vitalitu a mikrocirkuláciu a prináša okamžitý osviežujúci pocit bez potreby oplachovania.',
            'for_whom'    => [
                'unavená vlasová pokožka',
                'oslabené vlasy',
                'pokožka vyžadujúca pravidelnú stimuláciu',
                'vhodné aj ako každodenná scalp care',
            ],
            'expect'      => [
                'osvieženie pokožky',
                'energizujúci pocit',
                'podporu vitality vlasovej pokožky',
                'nezaťažujúcu bezoplachovú starostlivosť',
            ],
            'usage'       => 'Po umytí vlasov Energising Shampoo aplikujte Energising Leave-In Lotion priamo na čistú, uterákom vysušenú vlasovú pokožku, po jednotlivých sekciách. Jemne vmasírujte končekmi prstov, aby sa produkt rovnomerne rozložil a podporil mikrocirkuláciu, vitalitu a energizáciu vlasovej pokožky. Neoplachujte a pokračujte bežnou úpravou vlasov. Vhodný je aj na pravidelné používanie ako súčasť energizujúcej rutiny. REGROWTH Podpora rastu vlasov',
        ],
        'Regrowth Shampoo' => [
            'subtitle'    => 'Stimulačný šampón pri rednutí vlasov',
            'description' => 'Cielená čistiaca starostlivosť pre oslabené vlasy so sklonom k rednutiu a vypadávaniu. Pomáha vytvárať optimálne prostredie vlasovej pokožky a podporuje rast vlasov.',
            'for_whom'    => [
                'rednúce a oslabené vlasy',
                'zvýšené vypadávanie vlasov',
                'jemné vlasy strácajúce hustotu',
                'preventívna starostlivosť pri prvých prejavoch rednutia',
            ],
            'expect'      => [
                'revitalizovanú vlasovú pokožku',
                'podporu optimálneho prostredia pre rast vlasov',
                'silnejšie pôsobiace vlasy',
                'ideálny prvý krok anti-hair-loss rutiny',
            ],
            'usage'       => 'Naneste Hair Regrowth Shampoo na mokrú vlasovú pokožku a vlasy. Jemne vmasírujte končekmi prstov so zameraním na pokožku, aby sa produkt rovnomerne rozložil a podporil stimuláciu vlasovej pokožky a optimálne podmienky pre prirodzený rast vlasov. Nechajte pôsobiť 2–3 minúty a následne dôkladne opláchnite. V prípade potreby aplikáciu zopakujte. Pre kompletnú regrowth rutinu pokračujte Hair Regrowth Treatment, ktorý sa aplikuje priamo na čistú vlasovú pokožku a neoplachuje sa.',
        ],
        'Hair Regrowth Treatment' => [
            'subtitle'    => 'Intenzívna kúra na podporu rastu vlasov',
            'description' => 'Koncentrovaná bezoplachová starostlivosť určená pri rednutí a zvýšenom vypadávaní vlasov. Pôsobí priamo na vlasovú pokožku a podporuje podmienky potrebné pre rast silnejších a odolnejších vlasov.',
            'for_whom'    => [
                'zvýšené vypadávanie vlasov',
                'viditeľné rednutie',
                'oslabené vlasové korienky',
                'obdobia, keď vlasy strácajú hustotu a silu',
            ],
            'expect'      => [
                'podporu prirodzeného rastového cyklu vlasov',
                'silnejšie pôsobiace vlasy',
                'podporu hustoty',
                'revitalizovanú vlasovú pokožku',
            ],
            'usage'       => 'Po umytí vlasov Hair Regrowth Shampoo aplikujte Hair Regrowth Treatment priamo na čistú, uterákom vysušenú vlasovú pokožku, po jednotlivých sekciách. Jemne vmasírujte končekmi prstov, aby sa produkt rovnomerne rozložil a podporil vitalitu vlasovej pokožky, vlasové folikuly a optimálne podmienky pre prirodzený rast vlasov. Neoplachujte a pokračujte bežnou úpravou vlasov. Pre optimálny efekt používajte pravidelne ako súčasť kompletnej Regrowth rutiny.',
        ],
        'Regrowth Duo Kit' => [
            'subtitle'    => 'Kompletná rutina na podporu rastu vlasov',
            'description' => 'Dvojkroková intenzívna starostlivosť vytvorená pre oslabené vlasy so sklonom k rednutiu a vypadávaniu. Spája Regrowth Shampoo s koncentrovaným Hair Regrowth Treatment, čím pôsobí na vlasovú pokožku počas umývania aj po ňom. Pomáha podporovať prirodzený rastový cyklus vlasov, vitalitu vlasovej pokožky a podmienky pre rast silnejších a hustejšie pôsobiacich vlasov.

Balenie obsahuje:

Regrowth Shampoo 350 ml + Hair Regrowth Treatment 100 ml',
            'for_whom'    => [
                'vlasy so sklonom k zvýšenému vypadávaniu',
                'rednúce a oslabené vlasy',
                'jemné vlasy strácajúce hustotu a silu',
                'obdobia sezónneho alebo zvýšeného vypadávania',
                'každého, kto chce zaradiť kompletnú regrowth rutinu',
            ],
            'expect'      => [
                'podporu prirodzeného rastového cyklu vlasov',
                'revitalizovanú vlasovú pokožku',
                'podporu pevnosti a vitality vlasov',
                'silnejšie a hustejšie pôsobiace vlasy',
                'komplexnú starostlivosť proti rednutiu vlasov',
            ],
            'usage'       => 'Najskôr naneste Regrowth Shampoo na mokrú vlasovú pokožku, jemne vmasírujte, nechajte krátko pôsobiť a dôkladne opláchnite. Následne aplikujte Hair Regrowth Treatment priamo na čistú vlasovú pokožku po jednotlivých sekciách a jemne vmasírujte. Neoplachujte. Pre optimálny efekt používajte produkty pravidelne ako kompletnú rutinu. PURIFYING',
        ],
        'Purifying Shampoo' => [
            'subtitle'    => 'Detoxikačný šampón',
            'description' => 'Hĺbkovo čistí vlasovú pokožku od nadbytočného mazu, nečistôt a nahromadených zvyškov produktov. Pomáha obnoviť pocit čistoty, sviežosti a prirodzenej rovnováhy.',
            'for_whom'    => [
                'rýchlo sa mastiaca pokožka',
                'pokožka zaťažená stylingovými produktmi',
                'vlasy pri korienkoch bez objemu',
                'každý, kto potrebuje pravidelný scalp detox',
            ],
            'expect'      => [
                'dôkladne čistejší pocit',
                'sviežu vlasovú pokožku',
                'odstránenie nahromadených nečistôt',
                'ľahšie pôsobiace vlasy pri korienkoch',
            ],
            'usage'       => 'Naneste Purifying Shampoo na mokrú vlasovú pokožku a vlasy. Jemne vmasírujte končekmi prstov, aby sa produkt rovnomerne rozložil a dôkladne prečistil pokožku od nadbytočného mazu, nečistôt a zvyškov produktov. Nechajte krátko pôsobiť a dôkladne opláchnite. V prípade potreby aplikáciu zopakujte. Pre kompletnú detoxikačnú starostlivosť pokračujte Purifying Treatment, ktorý pomáha hĺbkovo prečistiť vlasovú pokožku a obnoviť jej prirodzenú rovnováhu.',
        ],
        'Purifying Treatment' => [
            'subtitle'    => 'Intenzívna detoxikačná starostlivosť',
            'description' => 'Cielená starostlivosť pre vlasovú pokožku, ktorá potrebuje dôkladnejšie prečistenie a obnovenie rovnováhy.',
            'for_whom'    => [
                'problematická alebo zaťažená pokožka',
                'nadmerná tvorba mazu',
                'pokožka s nánosmi produktov a nečistôt',
            ],
            'expect'      => [
                'intenzívnejšie prečistenie',
                'sviežejší pocit',
                'vyváženejšiu pokožku',
                'lepšie pripravenú pokožku na ďalšiu starostlivosť',
            ],
            'usage'       => 'Po umytí vlasov aplikujte Purifying Treatment priamo na čistú vlasovú pokožku po jednotlivých sekciách. Jemne vmasírujte končekmi prstov, aby sa produkt rovnomerne rozložil a podporil hĺbkové prečistenie a obnovenie rovnováhy pokožky. Nechajte pôsobiť 3-5 minút a následne dôkladne opláchnite.',
        ],
        'Purifying Leave-in Lotion' => [
            'subtitle'    => 'Čistiace bezoplachové tonikum',
            'description' => 'Ľahká bezoplachová starostlivosť, ktorá pomáha udržiavať vlasovú pokožku sviežu, čistú a vyváženú aj medzi jednotlivými umytiami.',
            'for_whom'    => [
                'mastnejšia vlasová pokožka',
                'pokožka so sklonom k nečistotám',
                'ako doplnok detoxikačnej rutiny',
            ],
            'expect'      => [
                'dlhšie trvajúci pocit sviežosti',
                'ľahkú nezaťažujúcu starostlivosť',
                'podporu rovnováhy pokožky',
            ],
            'usage'       => 'Po kompletnom umytí vlasov aplikujte Purifying Leave-In Lotion priamo na čistú, uterákom vysušenú vlasovú pokožku po jednotlivých sekciách. Jemne vmasírujte končekmi prstov, aby sa produkt rovnomerne rozložil a podporil prečistenie a rovnováhu vlasovej pokožky. Neoplachujte a pokračujte bežnou úpravou vlasov. DRY DANDRUFF Starostlivosť pri suchých lupinách',
        ],
        'Dry Dandruff Cleansing Shampoo' => [
            'subtitle'    => 'Šampón proti suchým lupinám',
            'description' => 'Jemná čistiaca starostlivosť pre suchú vlasovú pokožku so sklonom k tvorbe lupín a šupiniek. Pomáha redukovať viditeľné prejavy suchých lupín, upokojuje pokožku a podporuje zachovanie jej prirodzenej rovnováhy.',
            'for_whom'    => [
                'suchá vlasová pokožka so sklonom k lupinám',
                'jemné a suché šupinky',
                'pokožka s pocitom suchosti a pnutia',
                'pokožka vyžadujúca šetrnú anti-dandruff starostlivosť',
            ],
            'expect'      => [
                'postupnú redukciu viditeľných suchých lupín',
                'čistejšiu vlasovú pokožku',
                'menej nepríjemného pocitu suchosti',
                'upokojenejšiu a komfortnejšiu pokožku',
                'podporu prirodzenej rovnováhy pokožky',
            ],
            'usage'       => 'Naneste Dry Dandruff Cleansing Shampoo na mokrú vlasovú pokožku a vlasy. Jemne vmasírujte končekmi prstov so zameraním na pokožku, aby sa produkt rovnomerne rozložil a pomohol odstrániť suché lupiny a šupinky bez zbytočného vysušovania pokožky. Nechajte krátko pôsobiť a následne dôkladne opláchnite. V prípade potreby aplikáciu zopakujte. Pre intenzívnejšiu starostlivosť kombinujte so Scalp Peeling, ktorý pomáha odstrániť nahromadené odumreté bunky a suché šupinky z povrchu vlasovej pokožky.',
        ],
        'Scalp Cleanser' => [
            'subtitle'    => 'Predšampónový detox vlasovej pokožky',
            'description' => 'Hĺbkovo čistiaci krok pred umytím vlasov. Pomáha uvoľniť nahromadené nečistoty, zvyšky produktov a maz a pripravuje vlasovú pokožku na následnú starostlivosť.',
            'for_whom'    => [
                'pokožka vyžadujúca intenzívnejší detox',
                'používatelia väčšieho množstva stylingu',
                'mastná a zaťažená vlasová pokožka',
            ],
            'expect'      => [
                'pocit dokonalejšie vyčistenej pokožky',
                'odstránenie nánosov',
                'sviežejšie korienky',
                'lepšiu prípravu pokožky na ďalšie produkty',
            ],
            'usage'       => 'Scalp Cleanser aplikujte priamo na suchú vlasovú pokožku pred umytím vlasov, po jednotlivých sekciách. Jemne vmasírujte končekmi prstov, aby sa produkt rovnomerne rozložil a pomohol uvoľniť nahromadené nečistoty a zvyšky produktov. Nechajte krátko pôsobiť, následne vlasy navlhčite a pokračujte umytím vhodným šampónom podľa potrieb vlasovej pokožky.',
        ],
        'Scalp Peeling' => [
            'subtitle'    => 'Peeling vlasovej pokožky',
            'description' => 'Exfoliačná starostlivosť, ktorá pomáha odstrániť odumreté bunky, suché šupinky a nahromadené nečistoty z povrchu vlasovej pokožky. Pomáha pokožku dôkladnejšie prečistiť a pripraviť na následnú starostlivosť. Previa ho priamo zaraďuje do Dry Dandruff rutiny.',
            'for_whom'    => [
                'suchá a šupinatá vlasová pokožka',
                'viditeľné nánosy odumretých buniek',
                'pokožka so sklonom k suchým lupinám',
                'každý, kto potrebuje dôkladnejšie čistenie vlasovej pokožky',
            ],
            'expect'      => [
                'odstránenie suchých šupiniek',
                'hladší povrch vlasovej pokožky',
                'dôkladnejší pocit čistoty',
                'lepšiu prípravu pokožky na následnú starostlivosť',
            ],
            'usage'       => 'Scalp Peeling aplikujte priamo na suchú vlasovú pokožku pred umytím vlasov, po jednotlivých sekciách. Jemne masírujte končekmi prstov krúživými pohybmi, aby exfoliačné čiastočky pomohli uvoľniť a odstrániť odumreté bunky a suché šupinky z povrchu pokožky. Dôkladne opláchnite a následne pokračujte umytím vhodným šampónom podľa potrieb vlasovej pokožky. OILY DANDRUFF Starostlivosť pri mastných lupinách',
        ],
        'Oily Dandruff Cleansing Shampoo' => [
            'subtitle'    => 'Šampón proti mastným lupinám',
            'description' => 'Cielená čistiaca starostlivosť pre mastnú vlasovú pokožku so sklonom k tvorbe lupín. Pomáha odstraňovať mastné lupiny a nečistoty, prečisťuje vlasovú pokožku a podporuje obnovenie jej prirodzenej rovnováhy.',
            'for_whom'    => [
                'mastná vlasová pokožka so sklonom k lupinám',
                'mastné a priľnavé lupiny',
                'rýchlo sa mastiace korienky',
                'pokožka so zvýšenou tvorbou kožného mazu',
            ],
            'expect'      => [
                'postupnú redukciu mastných lupín',
                'dôkladnejšie vyčistenú pokožku',
                'sviežejší pocit pri korienkoch',
                'podporu rovnováhy vlasovej pokožky',
                'ľahšie a čistejšie pôsobiace vlasy',
            ],
            'usage'       => 'Naneste Oily Dandruff Cleansing Shampoo na mokrú vlasovú pokožku a vlasy. Jemne vmasírujte končekmi prstov so zameraním na pokožku, aby sa produkt rovnomerne rozložil a pomohol odstrániť mastné lupiny, nadbytočný maz a nečistoty. Nechajte krátko pôsobiť a následne dôkladne opláchnite. V prípade potreby aplikáciu zopakujte. Pre kompletnú starostlivosť pokračujte Rebalancing Treatment, ktorý pomáha regulovať nadmernú tvorbu mazu a obnoviť rovnováhu vlasovej pokožky.',
        ],
        'Rebalancing Treatment' => [
            'subtitle'    => 'Vyrovnávajúca starostlivosť pre mastnú vlasovú pokožku',
            'description' => 'Cielená starostlivosť určená na reguláciu nadmernej tvorby kožného mazu. Pomáha prečistiť mastnú vlasovú pokožku, obnoviť jej rovnováhu a predĺžiť pocit sviežosti. Previa tento produkt kombinuje aj s Oily Dandruff Shampoo vo svojej oficiálnej rutine.',
            'for_whom'    => [
                'mastná vlasová pokožka',
                'nadmerná tvorba kožného mazu',
                'vlasy, ktoré sa rýchlo mastia pri korienkoch',
                'mastná pokožka so sklonom k lupinám',
            ],
            'expect'      => [
                'regulovanejší pocit mastnoty',
                'čistejšiu a sviežejšiu vlasovú pokožku',
                'podporu prirodzenej rovnováhy pokožky',
                'dlhší pocit čistých korienkov',
            ],
            'usage'       => 'Aplikujte Rebalancing Treatment priamo na suchú vlasovú pokožku pred umytím vlasov, po jednotlivých sekciách. Jemne masírujte končekmi prstov približne 2–3 minúty, aby sa produkt rovnomerne rozložil a podporil reguláciu nadmernej tvorby mazu. Následne vlasy navlhčite a pokračujte umytím Rebalancing Shampoo alebo Oily Dandruff Cleansing Shampoo podľa potrieb vlasovej pokožky',
        ],
        'Rebalancing Shampoo' => [
            'subtitle'    => 'Šampón pre mastnú vlasovú pokožku',
            'description' => 'Vyrovnávajúci šampón vytvorený pre vlasovú pokožku s nadmernou tvorbou mazu a vlasy, ktoré sa rýchlo mastia. Pomáha pokožku dôkladne prečistiť, regulovať pocit mastnoty a obnovovať jej prirodzenú rovnováhu.',
            'for_whom'    => [
                'mastná vlasová pokožka',
                'rýchlo sa mastiace vlasy',
                'ťažké a spľasnuté korienky',
                'nadmerná tvorba kožného mazu',
            ],
            'expect'      => [
                'dôkladne vyčistenú vlasovú pokožku',
                'sviežejšie a ľahšie korienky',
                'podporu regulácie nadmerného mazu',
                'dlhšie trvajúci pocit čistoty',
            ],
            'usage'       => 'Naneste Rebalancing Shampoo na mokrú vlasovú pokožku a vlasy. Jemne vmasírujte končekmi prstov so zameraním na pokožku, aby sa produkt rovnomerne rozložil a pomohol odstrániť nadbytočný maz a nečistoty. Nechajte krátko pôsobiť a následne dôkladne opláchnite. V prípade potreby aplikáciu zopakujte. Pre intenzívnejšiu starostlivosť používajte v kombinácii s Rebalancing Treatment, ktorý pomáha regulovať nadmernú tvorbu mazu a podporuje prirodzenú rovnováhu vlasovej pokožky. CALMING Upokojenie citlivej vlasovej pokožky',
        ],
        'Calming Shampoo' => [
            'subtitle'    => 'Upokojujúci šampón pre citlivú vlasovú pokožku',
            'description' => 'Mimoriadne jemná čistiaca starostlivosť vytvorená pre citlivú, krehkú a podráždenú vlasovú pokožku. Šetrne odstraňuje nečistoty a zároveň pomáha zachovať komfort pokožky bez zbytočného vysušovania a zaťažovania. Obsahuje organické extrakty z arniky, nechtíka a harmančeka.',
            'for_whom'    => [
                'citlivá a reaktívna vlasová pokožka',
                'pokožka so sklonom k podráždeniu',
                'pocit svrbenia, pnutia alebo diskomfortu',
                'každý, kto potrebuje mimoriadne jemné čistenie',
            ],
            'expect'      => [
                'šetrné čistenie vlasovej pokožky',
                'upokojujúci pocit po umytí',
                'menej pocitu pnutia a diskomfortu',
                'komfortnejšiu a vyváženejšiu pokožku',
                'jemnú starostlivosť vhodnú aj pre citlivú pokožku',
            ],
            'usage'       => 'Naneste Calming Shampoo na mokrú vlasovú pokožku a vlasy. Jemne vmasírujte končekmi prstov bez zbytočného trenia, aby sa produkt rovnomerne rozložil a šetrne vyčistil citlivú alebo podráždenú pokožku. Nechajte krátko pôsobiť a následne dôkladne opláchnite. V prípade potreby aplikáciu zopakujte. Pre intenzívnejšie upokojenie používajte v kombinácii s Calming Serum, ktoré pomáha zmierniť pocit pnutia, svrbenia a diskomfortu vlasovej pokožky.',
        ],
        'Calming Serum' => [
            'subtitle'    => 'Upokojujúce sérum pre citlivú vlasovú pokožku',
            'description' => 'Koncentrovaná starostlivosť pre citlivú a krehkú vlasovú pokožku. Pomáha pokožku upokojiť, obnovovať jej lipidovú rovnováhu a zmierňovať nepríjemné pocity spojené s precitlivenosťou. Obsahuje organické extrakty z arniky, nechtíka a harmančeka.',
            'for_whom'    => [
                'citlivá a krehká vlasová pokožka',
                'podráždená alebo namáhaná pokožka',
                'pokožka so sklonom k pocitu pnutia a diskomfortu',
                'každý, kto potrebuje intenzívnejšie upokojenie pokožky',
            ],
            'expect'      => [
                'okamžitý upokojujúci pocit',
                'komfortnejšiu vlasovú pokožku',
                'podporu prirodzenej lipidovej bariéry',
                'zmiernenie pocitu precitlivenosti',
                'vyváženejší stav pokožky pri pravidelnom používaní',
            ],
            'usage'       => 'Aplikujte Calming Serum priamo na suchú vlasovú pokožku pred umytím vlasov, po jednotlivých sekciách. Jemne masírujte končekmi prstov približne 2–3 minúty, aby sa sérum rovnomerne rozložilo a pomohlo upokojiť citlivú pokožku a zmierniť pocit pnutia či diskomfortu. Následne vlasy navlhčite a pokračujte umytím Calming Shampoo. CURLFRIENDS Starostlivosť pre vlnité a kučeravé vlasy',
        ],
        'Luscious Curls Shampoo' => [
            'subtitle'    => 'Šampón pre vlnité a kučeravé vlasy',
            'description' => 'Jemný šampón vytvorený špeciálne pre potreby vlnitých a kučeravých vlasov. Čistí vlasovú pokožku a vlasy bez narušenia ich optimálnej hydratácie, pomáha zachovať prirodzenú pružnosť kučier a obmedzuje krepovatenie. Obsahuje organické extrakty z aloe vera a ľanových semien.',
            'for_whom'    => [
                'vlnité a kučeravé vlasy',
                'suchšie a dehydrované kučery',
                'vlasy so sklonom ku krepovateniu',
                'kučery, ktoré strácajú pružnosť a definíciu',
            ],
            'expect'      => [
                'šetrne vyčistené vlasy bez zbytočného vysušenia',
                'hydratovanejšie kučery',
                'väčšiu pružnosť a hebkosť',
                'menej krepovatenia',
                'prirodzenejšie definované vlny a kučery',
            ],
            'usage'       => 'Naneste Luscious Curls Shampoo na mokrú vlasovú pokožku a vlasy. Jemne vmasírujte končekmi prstov, aby sa produkt rovnomerne rozložil a šetrne vyčistil vlasy bez narušenia ich prirodzenej hydratácie. Dôkladne opláchnite a v prípade potreby aplikáciu zopakujte. Pre kompletnú starostlivosť o vlnité a kučeravé vlasy pokračujte Luscious Curls Conditioner, ktorý pomáha kučery hydratovať, zjemniť a podporiť ich prirodzenú pružnosť a definíciu.',
        ],
        'Luscious Curls Conditioner' => [
            'subtitle'    => 'Kondicionér pre pružné a definované kučery',
            'description' => 'Hydratačný a zjemňujúci kondicionér vytvorený pre vlnité a kučeravé vlasy. Pomáha vlasom udržať hydratáciu, zvyšuje ich pružnosť a uľahčuje rozčesávanie bez toho, aby kučery zbytočne zaťažoval. Súčasťou receptúry sú organické extrakty z ľanových semien a aloe vera.',
            'for_whom'    => [
                'vlnité a kučeravé vlasy',
                'suché a nepoddajné kučery',
                'vlasy so sklonom k zamotávaniu',
                'kučery bez pružnosti a hebkosti',
            ],
            'expect'      => [
                'jednoduchšie rozčesávanie',
                'mäkšie a hydratovanejšie vlasy',
                'pružnejšie kučery',
                'menej krepovatenia',
                'prirodzený pohyb bez zaťaženia',
            ],
            'usage'       => 'Po umytí vlasov Luscious Curls Shampoo naneste Luscious Curls Conditioner do uterákom vysušených vlasov, najmä do dĺžok a končekov. Rovnomerne rozpracujte a jemne prečešte prstami alebo hrebeňom so širokými zubami, aby ste nenarušili prirodzený tvar kučier. Nechajte krátko pôsobiť a následne dôkladne opláchnite. Pre výraznejšiu definíciu a kontrolu kučier pokračujte Luscious Curls Spray alebo Luscious Curls Foam podľa požadovaného stylingového efektu.',
        ],
        'Luscious Curls Spray' => [
            'subtitle'    => 'Stylingový sprej pre definované kučery',
            'description' => 'Bezoplachový stylingový sprej s anti-frizz účinkom vytvorený na zvýraznenie prirodzeného tvaru vĺn a kučier. Pomáha kučery definovať a udržať ich pružné, upravené a plné pohybu bez ťažkého pocitu vo vlasoch.',
            'for_whom'    => [
                'vlnité a kučeravé vlasy',
                'kučery, ktoré potrebujú zvýrazniť definíciu',
                'vlasy so sklonom ku krepovateniu',
                'každodenný styling prirodzených vĺn',
            ],
            'expect'      => [
                'výraznejšie definované kučery',
                'kontrolu krepovatenia',
                'pružnosť a prirodzený pohyb',
                'ľahký styling bez zaťaženia',
                'upravenejší tvar kučier',
            ],
            'usage'       => 'Po umytí a ošetrení vlasov nastriekajte Luscious Curls Spray rovnomerne do uterákom vysušených, vlhkých vlasov, najmä do dĺžok a končekov. Jemne zapracujte prstami a vytvarujte kučery stláčaním smerom nahor, aby ste podporili ich prirodzený tvar a definíciu. Neoplachujte. Nechajte voľne uschnúť alebo vysušte pomocou difuzéra. Pre výraznejšiu definíciu, objem a fixáciu pokračujte Luscious Curls Foam.',
        ],
        'Luscious Curls Foam' => [
            'subtitle'    => 'Definujúca pena pre objemné kučery',
            'description' => 'Ľahká stylingová pena vytvorená na definovanie kučier a podporu ich objemu. Pomáha zvýrazniť prirodzený tvar vlasov, dodáva im pružnosť a kontroluje krepovatenie bez toho, aby kučery pôsobili tvrdo alebo ťažko.',
            'for_whom'    => [
                'kučeravé a vlnité vlasy',
                'kučery bez objemu a definície',
                'vlasy so sklonom ku krepovateniu',
                'styling, ktorý potrebuje ľahkú fixáciu a pružnosť',
            ],
            'expect'      => [
                'definovanejšie kučery',
                'väčší objem',
                'pružnosť a pohyb',
                'kontrolu krepovatenia',
                'ľahký a prirodzený výsledok',
            ],
            'usage'       => 'Naneste Luscious Curls Foam do vlhkých, uterákom vysušených vlasov a rovnomerne zapracujte do dĺžok. Kučery jemne vytvarujte stláčaním vlasov smerom nahor, aby ste podporili ich prirodzený tvar, pružnosť a objem. Neoplachujte. Nechajte voľne uschnúť alebo vysušte pomocou difuzéra pre výraznejšiu definíciu a objem kučier. VOLUMISING / BODIFYING Objem a plnosť pre jemné vlasy',
        ],
        'Volumising Bodifying Shampoo' => [
            'subtitle'    => 'Šampón pre objem a plnosť vlasov',
            'description' => 'Ľahký objemový šampón pre jemné vlasy, ktoré pôsobia spľasnuto a bez života. Čistí bez zbytočného zaťaženia a pomáha vlasom dodať telo, pevnosť a objem od korienkov až po končeky. Obsahuje organické extrakty z uhorky a lipového kvetu.',
            'for_whom'    => [
                'jemné a tenké vlasy',
                'spľasnuté vlasy bez objemu',
                'oslabené vlasy bez vitality',
                'vlasy, ktoré sa ľahko zaťažia',
            ],
            'expect'      => [
                'väčší pocit objemu',
                'ľahšie a vzdušnejšie vlasy',
                'plnšie pôsobiace vlasové vlákno',
                'väčšiu hebkosť',
                'objem bez zbytočného zaťaženia',
            ],
            'usage'       => 'Naneste Volumising Bodifying Shampoo na mokrú vlasovú pokožku a vlasy. Jemne vmasírujte končekmi prstov, aby sa produkt rovnomerne rozložil a šetrne vyčistil vlasy bez zbytočného zaťaženia. Dôkladne opláchnite a v prípade potreby aplikáciu zopakujte. Pre kompletnú objemovú starostlivosť pokračujte Volumising Bodifying Conditioner, ktorý pomáha vlasom dodať hebkosť, pružnosť a plnší vzhľad bez straty objemu.',
        ],
        'Volumising Bodifying Conditioner' => [
            'subtitle'    => 'Objemový kondicionér pre jemné vlasy',
            'description' => 'Ľahký kondicionér vytvorený tak, aby jemným vlasom poskytol potrebnú starostlivosť bez straty objemu. Pomáha vlasové vlákno posilniť, zjemniť a dodať mu plnší vzhľad pri zachovaní ľahkosti a pohybu.',
            'for_whom'    => [
                'jemné a tenké vlasy',
                'vlasy bez objemu',
                'dĺžky, ktoré potrebujú hydratáciu, ale ľahko sa zaťažia',
                'oslabené a matné vlasy',
            ],
            'expect'      => [
                'ľahké rozčesávanie',
                'hebkosť bez zaťaženia',
                'plnšie pôsobiace vlasy',
                'väčší objem a pružnosť',
                'prirodzený pohyb vlasov',
            ],
            'usage'       => 'Po umytí vlasov Volumising Bodifying Shampoo naneste Volumising Bodifying Conditioner do uterákom vysušených vlasov, najmä do dĺžok a končekov. Rovnomerne rozpracujte a nechajte krátko pôsobiť, aby vlasom dodal hebkosť, pružnosť a plnší vzhľad bez zbytočného zaťaženia. Následne dôkladne opláchnite a pokračujte bežným stylingom pre zachovanie maximálneho objemu a ľahkosti vlasov. BLONDE / SILVER Starostlivosť pre blond, sivé a biele vlasy',
        ],
        'Blonde Silver Shampoo' => [
            'subtitle'    => 'Fialový šampón na neutralizáciu teplých tónov',
            'description' => 'Pigmentovaný šampón pre blond, zosvetľované, sivé a biele vlasy. Pomáha neutralizovať nežiaduce žlté a teplé odlesky a podporuje čistejší, chladnejší tón vlasov. Obsahuje organické extrakty z černíc a čučoriedok.',
            'for_whom'    => [
                'blond a zosvetľované vlasy',
                'melírované vlasy',
                'sivé a biele vlasy',
                'blond odtiene so sklonom k žltnutiu',
            ],
            'expect'      => [
                'neutralizáciu neželaných teplých tónov',
                'čistejší a chladnejší blond odtieň',
                'oživenie sivých a bielych vlasov',
                'väčšiu žiarivosť farby',
                'hebkejšie a lesklejšie pôsobiace vlasy',
            ],
            'usage'       => 'Naneste Blonde Silver Shampoo na mokré vlasy a rovnomerne rozpracujte od korienkov až po končeky. Jemne vmasírujte a nechajte krátko pôsobiť podľa požadovanej intenzity neutralizácie nežiaducich žltých a teplých tónov. Následne dôkladne opláchnite. Pre kompletnú starostlivosť pokračujte Blonde Silver Conditioner, ktorý podporuje neutralizáciu a zároveň dodáva blond vlasom hebkosť, hydratáciu a lesk.',
        ],
        'Blonde Silver Conditioner' => [
            'subtitle'    => 'Fialový kondicionér pre chladné blond tóny',
            'description' => 'Pigmentovaný kondicionér, ktorý spája starostlivosť o vlasové vlákno s neutralizáciou neželaných teplých odleskov. Pomáha zvýrazniť chladné tóny a zároveň zanecháva vlasy hebké, uhladené a lesklé. Previa uvádza neutralizačný účinok aj na žlté, oranžové a červené tóny.',
            'for_whom'    => [
                'blond a zosvetľované vlasy',
                'sivé a biele vlasy',
                'vlasy s neželanými teplými odleskami',
                'suchšie vlasy po zosvetľovaní',
            ],
            'expect'      => [
                'podporu chladnejšieho odtieňa',
                'neutralizáciu teplých odleskov',
                'väčšiu hebkosť',
                'jednoduchšie rozčesávanie',
                'žiarivejší a upravenejší vzhľad',
            ],
            'usage'       => 'Po umytí vlasov Blonde Silver Shampoo naneste Blonde Silver Conditioner rovnomerne do uterákom vysušených vlasov, najmä do dĺžok a končekov. Nechajte pôsobiť 3–5 minút podľa požadovanej intenzity neutralizácie nežiaducich teplých tónov a následne dôkladne opláchnite. Pre ešte intenzívnejšiu starostlivosť a jednoduchšie rozčesávanie pokračujte Blonde Biphasic Leave-In Conditioner.',
        ],
        'Blonde Biphasic Leave-in Conditioner' => [
            'subtitle'    => 'Dvojfázový bezoplachový kondicionér pre blond vlasy',
            'description' => 'Ľahká bezoplachová starostlivosť vytvorená pre potreby blond a zosvetľovaných vlasov. Pomáha vlasom dodať hydratáciu, hebkosť a lesk, uľahčuje rozčesávanie a stará sa o namáhané dĺžky bez potreby oplachovania. Patrí medzi tri produkty oficiálnej Silver rutiny Previa.',
            'for_whom'    => [
                'blond a zosvetľované vlasy',
                'suchšie a namáhané dĺžky',
                'vlasy, ktoré sa ťažko rozčesávajú',
                'blond vlasy vyžadujúce každodennú ľahkú starostlivosť',
            ],
            'expect'      => [
                'jednoduchšie rozčesávanie',
                'hebkejšie dĺžky',
                'väčší lesk',
                'ľahkú hydratáciu',
                'upravenejší vzhľad blond vlasov bez zaťaženia',
            ],
            'usage'       => 'Pred použitím Blonde Biphasic Leave-In Conditioner dôkladne pretrepte, aby sa obe fázy spojili. Nastriekajte rovnomerne do uterákom vysušených, vlhkých vlasov, najmä do dĺžok a končekov. Jemne prečešte, aby sa produkt rovnomerne rozložil a pomohol vlasy hydratovať, uhladiť a uľahčiť ich rozčesávanie. Neoplachujte a pokračujte bežným stylingom. MAN Každodenná starostlivosť a styling pre mužov Línia MAN je postavená na produktoch pre každodennú starostlivosť o vlasy, vlasovú pokožku a styling. Spoločným prvkom línie je organický extrakt z paliny pravej, ktorý Previa spája s tonizačným účinkom, prečistením vlasovej pokožky a podporou mikrocirkulácie.',
        ],
        'Man Wash' => [
            'subtitle'    => 'Tonizačný umývací produkt na vlasy a telo',
            'description' => 'Univerzálny čistiaci produkt pre každodennú starostlivosť o vlasy aj telo. Jemne čistí, osviežuje a tonizuje pokožku a vlasovú pokožku bez zbytočného vysušovania. Obsahuje organický extrakt z paliny pravej a kmeňové bunky z hrozna.',
            'for_whom'    => [
                'mužov, ktorí preferujú jednoduchú každodennú rutinu',
                'všetky typy vlasov',
                'normálnu až mastnejšiu vlasovú pokožku',
                'každodenné použitie na vlasy aj telo',
            ],
            'expect'      => [
                'svieži pocit pokožky a vlasov',
                'dôkladné, ale šetrné čistenie',
                'tonizovanú vlasovú pokožku',
                'jednoduchú starostlivosť 2 v 1',
                'čisté a ľahké vlasy',
            ],
            'usage'       => 'Naneste Man Wash na mokré vlasy a pokožku tela. Jemne vmasírujte do vlasovej pokožky a vlasov, kým sa vytvorí pena, a nechajte krátko pôsobiť. Následne dôkladne opláchnite. Pri použití na telo naneste na vlhkú pokožku, napeňte a opláchnite. Pre kompletnú starostlivosť o vlasovú pokožku pokračujte Man Tonic, ktorý sa aplikuje ku korienkom a neoplachuje sa.',
        ],
        'Man Tonic' => [
            'subtitle'    => 'Stimulačné tonikum pre vlasovú pokožku',
            'description' => 'Bezoplachové tonikum vytvorené na každodennú stimuláciu vlasovej pokožky. Pomáha podporovať mikrocirkuláciu a vitalitu pokožky a Previa ho odporúča aj ako preventívnu starostlivosť pri oslabovaní a vypadávaní vlasov. Obsahuje organický extrakt z paliny pravej a kmeňové bunky z hrozna.',
            'for_whom'    => [
                'oslabené vlasy',
                'vlasová pokožka bez vitality',
                'preventívna starostlivosť pri vypadávaní vlasov',
                'mužov, ktorí chcú zaradiť scalp care do každodennej rutiny',
            ],
            'expect'      => [
                'osvieženú vlasovú pokožku',
                'stimulačný a tonizačný pocit',
                'podporu mikrocirkulácie',
                'podporu vitality vlasovej pokožky',
                'ľahkú bezoplachovú starostlivosť',
            ],
            'usage'       => 'Po umytí vlasov Man Wash aplikujte Man Tonic priamo na čistú vlasovú pokožku, po jednotlivých sekciách. Jemne vmasírujte končekmi prstov, aby sa produkt rovnomerne rozložil a podporil tonizáciu, vitalitu a mikrocirkuláciu vlasovej pokožky. Neoplachujte a pokračujte bežnou úpravou vlasov. Vhodný je aj na pravidelné každodenné použitie.',
        ],
        'Man Wax Gel' => [
            'subtitle'    => 'Voskovo-gélový styling s lesklým efektom',
            'description' => 'Styling, ktorý kombinuje silnú fixáciu gélu s pružnosťou vosku. Pomáha účes definovať a udržať jeho tvar, pričom vlasom dodáva výraznejší lesklý finish.',
            'for_whom'    => [
                'krátke až stredne dlhé vlasy',
                'účesy vyžadujúce výraznejšiu fixáciu',
                'uhladené a definované stylingy',
                'každého, kto preferuje lesklý finish',
            ],
            'expect'      => [
                'silnejšiu fixáciu',
                'pružnejší výsledok než pri klasickom géle',
                'výraznejší lesk',
                'definovaný tvar účesu',
                'kontrolu vlasov počas dňa',
            ],
            'usage'       => 'Naneste malé množstvo Man Wax Gel do suchých alebo mierne vlhkých vlasov. Rozpracujte produkt medzi dlaňami a rovnomerne zapracujte do vlasov, následne vytvarujte účes podľa požadovaného výsledku. Pre výraznejšiu definíciu a lesklý efekt aplikujte do suchých vlasov; vo vlhkých vlasoch dosiahnete uhladenejší, kontrolovanejší styling. Neoplachujte.',
        ],
        'Man Paste' => [
            'subtitle'    => 'Modelovacia pasta s matným efektom',
            'description' => 'Modelovacia pasta s prirodzenejšou fixáciou a matným výsledkom. Umožňuje vlasy tvarovať, zvýrazniť ich textúru a vytvoriť nenútený styling bez lesklého alebo mokrého efektu.',
            'for_whom'    => [
                'krátke a stredne dlhé vlasy',
                'prirodzené a textúrované účesy',
                'styling bez lesku',
                'mužov, ktorí nechcú príliš tuhú fixáciu',
            ],
            'expect'      => [
                'prirodzene pôsobiacu fixáciu',
                'matný finish',
                'definovanejšiu textúru',
                'flexibilný styling',
                'upravený vzhľad bez tvrdého efektu',
            ],
            'usage'       => 'Naneste malé množstvo Man Paste do suchých vlasov. Produkt najskôr dôkladne rozpracujte medzi dlaňami a následne zapracujte do vlasov, pričom prstami vytvarujte požadovaný účes a zvýraznite jeho textúru. Podľa potreby pridajte malé množstvo produktu pre výraznejšiu definíciu a fixáciu. Neoplachujte. Ideálna pre prirodzený, textúrovaný styling s matným efektom.',
        ],
        'Man Wax' => [
            'subtitle'    => 'Silne fixačný vosk s matným efektom',
            'description' => 'Modelovací vosk pre účesy, ktoré potrebujú výraznejšiu kontrolu a dlhotrvajúci tvar. Poskytuje silnú fixáciu a matný efekt bez zbytočného lesku.',
            'for_whom'    => [
                'krátke až stredne dlhé vlasy',
                'účesy vyžadujúce pevnú fixáciu',
                'výraznú textúru a definíciu',
                'každého, kto preferuje matný výsledok',
            ],
            'expect'      => [
                'silnú fixáciu',
                'matný finish',
                'výraznejšiu textúru',
                'dlhšiu kontrolu nad účesom',
                'definovaný a upravený výsledok',
            ],
            'usage'       => 'Naneste malé množstvo Man Wax do suchých vlasov. Produkt najskôr dôkladne rozpracujte medzi dlaňami a následne zapracujte do vlasov, pričom prstami vytvarujte požadovaný účes a zvýraznite jednotlivé pramene a textúru. Podľa potreby pridajte malé množstvo produktu pre silnejšiu fixáciu a výraznejšiu definíciu. Neoplachujte. Ideálny pre pevný, kontrolovaný styling s matným efektom.',
        ],
        'Man Pomade' => [
            'subtitle'    => 'Pomáda s extra silnou fixáciou a leskom',
            'description' => 'Pomáda vytvorená pre uhladené a precízne účesy s výrazným leskom. Poskytuje extra silnú fixáciu, pričom vlasy zostávajú počas dňa mäkké a znovu upraviteľné.',
            'for_whom'    => [
                'uhladené a klasické pánske účesy',
                'slick-back styling',
                'krátke až stredne dlhé vlasy',
                'každého, kto preferuje lesklý a precízny finish',
            ],
            'expect'      => [
                'extra silnú fixáciu',
                'výrazný lesk',
                'precízne definovaný účes',
                'hladký a uhladený výsledok',
                'možnosť účes počas dňa znovu upraviť',
            ],
            'usage'       => 'Naneste malé množstvo Man Pomade do suchých alebo mierne vlhkých vlasov. Produkt najskôr dôkladne rozpracujte medzi dlaňami a následne rovnomerne zapracujte do vlasov. Pomocou prstov alebo hrebeňa vytvarujte požadovaný účes a uhlaďte jednotlivé pramene. Podľa potreby pridajte malé množstvo produktu pre silnejšiu fixáciu a výraznejší lesk. Neoplachujte. Ideálna pre precízne, uhladené účesy a slick-back styling. Styling, textúra, ochrana a finálny efekt',
        ],
        'Styling Creme' => [
            'subtitle'    => 'Bezoplachový stylingový krém proti krepovateniu',
            'description' => 'Ľahký bezoplachový krém vhodný pre všetky typy vlasov. Pomáha vlasy hydratovať, rozčesať a uhladiť, redukuje krepovatenie a pripravuje ich na následný styling. Zanecháva vlasy hebké, lesklé a prirodzene poddajné.',
            'for_whom'    => [
                'všetky typy vlasov',
                'suchšie a nepoddajné vlasy',
                'vlasy so sklonom ku krepovateniu',
                'vlasy, ktoré sa ťažšie rozčesávajú',
            ],
            'expect'      => [
                'jednoduchšie rozčesávanie',
                'intenzívnejší pocit hydratácie',
                'menej krepovatenia',
                'hebkosť a lesk',
                'prirodzenú ľahkú fixáciu bez zaťaženia',
            ],
            'usage'       => 'Naneste malé množstvo Styling Creme do uterákom vysušených, vlhkých vlasov, najmä do dĺžok a končekov. Rovnomerne rozpracujte prstami alebo prečešte, aby produkt pomohol vlasy hydratovať, uhladiť, uľahčiť ich rozčesávanie a kontrolovať krepovatenie. Neoplachujte a pokračujte fénovaním alebo požadovaným stylingom. Vhodný je aj ako príprava vlasov pred ďalšími stylingovými produktmi.',
        ],
        'Curl Definer' => [
            'subtitle'    => 'Definujúci fluid pre vlnité a kučeravé vlasy',
            'description' => 'Stylingový fluid vytvorený na oživenie a definovanie kučier. Pomáha zvýrazniť ich prirodzený tvar, pružnosť a pohyb a zároveň redukuje krepovatenie. Výsledkom sú mäkšie, pružnejšie a dlhšie definované kučery.',
            'for_whom'    => [
                'vlnité a kučeravé vlasy',
                'kučery bez definície',
                'suchšie a dehydrované kučery',
                'vlasy so sklonom ku krepovateniu',
            ],
            'expect'      => [
                'výraznejšie definované kučery',
                'väčšiu pružnosť',
                'menej krepovatenia',
                'hydratovanejší pocit vlasov',
                'prirodzený pohyb bez tvrdého efektu',
            ],
            'usage'       => 'Naneste malé množstvo Curl Definer do uterákom vysušených, vlhkých vlasov, najmä do dĺžok a končekov. Rovnomerne rozpracujte prstami alebo hrebeňom so širokými zubami a kučery jemne vytvarujte stláčaním smerom nahor, aby ste podporili ich prirodzený tvar, pružnosť a definíciu. Neoplachujte. Nechajte voľne uschnúť alebo vysušte pomocou difuzéra pre výraznejšie definované kučery.',
        ],
        'Taming Gloss' => [
            'subtitle'    => 'Uhladzujúci fluid proti krepovateniu a vlhkosti',
            'description' => 'Uhladzujúca starostlivosť, ktorá pomáha kontrolovať krepovatenie a chráni vlasy pred vplyvom vlhkosti. Zároveň funguje ako tepelná ochrana pri používaní stylingových nástrojov a dodáva vlasom hebkosť a lesk.',
            'for_whom'    => [
                'krepovité a nepoddajné vlasy',
                'suchšie a porézne dĺžky',
                'vlasy reagujúce na vlhkosť',
                'vlasy pravidelne upravované teplom',
            ],
            'expect'      => [
                'uhladenejší povrch vlasov',
                'redukciu krepovatenia',
                'lepšiu kontrolu pri vlhkom počasí',
                'väčší lesk a hebkosť',
                'tepelnú ochranu pri stylingu',
            ],
            'usage'       => 'Naneste malé množstvo Taming Gloss do uterákom vysušených, vlhkých vlasov, najmä do dĺžok a končekov. Rovnomerne rozpracujte prstami alebo prečešte, aby produkt pomohol uhladiť vlasové vlákno, kontrolovať krepovatenie a chrániť vlasy pred vlhkosťou a teplom počas stylingu. Neoplachujte a pokračujte fénovaním alebo tepelnou úpravou vlasov.',
        ],
        'Plumping Serum' => [
            'subtitle'    => 'Objemové sérum pre plnšie vlasy',
            'description' => 'Ľahké sérum vytvorené na zvýšenie pocitu hrúbky a hustoty vlasov. Pomáha jemným a tenkým vlasom pôsobiť plnšie, zároveň znižuje poréznosť vlasového vlákna a podporuje hebkosť a lesk.',
            'for_whom'    => [
                'jemné a tenké vlasy',
                'vlasy bez objemu',
                'redšie pôsobiace dĺžky',
                'porézne vlasy bez plnosti',
            ],
            'expect'      => [
                'plnšie pôsobiace vlasové vlákno',
                'väčší pocit hustoty',
                'ľahký objem',
                'hladší povrch vlasov',
                'viac hebkosti a lesku',
            ],
            'usage'       => 'Naneste malé množstvo Plumping Serum do uterákom vysušených, vlhkých vlasov, rovnomerne od dĺžok ku končekom. Jemne rozpracujte prstami alebo prečešte, aby sa produkt rovnomerne rozložil a pomohol vlasovému vláknu dodať plnší vzhľad, objem a hebkosť. Neoplachujte a pokračujte fénovaním alebo požadovaným stylingom. Pre maximálny objem vlasy pri fénovaní nadvihujte od korienkov.',
        ],
        'Sea Salt Spray' => [
            'subtitle'    => 'Texturizačný sprej s beach efektom',
            'description' => 'Stylingový sprej s morskou soľou pre prirodzenú textúru a uvoľnený beach look. Pomáha vlasom dodať objem, štruktúru a mierne rozstrapatený efekt bez potreby komplikovaného stylingu.',
            'for_whom'    => [
                'rovné aj vlnité vlasy',
                'vlasy bez textúry a objemu',
                'prirodzené beach waves',
                'uvoľnené, nedokonalé stylingy',
            ],
            'expect'      => [
                'výraznejšiu textúru',
                'prirodzený objem',
                'beach-wave efekt',
                'ľahko rozstrapatený finish',
                'lepšiu tvarovateľnosť vlasov',
            ],
            'usage'       => 'Nastriekajte Sea Salt Spray rovnomerne do vlhkých alebo suchých vlasov, najmä do dĺžok. Jemne zapracujte prstami a vlasy stláčajte alebo tvarujte podľa požadovaného výsledku, aby ste podporili textúru, objem a prirodzený beach-wave efekt. Neoplachujte. Nechajte voľne uschnúť alebo vysušte fénom či difuzérom pre výraznejšiu textúru a objem.',
        ],
        'Shine Glaze' => [
            'subtitle'    => 'Lesklý stylingový fluid s ľahkou fixáciou',
            'description' => 'Modelovací fluid, ktorý kombinuje jemnú fixáciu s intenzívnejším leskom. Pomáha vlasom dodať uhladený a upravený vzhľad bez pocitu tuhosti či zbytočného zaťaženia.',
            'for_whom'    => [
                'matné vlasy bez lesku',
                'styling vyžadujúci ľahkú fixáciu',
                'hladké a uhladené účesy',
                'všetky typy vlasov',
            ],
            'expect'      => [
                'výraznejší lesk',
                'ľahkú fixáciu',
                'hladší vzhľad vlasov',
                'prirodzený pohyb',
                'elegantný finálny efekt',
            ],
            'usage'       => 'Naneste malé množstvo Shine Glaze do vlhkých vlasov a rovnomerne rozpracujte najmä do dĺžok a končekov. Jemne prečešte, aby sa produkt rovnomerne rozložil a pomohol vlasom dodať lesk, uhladenie a ľahkú fixáciu bez zbytočného zaťaženia. Neoplachujte a pokračujte fénovaním alebo požadovaným stylingom.',
        ],
        'Acid Water' => [
            'subtitle'    => 'Okysľujúci sprej na ochranu farby a uhladenie vlasov',
            'description' => 'Okysľujúci sprej vytvorený najmä pre farbené, zosvetľované a poškodené vlasy. Pomáha vlasové vlákno uhladiť, podporuje ochranu farby a dodáva vlasom lesklejší a kompaktnejší vzhľad.',
            'for_whom'    => [
                'farbené vlasy',
                'zosvetľované a melírované vlasy',
                'poškodené a porézne vlasy',
                'vlasy bez lesku a hladkosti',
            ],
            'expect'      => [
                'uhladenejšiu vlasovú kutikulu',
                'výraznejší lesk',
                'podporu ochrany farby',
                'hladší a zdravší vzhľad vlasov',
                'menej porézny pocit v dĺžkach',
            ],
            'usage'       => 'Nastriekajte Acid Water rovnomerne do čistých, uterákom vysušených vlasov, najmä do dĺžok a končekov. Jemne prečešte, aby sa produkt rovnomerne rozložil a pomohol uzavrieť vlasovú kutikulu, uhladiť vlasové vlákno a zvýrazniť lesk. Neoplachujte a pokračujte fénovaním alebo požadovaným stylingom. Vhodný je najmä pre farbené, zosvetľované a porézne vlasy.',
        ],
        'Style and Finish Defining Paste' => [
            'subtitle'    => 'Definujúca pasta so strednou fixáciou',
            'description' => 'Modelovacia pasta pre tvar, definíciu a lesk. Poskytuje strednú fixáciu, pomáha zvýrazniť textúru účesu a zároveň redukuje statickú elektrinu vo vlasoch.',
            'for_whom'    => [
                'krátke až stredne dlhé vlasy',
                'účesy vyžadujúce definíciu',
                'textúrované stylingy',
                'vlasy so sklonom k statickej elektrine',
            ],
            'expect'      => [
                'strednú fixáciu',
                'výraznejšiu definíciu',
                'lepšie tvarovanie vlasov',
                'jemný lesk',
                'anti-static efekt',
            ],
            'usage'       => 'Naneste malé množstvo Defining Paste do suchých alebo mierne vlhkých vlasov. Produkt najskôr dôkladne rozpracujte medzi dlaňami a následne zapracujte do vlasov, pričom prstami vytvarujte požadovaný účes a zvýraznite jednotlivé pramene a textúru. Podľa potreby pridajte malé množstvo produktu pre výraznejšiu definíciu a fixáciu. Neoplachujte. Ideálna pre flexibilný styling so strednou fixáciou a prirodzeným leskom.',
        ],
        'Extra Firm Hairspray' => [
            'subtitle'    => 'Lak na vlasy s extra silnou fixáciou',
            'description' => 'Silno fixačný lak vytvorený na dlhotrvajúcu kontrolu účesu. Pomáha chrániť styling pred vlhkosťou a zároveň pôsobí proti statickej elektrine.',
            'for_whom'    => [
                'účesy vyžadujúce silnú fixáciu',
                'styling, ktorý musí vydržať celý deň',
                'vlasy reagujúce na vlhkosť',
                'precízne a spoločenské účesy',
            ],
            'expect'      => [
                'extra silnú fixáciu',
                'dlhotrvajúcu kontrolu účesu',
                'anti-humidity efekt',
                'redukciu statickej elektriny',
                'pevnejšie udržanie výsledného tvaru',
            ],
            'usage'       => 'Pred použitím Extra Firm Hairspray dôkladne pretrepte a nastriekajte rovnomerne na suché, upravené vlasy zo vzdialenosti približne 20–30 cm. Aplikujte ako záverečný krok stylingu pre extra silnú a dlhotrvajúcu fixáciu, kontrolu účesu a ochranu pred vlhkosťou. Pre výraznejšiu fixáciu aplikujte postupne v niekoľkých ľahkých vrstvách. Neoplachujte.',
        ],
        'No Gas Hairspray' => [
            'subtitle'    => 'Ekologický lak s extra silnou fixáciou',
            'description' => 'Bezplynový lak na vlasy s objemovým účinkom a extra silnou fixáciou. Pomáha účes prirodzene zafixovať bez viditeľných zvyškov a zároveň podporuje objem vlasov.',
            'for_whom'    => [
                'účesy vyžadujúce veľmi silnú fixáciu',
                'vlasy bez objemu',
                'zákazníkov preferujúcich bezplynový lak',
                'precízny styling bez viditeľných zvyškov produktu',
            ],
            'expect'      => [
                'extra silnú fixáciu',
                'podporu objemu',
                'prirodzenejší vzhľad',
                'minimum viditeľných zvyškov',
                'dlhotrvajúci výsledok',
            ],
            'usage'       => 'Nastriekajte No Gas Hairspray rovnomerne na suché, upravené vlasy zo vzdialenosti približne 20–30 cm. Aplikujte ako záverečný krok stylingu pre extra silnú a dlhotrvajúcu fixáciu, podporu objemu a kontrolu účesu. Pre výraznejšiu fixáciu aplikujte postupne v niekoľkých ľahkých vrstvách. Neoplachujte.',
        ],
        'Style and Finish Extra Firm Mousse' => [
            'subtitle'    => 'Pena s extra silnou fixáciou',
            'description' => 'Stylingová pena poskytujúca extra silnú fixáciu a podporu tvaru vlasov. Je vhodná najmä na definovanie pružných kučier, zvýraznenie objemu a kontrolu krepovatenia.',
            'for_whom'    => [
                'kučeravé a vlnité vlasy',
                'účesy vyžadujúce silnú fixáciu',
                'vlasy bez objemu',
                'vlasy so sklonom ku krepovateniu',
            ],
            'expect'      => [
                'extra silnú fixáciu',
                'výraznejšiu definíciu kučier',
                'väčšiu pružnosť',
                'podporu objemu',
                'anti-frizz efekt',
            ],
            'usage'       => 'Pred použitím Extra Firm Mousse dôkladne pretrepte. Naneste primerané množstvo peny do uterákom vysušených, vlhkých vlasov a rovnomerne rozpracujte od korienkov do dĺžok. Vytvarujte vlasy podľa požadovaného výsledku a pokračujte fénovaním alebo sušením difuzérom. Neoplachujte. Ideálna pre extra silnú fixáciu, podporu objemu a výraznejšiu definíciu vĺn a kučier.',
        ],
        'Instant Detangler' => [
            'subtitle'    => 'Okamžitá starostlivosť na jednoduchšie rozčesávanie',
            'description' => 'Bezoplachová starostlivosť vytvorená na okamžité uľahčenie rozčesávania vlasov. Pomáha znižovať zamotávanie a mechanické namáhanie pri česaní a zanecháva vlasy hladšie, mäkšie a poddajnejšie.',
            'for_whom'    => [
                'vlasy, ktoré sa ľahko zamotávajú',
                'dlhé a husté vlasy',
                'suchšie alebo porézne dĺžky',
                'vlasy náchylné na poškodenie pri česaní',
            ],
            'expect'      => [
                'okamžite jednoduchšie rozčesávanie',
                'menšie ťahanie pri česaní',
                'hladšie a mäkšie vlasy',
                'lepšiu ovládateľnosť vlasov',
                'menej mechanického namáhania',
            ],
            'usage'       => 'Nastriekajte Instant Detangler rovnomerne do čistých, uterákom vysušených vlasov, najmä do dĺžok a končekov. Jemne prečešte od končekov smerom nahor, aby produkt pomohol uvoľniť zamotané vlasy, uľahčiť rozčesávanie a obmedziť mechanické poškodenie pri česaní. Neoplachujte a pokračujte bežným stylingom.',
        ],
        'Scalp Protective Oil' => [
            'subtitle'    => 'Ochranný olej pre vlasovú pokožku',
            'description' => 'Ochranná starostlivosť určená na vlasovú pokožku pred profesionálnymi chemickými procesmi. Vytvára ochrannú bariéru a pomáha zmierniť pocit citlivosti a diskomfortu počas farbenia alebo iných technických služieb.',
            'for_whom'    => [
                'citlivá vlasová pokožka',
                'klienti so sklonom k diskomfortu počas farbenia',
                'profesionálne technické služby v salóne',
                'pokožka, ktorá potrebuje zvýšenú ochranu',
            ],
            'expect'      => [
                'ochrannú vrstvu na vlasovej pokožke',
                'väčší komfort počas chemických služieb',
                'zníženie pocitu podráždenia',
                'jednoduchú profesionálnu aplikáciu',
            ],
            'usage'       => 'Pred farbením alebo inou chemickou službou aplikujte Scalp Protective Oil priamo na suchú vlasovú pokožku, najmä na citlivé miesta a oblasti so sklonom k podráždeniu. Jemne rozotrite končekmi prstov, aby sa produkt rovnomerne rozložil a vytvoril ochrannú bariéru medzi pokožkou a chemickým produktom. Neoplachujte a následne pokračujte plánovanou profesionálnou službou. Určený najmä na profesionálne použitie v salóne.',
        ],
        // Doplnené 8. 9. 2026 (WhatsApp): Taming + Hair and Scalp.
        'Smoothing Taming Shampoo' => [
            'subtitle'    => 'Uhladzujúci šampón pre nepoddajné a krepovité vlasy',
            'description' => 'Jemne čistí vlasy a vlasovú pokožku a zároveň pomáha disciplinovať nepoddajné vlasové vlákno. Podporuje uhladenie, hebkosť a kontrolu krepovatenia a pripravuje vlasy na následnú uhladzujúcu starostlivosť. Ideálny pre vlasy, ktoré reagujú na vlhkosť, ťažšie sa upravujú alebo majú prirodzene hrubšiu a nepoddajnú štruktúru.',
            'for_whom'    => [
                'nepoddajné a krepovité vlasy',
                'hrubšie a ťažšie upraviteľné vlasy',
                'suché a porézne dĺžky',
                'vlasy reagujúce na vlhkosť',
                'vlasy, ktoré potrebujú uhladenie a väčšiu kontrolu',
            ],
            'expect'      => [
                'hladšie a disciplinovanejšie vlasy',
                'redukciu krepovatenia',
                'väčšiu hebkosť a poddajnosť',
                'jednoduchšie rozčesávanie a následný styling',
                'lesklejší a upravenejší vzhľad vlasov',
            ],
            'usage'       => 'Naneste Smoothing Taming Shampoo na mokrú vlasovú pokožku a vlasy. Jemne vmasírujte končekmi prstov a rovnomerne rozpracujte do dĺžok, aby sa vlasy šetrne vyčistili a pripravili na následnú uhladzujúcu starostlivosť. Dôkladne opláchnite a v prípade potreby aplikáciu zopakujte. Pre kompletnú Taming rutinu pokračujte Smoothing Taming Conditioner, ktorý pomáha vlasové vlákno ďalej uhladiť, zjemniť a kontrolovať krepovatenie.',
        ],
        'Smoothing Taming Conditioner' => [
            'subtitle'    => 'Uhladzujúci kondicionér pre nepoddajné a krepovité vlasy',
            'description' => 'Kondicionačná starostlivosť vytvorená pre vlasy, ktoré potrebujú väčšiu kontrolu, hebkosť a uhladenie. Pomáha disciplinovať nepoddajné vlasové vlákno, redukovať krepovatenie a uľahčiť rozčesávanie. Vlasy zostávajú hladšie, mäkšie a poddajnejšie bez straty prirodzeného pohybu.',
            'for_whom'    => [
                'nepoddajné a krepovité vlasy',
                'suché, drsné a porézne dĺžky',
                'hrubšie vlasy, ktoré sa ťažšie upravujú',
                'vlasy reagujúce na vlhkosť',
                'vlasy, ktoré potrebujú uhladenie bez zbytočného zaťaženia',
            ],
            'expect'      => [
                'výraznejšie uhladenie vlasového vlákna',
                'menej krepovatenia',
                'jednoduchšie rozčesávanie',
                'hebkejšie a poddajnejšie dĺžky',
                'väčší lesk',
                'disciplinovanejší a upravenejší výsledok',
            ],
            'usage'       => 'Po umytí vlasov Smoothing Taming Shampoo naneste Smoothing Taming Conditioner do uterákom vysušených vlasov, najmä do dĺžok a končekov. Rovnomerne rozpracujte a jemne prečešte, aby sa produkt dostal do všetkých dĺžok. Nechajte krátko pôsobiť a následne dôkladne opláchnite. Pokračujte bežným stylingom; pri vlasoch so sklonom ku krepovateniu môžete následne použiť Taming Gloss pre ešte výraznejšie uhladenie a kontrolu.',
        ],
        'Dry Scalp Massage Oil' => [
            'subtitle'    => 'Vyživujúci masážny olej pre suchú vlasovú pokožku',
            'description' => 'Intenzívna olejová starostlivosť vytvorená pre suchú, dehydrovanú a napätú vlasovú pokožku. Pomáha pokožku vyživovať, zjemňovať a obnovovať jej komfort, pričom masáž podporuje príjemný pocit uvoľnenia a celkovú starostlivosť o pokožku hlavy.',
            'for_whom'    => [
                'suchá a dehydrovaná vlasová pokožka',
                'pokožka s pocitom pnutia a diskomfortu',
                'suchá a šupinatá pokožka',
                'pokožka, ktorá potrebuje intenzívnejšiu výživu',
                'každý, kto chce zaradiť olejovú masáž do scalp-care rutiny',
            ],
            'expect'      => [
                'intenzívnejšie vyživenú vlasovú pokožku',
                'zmiernenie pocitu suchosti a pnutia',
                'mäkšiu a komfortnejšiu pokožku',
                'podporu prirodzenej rovnováhy pokožky',
                'príjemný relaxačný efekt masáže',
                'lepšie pripravenú pokožku na následné umytie a starostlivosť',
            ],
            'usage'       => 'Aplikujte Dry Scalp Massage Oil po jednotlivých sekciách priamo na suchú vlasovú pokožku pred umytím vlasov. Jemne masírujte končekmi prstov krúživými pohybmi, aby sa olej rovnomerne rozložil a pokožka ho mohla absorbovať. Nechajte krátko pôsobiť a následne pokračujte umytím vhodným šampónom podľa potrieb vlasovej pokožky. Pri suchej pokožke môžete následne pokračovať ďalšími produktmi rutiny.',
        ],
        'Hair and Scalp Tonic Conditioner' => [
            'subtitle'    => 'Ošetrujúci kondicionér pre vlasy a vlasovú pokožku',
            'description' => 'Kondicionér vytvorený pre komplexnú starostlivosť o vlasy aj vlasovú pokožku. Pomáha vlasové vlákno hydratovať, zjemniť a uľahčiť jeho rozčesávanie a zároveň poskytuje pokožke príjemnú osviežujúcu starostlivosť. Vlasy zostávajú hebké, poddajné a ľahké bez zbytočného zaťaženia.',
            'for_whom'    => [
                'všetky typy vlasov a vlasovej pokožky',
                'suchšie a dehydrované vlasy',
                'vlasy, ktoré sa ťažšie rozčesávajú',
                'vlasová pokožka, ktorá potrebuje osvieženie a starostlivosť',
                'každý, kto chce ošetriť vlasy aj vlasovú pokožku v jednom kroku',
            ],
            'expect'      => [
                'hebkejšie a hydratovanejšie vlasy',
                'jednoduchšie rozčesávanie',
                'svieži a komfortný pocit vlasovej pokožky',
                'väčšiu poddajnosť vlasov',
                'ľahké a nezaťažené dĺžky',
                'komplexnú starostlivosť o vlasy aj pokožku',
            ],
            'usage'       => 'Po umytí vlasov naneste Hair and Scalp Tonic Conditioner na čisté, uterákom vysušené vlasy a vlasovú pokožku. Jemne vmasírujte do pokožky a rovnomerne rozpracujte do dĺžok a končekov. Nechajte krátko pôsobiť a následne dôkladne opláchnite.',
        ],
        'Dry Shampoo' => [
            'subtitle'    => 'Suchý šampón pre okamžité osvieženie vlasov',
            'description' => 'Praktická starostlivosť na rýchle osvieženie vlasov medzi jednotlivými umytiami. Pomáha absorbovať nadbytočný maz pri korienkoch, obnoviť pocit čistoty a dodať vlasom ľahkosť a objem bez použitia vody. Ideálny aj na oživenie účesu počas dňa.',
            'for_whom'    => [
                'rýchlo sa mastiace vlasy a korienky',
                'vlasy, ktoré potrebujú osviežiť medzi umytiami',
                'jemné a spľasnuté vlasy bez objemu',
                'predĺženie sviežeho vzhľadu účesu',
                'každého, kto potrebuje rýchle osvieženie vlasov bez umývania',
            ],
            'expect'      => [
                'absorpciu nadbytočného mazu',
                'sviežejší a čistejší vzhľad korienkov',
                'ľahšie pôsobiace vlasy',
                'podporu objemu a textúry',
                'predĺženie času medzi jednotlivými umytiami',
                'rýchle oživenie účesu',
            ],
            'usage'       => 'Pred použitím Dry Shampoo dôkladne pretrepte. Nastriekajte na suché vlasy ku korienkom zo vzdialenosti približne 20–30 cm, najmä na miesta s viditeľnou mastnotou. Nechajte produkt krátko pôsobiť, aby absorboval nadbytočný maz, následne jemne vmasírujte končekmi prstov a vlasy dôkladne prečešte. Neoplachujte a pokračujte bežným stylingom.',
        ],
    ];
}
