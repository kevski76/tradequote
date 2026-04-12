<?php

namespace App\Calculators;

use App\Contracts\ModuleCalculator;

class FenceCalculator implements ModuleCalculator
{
    /**
     * Calculate all fencing item quantities and prices.
     *
     * Fencing rules:
     *  - effective_length = length - gate_width  (when gate_width > 0)
     *  - Panel fencing:
     *      panels       = ceil(effective_length / 1.8)
     *      posts        = panels + 1
     *      gravel_boards = panels
     *  - Featheredge fencing:
     *      boards       = ceil(effective_length / 0.1)
     *      posts        = ceil(effective_length / 1.8) + 1
     *      arris_rails  = posts * 2
     *  - Height affects price only, not quantities
     *  - Gates are separate items (included when gate_width > 0)
     *  - Labour quantity = full length (not effective_length)
     *
     * @param array{
     *     length: float,
    *     height?: float,
     *     fencing_type?: string,
     *     gate_width?: float,
     *     gates?: array,
     *     items: array,
     *     item_inputs: array,
     *     labour_rate: float,
    *     use_markup?: bool,
     * } $data
     */
    public function calculateItems(array $data): array
    {
        $length      = max(0.0, (float) ($data['length'] ?? 0));
        $height      = (float) ($data['height'] ?? 1.8);
        $fencingType = strtolower((string) ($data['fencing_type'] ?? 'panels'));
        $gateWidth   = max(0.0, (float) ($data['gate_width'] ?? 0));
        $gates       = is_array($data['gates'] ?? null) ? $data['gates'] : [];
        $preItems    = is_array($data['items'] ?? null) ? $data['items'] : [];
        $itemInputs  = is_array($data['item_inputs'] ?? null) ? $data['item_inputs'] : [];
        $labourRate  = max(0.0, (float) ($data['labour_rate'] ?? 0));
        $useMarkup   = (bool) ($data['use_markup'] ?? false);

        if (count($gates) > 0) {
            // If multiple gates, total gate width reduces effective length
            $totalGateWidth = array_reduce($gates, function ($carry, $gate) {
                return $carry + max(0.0, (float) ($gate['width'] ?? 0));
            }, 0.0);

            $gateWidth = min($totalGateWidth, $length);
        } else {
            $gateWidth = 0.0;
        }

        // Effective length for material quantity calculations
        $effectiveLength = ($gateWidth > 0 && $gateWidth < $length)
            ? $length - $gateWidth
            : $length;

        $result = [];

        foreach ($preItems as $raw) {
            // Skip disabled items (e.g. the inactive fencing type, optional items not enabled)
            if (isset($raw['enabled']) && $raw['enabled'] === false) {
                continue;
            }

            $key  = (string) ($raw['key'] ?? '');

            // When dynamic gates exist, ignore any static/preloaded gate rows.
            // A single aggregated gate row is injected below from gates[].
            if ($key === 'gate' && count($gates) > 0) {
                continue;
            }

            $type = strtolower((string) ($raw['type'] ?? 'material'));
            $calc = strtolower((string) ($raw['calculation'] ?? ''));

            // --- Quantity resolution ---
            $inputQty = isset($itemInputs[$key]['quantity']) ? (int) ($itemInputs[$key]['quantity']) : 0;
            if ($inputQty > 0) {
                $quantity = (float) $inputQty;
            } else {
                $quantity = $this->resolveQuantity($key, $type, $calc, $length, $effectiveLength);
            }

            $quantity = round($quantity, 2);

            // --- Price resolution ---
            // Labour: always use the dedicated labour_rate input
            // Other: user override > pre-loaded org price
            if ($type === 'labour') {
                $unitPrice = $labourRate;
            } elseif ($key === 'gate') {
                $inputPrice = isset($itemInputs[$key]['price']) ? (float) ($itemInputs[$key]['price']) : 0.0;
                if ($inputPrice > 0) {
                    $unitPrice = $inputPrice;
                } elseif ((float) ($raw['unit_price'] ?? 0) > 0) {
                    $unitPrice = (float) $raw['unit_price'];
                } else {
                    $unitPrice = 0.0;
                }
            } else {
                $inputPrice = isset($itemInputs[$key]['price']) ? (float) ($itemInputs[$key]['price']) : 0.0;
                $basePrice = $this->resolvePriceByHeight($raw, $height, $useMarkup);

                if ($basePrice <= 0) {
                    $basePrice = (float) ($raw['unit_price'] ?? 0.0);
                }

                if ($this->isUserPriceOverride($inputPrice, $basePrice)) {
                    $unitPrice = $inputPrice;
                } else {
                    $unitPrice = $basePrice;
                }
            }

            $unitPrice = round($unitPrice, 2);
            $total     = round($quantity * $unitPrice, 2);

            $result[] = [
                'module_item_id' => (int) ($raw['module_item_id'] ?? 0),
                'key'            => $key,
                'name'           => (string) ($raw['name'] ?? 'Item'),
                'type'           => $type,
                'quantity'       => $quantity,
                'unit_price'     => $unitPrice,
                'total'          => $total,
            ];
        }

        // Inject gate item when gate_width > 0 and no gate key was in pre-loaded items
        if (count($gates) > 0 && ! $this->hasKey($result, 'gate')) {
            $gatePrice = 0.0;
            foreach ($gates as $index => $gate) {
                $gatePrice += isset($gate['price']) ? (float) $gate['price'] : 0.0;
            }

            $gateCount = count($gates);
            $gatePrice = round($gatePrice, 2);
            $gateName = $gateCount === 1 ? 'Gate' : 'Gates';
            $result[]  = [
                'module_item_id' => 0,
                'key'            => 'gate',
                'name'           => $gateName . ' (' . number_format($gateWidth, 1) . 'm)',
                'type'           => 'material',
                'quantity'       => (float) $gateCount,
                'unit_price'     => round($gatePrice, 2),
                'total'          => round($gatePrice, 2),
            ];
        }

        return $result;
    }

