<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Modules;
use App\Models\ModuleItems;
use App\Models\OrganisationPrices;

class VerifyFencingDatabase extends Command
{
    protected $signature = 'verify:fencing-db';
    protected $description = 'Verify fencing Quote database prerequisites';

    public function handle()
    {
        $this->info('=== PHASE 1: DATABASE VERIFICATION FOR FENCING QUOTES ===');

        // 1. Check fencing module
        $this->info("\n1. Checking fencing module...");
        $module = Modules::where('slug', 'fencing')->first();
        if ($module) {
            $this->info("   ✓ Fencing module exists:");
            $this->info("     ID: {$module->id}, Name: {$module->name}, Slug: {$module->slug}");
        } else {
            $this->error("   ✗ Fencing module NOT FOUND");
            return 1;
        }

        // 2. Check module items
        $this->info("\n2. Checking fencing module_items...");
        $items = ModuleItems::where('module_id', $module->id)->orderBy('id')->get();
        if ($items->count() > 0) {
            $this->info("   ✓ Found {$items->count()} module_items:");
            foreach ($items as $item) {
                $optional = $item->is_optional ? '(optional)' : '(required)';
                $this->line("     - {$item->key} ({$item->name}): type={$item->type}, calculation={$item->calculation}, formula_key={$item->formula_key} $optional");
            }
        } else {
            $this->error("   ✗ NO module_items found for fencing");
            return 1;
        }

        // 3. Check organisation prices
        $this->info("\n3. Checking organisation_prices for fencing items...");
        $itemIds = $items->pluck('id')->all();
        $priceRows = OrganisationPrices::whereIn('module_item_id', $itemIds)->get();

        foreach ($items as $item) {
            $prices = $priceRows->where('module_item_id', $item->id);
            if ($prices->count() > 0) {
                $this->line("   ✓ {$item->key}: {$prices->count()} prices");
                foreach ($prices as $price) {
                    $sellPrice = $price->sell_price / 100;
                    $this->line("     - Org ID {$price->organisation_id}: £{$sellPrice} (sell_price={$price->sell_price}p, cost_price={$price->cost_price}p)");
                }
            } else {
                $this->warn("   ⚠ {$item->key}: NO prices found");
            }
        }

        $this->info("\n=== VERIFICATION COMPLETE ===\n");
        return 0;
    }
}
