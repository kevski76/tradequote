<?php

namespace App\Calculators;

use App\Contracts\ModuleCalculator;

class RoofingCalculator implements ModuleCalculator
{
    /**
     * Calculate roofing item quantities and prices.
     *
     * Stub implementation — roofing-specific rules to be added when the roofing
     * module items are seeded in the database.
     *
     * @param array{
     *     length: float,
     *     items: array,
     *     item_inputs: array,
     *     labour_rate: float,
     * } $data
     */
    public function calculateItems(array $data): array
    {
        $length     = max(0.0, (float) ($data['length'] ?? 0));
        $preItems   = is_array($data['items'] ?? null) ? $data['items'] : [];
        $itemInputs = is_array($data['item_inputs'] ?? null) ? $data['item_inputs'] : [];
        $labourRate = max(0.0, (float) ($data['labour_rate'] ?? 0));

        $result = [];

        foreach ($preItems as $raw) {
            if (isset($raw['enabled']) && $raw['enabled'] === false) {
                continue;
            }

            $key  = (string) ($raw['key'] ?? '');
            $type = strtolower((string) ($raw['type'] ?? 'material'));
            $calc = strtolower((string) ($raw['calculation'] ?? ''));

            $inputQty = isset($itemInputs[$key]['quantity']) ? (int) ($itemInputs[$key]['quantity']) : 0;
            if ($inputQty > 0) {
                $quantity = (float) $inputQty;
            } elseif ($calc === 'direct') {
                $quantity = 1.0;
            } else {
                $quantity = $length;
            }

            $quantity = round($quantity, 2);

            if ($type === 'labour') {
                $unitPrice = $labourRate;
            } else {
                $inputPrice = isset($itemInputs[$key]['price']) ? (float) ($itemInputs[$key]['price']) : 0.0;
                $unitPrice  = $inputPrice > 0 ? $inputPrice : (float) ($raw['unit_price'] ?? 0);
            }

            $unitPrice = round($unitPrice, 2);

            $result[] = [
                'module_item_id' => (int) ($raw['module_item_id'] ?? 0),
                'key'            => $key,
                'name'           => (string) ($raw['name'] ?? 'Item'),
                'type'           => $type,
                'quantity'       => $quantity,
                'unit_price'     => $unitPrice,
                'total'          => round($quantity * $unitPrice, 2),
            ];
        }

        return $result;
    }
}
