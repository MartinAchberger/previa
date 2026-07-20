<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('proforma_sf_id')->nullable()->after('notes');
            $table->string('proforma_number')->nullable()->after('proforma_sf_id');
            $table->string('proforma_pdf_path')->nullable()->after('proforma_number');

            $table->unsignedBigInteger('invoice_sf_id')->nullable()->after('proforma_pdf_path');
            $table->string('invoice_number')->nullable()->after('invoice_sf_id');
            $table->string('invoice_pdf_path')->nullable()->after('invoice_number');

            $table->string('invoice_status', 16)->default('none')->after('invoice_pdf_path');
            $table->text('invoice_error')->nullable()->after('invoice_status');
            $table->timestamp('invoiced_at')->nullable()->after('invoice_error');
            $table->timestamp('invoice_sent_at')->nullable()->after('invoiced_at');

            $table->index('invoice_status');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['invoice_status']);
            $table->dropColumn([
                'proforma_sf_id', 'proforma_number', 'proforma_pdf_path',
                'invoice_sf_id', 'invoice_number', 'invoice_pdf_path',
                'invoice_status', 'invoice_error',
                'invoiced_at', 'invoice_sent_at',
            ]);
        });
    }
};
