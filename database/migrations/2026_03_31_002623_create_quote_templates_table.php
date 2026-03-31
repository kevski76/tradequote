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
        Schema::create('quote_templates', function (Blueprint $table) {
            $table->id();         
            $table->unsignedBigInteger('organisation_id')->default(0);
            $table->unsignedBigInteger('created_by')->default(0); // user_id
            $table->string('name')->nullable();
            $table->unsignedBigInteger('module_id')->default(0);
            $table->string('variant_key')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_templates');
    }
};
