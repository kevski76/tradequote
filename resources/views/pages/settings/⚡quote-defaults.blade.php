<?php

use App\Models\ModuleItems;
use App\Models\Modules;
use App\Models\OrganisationPrices;
use App\Models\Organisations;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Quote defaults')] class extends Component {
    private const DEFAULT_FENCE_HEIGHT = '1.8';

    public array $availableModules = [];

    public array $fenceHeightOptions = [
        '1_5' => '1.5',
        '1_8' => '1.8',
        '2_0' => '2.0',
    ];

    public string $selectedModule = 'fencing';

    public bool $canManage = false;

    public string $global_vat_rate = '';
    public string $global_payment_terms = '';

    public string $module_length = '';
    public string $module_labour_rate = '';
    public string $module_markup = '';
    public string $module_waste = '';

    public bool $use_markup = false;

    public string $fenceType = 'panel'; // panel | closeboard
    public string $module_payment_terms = '';

    /** @var array<int, array{id: int, key: string, name: string, type: string, height_priced: bool}> */
    public array $moduleItems = [];

    /** @var array<string, array{sell_price: string, cost_price: string, sell_prices_by_height: array<string, string>, cost_prices_by_height: array<string, string>}> */
    public array $itemPrices = [];

    public function mount(): void
    {
        $dbModules = Modules::query()
            ->orderBy('name')
            ->pluck('slug')
            ->filter(fn ($slug) => is_string($slug) && trim($slug) !== '')
            ->map(fn ($slug) => (string) $slug)
            ->unique()
            ->values()
            ->all();

        $configModules = array_keys((array) config('quotes.form_defaults.modules', [])); 

        $this->availableModules = collect(array_merge($dbModules, $configModules))
            ->filter(fn ($slug) => is_string($slug) && trim($slug) !== '')
            ->map(fn ($slug) => (string) $slug)
            ->unique()
            ->values()
            ->all();

        if ($this->availableModules === []) {
            $this->availableModules = ['fencing'];
        }

        if (! in_array($this->selectedModule, $this->availableModules, true)) {
            $this->selectedModule = $this->availableModules[0];
        }

        $this->loadDefaultsIntoForm();
    }

    public function updatedSelectedModule(): void
    {
        $this->loadModuleDefaultsIntoForm();
    }

    public function saveGlobalDefaults(): void
    {
        $validated = $this->validate([
            'global_vat_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'global_payment_terms' => ['nullable', 'string', 'max:1000'],
        ]);

        $organisation = $this->organisation(createIfMissing: true);

        if (! $organisation || ! $this->canManage) {
            $this->dispatch('toast', message: 'Only organisation owners can update quote defaults.', type: 'error');
            return;
        }

        $defaults = is_array($organisation->quote_defaults) ? $organisation->quote_defaults : [];
        $defaults['modules'] = is_array($defaults['modules'] ?? null) ? $defaults['modules'] : [];
        $defaults['global'] = [
            'vat_rate' => (float) $validated['global_vat_rate'],
            'payment_terms' => trim((string) ($validated['global_payment_terms'] ?? '')),
        ];

        $organisation->update(['quote_defaults' => $defaults]);

        $this->dispatch('toast', message: 'Global quote defaults saved.', type: 'success');
        $this->loadDefaultsIntoForm();
    }

    public function saveModuleDefaults(): void
    {
        $validated = $this->validate([
            'selectedModule'          => ['required', 'string', 'alpha_dash', 'max:80'],
            'module_length'           => ['required', 'numeric', 'min:0.1', 'max:10000'],
            'module_labour_rate'      => ['required', 'numeric', 'min:0', 'max:10000'],
            'module_markup'           => [$this->use_markup ? 'required' : 'nullable', 'numeric', 'min:0', 'max:200'],
            'module_waste'            => ['required', 'numeric', 'min:0', 'max:100'],
            'itemPrices.*.sell_price' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'itemPrices.*.cost_price' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'itemPrices.*.sell_prices_by_height.*' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'itemPrices.*.cost_prices_by_height.*' => ['nullable', 'numeric', 'min:0', 'max:999999'],
        ]);

        $organisation = $this->organisation(createIfMissing: true);

        if (! $organisation || ! $this->canManage) {
            $this->dispatch('toast', message: 'Only organisation owners can update quote defaults.', type: 'error');
            return;
        }

        $defaults = is_array($organisation->quote_defaults) ? $organisation->quote_defaults : [];
        $defaults['global'] = is_array($defaults['global'] ?? null) ? $defaults['global'] : config('quotes.form_defaults.global', []);
        $defaults['modules'] = is_array($defaults['modules'] ?? null) ? $defaults['modules'] : [];

        $defaults['modules'][$validated['selectedModule']] = [
            'length'      => (float) $validated['module_length'],
            'labour_rate' => (float) $validated['module_labour_rate'],
            'markup'      => (float) ($validated['module_markup'] ?? 0),
            'waste'       => (float) $validated['module_waste'],
            'use_markup'  => $this->use_markup,
        ];

        $organisation->update(['quote_defaults' => $defaults]);

        // Upsert per-item sell/cost prices into organisation_prices (stored in pence)
        foreach ($this->moduleItems as $item) {
            $itemId    = (int) $item['id'];
            $priceData = $this->itemPrices[(string) $itemId] ?? [];
            $sellPricesByHeight = $this->normalizeHeightPricesForStorage($priceData['sell_prices_by_height'] ?? []);
            $costPricesByHeight = $this->normalizeHeightPricesForStorage($priceData['cost_prices_by_height'] ?? []);
            $isHeightPriced = $this->selectedModule === 'fencing' && (bool) ($item['height_priced'] ?? false);

            $sell      = $isHeightPriced
                ? $this->preferredStoredHeightPrice($sellPricesByHeight)
                : (isset($priceData['sell_price']) && $priceData['sell_price'] !== ''
                    ? (int) round((float) $priceData['sell_price'] * 100)
                    : 0);
            $cost      = $isHeightPriced
                ? $this->preferredStoredHeightPrice($costPricesByHeight)
                : (isset($priceData['cost_price']) && $priceData['cost_price'] !== ''
                    ? (int) round((float) $priceData['cost_price'] * 100)
                    : 0);

            OrganisationPrices::updateOrCreate(
                [
                    'organisation_id' => (int) $organisation->id,
                    'module_item_id'  => $itemId,
                ],
                [
                    'sell_price' => $sell,
                    'cost_price' => $cost,
                    'sell_prices_by_height' => $isHeightPriced && $sellPricesByHeight !== [] ? $sellPricesByHeight : null,
                    'cost_prices_by_height' => $isHeightPriced && $costPricesByHeight !== [] ? $costPricesByHeight : null,
                ]
            );
        }

        $this->dispatch('toast', message: 'Module defaults saved for '.$validated['selectedModule'].'.', type: 'success');
        $this->loadModuleDefaultsIntoForm();
    }

    public function clearModuleOverride(): void
    {
        $organisation = $this->organisation(createIfMissing: false);

        if (! $organisation || ! $this->canManage) {
            $this->dispatch('toast', message: 'Only organisation owners can update quote defaults.', type: 'error');
            return;
        }

        $defaults = is_array($organisation->quote_defaults) ? $organisation->quote_defaults : [];

        if (is_array($defaults['modules'] ?? null) && array_key_exists($this->selectedModule, $defaults['modules'])) {
            unset($defaults['modules'][$this->selectedModule]);
            $organisation->update(['quote_defaults' => $defaults]);
            $this->dispatch('toast', message: 'Module override removed. Inherited defaults are now active.', type: 'success');
        }

        $this->loadModuleDefaultsIntoForm();
    }

    private function loadDefaultsIntoForm(): void
    {
        $organisation = $this->organisation(createIfMissing: false);
        $defaults = is_array($organisation?->quote_defaults) ? $organisation->quote_defaults : [];

        $globalDefaults = array_merge(
            config('quotes.form_defaults.global', []),
            is_array($defaults['global'] ?? null) ? $defaults['global'] : []
        );

        $this->global_vat_rate = (string) ($globalDefaults['vat_rate'] ?? 20);
        $this->global_payment_terms = (string) ($globalDefaults['payment_terms'] ?? '');

        $this->loadModuleDefaultsIntoForm();
    }

    private function loadModuleDefaultsIntoForm(): void
    {
        $organisation = $this->organisation(createIfMissing: false);
        $defaults = is_array($organisation?->quote_defaults) ? $organisation->quote_defaults : [];

        $global = config('quotes.form_defaults.global', []);
        $module = config('quotes.form_defaults.modules.'.$this->selectedModule, []);
        $organisationGlobal = is_array($defaults['global'] ?? null) ? $defaults['global'] : [];
        $organisationModule = is_array($defaults['modules'][$this->selectedModule] ?? null)
            ? $defaults['modules'][$this->selectedModule]
            : [];

        $resolved = array_merge($global, $module, $organisationGlobal, $organisationModule);

        $this->fenceType = ($this->selectedModule === 'fencing' && isset($resolved['type'])) ? $resolved['type'] : 'panel';

        $this->module_length = (string) ($resolved['length'] ?? 15);
        $this->module_labour_rate = (string) ($resolved['labour_rate'] ?? 35);
        $this->module_markup = (string) ($resolved['markup'] ?? 15);
        $this->module_waste = (string) ($resolved['waste'] ?? 8);
        $this->module_payment_terms = (string) ($resolved['payment_terms'] ?? '');
        $this->use_markup = (bool) ($resolved['use_markup'] ?? false);

        // Load module items and their saved org prices for the Item Prices section
        $this->moduleItems = [];
        $this->itemPrices  = [];

        $moduleModel = Modules::query()->where('slug', $this->selectedModule)->first();

        if ($moduleModel) {
            $items = ModuleItems::query()
                ->where('module_id', (int) $moduleModel->id)
                ->orderBy('id')
                ->get();

            $orgForPrices = $this->organisation(createIfMissing: false);
            $priceMap     = collect();

            if ($orgForPrices) {
                $priceMap = OrganisationPrices::query()
                    ->where('organisation_id', (int) $orgForPrices->id)
                    ->whereIn('module_item_id', $items->pluck('id')->all())
                    ->get()
                    ->keyBy('module_item_id');
            }

            $configItemPrices = (array) config('quotes.form_defaults.modules.'.$this->selectedModule.'.item_prices', []);

            foreach ($items as $item) {
                $pricing    = $priceMap->get((int) $item->id);
                $itemKey    = (string) ($item->key ?? '');
                $configPrice = $configItemPrices[$itemKey] ?? [];
                $itemMeta = is_array($item->meta) ? $item->meta : [];
                $heightPriced = $this->selectedModule === 'fencing'
                    && strtolower((string) ($item->type ?? 'material')) === 'material'
                    && $itemKey !== 'gate';

                $this->moduleItems[] = [
                    'id'   => (int) $item->id,
                    'key'  => $itemKey,
                    'name' => (string) ($item->name ?? 'Item'),
                    'type' => strtolower((string) ($item->type ?? 'material')),
                    'height_priced' => $heightPriced,
                ];
                $this->itemPrices[(string) $item->id] = [
                    // DB stores pence; divide by 100 for display. Config fallbacks are already in pounds.
                    'sell_price' => $pricing && (int) ($pricing->sell_price ?? 0) > 0
                        ? (string) ((float) $pricing->sell_price / 100)
                        : (string) ($configPrice['sell_price'] ?? ''),
                    'cost_price' => $pricing && (int) ($pricing->cost_price ?? 0) > 0
                        ? (string) ((float) $pricing->cost_price / 100)
                        : (string) ($configPrice['cost_price'] ?? ''),
                    'sell_prices_by_height' => $heightPriced
                        ? $this->resolveHeightPricesForForm(
                            $pricing?->sell_prices_by_height,
                            $itemMeta,
                            'sell',
                            $pricing,
                            $configPrice,
                        )
                        : $this->emptyHeightPriceFormState(),
                    'cost_prices_by_height' => $heightPriced
                        ? $this->resolveHeightPricesForForm(
                            $pricing?->cost_prices_by_height,
                            $itemMeta,
                            'cost',
                            $pricing,
                            $configPrice,
                        )
                        : $this->emptyHeightPriceFormState(),
                ];
            }
        }
    }

    private function emptyHeightPriceFormState(): array
    {
        $empty = [];

        foreach ($this->fenceHeightOptions as $heightKey => $heightLabel) {
            $empty[$heightKey] = '';
        }

        return $empty;
    }

    private function resolveHeightPricesForForm(
        mixed $storedPrices,
        array $itemMeta,
        string $priceType,
        ?OrganisationPrices $pricing,
        array $configPrice,
    ): array {
        $formState = $this->emptyHeightPriceFormState();
        $resolved = [];

        if (is_array($storedPrices) && $storedPrices !== []) {
            foreach ($storedPrices as $height => $price) {
                $heightKey = $this->fenceHeightFormKey((string) $height);

                if ($heightKey === null || ! is_numeric($price)) {
                    continue;
                }

                $resolved[$heightKey] = (string) round(((float) $price) / 100, 2);
            }
        }

        if ($resolved === [] && $priceType === 'sell') {
            $metaPrices = $itemMeta['prices'] ?? null;

            if (is_array($metaPrices)) {
                foreach ($metaPrices as $height => $price) {
                    $heightKey = $this->fenceHeightFormKey((string) $height);

                    if ($heightKey === null || ! is_numeric($price)) {
                        continue;
                    }

                    $resolved[$heightKey] = (string) round((float) $price, 2);
                }
            }
        }

        if ($resolved === []) {
            $fallbackPrice = 0.0;

            if ($pricing) {
                $fallbackPrice = $priceType === 'cost'
                    ? max(0.0, (float) ($pricing->cost_price ?? 0) / 100)
                    : max(0.0, (float) ($pricing->sell_price ?? 0) / 100);
            }

            if ($fallbackPrice <= 0) {
                $configKey = $priceType === 'cost' ? 'cost_price' : 'sell_price';
                $fallbackPrice = max(0.0, (float) ($configPrice[$configKey] ?? 0));
            }

            if ($fallbackPrice > 0) {
                foreach (array_keys($this->fenceHeightOptions) as $heightKey) {
                    $resolved[$heightKey] = (string) round($fallbackPrice, 2);
                }
            }
        }

        return array_replace($formState, $resolved);
    }

    private function normalizeHeightPricesForStorage(array $formPrices): array
    {
        $normalized = [];

        foreach ($this->fenceHeightOptions as $heightKey => $heightLabel) {
            $price = $formPrices[$heightKey] ?? null;

            if ($price === null || $price === '' || ! is_numeric($price)) {
                continue;
            }

            $normalized[$heightLabel] = (int) round((float) $price * 100);
        }

        return $normalized;
    }

    private function preferredStoredHeightPrice(array $prices): int
    {
        if (isset($prices[self::DEFAULT_FENCE_HEIGHT]) && is_numeric($prices[self::DEFAULT_FENCE_HEIGHT])) {
            return (int) $prices[self::DEFAULT_FENCE_HEIGHT];
        }

        foreach ($prices as $price) {
            if (is_numeric($price) && (int) $price > 0) {
                return (int) $price;
            }
        }

        return 0;
    }

    private function fenceHeightFormKey(string $height): ?string
    {
        foreach ($this->fenceHeightOptions as $heightKey => $heightLabel) {
            if ($heightLabel === $height) {
                return $heightKey;
            }
        }

        return null;
    }

    private function organisation(bool $createIfMissing): ?Organisations
    {
        $user = Auth::user();

        if (! $user) {
            $this->canManage = false;
            return null;
        }

        $organisation = null;

        if ((int) ($user->organisation_id ?? 0) > 0) {
            $organisation = Organisations::query()->find((int) $user->organisation_id);
        } elseif ($createIfMissing) {
            $organisation = Organisations::query()->create([
                'owner_id' => (int) $user->id,
                'quote_defaults' => [
                    'global' => config('quotes.form_defaults.global', []),
                    'modules' => [],
                ],
            ]);

            $user->update([
                'organisation_id' => (int) $organisation->id,
                'organisation_role' => $user->organisation_role ?: 'owner',
            ]);
        }

        $this->canManage = (bool) ($organisation
            && (((string) $user->organisation_role) === 'owner'
                || ((int) $organisation->owner_id === (int) $user->id)));

        return $organisation;
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Quote Defaults')" :subheading="__('Set organisation-wide and per-module default values for the quote form')">
        @unless ($canManage)
            <flux:callout variant="warning" icon="exclamation-triangle">
                {{ __('Only organisation owners can update quote defaults.') }}
            </flux:callout>
        @endunless

        <form wire:submit="saveGlobalDefaults" class="my-6 w-full space-y-4">
            <flux:heading size="lg">{{ __('Business Defaults') }}</flux:heading>
            <flux:input wire:model="global_vat_rate" :label="__('Default VAT Rate (%)')" type="number" step="0.01" min="0" required />
            <flux:textarea wire:model="global_payment_terms" :label="__('Default Payment Terms')" rows="3" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" :disabled="! $canManage">
                    {{ __('Save Business Defaults') }}
                </flux:button>
            </div>
        </form>

        <flux:separator class="my-6" />

        <form wire:submit="saveModuleDefaults" class="w-full space-y-4">
            <flux:heading size="lg">{{ __('Module Defaults') }}</flux:heading>

            <div>
                <label class="hidden mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Module') }}</label>
                <select wire:model.live="selectedModule" class="hidden w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                    @foreach ($availableModules as $moduleSlug)
                        <option value="{{ $moduleSlug }}">{{ \Illuminate\Support\Str::title(str_replace('-', ' ', $moduleSlug)) }}</option>
                    @endforeach
                </select>
                <p class="font-bold">Fencing Module</p>
            </div>

            <flux:input wire:model="module_length" :label="__('Default Length (m)')" type="number" step="0.1" min="0.1" required />
            <flux:input wire:model="module_labour_rate" :label="__('Default Labour Rate')" type="number" step="0.01" min="0" required />
            <flux:input wire:model="module_waste" :label="__('Default Waste (%)')" type="number" step="0.01" min="0" required />

            <flux:checkbox wire:model.live="use_markup" :label="__('Use markup')" />

            @if ($use_markup)
                <flux:input wire:model="module_markup" :label="__('Default Markup (%)')" type="number" step="0.01" min="0" required />
            @endif

            @if (count($moduleItems) > 0)
            <div>
                <flux:heading size="sm" class="mb-1">{{ __('Item Prices') }}</flux:heading>
                <p class="mb-4 text-sm text-zinc-500 dark:text-zinc-400">
                    @if ($use_markup)
                        {{ __('Enter the cost price for each item. The sell price will be calculated using the markup percentage above.') }}
                    @else
                        {{ __('Default sell prices populated into the quote form.') }}
                    @endif
                </p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($moduleItems as $item)
                        <div @class([
                            'rounded-2xl border border-zinc-200/80 bg-zinc-50/60 p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900/40',
                            'sm:col-span-2' => $selectedModule === 'fencing' && $item['height_priced'],
                        ])>
                            @if ($selectedModule === 'fencing' && $item['type'] == 'labour')
                                <p class="mb-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                    {{ $item['name'] }}
                                    <span class="ml-1 text-xs font-normal text-zinc-400">(Charge per meter)</span>
                                </p>
                            @else
                                <p class="mb-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                    {{ $item['name'] }}
                                    <span class="ml-1 text-xs font-normal text-zinc-400">({{ $item['type'] }})</span>
                                </p>
                            @endif
                            @if ($selectedModule === 'fencing' && $item['height_priced'])
                                @php($priceField = $use_markup ? 'cost_prices_by_height' : 'sell_prices_by_height')

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                                    @foreach ($fenceHeightOptions as $heightKey => $heightLabel)
                                        <div class="rounded-xl border border-zinc-200/70 bg-white/80 p-3 dark:border-zinc-700 dark:bg-zinc-950/60">
                                            <label class="mb-1 block text-xs font-medium tracking-wide text-zinc-500 dark:text-zinc-400">{{ $heightLabel }}m fence height</label>
                                            <div class="relative">
                                                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-zinc-400 dark:text-zinc-500">£</span>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    wire:model.live="itemPrices.{{ $item['id'] }}.{{ $priceField }}.{{ $heightKey }}"
                                                    class="w-full rounded-xl border border-zinc-200 bg-white pl-7 pr-4 py-2.5 text-sm text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                                                    placeholder="0.00"
                                                >
                                            </div>
                                            @error("itemPrices.{$item['id']}.{$priceField}.{$heightKey}")
                                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex gap-4">
                                    @if ($use_markup)
                                        <div class="relative flex-1">
                                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-zinc-400 dark:text-zinc-500">£</span>
                                            <input
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                wire:model.live="itemPrices.{{ $item['id'] }}.cost_price"
                                                class="w-full rounded-xl border border-zinc-200 bg-white pl-7 pr-4 py-2.5 text-sm text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                                                placeholder="{{ __('Cost price (£)') }}"
                                            >
                                            @error("itemPrices.{$item['id']}.cost_price")
                                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @else
                                        <div class="relative flex-1">
                                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-zinc-400 dark:text-zinc-500">£</span>
                                            <input
                                                type="number"
                                                min="0"
                                                step="0.01"
                                                wire:model.live="itemPrices.{{ $item['id'] }}.sell_price"
                                                class="w-full rounded-xl border border-zinc-200 bg-white pl-7 pr-4 py-2.5 text-sm text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                                                placeholder="{{ __('Sell price (£)') }}"
                                            >
                                            @error("itemPrices.{$item['id']}.sell_price")
                                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="flex gap-3">
                <flux:button variant="primary" type="submit" class="w-full" :disabled="! $canManage">
                    {{ __('Save Module Defaults') }}
                </flux:button>
                <flux:button type="button" wire:click="clearModuleOverride" class="w-full" :disabled="! $canManage">
                    {{ __('Reset To Inherited') }}
                </flux:button>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
