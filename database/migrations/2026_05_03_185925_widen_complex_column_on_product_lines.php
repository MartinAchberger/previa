<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_lines', function (Blueprint $table) {
            $table->string('complex', 80)->nullable()->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('complex', 80)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('product_lines', function (Blueprint $table) {
            $table->string('complex', 16)->nullable()->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('complex', 16)->nullable()->change();
        });
    }
};
