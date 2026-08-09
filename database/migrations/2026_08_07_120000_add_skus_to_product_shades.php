<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data migration: every single shade is its own warehouse item, so each
     * shade entry gets a persistent `sku` ("<product sku>-<shade code>",
     * e.g. "30-7.32"). Idempotent — shades that already carry a sku (e.g.
     * edited in the admin after Foxlog returns their own codes) are kept.
     */
    public function up(): void
    {
        $products = DB::table('products')
            ->whereNotNull('shades')
            ->get(['id', 'code', 'sku', 'shades']);

        foreach ($products as $product) {
            $shades = json_decode($product->shades, true);
            if (!is_array($shades) || $shades === []) {
                continue;
            }

            $prefix = $product->sku ?: $product->code;
            $changed = false;
            foreach ($shades as &$shade) {
                if (!is_array($shade) || empty($shade['code']) || !empty($shade['sku'])) {
                    continue;
                }
                $shade['sku'] = $prefix . '-' . $shade['code'];
                $changed = true;
            }
            unset($shade);

            if ($changed) {
                DB::table('products')->where('id', $product->id)
                    ->update(['shades' => json_encode($shades, JSON_UNESCAPED_UNICODE)]);
            }
        }
    }

    public function down(): void
    {
        // Data migration — nothing to reverse.
    }
};
