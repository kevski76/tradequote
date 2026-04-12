<?php

namespace App\Services;

use App\Calculators\FenceCalculator;
use App\Calculators\PavingCalculator;
use App\Calculators\RoofingCalculator;
use App\Contracts\ModuleCalculator;
use App\Models\ModuleItems;
use App\Models\Modules;
use App\Models\OrganisationPrices;
use InvalidArgumentException;

class QuoteService
{
    private const DEFAULT_FENCE_HEIGHT = '1.8';

    /**
     * Calculate a full quote breakdown.
     *
     * The structured breakdown returned always uses the canonical key names:
     *   items, materials_total, materials_with_waste, waste_amount,
     *   labour_total, subtotal, subtotal_with_markup, markup_amount,
     *   vat, vat_rate, total, length, labour_rate, markup, waste
     *
     * Payload shape:
     * [
     *   organisation_id  => int,
     *   module_slug      => string,       // 'fencing' | 'paving' | 'roofing'
     *   inputs           => [
     *     length         => float|string,
     *     labour_rate    => float|string,
     *     markup         => float|string,
     *     waste          => float|string,
     *     vat_rate       => float|string,
     *   ],
     *   // Optional – pre-loaded form items from QuoteFormBuilder (fast path)
     *   items            => array,
     *   item_inputs      => array,        // user price/qty overrides keyed by item key
     *   // Fencing-specific (optional)
     *   fencing_type     => 'panels'|'boards',
     *   gate_width       => float,
     *   gates       => array,        // e.g. ['width' => float, 'price' => float]
     * ]
     *
     * @return array{
     *   items: list<array{module_item_id:int, key:string, name:string, type:string, quantity:float, unit_price:float, total:float}>,
     *   materials_total: float,
     *   materials_with_waste: float,
     *   waste_amount: float,
     *   labour_total: float,
     *   subtotal: float,
     *   subtotal_with_markup: float,
     *   markup_amount: float,
     *   vat: float,
     *   vat_rate: float,
     *   total: float,
     *   length: float,
     *   labour_rate: float,
     *   markup: float,
     *   waste: float,
     * }
     */
    public function calculate(array $payload): array
    {
        $organisationId = (int) ($payload['organisation_id'] ?? 0);
        $moduleSlug     = (string) ($payload['module_slug'] ?? '');
        $inputs         = is_array($payload['inputs'] ?? null) ? $payload['inputs'] : [];
        $itemInputs     = is_array($payload['item_inputs'] ?? null) ? $payload['item_inputs'] : [];
        $gates          = is_array($payload['gates'] ?? null) ? $payload['gates'] : [];

        $length     = $this->toFloat($inputs['length'] ?? 0);
        $labourRate = $this->toFloat($inputs['labour_rate'] ?? 0);
        $markup     = $this->toFloat($inputs['markup'] ?? 0);
        $waste      = $this->toFloat($inputs['waste'] ?? 0);
        $vatRate    = $this->toFloat($inputs['vat_rate'] ?? 0);
        $labourTotalOverride = $this->resolveNullableMoneyValue(
            $inputs['labour_total_override'] ?? $payload['labour_total_override'] ?? null
        );

        $fencingType = (string) ($payload['fencing_type'] ?? 'panels');
        $gateWidth   = $this->toFloat($payload['gate_width'] ?? 0);
        $height      = $this->toFloat($payload['height'] ?? 1.8);
        $useMarkup   = (bool) ($payload['use_markup'] ?? false);

        // ---------------------------------------------------------------
        // Primary path: use pre-loaded form items (already carry org prices
        // resolved by QuoteFormBuilder). DB is not re-queried on every render.
        // ---------------------------------------------------------------
        $preBuiltItems = is_array($payload['items'] ?? null) && count($payload['items'] ?? []) > 0
            ? $payload['items']
            : null;

        if ($preBuiltItems !== null) {
            $calculator = $this->resolveCalculator($moduleSlug);

            $items = $calculator->calculateItems([
                'length'       => $length,
                'height'       => $height,
                'fencing_type' => $fencingType,
                'gate_width'   => $gateWidth,
                'gates'        => $gates,
                'items'        => $preBuiltItems,
                'item_inputs'  => $itemInputs,
                'labour_rate'  => $labourRate,
                'use_markup'   => $useMarkup,
            ]); 

            return $this->applyTotals($items, $length, $labourRate, $markup, $waste, $vatRate, $labourTotalOverride);
        }

        // ---------------------------------------------------------------
        // DB fallback path — used when no pre-built items are supplied
        // (e.g. QuotePdfController or future API callers).
        // ---------------------------------------------------------------
        $module = Modules::query()->where('slug', $moduleSlug)->first();
        if (! $module) {
            return $this->emptyBreakdown($length, $labourRate, $markup, $waste, $vatRate);
        }

        $moduleItems = ModuleItems::query()
            ->where('module_id', (int) $module->id)
            ->orderBy('id')
            ->get();

        if ($moduleItems->isEmpty()) {
            return $this->emptyBreakdown($length, $labourRate, $markup, $waste, $vatRate);
        }

        $priceMap = $organisationId > 0
            ? OrganisationPrices::query()
                ->where('organisation_id', $organisationId)
                ->whereIn('module_item_id', $moduleItems->pluck('id')->all())
                ->get()
                ->keyBy('module_item_id')
            : collect();

        // Build a normalised items array from DB records, then hand off to the calculator
        $dbItems = [];
        foreach ($moduleItems as $moduleItem) {
            $pricing   = $priceMap->get((int) $moduleItem->id);
            $sellPricesByHeight = $this->resolveHeightPriceMap(
                pricing: $pricing,
                moduleItem: $moduleItem,
                priceType: 'sell',
                moduleSlug: $moduleSlug,
            );
            $costPricesByHeight = $this->resolveHeightPriceMap(
                pricing: $pricing,
                moduleItem: $moduleItem,
                priceType: 'cost',
                moduleSlug: $moduleSlug,
            );
            $unitPrice = $this->resolveUnitPriceFromDb($moduleItem, $pricing, $labourRate, $sellPricesByHeight, $costPricesByHeight);

            $dbItems[] = [
                'module_item_id' => (int) $moduleItem->id,
                'key'            => (string) ($moduleItem->key ?? ''),
                'name'           => (string) ($moduleItem->name ?? 'Item #' . $moduleItem->id),
                'type'           => strtolower((string) ($moduleItem->type ?? 'material')),
                'calculation'    => (string) ($moduleItem->calculation ?? ''),
                'formula_key'    => (string) ($moduleItem->formula_key ?? $moduleItem->key ?? ''),
                'meta'           => is_array($moduleItem->meta) ? $moduleItem->meta : [],
                'sell_prices_by_height' => $sellPricesByHeight,
                'cost_prices_by_height' => $costPricesByHeight,
                'unit_price'     => $unitPrice,
                'quantity'       => 0,
                'enabled'        => ! (bool) ($moduleItem->is_optional ?? false),
            ];
        }

        $calculator = $this->resolveCalculator($moduleSlug);

        $items = $calculator->calculateItems([
            'length'       => $length,
            'height'       => $height,
            'fencing_type' => $fencingType,
            'gate_width'   => $gateWidth,
            'gates'        => $gates,
            'items'        => $dbItems,
            'item_inputs'  => $itemInputs,
            'labour_rate'  => $labourRate,
            'use_markup'   => $useMarkup,
        ]);

        return $this->applyTotals($items, $length, $labourRate, $markup, $waste, $vatRate, $labourTotalOverride);
    }

