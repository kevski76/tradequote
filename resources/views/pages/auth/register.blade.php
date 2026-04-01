<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Your Name *required')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Your Email Address *required')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Business Name -->
            <flux:input
                name="business_name"
                :label="__('Business Name *required')"
                :value="old('business_name')"
                type="text"
                required
                autofocus
                autocomplete="business_name"
                :placeholder="__('Business Name')"
            />

            <!-- Address -->
            <flux:input
                name="address"
                :label="__('Business Address (optional)')"
                :value="old('address')"
                type="text"
                autofocus
                autocomplete="address"
                :placeholder="__('Business Address')"
            />

            <!-- City -->
            <flux:input
                name="city"
                :label="__('City/Town (optional)')"
                :value="old('city')"
                type="text"
                autofocus
                autocomplete="city"
                :placeholder="__('City')"
            />

            <!-- Post Code -->
            <flux:input
                name="postcode"
                :label="__('Post Code (optional)')"
                :value="old('postcode')"
                type="text"
                autofocus
                autocomplete="postcode"
                :placeholder="__('Postcode')"
            />

            <!-- Phone -->
            <flux:input
                name="phone"
                :label="__('Business Phone *required')"
                :value="old('phone')"
                type="text"
                required
                autofocus
                autocomplete="phone"
                :placeholder="__('Business phone')"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password *required')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password *required')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
