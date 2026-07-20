<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        $g = function (string $code, string $group, string $color = '', ?string $name = null): array {
            return [
                'code'  => $code,
                'name'  => $name ?? strtolower($group),
                'group' => $group,
                'color' => $color,
                'price' => null,
            ];
        };

        $shades = array_merge(
            // .0 NATURAL
            [
                $g('1.0',  'NATURAL', '#1a1410'),
                $g('3.0',  'NATURAL', '#2e2520'),
                $g('4.0',  'NATURAL', '#3f3328'),
                $g('5.0',  'NATURAL', '#564434'),
                $g('6.0',  'NATURAL', '#6e5a44'),
                $g('7.0',  'NATURAL', '#8e7558'),
                $g('8.0',  'NATURAL', '#ad8e6d'),
                $g('9.0',  'NATURAL', '#c6a888'),
                $g('10.0', 'NATURAL', '#ddc6a8'),
            ],
            // .0 COLD NATURAL
            [
                $g('55.0', 'COLD NATURAL', '#544a40'),
                $g('66.0', 'COLD NATURAL', '#6a5e52'),
                $g('77.0', 'COLD NATURAL', '#86766a'),
                $g('88.0', 'COLD NATURAL', '#a8988a'),
                $g('99.0', 'COLD NATURAL', '#c0b0a0'),
            ],
            // .1 ASH
            [
                $g('1.10', 'ASH', '#1c1c1a'),
                $g('5.1',  'ASH', '#5a5a56'),
                $g('6.1',  'ASH', '#6e6e68'),
                $g('7.1',  'ASH', '#888884'),
                $g('8.1',  'ASH', '#a4a49e'),
                $g('9.1',  'ASH', '#bcbcb4'),
                $g('10.1', 'ASH', '#d2d2c8'),
            ],
            // .08 NATURAL PEARL
            [
                $g('6.08',  'NATURAL PEARL', '#807468'),
                $g('8.08',  'NATURAL PEARL', '#a89e88'),
                $g('10.08', 'NATURAL PEARL', '#d4ccb6'),
            ],
            // .12 PEARL
            [
                $g('6.12',  'PEARL', '#7a7066'),
                $g('8.12',  'PEARL', '#ada48e'),
                $g('10.12', 'PEARL', '#d8d0bc'),
            ],
            // .17 ARCTIC
            [
                $g('6.17',  'ARCTIC', '#74797a'),
                $g('8.17',  'ARCTIC', '#a8b0ae'),
                $g('10.17', 'ARCTIC', '#d4d8d2'),
            ],
            // .72 COLD BROWN
            [
                $g('6.72',  'COLD BROWN', '#5a4838'),
                $g('8.72',  'COLD BROWN', '#7a685a'),
                $g('10.72', 'COLD BROWN', '#a8988a'),
            ],
            // .71 COLD BEIGE
            [
                $g('5.71',  'COLD BEIGE', '#56483a'),
                $g('6.71',  'COLD BEIGE', '#6e5e4e'),
                $g('7.71',  'COLD BEIGE', '#8a7a6a'),
                $g('8.71',  'COLD BEIGE', '#a89886'),
                $g('9.71',  'COLD BEIGE', '#c0b0a0'),
                $g('10.71', 'COLD BEIGE', '#d4c8b6'),
            ],
            // .3 GOLDEN
            [
                $g('4.3',  'GOLDEN', '#5a4020'),
                $g('5.3',  'GOLDEN', '#7a5c30'),
                $g('6.3',  'GOLDEN', '#a07a44'),
                $g('7.3',  'GOLDEN', '#b58647'),
                $g('8.3',  'GOLDEN', '#cca068'),
                $g('9.3',  'GOLDEN', '#dcb87e'),
                $g('10.3', 'GOLDEN', '#e8d0a0'),
            ],
            // .35 WARM BROWN
            [
                $g('3.35', 'WARM BROWN', '#2c2018'),
                $g('4.35', 'WARM BROWN', '#3e2e20'),
                $g('5.35', 'WARM BROWN', '#564030'),
                $g('6.35', 'WARM BROWN', '#6c5238'),
                $g('7.35', 'WARM BROWN', '#8a6a45'),
                $g('8.35', 'WARM BROWN', '#a48560'),
                $g('9.35', 'WARM BROWN', '#bea07e'),
            ],
            // .4 | .44 COPPER / INTENSE COPPER
            [
                $g('5.4',  'COPPER / INTENSE COPPER', '#7a4828', 'copper'),
                $g('6.4',  'COPPER / INTENSE COPPER', '#a05a34', 'copper'),
                $g('7.4',  'COPPER / INTENSE COPPER', '#c0744a', 'copper'),
                $g('7.44', 'COPPER / INTENSE COPPER', '#c45a30', 'intense copper'),
                $g('8.44', 'COPPER / INTENSE COPPER', '#d47446', 'intense copper'),
            ],
            // .66 RED
            [
                $g('5.66', 'RED', '#7a2a24'),
                $g('6.66', 'RED', '#9a342c'),
                $g('7.66', 'RED', '#b84a3a'),
            ],
            // ALLURE
            [
                $g('3.28', 'ALLURE', '#26171a'),
                $g('4.68', 'ALLURE', '#4e2820'),
                $g('5.27', 'ALLURE', '#503a38'),
                $g('6.61', 'ALLURE', '#7e463e'),
                $g('7.28', 'ALLURE', '#7a5040'),
                $g('8.32', 'ALLURE', '#c0a07a'),
                $g('9.36', 'ALLURE', '#e3c7b0'),
            ],
            // .55 MAHOGANY
            [
                $g('4.55', 'MAHOGANY', '#562422'),
                $g('5.55', 'MAHOGANY', '#7a3a36'),
            ],
            // .2 VIOLET
            [
                $g('4.2',  'VIOLET', '#3a2a36'),
                $g('6.2',  'VIOLET', '#6a4858'),
                $g('10.2', 'VIOLET', '#ddd0d8'),
            ],
            // SUPER LIGHTENER
            [
                $g('11.0',  'SUPER LIGHTENER', '#ece2c8'),
                $g('11.17', 'SUPER LIGHTENER', '#e8e4d4'),
                $g('11.1',  'SUPER LIGHTENER', '#e8e4d8'),
                $g('11.02', 'SUPER LIGHTENER', '#ede2cc'),
            ],
            // BOOSTER
            [
                $g('BOOST', 'BOOSTER', '#d0d0c8', 'booster'),
            ],
            // TONER
            [
                $g('T.PEARL',  'TONER', '#d4ccba', 'pearl'),
                $g('T.SILVER', 'TONER', '#b0aea8', 'silver'),
            ],
            // PURE PIGMENTS
            [
                $g('PIG.BLUE',   'PURE PIGMENTS', '#2050a0', 'pure blue'),
                $g('PIG.RED',    'PURE PIGMENTS', '#c0303a', 'pure red'),
                $g('PIG.YELLOW', 'PURE PIGMENTS', '#e0c040', 'pure yellow'),
            ],
            // LOLLIPOP
            [
                $g('LP.SCG',    'LOLLIPOP', '#5a5a5a', 'sweet coal grey'),
                $g('LP.SJB',    'LOLLIPOP', '#5a9adc', 'smurf jelly blue'),
                $g('LP.CFP',    'LOLLIPOP', '#f5b0c8', 'candy floss pink'),
                $g('LP.MARSH',  'LOLLIPOP', '#f0e8e0', 'marshmallow pearl'),
                $g('LP.ICING',  'LOLLIPOP', '#fafaf6', 'icing sugar white'),
            ],
        );

        Product::withoutGlobalScopes()
            ->where('slug', 'argan-keratin-color')
            ->update([
                'shades'   => json_encode($shades, JSON_UNESCAPED_UNICODE),
                'b2b_only' => true,
            ]);
    }

    public function down(): void
    {
        // no-op: shade data is content
    }
};
