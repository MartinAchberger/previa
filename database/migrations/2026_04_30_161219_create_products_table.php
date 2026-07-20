<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('line_id')->nullable()->constrained('product_lines')->nullOnDelete();
            $table->string('code', 8);                  // 01, 02, ... (n° 01)
            $table->string('slug')->unique();           // restructure-shampoo
            $table->string('name');                     // Restructure Shampoo
            $table->string('subtitle')->nullable();     // "Reštrukturalizačný šampón"
            $table->string('line_label');               // "Pre oslabené vlasy"
            $table->string('complex', 16)->nullable(); // HX-94
            $table->string('volume', 32)->nullable();   // "250 ml"
            $table->decimal('price', 10, 2);            // 42.00
            $table->string('badge', 32)->nullable();    // "Nové", "Bestseller", "−15 %"
            $table->string('kind', 16)->default('tall');// tall, jar, sachet
            $table->string('tone', 16)->default('#0b0b0a');
            $table->string('cap', 16)->nullable();
            $table->longText('description')->nullable();
            $table->json('ingredients')->nullable();    // [{n:1, name:"...", small:"...", pct:"1.8 %"}, ...]
            $table->json('results')->nullable();        // [{label, small, value, fill}, ...]
            $table->json('compatibility')->nullable();  // [{ok:true, name, small, value}, ...]
            $table->json('protocol')->nullable();       // [{step, title, desc}, ...]
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('published')->default(true);
            $table->timestamps();
            $table->index(['published', 'sort_order']);
            $table->index('line_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
