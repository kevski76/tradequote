<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('variant_key');
            $table->string('job_name')->nullable()->after('customer_name');
            $table->string('status')->default('draft')->after('job_name');
            $table->integer('subtotal_price')->default(0)->after('materials_total');
            $table->decimal('vat_rate', 5, 2)->default(0)->after('subtotal_price');
            $table->integer('vat_total')->default(0)->after('vat_rate');
            $table->text('payment_terms')->nullable()->after('vat_total');
            $table->json('calculation_data')->nullable()->after('payment_terms');
            $table->string('pdf_path')->nullable()->after('calculation_data');
            $table->timestamp('pdf_generated_at')->nullable()->after('pdf_path');

            $table->index(['created_by', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropIndex('quotes_created_by_status_index');
            $table->dropColumn([
                'customer_name',
                'job_name',
                'status',
                'subtotal_price',
                'vat_rate',
                'vat_total',
                'payment_terms',
                'calculation_data',
                'pdf_path',
                'pdf_generated_at',
            ]);
        });
    }
};
