<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_lines', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8);                 // 01, 02, ...
            $table->string('slug')->unique();          // restructure, hydra, ...
            $table->string('name');                    // "Restructure"
            $table->string('eyebrow');                 // "Pre oslabené vlasy"
            $table->text('description')->nullable();
            $table->string('complex', 16)->nullable(); // HX-94
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('published')->default(true);
            $table->timestamps();
            $table->index(['published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_lines');
    }
};
