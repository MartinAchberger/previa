<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Warehouse SKU used by Foxlog to match products (stock + order lines).
            $table->string('sku')->nullable()->index()->after('code');
            // Stock level synced from the fulfilment warehouse. null = not tracked.
            $table->integer('stock')->nullable()->after('sku');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_number')->nullable()->after('shipping_method');
            $table->string('shipping_carrier')->nullable()->after('tracking_number'); // Foxlog shipping_id
            $table->string('pickup_point_id')->nullable()->after('shipping_carrier');  // branch/pickup point
            $table->string('foxlog_status')->nullable()->after('pickup_point_id');      // last status from Foxlog
            $table->timestamp('foxlog_sent_at')->nullable()->after('foxlog_status');     // when order was pushed
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sku', 'stock']);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tracking_number', 'shipping_carrier', 'pickup_point_id', 'foxlog_status', 'foxlog_sent_at']);
        });
    }
};
