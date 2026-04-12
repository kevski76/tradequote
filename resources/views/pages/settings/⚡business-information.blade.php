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
    public string $google_review_url = '';
    public string $feedback_notification_email = '';

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

                $defaults = is_array($organisation->quote_defaults) ? $organisation->quote_defaults : [];
                $globalDefaults = is_array($defaults['global'] ?? null) ? $defaults['global'] : [];
                $this->google_review_url = (string) ($globalDefaults['google_review_url'] ?? '');
                $this->feedback_notification_email = (string) ($globalDefaults['feedback_notification_email'] ?? '');
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
            'google_review_url' => ['nullable', 'url', 'max:2048'],
            'feedback_notification_email' => ['nullable', 'email', 'max:255'],
        ]);

        $user = Auth::user();

        if ($user->organisation_id) {
            $organisation = Organisations::find($user->organisation_id);
        }

        if (empty($organisation)) {
            $organisation = Organisations::create([
                'owner_id' => $user->id,
                'quote_defaults' => [
                    'global' => config('quotes.form_defaults.global', []),
                    'modules' => config('quotes.form_defaults.modules', []),
                ],
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

        $defaults = is_array($organisation->quote_defaults) ? $organisation->quote_defaults : [];
        $defaults['global'] = is_array($defaults['global'] ?? null) ? $defaults['global'] : [];
        $defaults['global']['google_review_url'] = trim((string) ($validated['google_review_url'] ?? ''));
        $defaults['global']['feedback_notification_email'] = trim((string) ($validated['feedback_notification_email'] ?? ''));

        $organisation->update([
            'quote_defaults' => $defaults,
        ]);

        $this->dispatch('toast', message: 'Business information saved.', type: 'success');
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

            <flux:input
                wire:model="google_review_url"
                :label="__('Google Review Link')"
                type="url"
                placeholder="https://g.page/r/.../review"
            />

            <flux:input
                wire:model="feedback_notification_email"
                :label="__('Private Feedback Email')"
                type="email"
                placeholder="team@yourbusiness.com"
            />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>
    </x-pages::settings.layout>
</section>
