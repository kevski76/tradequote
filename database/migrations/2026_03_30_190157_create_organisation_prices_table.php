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
        Schema::create('organisation_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_id')->constrained()->cascadeOnDelete()->default(0);
            $table->foreignId('module_item_id')->constrained()->cascadeOnDelete()->default(0);
            $table->integer('cost_price')->default(0); // in pence
            $table->string('pricing_type')->nullable(); // fixed | markup
            $table->decimal('markup_percent', 5, 2)->default(0);
            $table->integer('sell_price')->default(0); // in pence
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organisation_prices');
    }
};
