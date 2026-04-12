<?php

namespace App\Services;

use App\Models\ModuleItems;
use App\Models\Modules;
use App\Models\OrganisationPrices;

class QuoteFormBuilder
{
    private const DEFAULT_FENCE_HEIGHT = '1.8';

    /**
     * Build the form structure for a given module and organisation.
     *
     * Returns:
     * [
     *   'inputs' => ['length' => 15, 'labour_rate' => 35, ...],
     *   'items'  => [
     *     ['module_item_id', 'key', 'name', 'type', 'calculation', 'formula_key',
     *      'unit_price', 'quantity', 'enabled'],
     *     ...
     *   ],
     * ]
     */
    public function build(string $moduleSlug, int $organisationId): array
    {
        $inputs = $this->resolveInputDefaults($moduleSlug);
        $module = Modules::query()->where('slug', $moduleSlug)->first();

        if (! $module) {
            return ['inputs' => $inputs, 'items' => []];
        }

        $moduleItems = ModuleItems::query()
            ->where('module_id', (int) $module->id)
            ->orderBy('id')
            ->get();

        if ($moduleItems->isEmpty()) {
            return ['inputs' => $inputs, 'items' => []];
        }

        // Only query prices when we have a real organisation
        $priceMap = $organisationId > 0
            ? OrganisationPrices::query()
                ->where('organisation_id', $organisationId)
                ->whereIn('module_item_id', $moduleItems->pluck('id')->all())
                ->get()
                ->keyBy('module_item_id')
            : collect();

        // Config item_prices (in pounds) act as fallback for users with no saved org prices.
        $configItemPrices = (array) config('quotes.form_defaults.modules.'.$moduleSlug.'.item_prices', []);
        $items = [];

        foreach ($moduleItems as $moduleItem) {
            $pricing   = $priceMap->get((int) $moduleItem->id);
            $sellPricesByHeight = $this->resolveHeightPriceMap(
                pricing: $pricing,
                moduleItem: $moduleItem,
                configItemPrices: $configItemPrices,
                priceType: 'sell',
                moduleSlug: $moduleSlug,
            );
            $costPricesByHeight = $this->resolveHeightPriceMap(
                pricing: $pricing,
                moduleItem: $moduleItem,
                configItemPrices: $configItemPrices,
                priceType: 'cost',
                moduleSlug: $moduleSlug,
            );
            $unitPrice = $this->resolveUnitPrice($pricing, $sellPricesByHeight, $costPricesByHeight);

            // No DB price — fall back to config defaults so the form is pre-populated.
            if ($unitPrice === 0.0) {
                $itemKey     = (string) ($moduleItem->key ?? '');
                $configPrice = $configItemPrices[$itemKey] ?? [];
                $unitPrice   = (float) ($configPrice['sell_price'] ?? $configPrice['cost_price'] ?? 0);
            }

            // formula_key takes precedence over key; both columns supported
            $formulaKey = (string) ($moduleItem->formula_key ?? $moduleItem->key ?? '');

            $items[] = [
                'module_item_id' => (int) $moduleItem->id,
                'key'            => (string) ($moduleItem->key ?? ''),
                'name'           => (string) ($moduleItem->name ?? 'Item #'.$moduleItem->id),
                'type'           => strtolower((string) ($moduleItem->type ?? 'material')),
                'calculation'    => (string) ($moduleItem->calculation ?? ''),
                'formula_key'    => $formulaKey,
                'meta'           => is_array($moduleItem->meta) ? $moduleItem->meta : [],
                'sell_prices_by_height' => $sellPricesByHeight,
                'cost_prices_by_height' => $costPricesByHeight,
                'unit_price'     => $unitPrice,
                'quantity'       => 0,
                // Optional items start disabled; required items are always on.
                'enabled'        => ! (bool) ($moduleItem->is_optional ?? false),
            ];
        }

        return [
            'inputs' => $inputs,
            'items'  => $items,
        ];
    }

    private function resolveUnitPrice(?OrganisationPrices $pricing, array $sellPricesByHeight, array $costPricesByHeight): float
    {
        $preferredSellPrice = $this->resolvePreferredHeightPrice($sellPricesByHeight);
        if ($preferredSellPrice > 0) {
            return $preferredSellPrice;
        }

        $preferredCostPrice = $this->resolvePreferredHeightPrice($costPricesByHeight);
        if ($preferredCostPrice > 0) {
            return $preferredCostPrice;
        }

        if (! $pricing) {
            return 0;
        }

        // Prices are stored in pence; divide by 100 to return pounds.
        $sell = (float) ($pricing->sell_price ?? 0) / 100;
        if ($sell > 0) {
            return $sell;
        }

        return max(0.0, (float) ($pricing->cost_price ?? 0) / 100);
    }

