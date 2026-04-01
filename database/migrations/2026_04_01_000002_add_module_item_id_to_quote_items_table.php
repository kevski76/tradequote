<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            if (! Schema::hasColumn('quote_items', 'module_item_id')) {
                $table->foreignId('module_item_id')
                    ->nullable()
                    ->after('quote_id')
                    ->constrained('module_items')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('quote_items', function (Blueprint $table) {
            if (Schema::hasColumn('quote_items', 'module_item_id')) {
                $table->dropConstrainedForeignId('module_item_id');
            }
        });
    }
};
