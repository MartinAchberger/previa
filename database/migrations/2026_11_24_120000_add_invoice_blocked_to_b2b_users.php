<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('b2b_users', function (Blueprint $table) {
            // Admin switch: salon may not pay by invoice (e.g. unpaid invoices).
            $table->boolean('invoice_blocked')->default(false)->after('discount_pct');
        });
    }

    public function down(): void
    {
        Schema::table('b2b_users', function (Blueprint $table) {
            $table->dropColumn('invoice_blocked');
        });
    }
};