    /**
     * Resolve the correct calculator for a given module slug.
     *
     * Each module must have a dedicated Calculator class that implements
     * ModuleCalculator. To add a new module, add a match arm here and create
     * the corresponding class in app/Calculators/.
     */
    private function resolveCalculator(string $moduleSlug): ModuleCalculator
    {
        return match ($moduleSlug) {
            'fencing' => app(FenceCalculator::class),
            'paving'  => app(PavingCalculator::class),
            'roofing' => app(RoofingCalculator::class),
            default   => throw new InvalidArgumentException("No calculator registered for module: {$moduleSlug}"),
        };
    }

    /**
     * Apply the strict calculation order to a resolved items array.
     *
     * Order:
     *   1. materials_total     = sum of material item totals (raw, pre-waste)
     *   2. materials_with_waste = materials_total × (1 + waste%)
     *   3. labour_total        = sum of labour item totals
     *   4. subtotal            = materials_with_waste + labour_total
     *   5. subtotal_with_markup = subtotal × (1 + markup%)
     *   6. markup_amount       = subtotal_with_markup - subtotal
     *   7. vat                 = subtotal_with_markup × (vat_rate / 100)
     *   8. total               = subtotal_with_markup + vat
     *
     * @param  list<array{type:string, total:float}> $items
     */
    private function applyTotals(
        array $items,
        float $length,
        float $labourRate,
        float $markup,
        float $waste,
        float $vatRate,
        ?float $labourTotalOverride = null,
    ): array {
        $materialsRaw = 0.0;
        $labourRaw    = 0.0;

        $labourItemIndexes = [];

        foreach ($items as $index => $item) {
            $type = strtolower((string) ($item['type'] ?? 'material'));
            if ($type === 'labour') {
                $labourRaw += (float) ($item['total'] ?? 0);
                $labourItemIndexes[] = $index;
            } else {
                $materialsRaw += (float) ($item['total'] ?? 0);
            }
        }

        if ($labourTotalOverride !== null) {
            $items = $this->applyLabourTotalOverrideToItems($items, $labourItemIndexes, $labourRaw, $labourTotalOverride);
            $labourRaw = $labourTotalOverride;
        }

        $materialsTotal     = round($materialsRaw, 2);
        $materialsWithWaste = round($materialsRaw * (1 + ($waste / 100)), 2);
        $wasteAmount        = round($materialsWithWaste - $materialsRaw, 2);
        $labourTotal        = round($labourRaw, 2);
        $subtotal           = round($materialsWithWaste + $labourTotal, 2);
        $subtotalWithMarkup = round($subtotal * (1 + ($markup / 100)), 2);
        $markupAmount       = round($subtotalWithMarkup - $subtotal, 2);
        $vat                = round($subtotalWithMarkup * ($vatRate / 100), 2);
        $total              = round($subtotalWithMarkup + $vat, 2);

        return [
            'items'               => $items,
            'materials_total'     => $materialsTotal,
            'materials_with_waste' => $materialsWithWaste,
            'waste_amount'        => $wasteAmount,
            'labour_total'        => $labourTotal,
            'subtotal'            => $subtotal,
            'subtotal_with_markup' => $subtotalWithMarkup,
            'markup_amount'       => $markupAmount,
            'vat'                 => $vat,
            'vat_rate'            => $vatRate,
            'total'               => $total,
            'length'              => $length,
            'labour_rate'         => $labourRate,
            'markup'              => $markup,
            'waste'               => $waste,
        ];
    }

