<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PDP content per client feedback (7. 9. 2026): "Aktívne zložky / Klinický test /
 * Kompatibilita / Protokol" replaced by "Pre koho je / Čo očakávať / Použitie".
 * Also: products can be listed in extra collections, and a homepage "featured" flag.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('extra_line_ids')->nullable()->after('line_id');
            $table->json('for_whom')->nullable()->after('description');
            $table->json('expect')->nullable()->after('for_whom');
            $table->text('usage')->nullable()->after('expect');
            $table->boolean('featured')->default(false)->after('published');
            $table->dropColumn(['ingredients', 'results', 'compatibility', 'protocol']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['extra_line_ids', 'for_whom', 'expect', 'usage', 'featured']);
            $table->json('ingredients')->nullable();
            $table->json('results')->nullable();
            $table->json('compatibility')->nullable();
            $table->json('protocol')->nullable();
        });
    }
};
