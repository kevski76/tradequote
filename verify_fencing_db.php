<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\Modules;
use App\Models\ModuleItems;
use App\Models\OrganisationPrices;

echo "=== PHASE 1: DATABASE VERIFICATION FOR FENCING QUOTES ===\n\n";

// 1. Check fencing module
echo "1. Checking fencing module...\n";
$module = Modules::where('slug', 'fencing')->first();
if ($module) {
    echo "   ✓ Fencing module exists:\n";
    echo "     ID: {$module->id}, Name: {$module->name}, Slug: {$module->slug}\n";
} else {
    echo "   ✗ Fencing module NOT FOUND\n";
    exit(1);
}

// 2. Check module items
echo "\n2. Checking fencing module_items...\n";
$items = ModuleItems::where('module_id', $module->id)->orderBy('id')->get();
if ($items->count() > 0) {
    echo "   ✓ Found {$items->count()} module_items:\n";
    foreach ($items as $item) {
        echo "     - {$item->key} ({$item->name}): type={$item->type}, calculation={$item->calculation}, formula_key={$item->formula_key}, optional={$item->is_optional}\n";
    }
} else {
    echo "   ✗ NO module_items found for fencing\n";
    exit(1);
}

// 3. Check organisation prices
echo "\n3. Checking organisation_prices for fencing items...\n";
$itemIds = $items->pluck('id')->all();
$priceRows = OrganisationPrices::whereIn('module_item_id', $itemIds)->get();

foreach ($items as $item) {
    $prices = $priceRows->where('module_item_id', $item->id);
    if ($prices->count() > 0) {
        echo "   ✓ {$item->key}: {$prices->count()} prices\n";
        foreach ($prices as $price) {
            $sellPrice = $price->sell_price / 100;
            echo "     - Org ID {$price->organisation_id}: £{$sellPrice} (sell_price={$price->sell_price}p)\n";
        }
    } else {
        echo "   ✗ {$item->key}: NO prices found\n";
    }
}

echo "\n=== VERIFICATION COMPLETE ===\n";
