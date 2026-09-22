<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Blog was dropped from the product (client decision, 9/2026). */
    public function up(): void
    {
        Schema::dropIfExists('blog_articles');
    }

    public function down(): void
    {
        // Table is gone for good — recreate via the original migration if ever needed.
    }
};
