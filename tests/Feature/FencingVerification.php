<?php

use App\Models\Modules;
use App\Models\ModuleItems;
use App\Models\OrganisationPrices;

test('fencing module exists', function () {
    $module = Modules::where('slug', 'fencing')->first();
    expect($module)->not->toBeNull();
    expect($module->name)->toBe('Fencing');
    expect($module->slug)->toBe('fencing');
    
    echo "✓ Fencing module exists: ID={$module->id}, Name={$module->name}, Slug={$module->slug}\n";
});

test('fencing module items exist', function () {
    $module = Modules::where('slug', 'fencing')->first();
    $items = ModuleItems::where('module_id', $module->id)->orderBy('id')->get();
    
    expect($items)->toHaveCount(5);
    
    $expectedKeys = ['posts', 'panels', 'gravel_boards', 'rails', 'labour'];
    $actualKeys = $items->pluck('key')->all();
    
    expect($actualKeys)->toBe($expectedKeys);
    
    echo "✓ Fencing module_items exist (5 items):\n";
    foreach ($items as $item) {
        echo "  - {$item->key} ({$item->name}): type={$item->type}, calculation={$item->calculation}, optional={$item->is_optional}\n";
    }
});

test('organisation prices exist for fencing items', function () {
    $module = Modules::where('slug', 'fencing')->first();
    $items = ModuleItems::where('module_id', $module->id)->get();
    
    $prices = OrganisationPrices::whereIn('module_item_id', $items->pluck('id'))->get();
    expect($prices)->not->toBeEmpty();
    
    echo "✓ Organisation prices exist ({$prices->count()} total rows):\n";
    
    foreach ($items as $item) {
        $itemPrices = $prices->where('module_item_id', $item->id);
        echo "  {$item->key}: {$itemPrices->count()} prices\n";
        foreach ($itemPrices as $price) {
            $pounds = $price->sell_price / 100;
            echo "    - Org {$price->organisation_id}: £{$pounds} (sell_price={$price->sell_price}p)\n";
        }
    }
});