    private function resolveHeightPriceMap(
        ?OrganisationPrices $pricing,
        ModuleItems $moduleItem,
        array $configItemPrices,
        string $priceType,
        string $moduleSlug,
    ): array {
        if (! $this->isFenceHeightPricedItem($moduleItem, $moduleSlug)) {
            return [];
        }

        $storedKey = $priceType === 'cost' ? 'cost_prices_by_height' : 'sell_prices_by_height';
        $storedMap = $this->normalizeStoredPriceMap($pricing?->{$storedKey});
        if ($storedMap !== []) {
            return $storedMap;
        }

        if ($priceType === 'sell') {
            $metaPrices = $this->normalizeMetaPriceMap($moduleItem->meta);
            if ($metaPrices !== []) {
                return $metaPrices;
            }
        }

        $itemKey = (string) ($moduleItem->key ?? '');
        $configPrice = $configItemPrices[$itemKey] ?? [];
        $fallbackPrice = 0.0;

        if ($pricing) {
            $fallbackPrice = $priceType === 'cost'
                ? max(0.0, (float) ($pricing->cost_price ?? 0) / 100)
                : max(0.0, (float) ($pricing->sell_price ?? 0) / 100);
        }

        if ($fallbackPrice <= 0) {
            $configKey = $priceType === 'cost' ? 'cost_price' : 'sell_price';
            $fallbackPrice = max(0.0, (float) ($configPrice[$configKey] ?? 0));
        }

        return $this->repeatHeightPrice($fallbackPrice);
    }

    private function isFenceHeightPricedItem(ModuleItems $moduleItem, string $moduleSlug): bool
    {
        return $moduleSlug === 'fencing'
            && strtolower((string) ($moduleItem->type ?? 'material')) === 'material'
            && (string) ($moduleItem->key ?? '') !== 'gate';
    }

    private function normalizeStoredPriceMap(mixed $priceMap): array
    {
        if (! is_array($priceMap)) {
            return [];
        }

        $normalized = [];

        foreach ($priceMap as $height => $price) {
            if (! is_numeric($price)) {
                continue;
            }

            $normalized[(string) $height] = round(((float) $price) / 100, 2);
        }

        return $normalized;
    }

    private function normalizeMetaPriceMap(mixed $meta): array
    {
        if (! is_array($meta)) {
            return [];
        }

        $prices = $meta['prices'] ?? null;
        if (! is_array($prices)) {
            return [];
        }

        $normalized = [];

        foreach ($prices as $height => $price) {
            if (! is_numeric($price)) {
                continue;
            }

            $normalized[(string) $height] = round((float) $price, 2);
        }

        return $normalized;
    }

    private function repeatHeightPrice(float $price): array
    {
        if ($price <= 0) {
            return [];
        }

        return [
            '1.5' => round($price, 2),
            self::DEFAULT_FENCE_HEIGHT => round($price, 2),
            '2.0' => round($price, 2),
        ];
    }

    private function resolvePreferredHeightPrice(array $priceMap): float
    {
        if (isset($priceMap[self::DEFAULT_FENCE_HEIGHT]) && is_numeric($priceMap[self::DEFAULT_FENCE_HEIGHT])) {
            return max(0.0, (float) $priceMap[self::DEFAULT_FENCE_HEIGHT]);
        }

        foreach ($priceMap as $price) {
            if (is_numeric($price) && (float) $price > 0) {
                return (float) $price;
            }
        }

        return 0.0;
    }

    private function resolveInputDefaults(string $moduleSlug): array
    {
        $global = config('quotes.form_defaults.global', []);
        $module = config('quotes.form_defaults.modules.'.$moduleSlug, []);

        $merged = array_merge($global, $module);

        return [
            'length'      => (float) ($merged['length'] ?? 15),
            'labour_rate' => (int) ($merged['labour_rate'] ?? 35),
            'markup'      => (float) ($merged['markup'] ?? 15),
            'waste'       => (float) ($merged['waste'] ?? 8),
            'vat_rate'    => (float) ($merged['vat_rate'] ?? 20),
        ];
    }
}
