<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('billing_address')->nullable()->after('shipping_country');
            $table->string('billing_city')->nullable()->after('billing_address');
            $table->string('billing_zip', 16)->nullable()->after('billing_city');
            $table->string('billing_country', 2)->nullable()->after('billing_zip');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['billing_address', 'billing_city', 'billing_zip', 'billing_country']);
        });
    }
};
