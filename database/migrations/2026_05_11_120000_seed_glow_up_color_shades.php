<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $shades = [
            ['code' => '.00',   'name' => 'gloss',                              'color' => '#e6e0d3', 'price' => null],
            ['code' => '0.N',   'name' => 'natural',                            'color' => '#b8a78f', 'price' => null],
            ['code' => '1.28',  'name' => 'moka chocolate black',               'color' => '#1c1612', 'price' => null],
            ['code' => '4.1',   'name' => 'ash chestnut',                       'color' => '#3a322c', 'price' => null],
            ['code' => '5.2',   'name' => 'light brown violet',                 'color' => '#4b3a39', 'price' => null],
            ['code' => '5.71',  'name' => 'cold ash light chestnut',            'color' => '#564a40', 'price' => null],
            ['code' => '6.0',   'name' => 'natural dark blonde',                'color' => '#6b4f33', 'price' => null],
            ['code' => '6.28',  'name' => 'moka chocolate dark blonde',         'color' => '#5a3e2b', 'price' => null],
            ['code' => '7.32',  'name' => 'golden pearl blonde',                'color' => '#b88c5a', 'price' => null],
            ['code' => '7.33',  'name' => 'intense golden blonde',              'color' => '#c19461', 'price' => null],
            ['code' => '7.66',  'name' => 'intense red blonde',                 'color' => '#b85a3c', 'price' => null],
            ['code' => '7.71',  'name' => 'cold blonde ash',                    'color' => '#8a7a64', 'price' => null],
            ['code' => '8.21',  'name' => 'light blonde ash violet',            'color' => '#a89580', 'price' => null],
            ['code' => '8.22',  'name' => 'light blonde violet',                'color' => '#b09486', 'price' => null],
            ['code' => '8.44',  'name' => 'light blonde copper',                'color' => '#c97f4f', 'price' => null],
            ['code' => '9.0',   'name' => 'very light natural blonde',          'color' => '#d4b88a', 'price' => null],
            ['code' => '9.2',   'name' => 'very light lavender blonde',         'color' => '#c8b5a3', 'price' => null],
            ['code' => '9.11',  'name' => 'very light ash blonde',              'color' => '#c4b39a', 'price' => null],
            ['code' => '9.32',  'name' => 'very light golden pearl blonde',     'color' => '#d7be8e', 'price' => null],
            ['code' => '10.2',  'name' => 'lavender platinum blonde',           'color' => '#ddd0c0', 'price' => null],
            ['code' => '10.11', 'name' => 'ash platinum blonde',                'color' => '#d6cebd', 'price' => null],
            ['code' => '10.32', 'name' => 'golden pearl platinum blonde',       'color' => '#e0cfa8', 'price' => null],
            ['code' => '10.36', 'name' => 'champagne rosé platinum blonde',     'color' => '#e3c7b0', 'price' => null],
        ];

        Product::withoutGlobalScopes()
            ->where('slug', 'glow-up-color')
            ->update([
                'shades' => json_encode($shades, JSON_UNESCAPED_UNICODE),
                'b2b_only' => true,
            ]);
    }

    public function down(): void
    {
        // no-op: shade data is content, do not destroy on rollback
    }
};
