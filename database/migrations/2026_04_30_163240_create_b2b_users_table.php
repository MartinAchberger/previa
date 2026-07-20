<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('b2b_users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('contact_name');
            $table->string('salon_name');
            $table->string('phone')->nullable();
            $table->string('ico', 16)->nullable();
            $table->string('vat_id', 24)->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zip', 16)->nullable();
            $table->string('country', 2)->default('SK');
            $table->unsignedTinyInteger('discount_pct')->default(0);
            $table->string('tier', 32)->default('Tier 01');
            $table->string('status', 16)->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('b2b_users');
    }
};
