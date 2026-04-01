<?php

use App\Models\Organisations;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Business information')] class extends Component {
    public string $business_name = '';
    public string $address = '';
    public string $city = '';
    public string $postcode = '';
    public string $phone = '';

    public function mount(): void
    {
        $user = Auth::user();

        if ($user->organisation_id) {
            $organisation = Organisations::find($user->organisation_id);

            if ($organisation) {
                $this->business_name = $organisation->name ?? '';
                $this->address       = $organisation->address ?? '';
                $this->city          = $organisation->city ?? '';
                $this->postcode      = $organisation->postcode ?? '';
                $this->phone         = $organisation->phone ?? '';
            }
        }
    }

    public function updateBusinessInformation(): void
    {
        $validated = $this->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'address'       => ['nullable', 'string', 'max:255'],
            'city'          => ['nullable', 'string', 'max:100'],
            'postcode'      => ['nullable', 'string', 'max:20'],
            'phone'         => ['required', 'string', 'regex:/^[\+]?[\d\s\-\(\)\.]{7,20}$/'],
        ]);

        $user = Auth::user();

        if ($user->organisation_id) {
            $organisation = Organisations::find($user->organisation_id);
        }

        if (empty($organisation)) {
            $organisation = Organisations::create([
                'owner_id' => $user->id,
            ]);

            $user->update(['organisation_id' => $organisation->id]);
        }

        $organisation->update([
            'name'     => $validated['business_name'],
            'address'  => $validated['address'] ?? null,
            'city'     => $validated['city'] ?? null,
            'postcode' => $validated['postcode'] ?? null,
            'phone'    => $validated['phone'],
        ]);

        $this->dispatch('business-updated');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-pages::settings.layout :heading="__('Business Information')" :subheading="__('Update your business details')">
        <form wire:submit="updateBusinessInformation" class="my-6 w-full space-y-6">
            <flux:input
                wire:model="business_name"
                :label="__('Business Name')"
                type="text"
                required
                autofocus
                autocomplete="organization"
            />

            <flux:input
                wire:model="address"
                :label="__('Address')"
                type="text"
                autocomplete="street-address"
            />

            <flux:input
                wire:model="city"
                :label="__('City / Town')"
                type="text"
                autocomplete="address-level2"
            />

            <flux:input
                wire:model="postcode"
                :label="__('Post Code')"
                type="text"
                autocomplete="postal-code"
            />

            <flux:input
                wire:model="phone"
                :label="__('Phone')"
                type="tel"
                required
                autocomplete="tel"
            />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="business-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