    /**
     * Resolve a unit price from a DB ModuleItems record and its associated org pricing.
     * Used only by the DB fallback path.
     */
    private function resolveUnitPriceFromDb(
        ModuleItems $item,
        ?OrganisationPrices $pricing,
        float $labourRate,
        array $sellPricesByHeight,
        array $costPricesByHeight,
    ): float {
        // Labour items use the labour_rate input directly
        if (strtolower((string) ($item->type ?? '')) === 'labour') {
            if (! $pricing || ((float) ($pricing->sell_price ?? 0) === 0.0 && (float) ($pricing->cost_price ?? 0) === 0.0)) {
                return $labourRate;
            }
        }

        $preferredSellPrice = $this->resolvePreferredHeightPrice($sellPricesByHeight);
        if ($preferredSellPrice > 0) {
            return $preferredSellPrice;
        }

        $preferredCostPrice = $this->resolvePreferredHeightPrice($costPricesByHeight);
        if ($preferredCostPrice > 0) {
            return $preferredCostPrice;
        }

        if (! $pricing) {
            return 0.0;
        }

        // Prices are stored in pence; divide by 100 to return pounds
        $sellPrice = (float) ($pricing->sell_price ?? 0) / 100;
        if ($sellPrice > 0) {
            return $sellPrice;
        }

        return max(0.0, (float) ($pricing->cost_price ?? 0) / 100);
    }