    /**
     * Resolve quantity for a known fencing item key.
     *
     * Labour quantity uses full length. All material quantities use effectiveLength.
     */
    private function resolveQuantity(
        string $key,
        string $type,
        string $calculation,
        float $length,
        float $effectiveLength,
    ): float {
        if ($effectiveLength <= 0 && $type !== 'labour') {
            return 0.0;
        }

        if ($calculation === 'direct') {
            return 1.0;
        }

        if ($type === 'labour') {
            // Labour quantity is always the full run length
            return $length > 0 ? $length : 0.0;
        }

        return match ($key) {
            'panels'        => ceil($effectiveLength / 1.8),
            'posts'         => $this->isFeatheredge($key) ? ceil($effectiveLength / 1.8) + 1 : ceil($effectiveLength / 1.8) + 1,
            'gravel_boards' => ceil($effectiveLength / 1.8),
            'boards'        => ceil($effectiveLength / 0.1),
            'rails',
            'arris_rails'   => (ceil($effectiveLength / 1.8) + 1) * 2,
            default         => $effectiveLength,
        };
    }

    /**
     * Posts quantity is the same formula for both panel and featheredge.
     * This helper is kept for clarity should the formulas ever diverge.
     */
    private function isFeatheredge(string $key): bool
    {
        return false; // both types use ceil(eff/1.8) + 1
    }

    private function resolvePriceByHeight(array $item, float $height, bool $useMarkup = false): float
    {
        $selectedPrices = $useMarkup
            ? ($item['cost_prices_by_height'] ?? [])
            : ($item['sell_prices_by_height'] ?? []);

        if (is_array($selectedPrices) && $selectedPrices !== []) {
            $resolved = $this->resolveFromHeightMap($selectedPrices, $height);

            if ($resolved > 0) {
                return $resolved;
            }
        }

        $meta = $item['meta'] ?? null;

        if (is_array($meta) && isset($meta['prices']) && is_array($meta['prices'])) {
            $resolved = $this->resolveFromHeightMap($meta['prices'], $height);

            if ($resolved > 0) {
                return $resolved;
            }
        }

        return (float) ($item['unit_price'] ?? 0);
    }

    private function resolveFromHeightMap(array $prices, float $height): float
    {
        foreach ($prices as $configuredHeight => $configuredPrice) {
            if (abs((float) $configuredHeight - $height) < 0.0001) {
                return (float) $configuredPrice;
            }
        }

        $closestHigherKey = collect($prices)
            ->keys()
            ->map(fn ($configuredHeight) => (string) $configuredHeight)
            ->sortBy(fn (string $configuredHeight) => (float) $configuredHeight)
            ->first(fn (string $configuredHeight) => (float) $configuredHeight >= $height);

        if ($closestHigherKey !== null && isset($prices[$closestHigherKey])) {
            return (float) $prices[$closestHigherKey];
        }

        return 0.0;
    }

    private function isUserPriceOverride(float $inputPrice, float $basePrice): bool
    {
        if ($inputPrice <= 0) {
            return false;
        }

        if ($basePrice <= 0) {
            return true;
        }

        return abs($inputPrice - $basePrice) > 0.0001;
    }

    private function hasKey(array $items, string $key): bool
    {
        foreach ($items as $item) {
            if (($item['key'] ?? '') === $key) {
                return true;
            }
        }

        return false;
    }
}
