<?php

namespace App\Services\HairQuiz;

use App\Models\Product;
use App\Models\ProductLine;
use Illuminate\Support\Collection;

class Diagnoser
{
    public const TYPES = [
        'fine'     => 'Jemné',
        'normal'   => 'Normálne',
        'thick'    => 'Hrubé',
        'curly'    => 'Vlnité / kučeravé',
    ];

    public const PROBLEMS = [
        'damaged'         => 'Poškodené, lámavé',
        'dry'             => 'Suché, dehydratované',
        'frizz'           => 'Krepovaté, nepoddajné',
        'sensitive_scalp' => 'Citlivá pokožka, oslabené',
        'none'            => 'Bez väčších problémov',
    ];

    public const TREATMENTS = [
        'colored'      => 'Farbenie',
        'blonde'       => 'Melír alebo zosvetľovanie',
        'straightened' => 'Keratínové vyhladenie',
        'none'         => 'Žiadna z uvedených',
    ];

    public const FREQUENCIES = [
        'daily'   => 'Každý deň',
        'weekly3' => 'Každé 2-3 dni',
        'weekly1' => '1-2x týždenne',
        'rarely'  => 'Menej ako uvedené',
    ];

    public function diagnose(array $answers): array
    {
        $scores = $this->scoreLines($answers);
        $primarySlug = array_key_first($scores);
        if ($scores[$primarySlug] === 0) {
            $primarySlug = 'reconstruct';
        }

        $line = ProductLine::where('slug', $primarySlug)->first();
        $ritual = $line ? $this->buildRitual($line) : new Collection;

        return [
            'line'      => $line,
            'ritual'    => $ritual,
            'rationale' => $this->rationale($answers, $line),
            'scores'    => $scores,
        ];
    }

    private function scoreLines(array $answers): array
    {
        $scores = [
            'reconstruct'         => 0,
            'keeping-after-color' => 0,
            'blonde'              => 0,
            'taming'              => 0,
            'calming'             => 0,
            'curl-friends'        => 0,
            'bodifying'           => 0,
        ];

        $treatment = $answers['treatment'] ?? null;
        if ($treatment === 'blonde')       $scores['blonde']              += 6;
        if ($treatment === 'colored')      $scores['keeping-after-color'] += 5;
        if ($treatment === 'straightened') $scores['taming']              += 5;

        $problem = $answers['problem'] ?? null;
        if ($problem === 'damaged')         $scores['reconstruct'] += 5;
        if ($problem === 'dry')             $scores['reconstruct'] += 4;
        if ($problem === 'sensitive_scalp') $scores['calming']     += 5;
        if ($problem === 'frizz')           $scores['taming']      += 3;

        $type = $answers['type'] ?? null;
        if ($type === 'curly') $scores['curl-friends'] += 3;
        if ($type === 'fine')  $scores['bodifying']    += 1;

        arsort($scores);
        return $scores;
    }

    private function buildRitual(ProductLine $line): Collection
    {
        $products = $line->products()->where('published', true)->orderBy('sort_order')->get();

        $picked = collect()
            ->push($products->first(fn ($p) => $p->type === 'sampon'))
            ->push($products->first(fn ($p) => in_array($p->type, ['maska', 'kondicioner'])))
            ->push($products->first(fn ($p) => $p->type === 'olej-serum'))
            ->filter()
            ->values();

        if ($picked->count() < 3) {
            $rest = $products->whereNotIn('id', $picked->pluck('id'))->take(3 - $picked->count());
            $picked = $picked->concat($rest)->values();
        }

        // Doplň stylingový produkt do rituálu - z línie Styling and Basics (finálny krok).
        $styleLine = ProductLine::where('slug', 'styling-and-basics')->first();
        $styling = $styleLine
            ? $styleLine->products()
                ->where('published', true)
                ->orderBy('sort_order')
                ->get()
                ->first(fn ($p) => $p->type === 'styling')
            : null;
        if ($styling && !$picked->contains('id', $styling->id)) {
            $picked = $picked->push($styling)->values();
        }

        return $picked;
    }

    private function rationale(array $answers, ?ProductLine $line): string
    {
        if (!$line) {
            return 'Toto je výber pre všeobecnú každodennú starostlivosť.';
        }

        $bits = [];
        if (!empty($answers['treatment']) && $answers['treatment'] !== 'none') {
            $bits[] = self::TREATMENTS[$answers['treatment']] ?? '';
        }
        if (!empty($answers['problem']) && $answers['problem'] !== 'none') {
            $bits[] = mb_strtolower(self::PROBLEMS[$answers['problem']] ?? '');
        }
        if (!empty($answers['type'])) {
            $bits[] = mb_strtolower(self::TYPES[$answers['type']] ?? '') . ' vlasy';
        }

        $bits = array_filter($bits);
        if (empty($bits)) {
            return 'Línia ' . $line->name . ' je všestranná každodenná voľba.';
        }

        return 'Vybrali sme ' . $line->name . ' pretože tvoje odpovede ukazujú: ' . implode(' · ', $bits) . '.';
    }
}
