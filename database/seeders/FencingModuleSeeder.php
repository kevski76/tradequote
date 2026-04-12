<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the fencing module_items and organisation_prices for all existing organisations.
 *
 * Fencing item keys and their quantity formulas (applied in QuoteCalculator):
 *
 *   posts         → ceil(length / 1.8) + 1     (concrete or timber posts)
 *   panels        → floor(length / 1.8)         (1.8m fence panels)
 *   gravel_boards → floor(length / 1.8)         (one gravel board per bay)
 *   rails         → floor(length / 1.8) × 2     (top + bottom arris rails)
 *   labour        → length metres               (per-metre install labour)
 *
 * unit_price values here are illustrative defaults only.
 * Each organisation should update their own prices in the admin panel.
 */
class FencingModuleSeeder extends Seeder
{
    private const HEIGHT_KEYS = ['1.5', '1.8', '2.0'];

    public function run(): void
    {
        // ── 1. Ensure the fencing module row exists ──────────────────────────
        $moduleId = DB::table('modules')->where('slug', 'fencing')->value('id');

        if (! $moduleId) {
            $moduleId = DB::table('modules')->insertGetId([
                'name'       => 'Fencing',
                'slug'       => 'fencing',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ── 2. Seed module_items ─────────────────────────────────────────────
        //
        // calculation values:
        //   'length'   → quantity driven by job length via key-based formula
        //   'direct'   → quantity entered directly (no formula)
        //
        $items = [
            [
                'key'         => 'posts',
                'name'        => 'Fence Posts',
                'type'        => 'material',
                'calculation' => 'length',
                'formula_key' => 'posts',
                'is_optional' => false,
            ],
            [
                'key'         => 'panels',
                'name'        => 'Fence Panels',
                'type'        => 'material',
                'calculation' => 'length',
                'formula_key' => 'panels',
                'is_optional' => false,
            ],
            [
                'key'         => 'boards',
                'name'        => 'Fencing Boards',
                'type'        => 'material',
                'calculation' => 'length',
                'formula_key' => 'boards',
                'is_optional' => false,
            ],
            [
                'key'         => 'gravel_boards',
                'name'        => 'Gravel Boards',
                'type'        => 'material',
                'calculation' => 'length',
                'formula_key' => 'gravel_boards',
                'is_optional' => true,
            ],
            [
                'key'         => 'rails',
                'name'        => 'Arris Rails',
                'type'        => 'material',
                'calculation' => 'length',
                'formula_key' => 'rails',
                'is_optional' => true,
            ],
            [
                'key'         => 'labour',
                'name'        => 'Installation Labour',
                'type'        => 'labour',
                'calculation' => 'length',
                'formula_key' => 'labour',
                'is_optional' => false,
            ],
            [
                'key'         => 'gate',
                'name'        => 'Fence Gate',
                'type'        => 'material',
                'calculation' => 'length',
                'formula_key' => 'gate',
                'is_optional' => true,
            ],
        ];

        $heightPriceMeta = [
            'posts' => ['prices' => ['1.5' => 7.00, '1.8' => 8.00, '2.0' => 9.00]],
            'panels' => ['prices' => ['1.5' => 65.00, '1.8' => 75.00, '2.0' => 85.00]],
            'boards' => ['prices' => ['1.5' => 18.00, '1.8' => 22.00, '2.0' => 26.00]],
            'rails' => ['prices' => ['1.5' => 10.00, '1.8' => 12.00, '2.0' => 14.00]],
            'gravel_boards' => ['prices' => ['1.5' => 13.00, '1.8' => 15.00, '2.0' => 17.00]],
        ];

        // Insert missing items and update existing item definitions/meta.
        $existingItems = DB::table('module_items')
            ->where('module_id', $moduleId)
            ->get()
            ->keyBy('key');

        $moduleItemIds = []; // key → id

        foreach ($items as $item) {
            $existing = $existingItems->get($item['key']);

            if ($existing) {
                $existingMeta = [];
                if (is_string($existing->meta) && $existing->meta !== '') {
                    $decoded = json_decode($existing->meta, true);
                    if (is_array($decoded)) {
                        $existingMeta = $decoded;
                    }
                } elseif (is_array($existing->meta)) {
                    $existingMeta = $existing->meta;
                }

                $mergedMeta = $existingMeta;
                if (isset($heightPriceMeta[$item['key']])) {
                    $mergedMeta = array_merge($existingMeta, $heightPriceMeta[$item['key']]);
                }

                DB::table('module_items')
                    ->where('id', (int) $existing->id)
                    ->update([
                        'name' => $item['name'],
                        'type' => $item['type'],
                        'calculation' => $item['calculation'],
                        'formula_key' => $item['formula_key'],
                        'is_optional' => $item['is_optional'],
                        'meta' => $mergedMeta,
                        'updated_at' => now(),
                    ]);

                $id = (int) $existing->id;
            } else {
                $meta = $heightPriceMeta[$item['key']] ?? null;

                $id = DB::table('module_items')->insertGetId(array_merge(
                    ['module_id' => $moduleId],
                    $item,
                    ['meta' => $meta, 'created_at' => now(), 'updated_at' => now()]
                ));
            }

            $moduleItemIds[$item['key']] = $id;
        }

        $this->command->info('Fencing module items seeded: '.implode(', ', array_keys($moduleItemIds)));

        // ── 3. Seed organisation_prices for every organisation ───────────────
        //
        // Default sell prices stored in pence (column type: integer, unit: pence).
        // QuoteFormBuilder::resolveUnitPrice() divides by 100 to return pounds.
        // Organisations can override these in their settings.
        $defaultPrices = [
            'posts'         => 1800,  // £18.00 per post
            'panels'        => 3500,  // £35.00 per panel
            'boards'        => 2200,  // £22.00 per board bundle/unit
            'gravel_boards' => 1200,  // £12.00 per gravel board
            'rails'         =>  600,  //  £6.00 per arris rail
            'labour'        => 3500,  // £35.00 per metre
            'gate'          => 2200,  // £22.00 per gate default
        ];

        $organisations = DB::table('organisations')->pluck('id');

        foreach ($organisations as $orgId) {
            foreach ($moduleItemIds as $key => $moduleItemId) {
                $exists = DB::table('organisation_prices')
                    ->where('organisation_id', $orgId)
                    ->where('module_item_id', $moduleItemId)
                    ->exists();

                if (! $exists) {
                    $sellPricesByHeight = null;
                    $costPricesByHeight = null;

                    if (isset($heightPriceMeta[$key]['prices']) && is_array($heightPriceMeta[$key]['prices'])) {
                        $sellPricesByHeight = [];
                        $costPricesByHeight = [];

                        foreach ($heightPriceMeta[$key]['prices'] as $height => $price) {
                            $sellPence = (int) round((float) $price * 100);
                            $sellPricesByHeight[(string) $height] = $sellPence;
                            $costPricesByHeight[(string) $height] = (int) round($sellPence * 0.7);
                        }

                        foreach (self::HEIGHT_KEYS as $height) {
                            if (! isset($sellPricesByHeight[$height])) {
                                $sellPricesByHeight[$height] = $defaultPrices[$key];
                            }

                            if (! isset($costPricesByHeight[$height])) {
                                $costPricesByHeight[$height] = (int) round($defaultPrices[$key] * 0.7);
                            }
                        }
                    }

                    DB::table('organisation_prices')->insert([
                        'organisation_id' => $orgId,
                        'module_item_id'  => $moduleItemId,
                        'cost_price'      => (int) round($defaultPrices[$key] * 0.7), // ~30% gross margin
                        'pricing_type'    => 'fixed',
                        'markup_percent'  => 0,
                        'sell_price'      => $defaultPrices[$key],
                        'sell_prices_by_height' => $sellPricesByHeight !== null ? json_encode($sellPricesByHeight, JSON_THROW_ON_ERROR) : null,
                        'cost_prices_by_height' => $costPricesByHeight !== null ? json_encode($costPricesByHeight, JSON_THROW_ON_ERROR) : null,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            }
        }

        $this->command->info('Organisation prices seeded for '.count($organisations).' organisation(s).');
    }
}