    private function resolveHeightPriceMap(
        ?OrganisationPrices $pricing,
        ModuleItems $moduleItem,
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

        if ($pricing) {
            $fallback = $priceType === 'cost'
                ? max(0.0, (float) ($pricing->cost_price ?? 0) / 100)
                : max(0.0, (float) ($pricing->sell_price ?? 0) / 100);

            return $this->repeatHeightPrice($fallback);
        }

        return [];
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

    private function emptyBreakdown(float $length, float $labourRate, float $markup, float $waste, float $vatRate): array
    {
        return [
            'items'               => [],
            'materials_total'     => 0.0,
            'materials_with_waste' => 0.0,
            'waste_amount'        => 0.0,
            'labour_total'        => 0.0,
            'subtotal'            => 0.0,
            'subtotal_with_markup' => 0.0,
            'markup_amount'       => 0.0,
            'vat'                 => 0.0,
            'vat_rate'            => $vatRate,
            'total'               => 0.0,
            'length'              => $length,
            'labour_rate'         => $labourRate,
            'markup'              => $markup,
            'waste'               => $waste,
        ];
    }

    private function toFloat(mixed $value): float
    {
        return max(0.0, (float) $value);
    }

    private function resolveNullableMoneyValue(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            return null;
        }

        return round(max(0.0, (float) $value), 2);
    }

    /**
     * @param list<array<int|string, mixed>> $items
     * @param list<int> $labourItemIndexes
     * @return list<array<int|string, mixed>>
     */
    private function applyLabourTotalOverrideToItems(
        array $items,
        array $labourItemIndexes,
        float $existingLabourTotal,
        float $override,
    ): array {
        if ($labourItemIndexes === []) {
            return $items;
        }

        $override = round(max(0.0, $override), 2);

        if ($existingLabourTotal <= 0) {
            $firstIndex = $labourItemIndexes[0];
            $quantity = max(0.0, (float) ($items[$firstIndex]['quantity'] ?? 0));
            $items[$firstIndex]['total'] = $override;
            $items[$firstIndex]['unit_price'] = $quantity > 0
                ? round($override / $quantity, 2)
                : $override;

            foreach (array_slice($labourItemIndexes, 1) as $index) {
                $items[$index]['total'] = 0.0;
            }

            return $items;
        }

        $assigned = 0.0;
        $lastPosition = count($labourItemIndexes) - 1;

        foreach ($labourItemIndexes as $position => $index) {
            if ($position === $lastPosition) {
                $newTotal = round($override - $assigned, 2);
            } else {
                $share = ((float) ($items[$index]['total'] ?? 0)) / $existingLabourTotal;
                $newTotal = round($override * $share, 2);
                $assigned += $newTotal;
            }

            $quantity = max(0.0, (float) ($items[$index]['quantity'] ?? 0));
            $items[$index]['total'] = $newTotal;
            $items[$index]['unit_price'] = $quantity > 0
                ? round($newTotal / $quantity, 2)
                : $newTotal;
        }

        return $items;
    }
}
