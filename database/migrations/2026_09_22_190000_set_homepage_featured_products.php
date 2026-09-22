<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Homepage "recommended" picks requested by the client on 22. 9. 2026.
 * Data-only: flags exactly these four products as featured, clears the rest.
 * Matched by name + volume so it is safe to re-run and independent of IDs.
 */
return new class extends Migration
{
    private const PICKS = [
        ['Reconstruct Biphasic Leave-in Filler Conditioner', '200 ml'],
        ['Regrowth Shampoo', '350 ml'],
        ['Reconstruct Serum', '50 ml'],
        ['Extra Firm Hairspray', '400 ml'],
    ];

    public function up(): void
    {
        DB::table('products')->update(['featured' => false]);
        foreach (self::PICKS as [$name, $volume]) {
            DB::table('products')->where('name', $name)->where('volume', $volume)->update(['featured' => true]);
        }
    }

    public function down(): void
    {
        // Intentionally left empty: previous picks are not worth restoring.
    }
};
