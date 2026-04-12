<?php

use App\Calculators\FenceCalculator;

function fencingItems(): array
{
    return [
        [
            'module_item_id' => 1,
            'key' => 'panels',
            'name' => 'Fence Panels',
            'type' => 'material',
            'calculation' => 'formula',
            'formula_key' => 'panels',
            'meta' => [
                'prices' => [
                    '1.5' => 22,
                    '1.8' => 25,
                    '2.0' => 30,
                ],
            ],
            'unit_price' => 20,
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
            'meta' => [],
            'unit_price' => 999,
            'quantity' => 0,
            'enabled' => true,
        ],
    ];
}

function findCalculatedItem(array $items, string $key): array
{
    foreach ($items as $item) {
        if (($item['key'] ?? '') === $key) {
            return $item;
        }
    }

    throw new RuntimeException("Item [{$key}] not found.");
}

test('material pricing uses exact height meta price', function () {
    $calculator = new FenceCalculator();

    $items = $calculator->calculateItems([
        'length' => 9,
        'height' => 2.0,
        'items' => fencingItems(),
        'item_inputs' => [],
        'labour_rate' => 35,
    ]);

    expect(findCalculatedItem($items, 'panels')['unit_price'])->toBe(30.0);
});

test('material pricing defaults to 1 point 8 metres when height is omitted', function () {
    $calculator = new FenceCalculator();

    $items = $calculator->calculateItems([
        'length' => 9,
        'items' => fencingItems(),
        'item_inputs' => [],
        'labour_rate' => 35,
    ]);

    expect(findCalculatedItem($items, 'panels')['unit_price'])->toBe(25.0);
});

test('material pricing falls back to closest higher configured height', function () {
    $calculator = new FenceCalculator();

    $items = $calculator->calculateItems([
        'length' => 9,
        'height' => 1.7,
        'items' => fencingItems(),
        'item_inputs' => [],
        'labour_rate' => 35,
    ]);

    expect(findCalculatedItem($items, 'panels')['unit_price'])->toBe(25.0);
});

test('organisation sell prices by height override meta defaults', function () {
    $calculator = new FenceCalculator();

    $items = $calculator->calculateItems([
        'length' => 9,
        'height' => 2.0,
        'items' => [[
            ...fencingItems()[0],
            'sell_prices_by_height' => [
                '1.5' => 41,
                '1.8' => 46,
                '2.0' => 52,
            ],
        ]],
        'item_inputs' => [],
        'labour_rate' => 35,
    ]);

    expect(findCalculatedItem($items, 'panels')['unit_price'])->toBe(52.0);
});

test('organisation cost prices by height are used when markup mode is enabled', function () {
    $calculator = new FenceCalculator();

    $items = $calculator->calculateItems([
        'length' => 9,
        'height' => 1.8,
        'use_markup' => true,
        'items' => [[
            ...fencingItems()[0],
            'cost_prices_by_height' => [
                '1.5' => 18,
                '1.8' => 21,
                '2.0' => 24,
            ],
        ]],
        'item_inputs' => [],
        'labour_rate' => 35,
    ]);

    expect(findCalculatedItem($items, 'panels')['unit_price'])->toBe(21.0);
});

test('user material price override wins over height pricing', function () {
    $calculator = new FenceCalculator();

    $items = $calculator->calculateItems([
        'length' => 9,
        'height' => 2.0,
        'items' => fencingItems(),
        'item_inputs' => [
            'panels' => ['price' => '44.50'],
        ],
        'labour_rate' => 35,
    ]);

    expect(findCalculatedItem($items, 'panels')['unit_price'])->toBe(44.5);
});

test('prefilled material input equal to current resolved height price does not block height pricing', function () {
    $calculator = new FenceCalculator();

    $items = $calculator->calculateItems([
        'length' => 9,
        'height' => 2.0,
        'items' => fencingItems(),
        'item_inputs' => [
            'panels' => ['price' => 30],
        ],
        'labour_rate' => 35,
    ]);

    expect(findCalculatedItem($items, 'panels')['unit_price'])->toBe(30.0);
});

test('items without height meta fall back to unit price', function () {
    $calculator = new FenceCalculator();

    $items = $calculator->calculateItems([
        'length' => 9,
        'height' => 2.0,
        'items' => [[
            'module_item_id' => 3,
            'key' => 'posts',
            'name' => 'Posts',
            'type' => 'material',
            'calculation' => 'formula',
            'formula_key' => 'posts',
            'unit_price' => 12.5,
            'quantity' => 0,
            'enabled' => true,
        ]],
        'item_inputs' => [],
        'labour_rate' => 35,
    ]);

    expect(findCalculatedItem($items, 'posts')['unit_price'])->toBe(12.5);
});

test('labour and gates are not affected by height pricing', function () {
    $calculator = new FenceCalculator();

    $items = $calculator->calculateItems([
        'length' => 9,
        'height' => 2.0,
        'items' => [
            [
                'module_item_id' => 10,
                'key' => 'labour',
                'name' => 'Labour',
                'type' => 'labour',
                'calculation' => 'formula',
                'formula_key' => 'labour',
                'meta' => [
                    'prices' => ['2.0' => 500],
                ],
                'unit_price' => 500,
                'quantity' => 0,
                'enabled' => true,
            ],
            [
                'module_item_id' => 11,
                'key' => 'gate',
                'name' => 'Gate',
                'type' => 'material',
                'calculation' => 'direct',
                'formula_key' => 'gate',
                'meta' => [
                    'prices' => ['2.0' => 250],
                ],
                'unit_price' => 80,
                'quantity' => 0,
                'enabled' => true,
            ],
        ],
        'item_inputs' => [],
        'labour_rate' => 35,
        'gates' => [],
    ]);

    expect(findCalculatedItem($items, 'labour')['unit_price'])->toBe(35.0)
        ->and(findCalculatedItem($items, 'gate')['unit_price'])->toBe(80.0);
});

test('dynamic gate totals still come from gate inputs only', function () {
    $calculator = new FenceCalculator();

    $items = $calculator->calculateItems([
        'length' => 12,
        'height' => 2.0,
        'items' => fencingItems(),
        'item_inputs' => [],
        'labour_rate' => 35,
        'gates' => [
            ['width' => 1.0, 'price' => 125],
            ['width' => 1.2, 'price' => 175],
        ],
    ]);

    $gate = findCalculatedItem($items, 'gate');

    expect($gate['quantity'])->toBe(2.0)
        ->and($gate['unit_price'])->toBe(300.0)
        ->and($gate['total'])->toBe(300.0);
});