<?php

use App\Models\Modules;
use App\Models\Organisations;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Quote defaults')] class extends Component {
    public array $availableModules = [];

    public string $selectedModule = 'fencing';

    public bool $canManage = false;

    public string $global_length = '';
    public string $global_labour_rate = '';
    public string $global_markup = '';
    public string $global_waste = '';
    public string $global_vat_rate = '';
    public string $global_payment_terms = '';
    public string $global_whatsapp_phone = '';

    public string $module_length = '';
    public string $module_labour_rate = '';
    public string $module_markup = '';
    public string $module_waste = '';
    public string $module_vat_rate = '';
    public string $module_payment_terms = '';
    public string $module_whatsapp_phone = '';

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
            'global_length' => ['required', 'numeric', 'min:0.1', 'max:10000'],
            'global_labour_rate' => ['required', 'numeric', 'min:0', 'max:10000'],
            'global_markup' => ['required', 'numeric', 'min:0', 'max:200'],
            'global_waste' => ['required', 'numeric', 'min:0', 'max:100'],
            'global_vat_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'global_payment_terms' => ['nullable', 'string', 'max:1000'],
            'global_whatsapp_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $organisation = $this->organisation(createIfMissing: true);

        if (! $organisation || ! $this->canManage) {
            $this->dispatch('toast', message: 'Only organisation owners can update quote defaults.', type: 'error');

            return;
        }

        $defaults = is_array($organisation->quote_defaults) ? $organisation->quote_defaults : [];
        $defaults['modules'] = is_array($defaults['modules'] ?? null) ? $defaults['modules'] : [];
        $defaults['global'] = [
            'length' => (float) $validated['global_length'],
            'labour_rate' => (float) $validated['global_labour_rate'],
            'markup' => (float) $validated['global_markup'],
            'waste' => (float) $validated['global_waste'],
            'vat_rate' => (float) $validated['global_vat_rate'],
            'payment_terms' => trim((string) ($validated['global_payment_terms'] ?? '')),
            'whatsapp_phone' => trim((string) ($validated['global_whatsapp_phone'] ?? '')),
        ];

        $organisation->update(['quote_defaults' => $defaults]);

        $this->dispatch('toast', message: 'Global quote defaults saved.', type: 'success');
        $this->loadDefaultsIntoForm();
    }

    public function saveModuleDefaults(): void
    {
        $validated = $this->validate([
            'selectedModule' => ['required', 'string', 'alpha_dash', 'max:80'],
            'module_length' => ['required', 'numeric', 'min:0.1', 'max:10000'],
            'module_labour_rate' => ['required', 'numeric', 'min:0', 'max:10000'],
            'module_markup' => ['required', 'numeric', 'min:0', 'max:200'],
            'module_waste' => ['required', 'numeric', 'min:0', 'max:100'],
            'module_vat_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'module_payment_terms' => ['nullable', 'string', 'max:1000'],
            'module_whatsapp_phone' => ['nullable', 'string', 'max:50'],
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
            'length' => (float) $validated['module_length'],
            'labour_rate' => (float) $validated['module_labour_rate'],
            'markup' => (float) $validated['module_markup'],
            'waste' => (float) $validated['module_waste'],
            'vat_rate' => (float) $validated['module_vat_rate'],
            'payment_terms' => trim((string) ($validated['module_payment_terms'] ?? '')),
            'whatsapp_phone' => trim((string) ($validated['module_whatsapp_phone'] ?? '')),
        ];

        $organisation->update(['quote_defaults' => $defaults]);

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

        $this->global_length = (string) ($globalDefaults['length'] ?? 15);
        $this->global_labour_rate = (string) ($globalDefaults['labour_rate'] ?? 35);
        $this->global_markup = (string) ($globalDefaults['markup'] ?? 15);
        $this->global_waste = (string) ($globalDefaults['waste'] ?? 8);
        $this->global_vat_rate = (string) ($globalDefaults['vat_rate'] ?? 20);
        $this->global_payment_terms = (string) ($globalDefaults['payment_terms'] ?? '');
        $this->global_whatsapp_phone = (string) ($globalDefaults['whatsapp_phone'] ?? '+44');

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

        $this->module_length = (string) ($resolved['length'] ?? 15);
        $this->module_labour_rate = (string) ($resolved['labour_rate'] ?? 35);
        $this->module_markup = (string) ($resolved['markup'] ?? 15);
        $this->module_waste = (string) ($resolved['waste'] ?? 8);
        $this->module_vat_rate = (string) ($resolved['vat_rate'] ?? 20);
        $this->module_payment_terms = (string) ($resolved['payment_terms'] ?? '');
        $this->module_whatsapp_phone = (string) ($resolved['whatsapp_phone'] ?? '+44');
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
            <flux:heading size="lg">{{ __('Global Defaults') }}</flux:heading>

            <flux:input wire:model="global_length" :label="__('Default Length (m)')" type="number" step="0.1" min="0.1" required />
            <flux:input wire:model="global_labour_rate" :label="__('Default Labour Rate')" type="number" step="0.01" min="0" required />
            <flux:input wire:model="global_markup" :label="__('Default Markup (%)')" type="number" step="0.01" min="0" required />
            <flux:input wire:model="global_waste" :label="__('Default Waste (%)')" type="number" step="0.01" min="0" required />
            <flux:input wire:model="global_vat_rate" :label="__('Default VAT Rate (%)')" type="number" step="0.01" min="0" required />
            <flux:input wire:model="global_whatsapp_phone" :label="__('Default WhatsApp Prefix')" type="text" />
            <flux:textarea wire:model="global_payment_terms" :label="__('Default Payment Terms')" rows="3" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" :disabled="! $canManage">
                    {{ __('Save Global Defaults') }}
                </flux:button>
            </div>
        </form>

        <flux:separator class="my-6" />

        <form wire:submit="saveModuleDefaults" class="w-full space-y-4">
            <flux:heading size="lg">{{ __('Module Defaults') }}</flux:heading>

            <div>
                <label class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Module') }}</label>
                <select wire:model.live="selectedModule" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
                    @foreach ($availableModules as $moduleSlug)
                        <option value="{{ $moduleSlug }}">{{ \Illuminate\Support\Str::title(str_replace('-', ' ', $moduleSlug)) }}</option>
                    @endforeach
                </select>
            </div>

            <flux:input wire:model="module_length" :label="__('Default Length (m)')" type="number" step="0.1" min="0.1" required />
            <flux:input wire:model="module_labour_rate" :label="__('Default Labour Rate')" type="number" step="0.01" min="0" required />
            <flux:input wire:model="module_markup" :label="__('Default Markup (%)')" type="number" step="0.01" min="0" required />
            <flux:input wire:model="module_waste" :label="__('Default Waste (%)')" type="number" step="0.01" min="0" required />
            <flux:input wire:model="module_vat_rate" :label="__('Default VAT Rate (%)')" type="number" step="0.01" min="0" required />
            <flux:input wire:model="module_whatsapp_phone" :label="__('Default WhatsApp Prefix')" type="text" />
            <flux:textarea wire:model="module_payment_terms" :label="__('Default Payment Terms')" rows="3" />

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
