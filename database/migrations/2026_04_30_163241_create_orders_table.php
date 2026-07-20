<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('b2b_user_id')->nullable()->constrained('b2b_users')->nullOnDelete();
            $table->string('order_type', 8)->default('b2c');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->string('company_name')->nullable();
            $table->string('ico', 16)->nullable();
            $table->string('vat_id', 24)->nullable();
            $table->string('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_zip', 16);
            $table->string('shipping_country', 2)->default('SK');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('shipping', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->unsignedTinyInteger('discount_pct')->default(0);
            $table->string('payment_method', 16)->default('cod');
            $table->string('payment_status', 16)->default('unpaid');
            $table->string('shipping_method', 16)->default('courier');
            $table->string('status', 16)->default('new');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('order_type');
            $table->index('b2b_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
