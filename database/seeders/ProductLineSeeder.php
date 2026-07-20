<?php

namespace Database\Seeders;

use App\Models\ProductLine;
use Illuminate\Database\Seeder;

class ProductLineSeeder extends Seeder
{
    private const LOREM = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

    public function run(): void
    {
        ProductLine::query()->delete();

        // Línie podľa PREVIA CENNÍK 2026. Texty (eyebrow/description) sú
        // placeholder do dodania finálnych textov.
        $lines = [
            ['code' => '01', 'slug' => 'reconstruct',                'name' => 'Reconstruct'],
            ['code' => '02', 'slug' => 'keeping-after-color',        'name' => 'Keeping After Color'],
            ['code' => '03', 'slug' => 'energising',                 'name' => 'Energising'],
            ['code' => '04', 'slug' => 'regrowth',                   'name' => 'Regrowth'],
            ['code' => '05', 'slug' => 'purifying',                  'name' => 'Purifying'],
            ['code' => '06', 'slug' => 'dry-dandruff',               'name' => 'Dry Dandruff'],
            ['code' => '07', 'slug' => 'oily-dandruff',              'name' => 'Oily Dandruff'],
            ['code' => '08', 'slug' => 'calming',                    'name' => 'Calming'],
            ['code' => '09', 'slug' => 'rebalancing',                'name' => 'Rebalancing'],
            ['code' => '10', 'slug' => 'hair-and-scalp',             'name' => 'Hair and Scalp'],
            ['code' => '11', 'slug' => 'taming',                     'name' => 'Taming'],
            ['code' => '12', 'slug' => 'curl-friends',               'name' => 'Curl Friends'],
            ['code' => '13', 'slug' => 'bodifying',                  'name' => 'Bodifying'],
            ['code' => '14', 'slug' => 'blonde',                     'name' => 'Blonde'],
            ['code' => '15', 'slug' => 'styling-and-basics',         'name' => 'Styling and Basics'],
            ['code' => '16', 'slug' => 'man',                        'name' => 'Man'],
            ['code' => '17', 'slug' => 'earth-professional-color',   'name' => 'Earth Professional Color'],
            ['code' => '18', 'slug' => 'virtuos-professional-color', 'name' => 'Virtuos Professional Color'],
            ['code' => '19', 'slug' => 'waving-system',              'name' => 'Waving System'],
            ['code' => '20', 'slug' => 'doplnky',                    'name' => 'Doplnky'],
        ];

        foreach ($lines as $i => $row) {
            $row['eyebrow'] = 'Lorem ipsum dolor';
            $row['description'] = self::LOREM;
            $row['complex'] = 'Lorem ipsum';
            $row['sort_order'] = $i + 1;
            $row['published'] = true;
            ProductLine::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }
}
