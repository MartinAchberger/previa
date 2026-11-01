<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Zľavové kódy spravované v admine (názov, kód, zľava v %).
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 40)->unique(); // stored uppercase
            $table->unsignedTinyInteger('percent');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            // Code used at checkout; its percent lands in discount_pct.
            $table->string('discount_code', 40)->nullable()->after('discount_pct')->index();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['discount_code']);
            $table->dropColumn('discount_code');
        });
        Schema::dropIfExists('discount_codes');
    }
};
