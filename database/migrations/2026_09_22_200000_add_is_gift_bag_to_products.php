<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Offered as "Darčekové balenie" in the checkout dropdown (admin checkbox).
            $table->boolean('is_gift_bag')->default(false)->after('b2b_only');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('is_gift_bag');
        });
    }
};
