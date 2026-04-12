<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->nullable()->constrained('quotes')->nullOnDelete();
            $table->foreignId('organisation_id')->nullable()->constrained('organisations')->nullOnDelete();
            $table->string('quote_uuid', 64)->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_submissions');
    }
};
