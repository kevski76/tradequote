<?php

use App\Models\ModuleItems;
use App\Models\Modules;
use App\Models\OrganisationPrices;
use App\Models\Organisations;
use App\Models\Quotes;
use App\Models\QuoteTemplates;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedFencingModuleForOverrides(int $organisationId): Modules
{
    $module = Modules::query()->create([
        'name' => 'Fencing',
        'slug' => 'fencing',
    ]);

    $panels = ModuleItems::query()->create([
        'module_id' => (int) $module->id,
        'key' => 'panels',
        'name' => 'Fence Panels',
        'type' => 'material',
        'calculation' => 'formula',
        'formula_key' => 'panels',
        'is_optional' => false,
        'meta' => ['prices' => ['1.5' => 22, '1.8' => 25, '2.0' => 30]],
    ]);

    $labour = ModuleItems::query()->create([
        'module_id' => (int) $module->id,
        'key' => 'labour',
        'name' => 'Labour',
        'type' => 'labour',
        'calculation' => 'formula',
        'formula_key' => 'labour',
        'is_optional' => false,
    ]);

    OrganisationPrices::query()->create([
        'organisation_id' => $organisationId,
        'module_item_id' => (int) $panels->id,
        'cost_price' => 1800,
        'pricing_type' => 'fixed',
        'markup_percent' => 0,
        'sell_price' => 2500,
        'sell_prices_by_height' => ['1.5' => 2200, '1.8' => 2500, '2.0' => 3000],
        'cost_prices_by_height' => ['1.5' => 1600, '1.8' => 1800, '2.0' => 2100],
    ]);

    OrganisationPrices::query()->create([
        'organisation_id' => $organisationId,
        'module_item_id' => (int) $labour->id,
        'cost_price' => 0,
        'pricing_type' => 'fixed',
        'markup_percent' => 0,
        'sell_price' => 3500,
    ]);

    return $module;
}

test('template data persists labour and quantity overrides for create flow', function () {
    $user = User::factory()->create();

    $organisation = Organisations::query()->create([
        'name' => 'Override Test Org',
        'owner_id' => (int) $user->id,
    ]);

    $user->forceFill([
        'organisation_id' => (int) $organisation->id,
        'organisation_role' => 'owner',
    ])->save();

    seedFencingModuleForOverrides((int) $organisation->id);

    $template = QuoteTemplates::query()->create([
        'organisation_id' => (int) $organisation->id,
        'created_by' => (int) $user->id,
        'name' => 'Template Roundtrip Create',
        'module_id' => (int) Modules::query()->where('slug', 'fencing')->value('id'),
        'variant_key' => 'fencing',
        'data' => [
            'job_name' => 'Template Roundtrip Create',
            'length' => 10,
            'height' => 1.8,
            'labour_rate' => 35,
            'markup' => 15,
            'waste' => 8,
            'vat_rate' => 20,
            'labour_total_override' => 321.45,
            'item_quantity_overrides' => ['panels' => 7],
        ],
    ]);

    $freshTemplate = QuoteTemplates::query()->find((int) $template->id);

    expect($freshTemplate)->not->toBeNull()
        ->and((float) ($freshTemplate->data['labour_total_override'] ?? 0))->toBe(321.45)
        ->and((int) ($freshTemplate->data['item_quantity_overrides']['panels'] ?? 0))->toBe(7);
});

test('quote calculation data persists labour and quantity overrides for edit flow', function () {
    $user = User::factory()->create();

    $organisation = Organisations::query()->create([
        'name' => 'Edit Template Org',
        'owner_id' => (int) $user->id,
    ]);

    $user->forceFill([
        'organisation_id' => (int) $organisation->id,
        'organisation_role' => 'owner',
    ])->save();

    $module = seedFencingModuleForOverrides((int) $organisation->id);

    $quote = Quotes::query()->create([
        'organisation_id' => (int) $organisation->id,
        'created_by' => (int) $user->id,
        'module_id' => (int) $module->id,
        'variant_key' => 'fencing',
        'customer_name' => 'Edit Test Customer',
        'job_name' => 'Edit Source Quote',
        'status' => 'draft',
        'length' => 12,
        'labour_type' => 'per_metre',
        'labour_total' => 0,
        'materials_total' => 0,
        'subtotal_price' => 0,
        'vat_rate' => 20,
        'vat_total' => 0,
        'calculation_data' => [
            'length' => 12,
            'height' => 1.8,
            'labour_rate' => 35,
            'markup' => 15,
            'waste' => 8,
            'vat_rate' => 20,
            'items' => [],
        ],
        'total_price' => 0,
    ]);

    $quote->forceFill([
        'calculation_data' => [
            'length' => 12,
            'height' => 1.8,
            'labour_rate' => 35,
            'markup' => 15,
            'waste' => 8,
            'vat_rate' => 20,
            'labour_total_override' => 455.75,
            'item_quantity_overrides' => ['panels' => 9],
            'items' => [],
        ],
    ])->save();

    $freshQuote = Quotes::query()->find((int) $quote->id);

    expect($freshQuote)->not->toBeNull()
        ->and((float) ($freshQuote->calculation_data['labour_total_override'] ?? 0))->toBe(455.75)
        ->and((int) ($freshQuote->calculation_data['item_quantity_overrides']['panels'] ?? 0))->toBe(9);
});
