<?php

namespace App\Services;

use App\Models\ModuleItems;
use App\Models\Modules;
use App\Models\OrganisationPrices;

class QuoteCalculator
{
    /**
     * @param array{
     *     organisation_id:int,
     *     module_slug:string,
     *     inputs:array{length?:mixed,labour_rate?:mixed,markup?:mixed,waste?:mixed,vat_rate?:mixed}
     * } $payload
     *
     * @return array{
     *     items:array<int,array{module_item_id:int,name:string,type:string,formula_key:string,quantity:float,unit_price:float,total:float}>,
     *     materials_total:float,
     *     labour_total:float,
     *     subtotal:float,
     *     markup_amount:float,
     *     vat_amount:float,
     *     total_price:float,
     *     length:float,
     *     labour_rate:float,
     *     markup:float,
     *     waste:float,
     *     vat_rate:float,
     *     materials_cost:float,
     *     labour_cost:float,
     *     posts_qty:int,
     *     posts_price:float,
     *     boards_qty:int,
     *     boards_price:float
     * }
     */
    public function calculate(array $payload): array
    {
        $organisationId = (int) ($payload['organisation_id'] ?? 0);
        $moduleSlug = (string) ($payload['module_slug'] ?? '');
        $inputs = is_array($payload['inputs'] ?? null) ? $payload['inputs'] : [];

        $length = $this->toFloat($inputs['length'] ?? 0);
        $labourRate = $this->toFloat($inputs['labour_rate'] ?? 0);
        $markup = $this->toFloat($inputs['markup'] ?? 0);
        $waste = $this->toFloat($inputs['waste'] ?? 0);
        $vatRate = $this->toFloat($inputs['vat_rate'] ?? 0);

        $module = Modules::query()
            ->where('slug', $moduleSlug)
            ->first();

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

        $priceMap = OrganisationPrices::query()
            ->where('organisation_id', $organisationId)
            ->whereIn('module_item_id', $moduleItems->pluck('id')->all())
            ->get()
            ->keyBy('module_item_id');

        $items = [];
        $materialsBase = 0.0;
        $labourBase = 0.0;

        foreach ($moduleItems as $moduleItem) {
            $formulaKey = (string) ($moduleItem->formula_key ?? $moduleItem->key ?? '');
            $quantity = $this->resolveQuantity($formulaKey, [
                'length' => $length,
                'labour_rate' => $labourRate,
                'markup' => $markup,
                'waste' => $waste,
                'vat_rate' => $vatRate,
            ]);

            $pricing = $priceMap->get((int) $moduleItem->id);
            $unitPrice = $this->resolveUnitPrice((string) $formulaKey, $pricing, $labourRate);
            $total = round($quantity * $unitPrice, 2);
            $type = strtolower((string) ($moduleItem->type ?? 'material'));

            if ($type === 'labour') {
                $labourBase += $total;
            } else {
                $materialsBase += $total;
            }

            $items[] = [
                'module_item_id' => (int) $moduleItem->id,
                'name' => (string) ($moduleItem->name ?? 'Item #'.$moduleItem->id),
                'type' => $type,
                'formula_key' => $formulaKey,
                'quantity' => round($quantity, 4),
                'unit_price' => round($unitPrice, 2),
                'total' => $total,
            ];
        }

        $materialsTotal = round($materialsBase * (1 + ($waste / 100)), 2);
        $labourTotal = round($labourBase, 2);
        $baseSubtotal = round($materialsTotal + $labourTotal, 2);
        $markupAmount = round($baseSubtotal * ($markup / 100), 2);
        $subtotal = round($baseSubtotal + $markupAmount, 2);
        $vatAmount = round($subtotal * ($vatRate / 100), 2);
        $totalPrice = round($subtotal + $vatAmount, 2);

        return array_merge(
            [
                'items' => $items,
                'materials_total' => $materialsTotal,
                'labour_total' => $labourTotal,
                'subtotal' => $subtotal,
                'markup_amount' => $markupAmount,
                'vat_amount' => $vatAmount,
                'total_price' => $totalPrice,
                'length' => $length,
                'labour_rate' => $labourRate,
                'markup' => $markup,
                'waste' => $waste,
                'vat_rate' => $vatRate,
                'materials_cost' => $materialsTotal,
                'labour_cost' => $labourTotal,
            ],
            $this->legacyFenceMetrics($items)
        );
    }

    private function resolveQuantity(string $formulaKey, array $inputs): float
    {
        $length = $this->toFloat($inputs['length'] ?? 0);

        return match ($formulaKey) {
            'posts_needed' => $length > 0 ? ((float) ceil($length / 1.8)) + 1 : 0.0,
            'boards_needed' => $length > 0 ? (float) ceil($length * 9) : 0.0,
            'gravel_needed' => round($length * 0.15, 4),
            'labour_per_metre' => $length,
            default => $length,
        };
    }

    private function resolveUnitPrice(string $formulaKey, ?OrganisationPrices $pricing, float $labourRate): float
    {
        if ($formulaKey === 'labour_per_metre' && $labourRate > 0) {
            return $labourRate;
        }

        if (! $pricing) {
            return 0.0;
        }

        $sellPrice = (float) ($pricing->sell_price ?? 0);
        if ($sellPrice > 0) {
            return $sellPrice;
        }

        return max(0, (float) ($pricing->cost_price ?? 0));
    }

    private function legacyFenceMetrics(array $items): array
    {
        $postsQty = 0;
        $postsPrice = 0.0;
        $boardsQty = 0;
        $boardsPrice = 0.0;

        foreach ($items as $item) {
            $formulaKey = (string) ($item['formula_key'] ?? '');

            if ($formulaKey === 'posts_needed') {
                $postsQty = (int) round((float) ($item['quantity'] ?? 0));
                $postsPrice = (float) ($item['total'] ?? 0);
            }

            if ($formulaKey === 'boards_needed') {
                $boardsQty = (int) round((float) ($item['quantity'] ?? 0));
                $boardsPrice = (float) ($item['total'] ?? 0);
            }
        }

        return [
            'posts_qty' => $postsQty,
            'posts_price' => round($postsPrice, 2),
            'boards_qty' => $boardsQty,
            'boards_price' => round($boardsPrice, 2),
        ];
    }

    private function emptyBreakdown(float $length, float $labourRate, float $markup, float $waste, float $vatRate): array
    {
        return [
            'items' => [],
            'materials_total' => 0.0,
            'labour_total' => 0.0,
            'subtotal' => 0.0,
            'markup_amount' => 0.0,
            'vat_amount' => 0.0,
            'total_price' => 0.0,
            'length' => $length,
            'labour_rate' => $labourRate,
            'markup' => $markup,
            'waste' => $waste,
            'vat_rate' => $vatRate,
            'materials_cost' => 0.0,
            'labour_cost' => 0.0,
            'posts_qty' => 0,
            'posts_price' => 0.0,
            'boards_qty' => 0,
            'boards_price' => 0.0,
        ];
    }

    private function toFloat(mixed $value): float
    {
        return max(0, (float) $value);
    }
}
