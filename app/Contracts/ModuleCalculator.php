<?php

namespace App\Contracts;

interface ModuleCalculator
{
    /**
     * Calculate item quantities and prices for a module.
     *
     * Receives:
     *   - length       float   — total run length in metres
     *   - fencing_type string  — 'panels' or 'boards' (fencing only)
     *   - gate_width   float   — gate opening to subtract from effective length (0 = no gate)
     *   - items        array   — module items with pre-loaded org prices from QuoteFormBuilder
     *   - item_inputs  array   — user price/qty overrides keyed by item key
     *   - labour_rate  float   — per-metre labour rate
     *
     * Returns an array of resolved items:
     * [
     *   [
     *     'module_item_id' => int,
     *     'key'            => string,
     *     'name'           => string,
     *     'type'           => 'material'|'labour',
     *     'quantity'       => float,
     *     'unit_price'     => float,
     *     'total'          => float,
     *   ],
     *   ...
     * ]
     *
     * NOTE: Calculators return ONLY items. Waste, markup, and VAT are applied
     * exclusively by QuoteService — never in a calculator.
     *
     * @param  array{
     *     length: float,
     *     fencing_type?: string,
     *     gate_width?: float,
     *     items: array,
     *     item_inputs: array,
     *     labour_rate: float,
     * } $data
     * @return list<array{module_item_id:int, key:string, name:string, type:string, quantity:float, unit_price:float, total:float}>
     */
    public function calculateItems(array $data): array;
}
