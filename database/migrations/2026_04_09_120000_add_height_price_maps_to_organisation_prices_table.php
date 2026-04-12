<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const HEIGHT_KEYS = ['1.5', '1.8', '2.0'];

    public function up(): void
    {
        Schema::table('organisation_prices', function (Blueprint $table) {
            $table->json('sell_prices_by_height')->nullable()->after('sell_price');
            $table->json('cost_prices_by_height')->nullable()->after('sell_prices_by_height');
        });

        $rows = DB::table('organisation_prices as organisation_prices')
            ->join('module_items as module_items', 'module_items.id', '=', 'organisation_prices.module_item_id')
            ->join('modules as modules', 'modules.id', '=', 'module_items.module_id')
            ->where('modules.slug', 'fencing')
            ->where('module_items.type', 'material')
            ->whereNot('module_items.key', 'gate')
            ->select([
                'organisation_prices.id',
                'organisation_prices.cost_price',
                'organisation_prices.sell_price',
                'module_items.meta',
            ])
            ->get();

        foreach ($rows as $row) {
            $meta = [];

            if (is_string($row->meta) && $row->meta !== '') {
                $decoded = json_decode($row->meta, true);
                if (is_array($decoded)) {
                    $meta = $decoded;
                }
            } elseif (is_array($row->meta)) {
                $meta = $row->meta;
            }

            $sellPrices = [];
            $metaPrices = is_array($meta['prices'] ?? null) ? $meta['prices'] : [];

            foreach (self::HEIGHT_KEYS as $height) {
                if (isset($metaPrices[$height]) && is_numeric($metaPrices[$height])) {
                    $sellPrices[$height] = (int) round((float) $metaPrices[$height] * 100);
                    continue;
                }

                if ((int) $row->sell_price > 0) {
                    $sellPrices[$height] = (int) $row->sell_price;
                }
            }

            $costPrices = [];
            foreach (self::HEIGHT_KEYS as $height) {
                if ((int) $row->cost_price > 0) {
                    $costPrices[$height] = (int) $row->cost_price;
                }
            }

            DB::table('organisation_prices')
                ->where('id', (int) $row->id)
                ->update([
                    'sell_prices_by_height' => $sellPrices !== [] ? json_encode($sellPrices, JSON_THROW_ON_ERROR) : null,
                    'cost_prices_by_height' => $costPrices !== [] ? json_encode($costPrices, JSON_THROW_ON_ERROR) : null,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('organisation_prices', function (Blueprint $table) {
            $table->dropColumn(['sell_prices_by_height', 'cost_prices_by_height']);
        });
    }
};