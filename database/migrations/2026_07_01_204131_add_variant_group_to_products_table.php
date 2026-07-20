<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Products sharing a variant_group are size variants of one product
            // (e.g. 250 ml + 1 l) — rendered as a size switcher on the PDP.
            $table->string('variant_group')->nullable()->index()->after('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['variant_group']);
            $table->dropColumn('variant_group');
        });
    }
};
