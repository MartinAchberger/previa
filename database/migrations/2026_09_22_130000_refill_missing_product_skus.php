<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data migration: some products on production ended up with an empty SKU
     * (admin saves before the SKU field existed). Foxlog stock sync matches on
     * products.sku, so fill it from the product code again. Idempotent — only
     * touches empty SKUs, admin-set values stay.
     */
    public function up(): void
    {
        DB::table('products')
            ->where(fn ($q) => $q->whereNull('sku')->orWhere('sku', ''))
            ->whereNotNull('code')
            ->update(['sku' => DB::raw('code')]);
    }

    public function down(): void
    {
        // Data migration — nothing to reverse.
    }
};
