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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organisation_id')->default(0); 
            $table->unsignedBigInteger('created_by')->default(0); // user who made quote
            $table->foreignId('module_id')->constrained()->default(0); // panel | closeboard
            $table->string('variant_key')->nullable();
            $table->decimal('length', 8, 2)->default(0);
            $table->string('labour_type')->nullable();
            $table->integer('labour_total')->default(0);
            $table->integer('materials_total')->default(0);
            $table->integer('total_price')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
