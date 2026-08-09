<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data migration: default every product's warehouse SKU to its catalog code.
     * Foxlog pairs SKUs on their side (unknown SKUs are auto-created from our
     * outbound orders, which already fall back to the code), but the inbound
     * stock webhook matches on products.sku — so the column must be filled for
     * stock levels to sync. Admin-set SKUs are left untouched.
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
