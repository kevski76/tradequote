<div class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-4 py-6 sm:px-6 lg:px-10"
    x-data="{
        waOpen: false,
        waPhone: '+44',
        waMessage: '',
        openModal(phone, message) {  
            this.waPhone = phone;
            this.waMessage = message;
            this.waOpen = true;
        },
        send() {
            let phone = this.waPhone.replace(/\s+/g, '');
            if (phone.startsWith('0')) {
                phone = '+44' + phone.slice(1);
            }
            phone = phone.replace(/[^\d+]/g, '');
            const encoded = encodeURIComponent(this.waMessage);
            window.open('https://wa.me/' + phone + '?text=' + encoded, '_blank');
            this.waOpen = false;
        }
    }"
    x-on:open-whatsapp-modal.window="openModal($wire.whatsappPhone, $wire.whatsappMessage)"
>
    <section class="space-y-2">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Create Quote</h1>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-12">
        <div class="space-y-6 xl:col-span-7">
            <article class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Job Details</h2>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="customerName" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Customer Name</label>
                        <input
                            id="customerName"
                            type="text"
                            wire:model.live="customerName"
                            class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                            placeholder="e.g. Jamie Wilson"
                        >
                        @error('customerName') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="customerPhone" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Customer Phone (Mobile)</label>
                        <input
                            id="customerPhone"
                            type="tel"
                            wire:model.live="customerPhone"
                            class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                            placeholder="e.g. 07123456789"
                        >
                        @error('customerPhone') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="jobName" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Job Name</label>
                        <input
                            id="jobName"
                            type="text"
                            wire:model.live="jobName"
                            class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                            placeholder="Fence job - 15m"
                        >
                        @error('jobName') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </article>

            <article class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
                <label class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Fence Dimensions</label>

                <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="length" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Length (metres)</label>
                        <input
                            id="length"
                            type="number"
                            min="0"
                            step="0.1"
                            wire:model.live.debounce.200ms="length"
                            class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-6 py-5 text-center text-3xl font-semibold text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                            placeholder="0"
                        >
                        @error('length') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="height" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Height (metres)</label>
                        <select
                            id="height"
                            wire:model.live="height"
                            class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-5 text-center text-3xl font-semibold text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                        >
                            <option value="1.5">1.5</option>
                            <option value="1.8">1.8</option>
                            <option value="2.0">2.0</option>
                        </select>
                        @error('height') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Gate toggle + width input --}}
                <div class="mt-4" x-data="{ includeGate: {{ $gateWidth > 0 ? 'true' : 'false' }} }">
                    <button
                        type="button"
                        @click="includeGate = !includeGate; if (!includeGate) $wire.set('gateWidth', 0)"
                        class="inline-flex items-center gap-2.5 text-sm font-medium text-zinc-700 dark:text-zinc-300"
                    >
                        <span
                            :class="includeGate ? 'bg-indigo-600' : 'bg-zinc-200 dark:bg-zinc-700'"
                            class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
                        >
                            <span
                                :class="includeGate ? 'translate-x-4' : 'translate-x-0'"
                                class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                            ></span>
                        </span>
                        Include Gate(s)
                    </button>

                    <div x-show="includeGate" x-transition class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach($gates as $index => $gate)
                            <div>
                                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Gate width (metres)</label>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.1"
                                    wire:model.live="gates.{{ $index }}.width"
                                    class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                                >
                            </div>
                            <div>
                                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Gate price (£)</label>
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    wire:model.live="gates.{{ $index }}.price"
                                    class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                                >                              
                            </div>
                            <div class="col-span-2 text-right">
                                <button wire:click="removeGate({{ $index }})" class="text-xs font-medium text-slate-600 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">Remove Gate</button>
                            </div>
                        @endforeach
                        <div class="text-left col-span-2">
                            <button wire:click="addGate" class="text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">+ Add Gate</button>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Fencing type toggle: Panels vs Boards (fencing module only) --}}
            @if ($moduleSlug === 'fencing')
                <article class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Fencing Type</h2>
                    <div class="mt-4 grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            wire:click="$set('fencingType', 'panels')"
                            class="rounded-xl border px-4 py-3 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-indigo-500/40
                                {{ $fencingType === 'panels'
                                    ? 'border-indigo-500 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-500'
                                    : 'border-zinc-200 bg-white text-zinc-600 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                        >
                            Fence Panels
                        </button>
                        <button
                            type="button"
                            wire:click="$set('fencingType', 'boards')"
                            class="rounded-xl border px-4 py-3 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-indigo-500/40
                                {{ $fencingType === 'boards'
                                    ? 'border-indigo-500 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-500'
                                    : 'border-zinc-200 bg-white text-zinc-600 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800' }}"
                        >
                            Fence Boards
                        </button>
                    </div>
                </article>
            @endif
            @if (count($formItems) > 0)
                @php
                    $excludedItemKey = $moduleSlug === 'fencing'
                        ? ($fencingType === 'panels' ? 'boards' : 'panels')
                        : null;
                @endphp
                <article class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Item Prices</h2>

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($formItems as $item)
                            @if ($excludedItemKey === null || $item['key'] !== $excludedItemKey)
                                @php
                                    // $item['enabled'] is false for optional items (from QuoteFormBuilder).
                                    // $itemInputs[key]['enabled'] holds the user's current toggle state.
                                    $isOptional   = ! $item['enabled'];
                                    $itemEnabled  = (bool) ($itemInputs[$item['key']]['enabled'] ?? $item['enabled']);
                                @endphp
                                @if($item['key'] != 'gate' && $item['key'] != 'labour')
                                    @if ($isOptional && ! $itemEnabled)
                                        {{-- Optional item not yet added — show a dashed placeholder with Add button --}}
                                        <div class="flex items-center justify-between rounded-xl border border-dashed border-zinc-200 px-4 py-3 dark:border-zinc-700">
                                            <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $item['name'] }}</span>
                                            <button
                                                type="button"
                                                wire:click="$set('itemInputs.{{ $item['key'] }}.enabled', true)"
                                                class="text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                                            >+ Add</button>
                                        </div>
                                    @else
                                        <div>
                                            <div class="flex items-center justify-between">
                                                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                                    {{ $item['name'] }}
                                                    <span class="ml-1 text-xs font-normal text-zinc-400">({{ $item['type'] }})</span>
                                                </label>
                                                @if ($isOptional)
                                                <button
                                                    type="button"
                                                    wire:click="$set('itemInputs.{{ $item['key'] }}.enabled', false)"
                                                    class="text-xs text-zinc-400 hover:text-red-500 dark:hover:text-red-400"
                                                >Remove</button>
                                                @endif
                                            </div>
                                            <div class="mt-2 flex gap-2">
                                                <div class="relative flex-1">
                                                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-zinc-400 dark:text-zinc-500">£</span>
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        step="0.01"
                                                        wire:model.live="itemInputs.{{ $item['key'] }}.price"
                                                        class="w-full rounded-xl border border-zinc-200 bg-white pl-7 pr-4 py-2.5 text-sm text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                                                        placeholder="Unit price"
                                                    >
                                                </div>
                                                @if ($item['calculation'] === 'formula')
                                                <div class="w-28">
                                                    <input
                                                        type="number"
                                                        min="0"
                                                        step="1"
                                                        wire:model.live="itemInputs.{{ $item['key'] }}.quantity"
                                                        class="w-full rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                                                        placeholder="Qty (auto)"
                                                    >
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        @endforeach
                    </div>
                </article>
            @endif

            <article x-data="{ open: false }" class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
                <button
                    type="button"
                    x-on:click="open = !open"
                    class="flex w-full items-center justify-between text-left"
                >
                    <span class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Advanced Settings</span>
                    <span class="text-xs font-medium text-indigo-600 dark:text-indigo-300" x-text="open ? 'Hide' : 'Show'"></span>
                </button>

                <div x-show="open" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="labourRate" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Labour rate (&pound;/m)</label>
                        <input
                            id="labourRate"
                            type="number"
                            min="0"
                            step="0.1"
                            wire:model.live="labourRate"
                            class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                        >
                        @error('labourRate') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="labourTotalOverride" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Labour total override (&pound;)</label>
                        <input
                            id="labourTotalOverride"
                            type="number"
                            min="0"
                            step="0.01"
                            wire:model.live="labourTotalOverride"
                            class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                            placeholder="Leave blank for automatic"
                        >
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Set a fixed labour total. Clear this field to return to automatic labour calculation.</p>
                        @error('labourTotalOverride') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="markup" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Markup (%)</label>
                        <input
                            id="markup"
                            type="number"
                            min="0"
                            step="0.1"
                            wire:model.live="markup"
                            class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                        >
                        @error('markup') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="waste" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Waste (%)</label>
                        <input
                            id="waste"
                            type="number"
                            min="0"
                            step="0.1"
                            wire:model.live="waste"
                            class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                        >
                        @error('waste') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="vatRate" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">VAT (%)</label>
                        <input
                            id="vatRate"
                            type="number"
                            min="0"
                            step="0.1"
                            wire:model.live="vatRate"
                            class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                        >
                        @error('vatRate') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="paymentTerms" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Payment terms (optional)</label>
                        <textarea
                            id="paymentTerms"
                            rows="3"
                            wire:model.live="paymentTerms"
                            class="mt-2 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                            placeholder="e.g. 50% deposit upfront, balance due within 7 days of completion"
                        ></textarea>
                        @error('paymentTerms') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </article>
        </div>

        <div class="space-y-6 xl:col-span-5">
            <article class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Live Calculation</h2>

                <div class="mt-4 space-y-3 text-sm">
                    {{-- Material items --}}
                    @php
                        $formulaMaterialKeys = collect($formItems)
                            ->filter(fn ($i) => ($i['type'] ?? '') === 'material' && ($i['key'] ?? '') !== 'gate' && ($i['calculation'] ?? '') !== 'direct')
                            ->pluck('key')
                            ->all();
                    @endphp

                    @php $materialItems = array_filter($breakdown['items'] ?? [], fn($i) => ($i['type'] ?? '') === 'material'); @endphp
                    @if (count($materialItems) > 0)
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-400 dark:text-zinc-500">Materials</p>
                        @foreach ($materialItems as $item)
                            <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-950/60">
                                @if($item['key'] === 'gate')
                                    <span class="text-zinc-600 dark:text-zinc-300">
                                        {{ $item['name'] }} ({{ count($gates) }})
                                    </span>
                                @elseif (in_array($item['key'], $formulaMaterialKeys, true))
                                    <span class="text-zinc-600 dark:text-zinc-300" x-data="{ editingQty: false }">
                                        <button
                                            type="button"
                                            x-show="!editingQty"
                                            x-on:click="editingQty = true; $nextTick(() => $refs.qtyInput.focus())"
                                            class="cursor-pointer underline decoration-zinc-800/20 underline-offset-4 hover:text-zinc-800 dark:hover:text-zinc-100"
                                        >
                                            {{ $item['name'] }} ({{ number_format($item['quantity']) }})
                                        </button>
                                        <span x-show="editingQty" class="inline-flex items-center gap-2">
                                            <span>{{ $item['name'] }}</span>
                                            <input
                                                x-ref="qtyInput"
                                                type="number"
                                                min="0"
                                                step="1"
                                                wire:model.live.debounce.200ms="itemInputs.{{ $item['key'] }}.quantity"
                                                x-on:blur="editingQty = false"
                                                x-on:keydown.enter.prevent="$event.target.blur()"
                                                x-on:keydown.escape.prevent="editingQty = false; $event.target.blur()"
                                                class="w-20 rounded-lg border border-zinc-200 bg-white px-2 py-1 text-xs text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                                            >
                                        </span>
                                    </span>
                                @else
                                    <span class="text-zinc-600 dark:text-zinc-300">{{ $item['name'] }} ({{ number_format($item['quantity']) }})</span>
                                @endif
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100">&pound;{{ number_format($item['total'], 2) }}</span>
                            </div>
                        @endforeach
                    @endif

                    {{-- Labour items --}}
                    @php $labourItems = array_filter($breakdown['items'] ?? [], fn($i) => ($i['type'] ?? '') === 'labour'); @endphp
                    @if (count($labourItems) > 0)
                        <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-zinc-400 dark:text-zinc-500">Labour</p>
                        @foreach ($labourItems as $item)
                            <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-950/60">
                                <span class="text-zinc-600 dark:text-zinc-300">{{ $item['name'] }} ({{ number_format($item['quantity'], 1) }}m)</span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-100">&pound;{{ number_format($item['total'], 2) }}</span>
                            </div>
                        @endforeach
                    @endif

                    {{-- Summary rows --}}
                    <div class="mt-2 border-t border-zinc-100 pt-3 dark:border-zinc-800"></div>

                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-950/60">
                        <span class="text-zinc-600 dark:text-zinc-300">Materials (inc. waste)</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">&pound;{{ number_format($breakdown['materials_with_waste'], 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-950/60">
                        <span class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300">
                            Labour
                            @if ($labourTotalOverride !== '')
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-800 dark:bg-amber-900/40 dark:text-amber-200">Override</span>
                            @endif
                        </span>
                        <div class="flex items-center gap-2" x-data="{ editingLabour: false }">
                            <button
                                type="button"
                                x-show="!editingLabour"
                                x-on:click="editingLabour = true; $nextTick(() => $refs.labourOverrideInput.focus())"
                                class="cursor-pointer font-semibold underline decoration-zinc-800/20 underline-offset-4 text-zinc-900 dark:text-zinc-100"
                            >
                                &pound;{{ number_format($breakdown['labour_total'], 2) }}
                            </button>
                            <div x-show="editingLabour" class="inline-flex items-center gap-1">
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">&pound;</span>
                                <input
                                    x-ref="labourOverrideInput"
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    wire:model.live.debounce.200ms="labourTotalOverride"
                                    placeholder="Auto"
                                    x-on:blur="editingLabour = false"
                                    x-on:keydown.enter.prevent="$event.target.blur()"
                                    x-on:keydown.escape.prevent="editingLabour = false; $event.target.blur()"
                                    class="w-24 rounded-lg border border-zinc-200 bg-white px-2 py-1 text-xs text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                                >
                            </div>
                            @if ($labourTotalOverride !== '')
                                <button
                                    type="button"
                                    wire:click="$set('labourTotalOverride', '')"
                                    class="text-xs font-medium text-zinc-500 underline underline-offset-4 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200"
                                >
                                    Auto
                                </button>
                            @endif
                        </div>
                    </div>

                    @if (($breakdown['markup_amount'] ?? 0) > 0)
                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-950/60">
                        <span class="text-zinc-600 dark:text-zinc-300">Markup ({{ number_format($breakdown['markup'], 1) }}%)</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">&pound;{{ number_format($breakdown['markup_amount'], 2) }}</span>
                    </div>
                    @endif

                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-950/60">
                        <span class="text-zinc-600 dark:text-zinc-300">VAT ({{ number_format($breakdown['vat_rate'], 1) }}%)</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">&pound;{{ number_format($breakdown['vat'], 2) }}</span>
                    </div>
                </div>
            </article>

            <article class="rounded-xl bg-indigo-600 p-6 text-white shadow-sm">
                <p class="text-sm font-medium text-indigo-100">Total Price</p>
                <p class="mt-2 text-4xl font-bold tracking-tight">&pound;{{ number_format($breakdown['total'], 2) }}</p>
            </article>

            <section class="grid grid-cols-1 gap-3 sm:grid-cols-3 xl:grid-cols-1">
                {{-- Save Quote --}}
                <button
                    type="button"
                    wire:click="saveQuote"
                    wire:loading.attr="disabled"
                    wire:target="saveQuote"
                    wire:loading.class="opacity-75 cursor-not-allowed"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-100"
                >
                    <svg wire:loading wire:target="saveQuote" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="saveQuote">Save Quote</span>
                    <span wire:loading wire:target="saveQuote">Saving...</span>
                </button>

                {{-- Send via WhatsApp --}}
                <button
                    type="button"
                    wire:click="prepareWhatsApp"
                    wire:loading.attr="disabled"
                    wire:target="prepareWhatsApp"
                    wire:loading.class="opacity-75 cursor-not-allowed"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-green-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-600 disabled:opacity-60"
                >
                    <svg wire:loading.remove wire:target="prepareWhatsApp" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.531 5.855L0 24l6.293-1.508A11.955 11.955 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.65-.502-5.18-1.381l-.371-.221-3.838.92.96-3.736-.242-.385A9.956 9.956 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                    </svg>
                    <svg wire:loading wire:target="prepareWhatsApp" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="prepareWhatsApp">Send via WhatsApp</span>
                    <span wire:loading wire:target="prepareWhatsApp">Preparing...</span>
                </button>

                {{-- Download PDF --}}
                <button
                    type="button"
                    wire:click="downloadPdf"
                    wire:loading.attr="disabled"
                    wire:target="downloadPdf"
                    wire:loading.class="opacity-75 cursor-not-allowed"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white px-5 py-3 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800"
                >
                    <svg wire:loading wire:target="downloadPdf" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="downloadPdf">Download PDF</span>
                    <span wire:loading wire:target="downloadPdf">Generating PDF...</span>
                </button>

                {{-- Save as Template --}}
                <button
                    type="button"
                    wire:click="saveTemplate"
                    wire:loading.attr="disabled"
                    wire:target="saveTemplate"
                    wire:loading.class="opacity-75 cursor-not-allowed"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white px-5 py-3 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800"
                >
                    <svg wire:loading wire:target="saveTemplate" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="saveTemplate">Save as Template</span>
                    <span wire:loading wire:target="saveTemplate">Saving...</span>
                </button>
            </section>
        </div>
    </section>

    {{-- WhatsApp Modal --}}
    <template x-teleport="body">
        <div
            x-show="waOpen"
            x-transition.opacity.duration.150ms
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            x-on:keydown.escape.window="waOpen = false"
            x-on:click.self="waOpen = false"
        >
            <div
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-on:click.stop
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl dark:bg-zinc-900"
            >
                <div class="mb-5 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.531 5.855L0 24l6.293-1.508A11.955 11.955 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.65-.502-5.18-1.381l-.371-.221-3.838.92.96-3.736-.242-.385A9.956 9.956 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                            </svg>
                        </span>
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Send via WhatsApp</h3>
                    </div>
                    <button type="button" x-on:click="waOpen = false" class="rounded-lg p-1 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Customer Name</label>
                        <input
                            type="text"
                            wire:model.live="customerName"
                            class="mt-1.5 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                            placeholder="Jamie Wilson"
                        >
                    </div>

                    <div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Phone Number</label>
                        <input
                            type="tel"
                            x-model="waPhone"
                            class="mt-1.5 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                            placeholder="+44 7700 900000"
                        >
                    </div>

                    <div>
                        <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Message Preview</label>
                        <textarea
                            x-model="waMessage"
                            rows="8"
                            class="mt-1.5 w-full rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                        ></textarea>
                    </div>
                </div>

                <div class="mt-5 flex gap-3">
                    <button
                        type="button"
                        x-on:click="send()"
                        class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-green-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                            <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.531 5.855L0 24l6.293-1.508A11.955 11.955 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.65-.502-5.18-1.381l-.371-.221-3.838.92.96-3.736-.242-.385A9.956 9.956 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                        </svg>
                        Open WhatsApp
                    </button>
                    <button
                        type="button"
                        x-on:click="waOpen = false"
                        class="rounded-xl border border-zinc-200 bg-white px-5 py-3 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
