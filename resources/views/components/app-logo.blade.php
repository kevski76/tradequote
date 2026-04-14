@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="FlashQuote" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-emerald-600 text-accent-foreground">
            <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" /> 
        </x-slot>
    </flux:sidebar.brand> <span class="text-xs font-thin text-slate-700">Beta</span>
@else
    <flux:brand name="FlashQuote" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-emerald-600 text-accent-foreground">
            <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
        </x-slot>
    </flux:brand>
@endif
