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
        Schema::table('module_items', function (Blueprint $table) {
            // formula_key drives quantity resolution (e.g. posts_needed, boards_needed, labour_per_metre).
            // Nullable so existing rows without a formula key fall back to the key column.
            if (! Schema::hasColumn('module_items', 'formula_key')) {
                $table->string('formula_key')->nullable()->after('key');
            }

            // is_optional marks items the user can toggle on/off in the quote form.
            if (! Schema::hasColumn('module_items', 'is_optional')) {
                $table->boolean('is_optional')->default(false)->after('formula_key');
            }
        });
    }

    public function down(): void
    {
        Schema::table('module_items', function (Blueprint $table) {
            $table->dropColumnIfExists('formula_key');
            $table->dropColumnIfExists('is_optional');
        });
    }
};
