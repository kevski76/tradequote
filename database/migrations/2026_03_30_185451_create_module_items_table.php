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
        Schema::create('module_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id')->default(0);
            $table->string('key')->nullable(); // panels, posts, boards, rails
            $table->string('name')->nullable();
            $table->string('type')->nullable(); // material | labour
            $table->string('calculation')->nullable(); // fixed | per_unit | area | length;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_items');
    }
};
