<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data migration: shade lists for the professional colours, transcribed from
     * the official Previa colour charts (VIRTUOUS COLOUR CHART, EARTH COLOUR
     * CHART BOOK 24, Earth Powder Infusion barevnice). Every shade carries its
     * own warehouse SKU ("<product sku>-<shade code>"). Swatch HEX values are
     * approximations of the chart — tune per shade in the admin if needed.
     * Idempotent: fills only products whose shades are still empty.
     */
    public function up(): void
    {
        $sets = [
            '111' => $this->virtuos(),
            '102' => $this->earth(),
            '110' => $this->powderInfusion(),
        ];

        foreach ($sets as $code => $shades) {
            $product = DB::table('products')->where('code', $code)->first(['id', 'shades']);
            if (!$product) {
                continue;
            }
            $current = json_decode($product->shades ?? '[]', true);
            if (is_array($current) && $current !== []) {
                continue; // already filled (possibly edited in admin) — never overwrite
            }
            DB::table('products')->where('id', $product->id)
                ->update(['shades' => json_encode($shades, JSON_UNESCAPED_UNICODE)]);
        }
    }

    private function virtuos(): array
    {
        return [
            ['code' => '1.0', 'name' => 'natural', 'group' => 'NATURAL', 'color' => '#1a1512', 'price' => null, 'sku' => '111-1.0'],
            ['code' => '3.0', 'name' => 'natural', 'group' => 'NATURAL', 'color' => '#30261e', 'price' => null, 'sku' => '111-3.0'],
            ['code' => '4.0', 'name' => 'natural', 'group' => 'NATURAL', 'color' => '#3e3024', 'price' => null, 'sku' => '111-4.0'],
            ['code' => '5.0', 'name' => 'natural', 'group' => 'NATURAL', 'color' => '#523e2c', 'price' => null, 'sku' => '111-5.0'],
            ['code' => '6.0', 'name' => 'natural', 'group' => 'NATURAL', 'color' => '#695037', 'price' => null, 'sku' => '111-6.0'],
            ['code' => '7.0', 'name' => 'natural', 'group' => 'NATURAL', 'color' => '#826544', 'price' => null, 'sku' => '111-7.0'],
            ['code' => '8.0', 'name' => 'natural', 'group' => 'NATURAL', 'color' => '#9e7f58', 'price' => null, 'sku' => '111-8.0'],
            ['code' => '9.0', 'name' => 'natural', 'group' => 'NATURAL', 'color' => '#b99b70', 'price' => null, 'sku' => '111-9.0'],
            ['code' => '10.0', 'name' => 'natural', 'group' => 'NATURAL', 'color' => '#d0b58c', 'price' => null, 'sku' => '111-10.0'],
            ['code' => '4.00', 'name' => 'intense natural', 'group' => 'INTENSE NATURAL', 'color' => '#3b2d21', 'price' => null, 'sku' => '111-4.00'],
            ['code' => '5.00', 'name' => 'intense natural', 'group' => 'INTENSE NATURAL', 'color' => '#4e3a28', 'price' => null, 'sku' => '111-5.00'],
            ['code' => '6.00', 'name' => 'intense natural', 'group' => 'INTENSE NATURAL', 'color' => '#654c33', 'price' => null, 'sku' => '111-6.00'],
            ['code' => '7.00', 'name' => 'intense natural', 'group' => 'INTENSE NATURAL', 'color' => '#7e6140', 'price' => null, 'sku' => '111-7.00'],
            ['code' => '8.00', 'name' => 'intense natural', 'group' => 'INTENSE NATURAL', 'color' => '#9a7b54', 'price' => null, 'sku' => '111-8.00'],
            ['code' => '5.1', 'name' => 'ash', 'group' => 'ASH', 'color' => '#463c31', 'price' => null, 'sku' => '111-5.1'],
            ['code' => '6.1', 'name' => 'ash', 'group' => 'ASH', 'color' => '#5c4e3d', 'price' => null, 'sku' => '111-6.1'],
            ['code' => '7.1', 'name' => 'ash', 'group' => 'ASH', 'color' => '#74634a', 'price' => null, 'sku' => '111-7.1'],
            ['code' => '8.1', 'name' => 'ash', 'group' => 'ASH', 'color' => '#8f7d5f', 'price' => null, 'sku' => '111-8.1'],
            ['code' => '9.1', 'name' => 'ash', 'group' => 'ASH', 'color' => '#a99977', 'price' => null, 'sku' => '111-9.1'],
            ['code' => '4.01', 'name' => 'natural ash', 'group' => 'NATURAL ASH', 'color' => '#362e27', 'price' => null, 'sku' => '111-4.01'],
            ['code' => '5.01', 'name' => 'natural ash', 'group' => 'NATURAL ASH', 'color' => '#493c30', 'price' => null, 'sku' => '111-5.01'],
            ['code' => '6.01', 'name' => 'natural ash', 'group' => 'NATURAL ASH', 'color' => '#604e3b', 'price' => null, 'sku' => '111-6.01'],
            ['code' => '7.01', 'name' => 'natural ash', 'group' => 'NATURAL ASH', 'color' => '#786348', 'price' => null, 'sku' => '111-7.01'],
            ['code' => '1.08', 'name' => 'natural pearl', 'group' => 'NATURAL PEARL', 'color' => '#1c1315', 'price' => null, 'sku' => '111-1.08'],
            ['code' => '5.08', 'name' => 'natural pearl', 'group' => 'NATURAL PEARL', 'color' => '#563a31', 'price' => null, 'sku' => '111-5.08'],
            ['code' => '6.08', 'name' => 'natural pearl', 'group' => 'NATURAL PEARL', 'color' => '#6d4c3d', 'price' => null, 'sku' => '111-6.08'],
            ['code' => '7.08', 'name' => 'natural pearl', 'group' => 'NATURAL PEARL', 'color' => '#86614a', 'price' => null, 'sku' => '111-7.08'],
            ['code' => '8.08', 'name' => 'natural pearl', 'group' => 'NATURAL PEARL', 'color' => '#a27b5f', 'price' => null, 'sku' => '111-8.08'],
            ['code' => '4.82', 'name' => 'cold chocolate pearl violet', 'group' => 'COLD CHOCOLATE PEARL VIOLET', 'color' => '#402a27', 'price' => null, 'sku' => '111-4.82'],
            ['code' => '6.82', 'name' => 'cold chocolate pearl violet', 'group' => 'COLD CHOCOLATE PEARL VIOLET', 'color' => '#6b483b', 'price' => null, 'sku' => '111-6.82'],
            ['code' => '5.3', 'name' => 'gold', 'group' => 'GOLD', 'color' => '#604520', 'price' => null, 'sku' => '111-5.3'],
            ['code' => '6.3', 'name' => 'gold', 'group' => 'GOLD', 'color' => '#78582a', 'price' => null, 'sku' => '111-6.3'],
            ['code' => '7.3', 'name' => 'gold', 'group' => 'GOLD', 'color' => '#926d36', 'price' => null, 'sku' => '111-7.3'],
            ['code' => '8.3', 'name' => 'gold', 'group' => 'GOLD', 'color' => '#b08849', 'price' => null, 'sku' => '111-8.3'],
            ['code' => '9.3', 'name' => 'gold', 'group' => 'GOLD', 'color' => '#cca460', 'price' => null, 'sku' => '111-9.3'],
            ['code' => '7.31', 'name' => 'sand gold ash', 'group' => 'SAND GOLD ASH', 'color' => '#88693c', 'price' => null, 'sku' => '111-7.31'],
            ['code' => '9.31', 'name' => 'sand gold ash', 'group' => 'SAND GOLD ASH', 'color' => '#c0a067', 'price' => null, 'sku' => '111-9.31'],
            ['code' => '6.32', 'name' => 'beige gold violet', 'group' => 'BEIGE GOLD VIOLET', 'color' => '#715037', 'price' => null, 'sku' => '111-6.32'],
            ['code' => '8.32', 'name' => 'beige gold violet', 'group' => 'BEIGE GOLD VIOLET', 'color' => '#a77f58', 'price' => null, 'sku' => '111-8.32'],
            ['code' => '6.34', 'name' => 'gold copper', 'group' => 'GOLD COPPER', 'color' => '#805628', 'price' => null, 'sku' => '111-6.34'],
            ['code' => '7.34', 'name' => 'gold copper', 'group' => 'GOLD COPPER', 'color' => '#9b6b34', 'price' => null, 'sku' => '111-7.34'],
            ['code' => '5.4', 'name' => 'copper', 'group' => 'COPPER', 'color' => '#703c19', 'price' => null, 'sku' => '111-5.4'],
            ['code' => '6.4', 'name' => 'copper', 'group' => 'COPPER', 'color' => '#894e22', 'price' => null, 'sku' => '111-6.4'],
            ['code' => '7.4', 'name' => 'copper', 'group' => 'COPPER', 'color' => '#a5632d', 'price' => null, 'sku' => '111-7.4'],
            ['code' => '7.44', 'name' => 'copper', 'group' => 'COPPER', 'color' => '#a5632d', 'price' => null, 'sku' => '111-7.44'],
            ['code' => '8.43', 'name' => 'copper gold', 'group' => 'COPPER GOLD', 'color' => '#bf8642', 'price' => null, 'sku' => '111-8.43'],
            ['code' => '6.41', 'name' => 'tobacco copper ash', 'group' => 'TOBACCO COPPER ASH', 'color' => '#78502c', 'price' => null, 'sku' => '111-6.41'],
            ['code' => '7.41', 'name' => 'tobacco copper ash', 'group' => 'TOBACCO COPPER ASH', 'color' => '#926538', 'price' => null, 'sku' => '111-7.41'],
            ['code' => '8.41', 'name' => 'tobacco copper ash', 'group' => 'TOBACCO COPPER ASH', 'color' => '#b07f4b', 'price' => null, 'sku' => '111-8.41'],
            ['code' => '4.48', 'name' => 'chocolate copper pearl', 'group' => 'CHOCOLATE COPPER PEARL', 'color' => '#492b21', 'price' => null, 'sku' => '111-4.48'],
            ['code' => '5.48', 'name' => 'chocolate copper pearl', 'group' => 'CHOCOLATE COPPER PEARL', 'color' => '#5e3928', 'price' => null, 'sku' => '111-5.48'],
            ['code' => '6.48', 'name' => 'chocolate copper pearl', 'group' => 'CHOCOLATE COPPER PEARL', 'color' => '#764a33', 'price' => null, 'sku' => '111-6.48'],
            ['code' => '7.48', 'name' => 'chocolate copper pearl', 'group' => 'CHOCOLATE COPPER PEARL', 'color' => '#905f40', 'price' => null, 'sku' => '111-7.48'],
            ['code' => '5.6', 'name' => 'red', 'group' => 'RED', 'color' => '#7a2e23', 'price' => null, 'sku' => '111-5.6'],
            ['code' => '5.66', 'name' => 'red', 'group' => 'RED', 'color' => '#7a2e23', 'price' => null, 'sku' => '111-5.66'],
            ['code' => '6.66', 'name' => 'red', 'group' => 'RED', 'color' => '#953f2e', 'price' => null, 'sku' => '111-6.66'],
            ['code' => '7.66', 'name' => 'red', 'group' => 'RED', 'color' => '#b1533a', 'price' => null, 'sku' => '111-7.66'],
            ['code' => '5.22', 'name' => 'violet', 'group' => 'VIOLET', 'color' => '#5e303f', 'price' => null, 'sku' => '111-5.22'],
            ['code' => '5.25', 'name' => 'sunset whisper', 'group' => 'SUNSET WHISPER', 'color' => '#5a3a3e', 'price' => null, 'sku' => '111-5.25'],
            ['code' => '5.46', 'name' => 'sunset whisper', 'group' => 'SUNSET WHISPER', 'color' => '#6b3a32', 'price' => null, 'sku' => '111-5.46'],
            ['code' => '6.45', 'name' => 'sunset whisper', 'group' => 'SUNSET WHISPER', 'color' => '#7d4436', 'price' => null, 'sku' => '111-6.45'],
            ['code' => '10.68', 'name' => 'sunset whisper', 'group' => 'SUNSET WHISPER', 'color' => '#d8b4a0', 'price' => null, 'sku' => '111-10.68'],
            ['code' => '3.24', 'name' => 'sunset whisper', 'group' => 'SUNSET WHISPER', 'color' => '#3a2c2e', 'price' => null, 'sku' => '111-3.24'],
            ['code' => '8.14', 'name' => 'sunset whisper', 'group' => 'SUNSET WHISPER', 'color' => '#a08a76', 'price' => null, 'sku' => '111-8.14'],
            ['code' => '9.42', 'name' => 'sunset whisper', 'group' => 'SUNSET WHISPER', 'color' => '#c69878', 'price' => null, 'sku' => '111-9.42'],
            ['code' => '11.00', 'name' => 'eleven', 'group' => 'ELEVEN', 'color' => '#e1cbab', 'price' => null, 'sku' => '111-11.00'],
            ['code' => '11.1', 'name' => 'eleven', 'group' => 'ELEVEN', 'color' => '#e1cbab', 'price' => null, 'sku' => '111-11.1'],
            ['code' => '11.02', 'name' => 'eleven', 'group' => 'ELEVEN', 'color' => '#e1cbab', 'price' => null, 'sku' => '111-11.02'],
            ['code' => 'H0', 'name' => 'high lift', 'group' => 'HIGH LIFT', 'color' => '#e6dcc8', 'price' => null, 'sku' => '111-H0'],
            ['code' => 'H1', 'name' => 'high lift', 'group' => 'HIGH LIFT', 'color' => '#ddd6c8', 'price' => null, 'sku' => '111-H1'],
        ];
    }

    private function earth(): array
    {
        return [
            ['code' => '1.0', 'name' => 'morning light', 'group' => 'NATURAL', 'color' => '#1a1512', 'price' => null, 'sku' => '102-1.0'],
            ['code' => '3.0', 'name' => 'morning light', 'group' => 'NATURAL', 'color' => '#30261e', 'price' => null, 'sku' => '102-3.0'],
            ['code' => '4.0', 'name' => 'morning light', 'group' => 'NATURAL', 'color' => '#3e3024', 'price' => null, 'sku' => '102-4.0'],
            ['code' => '5.0', 'name' => 'morning light', 'group' => 'NATURAL', 'color' => '#523e2c', 'price' => null, 'sku' => '102-5.0'],
            ['code' => '6.0', 'name' => 'morning light', 'group' => 'NATURAL', 'color' => '#695037', 'price' => null, 'sku' => '102-6.0'],
            ['code' => '7.0', 'name' => 'morning light', 'group' => 'NATURAL', 'color' => '#826544', 'price' => null, 'sku' => '102-7.0'],
            ['code' => '8.0', 'name' => 'morning light', 'group' => 'NATURAL', 'color' => '#9e7f58', 'price' => null, 'sku' => '102-8.0'],
            ['code' => '9.0', 'name' => 'morning light', 'group' => 'NATURAL', 'color' => '#b99b70', 'price' => null, 'sku' => '102-9.0'],
            ['code' => '10.0', 'name' => 'morning light', 'group' => 'NATURAL', 'color' => '#d0b58c', 'price' => null, 'sku' => '102-10.0'],
            ['code' => '4.1', 'name' => 'dusty clouds', 'group' => 'ASH', 'color' => '#332e29', 'price' => null, 'sku' => '102-4.1'],
            ['code' => '5.1', 'name' => 'dusty clouds', 'group' => 'ASH', 'color' => '#463c31', 'price' => null, 'sku' => '102-5.1'],
            ['code' => '6.1', 'name' => 'dusty clouds', 'group' => 'ASH', 'color' => '#5c4e3d', 'price' => null, 'sku' => '102-6.1'],
            ['code' => '7.1', 'name' => 'dusty clouds', 'group' => 'ASH', 'color' => '#74634a', 'price' => null, 'sku' => '102-7.1'],
            ['code' => '8.1', 'name' => 'dusty clouds', 'group' => 'ASH', 'color' => '#8f7d5f', 'price' => null, 'sku' => '102-8.1'],
            ['code' => '9.1', 'name' => 'dusty clouds', 'group' => 'ASH', 'color' => '#a99977', 'price' => null, 'sku' => '102-9.1'],
            ['code' => '10.1', 'name' => 'dusty clouds', 'group' => 'ASH', 'color' => '#beb294', 'price' => null, 'sku' => '102-10.1'],
            ['code' => '4.77', 'name' => 'wintry breeze', 'group' => 'MATT', 'color' => '#31331e', 'price' => null, 'sku' => '102-4.77'],
            ['code' => '5.77', 'name' => 'wintry breeze', 'group' => 'MATT', 'color' => '#444225', 'price' => null, 'sku' => '102-5.77'],
            ['code' => '6.77', 'name' => 'wintry breeze', 'group' => 'MATT', 'color' => '#5a542f', 'price' => null, 'sku' => '102-6.77'],
            ['code' => '7.77', 'name' => 'wintry breeze', 'group' => 'MATT', 'color' => '#72693c', 'price' => null, 'sku' => '102-7.77'],
            ['code' => '8.77', 'name' => 'wintry breeze', 'group' => 'MATT', 'color' => '#8c834f', 'price' => null, 'sku' => '102-8.77'],
            ['code' => '9.77', 'name' => 'wintry breeze', 'group' => 'MATT', 'color' => '#a6a067', 'price' => null, 'sku' => '102-9.77'],
            ['code' => '10.77', 'name' => 'wintry breeze', 'group' => 'MATT', 'color' => '#bcba82', 'price' => null, 'sku' => '102-10.77'],
            ['code' => '4.21', 'name' => 'wild roots', 'group' => 'COOL CHOCOLATE', 'color' => '#392a24', 'price' => null, 'sku' => '102-4.21'],
            ['code' => '5.21', 'name' => 'wild roots', 'group' => 'COOL CHOCOLATE', 'color' => '#4d372c', 'price' => null, 'sku' => '102-5.21'],
            ['code' => '6.21', 'name' => 'wild roots', 'group' => 'COOL CHOCOLATE', 'color' => '#634837', 'price' => null, 'sku' => '102-6.21'],
            ['code' => '7.21', 'name' => 'wild roots', 'group' => 'COOL CHOCOLATE', 'color' => '#7c5d44', 'price' => null, 'sku' => '102-7.21'],
            ['code' => '4.02', 'name' => 'twilight blossom', 'group' => 'IRISÉ', 'color' => '#432b2a', 'price' => null, 'sku' => '102-4.02'],
            ['code' => '5.02', 'name' => 'twilight blossom', 'group' => 'IRISÉ', 'color' => '#573933', 'price' => null, 'sku' => '102-5.02'],
            ['code' => '6.02', 'name' => 'twilight blossom', 'group' => 'IRISÉ', 'color' => '#6f4a3f', 'price' => null, 'sku' => '102-6.02'],
            ['code' => '7.02', 'name' => 'twilight blossom', 'group' => 'IRISÉ', 'color' => '#885f4c', 'price' => null, 'sku' => '102-7.02'],
            ['code' => '8.02', 'name' => 'twilight blossom', 'group' => 'IRISÉ', 'color' => '#a57861', 'price' => null, 'sku' => '102-8.02'],
            ['code' => '9.02', 'name' => 'twilight blossom', 'group' => 'IRISÉ', 'color' => '#c09479', 'price' => null, 'sku' => '102-9.02'],
            ['code' => '10.02', 'name' => 'twilight blossom', 'group' => 'IRISÉ', 'color' => '#d8ae96', 'price' => null, 'sku' => '102-10.02'],
            ['code' => '5.32', 'name' => 'dandelion blush', 'group' => 'ROSÉ', 'color' => '#623730', 'price' => null, 'sku' => '102-5.32'],
            ['code' => '6.32', 'name' => 'dandelion blush', 'group' => 'ROSÉ', 'color' => '#7a483b', 'price' => null, 'sku' => '102-6.32'],
            ['code' => '7.32', 'name' => 'dandelion blush', 'group' => 'ROSÉ', 'color' => '#945d48', 'price' => null, 'sku' => '102-7.32'],
            ['code' => '8.32', 'name' => 'dandelion blush', 'group' => 'ROSÉ', 'color' => '#b2765c', 'price' => null, 'sku' => '102-8.32'],
            ['code' => '9.32', 'name' => 'dandelion blush', 'group' => 'ROSÉ', 'color' => '#ce9275', 'price' => null, 'sku' => '102-9.32'],
            ['code' => '10.32', 'name' => 'dandelion blush', 'group' => 'ROSÉ', 'color' => '#e6ab91', 'price' => null, 'sku' => '102-10.32'],
            ['code' => '5.22', 'name' => 'berries underbrush', 'group' => 'VIOLET', 'color' => '#5e303f', 'price' => null, 'sku' => '102-5.22'],
            ['code' => '7.03', 'name' => 'sand storm', 'group' => 'WARM BEIGE', 'color' => '#8c693e', 'price' => null, 'sku' => '102-7.03'],
            ['code' => '8.03', 'name' => 'sand storm', 'group' => 'WARM BEIGE', 'color' => '#a98351', 'price' => null, 'sku' => '102-8.03'],
            ['code' => '9.03', 'name' => 'sand storm', 'group' => 'WARM BEIGE', 'color' => '#c5a069', 'price' => null, 'sku' => '102-9.03'],
            ['code' => '5.3', 'name' => 'seaside sunrise', 'group' => 'GOLDEN SAND', 'color' => '#5e4522', 'price' => null, 'sku' => '102-5.3'],
            ['code' => '7.3', 'name' => 'seaside sunrise', 'group' => 'GOLDEN SAND', 'color' => '#906d38', 'price' => null, 'sku' => '102-7.3'],
            ['code' => '9.3', 'name' => 'seaside sunrise', 'group' => 'GOLDEN SAND', 'color' => '#c9a462', 'price' => null, 'sku' => '102-9.3'],
            ['code' => '7.42', 'name' => 'amber ray', 'group' => 'COOL COPPER', 'color' => '#996534', 'price' => null, 'sku' => '102-7.42'],
            ['code' => '8.42', 'name' => 'amber ray', 'group' => 'COOL COPPER', 'color' => '#b67f46', 'price' => null, 'sku' => '102-8.42'],
            ['code' => '6.44', 'name' => 'autumn leaves', 'group' => 'BRIGHT COPPER', 'color' => '#8d5220', 'price' => null, 'sku' => '102-6.44'],
            ['code' => '7.44', 'name' => 'autumn leaves', 'group' => 'BRIGHT COPPER', 'color' => '#a9672b', 'price' => null, 'sku' => '102-7.44'],
            ['code' => '8.44', 'name' => 'autumn leaves', 'group' => 'BRIGHT COPPER', 'color' => '#c8813e', 'price' => null, 'sku' => '102-8.44'],
            ['code' => '5.66', 'name' => 'purple garden', 'group' => 'RED', 'color' => '#7a2e23', 'price' => null, 'sku' => '102-5.66'],
            ['code' => '6.66', 'name' => 'purple garden', 'group' => 'RED', 'color' => '#953f2e', 'price' => null, 'sku' => '102-6.66'],
            ['code' => '4.62', 'name' => 'nordic sunset', 'group' => 'MAHOGANY', 'color' => '#502327', 'price' => null, 'sku' => '102-4.62'],
            ['code' => '7.63', 'name' => 'coral reef', 'group' => 'CORAL REEF', 'color' => '#8d4438', 'price' => null, 'sku' => '102-7.63'],
            ['code' => '8.36', 'name' => 'coral reef', 'group' => 'CORAL REEF', 'color' => '#c99579', 'price' => null, 'sku' => '102-8.36'],
            ['code' => '10.16', 'name' => 'coral reef', 'group' => 'CORAL REEF', 'color' => '#dcb4a2', 'price' => null, 'sku' => '102-10.16'],
            ['code' => '4.86', 'name' => 'coral reef', 'group' => 'CORAL REEF', 'color' => '#2c2830', 'price' => null, 'sku' => '102-4.86'],
            ['code' => '6.47', 'name' => 'coral reef', 'group' => 'CORAL REEF', 'color' => '#6d5f3e', 'price' => null, 'sku' => '102-6.47'],
            ['code' => '3.87', 'name' => 'norwegian fjords', 'group' => 'NORWEGIAN FJORDS', 'color' => '#2b2624', 'price' => null, 'sku' => '102-3.87'],
            ['code' => '10.12', 'name' => 'norwegian fjords', 'group' => 'NORWEGIAN FJORDS', 'color' => '#c3bfb9', 'price' => null, 'sku' => '102-10.12'],
            ['code' => '4.24', 'name' => 'norwegian fjords', 'group' => 'NORWEGIAN FJORDS', 'color' => '#33382c', 'price' => null, 'sku' => '102-4.24'],
            ['code' => '6.37', 'name' => 'norwegian fjords', 'group' => 'NORWEGIAN FJORDS', 'color' => '#a8a29a', 'price' => null, 'sku' => '102-6.37'],
            ['code' => '11.01', 'name' => 'frost moonlight', 'group' => 'HIGHLIFT', 'color' => '#cfc2b2', 'price' => null, 'sku' => '102-11.01'],
            ['code' => '11.32', 'name' => 'frost moonlight', 'group' => 'HIGHLIFT', 'color' => '#d3bfa2', 'price' => null, 'sku' => '102-11.32'],
            ['code' => '11.21', 'name' => 'frost moonlight', 'group' => 'HIGHLIFT', 'color' => '#bfb0a8', 'price' => null, 'sku' => '102-11.21'],
            ['code' => '12.1', 'name' => 'chilled daylight', 'group' => 'SUPER HIGHLIFT', 'color' => '#8d8f8a', 'price' => null, 'sku' => '102-12.1'],
            ['code' => '12.02', 'name' => 'chilled daylight', 'group' => 'SUPER HIGHLIFT', 'color' => '#a9a2c2', 'price' => null, 'sku' => '102-12.02'],
            ['code' => '12.21', 'name' => 'chilled daylight', 'group' => 'SUPER HIGHLIFT', 'color' => '#9a8c80', 'price' => null, 'sku' => '102-12.21'],
            ['code' => 'PINK', 'name' => 'boreal bloom', 'group' => 'CREATIVE TONES', 'color' => '#b0526a', 'price' => null, 'sku' => '102-PINK'],
            ['code' => 'BLUE', 'name' => 'boreal bloom', 'group' => 'CREATIVE TONES', 'color' => '#3a4180', 'price' => null, 'sku' => '102-BLUE'],
            ['code' => 'EMERALD', 'name' => 'boreal bloom', 'group' => 'CREATIVE TONES', 'color' => '#1f7a5a', 'price' => null, 'sku' => '102-EMERALD'],
            ['code' => 'STEEL BLUE', 'name' => 'boreal bloom', 'group' => 'CREATIVE TONES', 'color' => '#bfc2d4', 'price' => null, 'sku' => '102-STEEL BLUE'],
            ['code' => 'GRAPHITE', 'name' => 'boreal bloom', 'group' => 'CREATIVE TONES', 'color' => '#9a9a94', 'price' => null, 'sku' => '102-GRAPHITE'],
            ['code' => 'SILVER', 'name' => 'boreal bloom', 'group' => 'CREATIVE TONES', 'color' => '#c4c6c8', 'price' => null, 'sku' => '102-SILVER'],
            ['code' => 'CLEAR', 'name' => 'boreal bloom', 'group' => 'CREATIVE TONES', 'color' => '#e9ece9', 'price' => null, 'sku' => '102-CLEAR'],
        ];
    }

    private function powderInfusion(): array
    {
        return [
            ['code' => '00', 'name' => 'giglio', 'group' => 'TONER', 'color' => '#e8e2d6', 'price' => null, 'sku' => '110-00'],
            ['code' => '32', 'name' => 'grano', 'group' => 'TONER', 'color' => '#d9b98c', 'price' => null, 'sku' => '110-32'],
            ['code' => '11', 'name' => 'agave', 'group' => 'TONER', 'color' => '#aab4ac', 'price' => null, 'sku' => '110-11'],
            ['code' => '23', 'name' => 'cannella', 'group' => 'TONER', 'color' => '#a3705a', 'price' => null, 'sku' => '110-23'],
            ['code' => '44', 'name' => 'clivia', 'group' => 'TONER', 'color' => '#c26a3c', 'price' => null, 'sku' => '110-44'],
            ['code' => '66', 'name' => 'papavero', 'group' => 'TONER', 'color' => '#a3403a', 'price' => null, 'sku' => '110-66'],
            ['code' => '65', 'name' => 'ossalide', 'group' => 'TONER', 'color' => '#8d4458', 'price' => null, 'sku' => '110-65'],
            ['code' => '5', 'name' => 'dalia', 'group' => 'TONER', 'color' => '#7c4a52', 'price' => null, 'sku' => '110-5'],
            ['code' => '53', 'name' => 'sandalo', 'group' => 'TONER', 'color' => '#8a5a40', 'price' => null, 'sku' => '110-53'],
        ];
    }

    public function down(): void
    {
        // Data migration — nothing to reverse.
    }
};
