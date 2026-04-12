<?php

use App\Services\QuoteService;

function baseQuoteServicePayload(array $inputs = []): array
{
    return [
        'organisation_id' => 0,
        'module_slug' => 'fencing',
        'height' => 1.8,
        'fencing_type' => 'panels',
        'gate_width' => 0,
        'gates' => [],
        'inputs' => array_merge([
            'length' => 9,
            'labour_rate' => 20,
            'markup' => 0,
            'waste' => 0,
            'vat_rate' => 0,
        ], $inputs),
        'items' => [
            [
                'module_item_id' => 1,
                'key' => 'panels',
                'name' => 'Panels',
                'type' => 'material',
                'calculation' => 'formula',
                'formula_key' => 'panels',
                'unit_price' => 10,
                'quantity' => 0,
                'enabled' => true,
            ],
            [
                'module_item_id' => 2,
                'key' => 'labour',
                'name' => 'Labour',
                'type' => 'labour',
                'calculation' => 'formula',
                'formula_key' => 'labour',
                'unit_price' => 0,
                'quantity' => 0,
                'enabled' => true,
            ],
        ],
        'item_inputs' => [],
    ];
}

function findServiceItem(array $items, string $key): array
{
    foreach ($items as $item) {
        if (($item['key'] ?? '') === $key) {
            return $item;
        }
    }

    throw new RuntimeException("Item [{$key}] not found.");
}

test('quote service uses calculated labour totals when no override is provided', function () {
    $service = app(QuoteService::class);

    $breakdown = $service->calculate(baseQuoteServicePayload());

    expect($breakdown['materials_total'])->toBe(50.0)
        ->and($breakdown['labour_total'])->toBe(180.0)
        ->and($breakdown['total'])->toBe(230.0);
});

test('quote service applies labour total override when provided', function () {
    $service = app(QuoteService::class);

    $breakdown = $service->calculate(baseQuoteServicePayload([
        'labour_total_override' => 250,
    ]));

    $labour = findServiceItem($breakdown['items'], 'labour');

    expect($breakdown['materials_total'])->toBe(50.0)
        ->and($breakdown['labour_total'])->toBe(250.0)
        ->and($breakdown['subtotal'])->toBe(300.0)
        ->and($breakdown['total'])->toBe(300.0)
        ->and($labour['total'])->toBe(250.0)
        ->and($labour['unit_price'])->toBe(27.78);
});
