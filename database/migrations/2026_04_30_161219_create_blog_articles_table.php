<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_articles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category', 64);             // Veda, Diagnostika, Filozofia, ...
            $table->string('read_time', 16)->nullable();// "8 min"
            $table->string('cover_url')->nullable();    // unsplash URL or local path
            $table->text('excerpt');
            $table->longText('body')->nullable();
            $table->boolean('featured')->default(false);
            $table->date('published_at')->nullable();
            $table->boolean('published')->default(true);
            $table->timestamps();
            $table->index(['published', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_articles');
    }
};
