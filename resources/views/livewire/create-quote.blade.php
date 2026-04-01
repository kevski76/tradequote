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

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="customerName" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Customer Name (optional)</label>
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
                <label for="length" class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Fence Length (metres)</label>

                <div class="mt-5 flex flex-col items-center gap-3">
                    <input
                        id="length"
                        type="number"
                        min="0"
                        step="0.1"
                        wire:model.live.debounce.200ms="length"
                        class="w-full max-w-md rounded-xl border border-zinc-200 bg-white px-6 py-5 text-center text-3xl font-semibold text-zinc-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100"
                        placeholder="0"
                    >
                    @error('length') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </article>

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
                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-950/60">
                        <span class="text-zinc-600 dark:text-zinc-300">Posts ({{ number_format($breakdown['posts_qty']) }})</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">&pound;{{ number_format($breakdown['posts_price'], 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-950/60">
                        <span class="text-zinc-600 dark:text-zinc-300">Boards or Panels ({{ number_format($breakdown['boards_qty']) }})</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">&pound;{{ number_format($breakdown['boards_price'], 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-950/60">
                        <span class="text-zinc-600 dark:text-zinc-300">Labour Cost</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">&pound;{{ number_format($breakdown['labour_cost'], 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-950/60">
                        <span class="text-zinc-600 dark:text-zinc-300">Materials (inc. waste)</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">&pound;{{ number_format($breakdown['materials_cost'], 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2 dark:bg-zinc-950/60">
                        <span class="text-zinc-600 dark:text-zinc-300">VAT ({{ number_format($breakdown['vat_rate'], 1) }}%)</span>
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">&pound;{{ number_format($breakdown['vat_amount'], 2) }}</span>
                    </div>
                </div>
            </article>

            <article class="rounded-xl bg-indigo-600 p-6 text-white shadow-sm">
                <p class="text-sm font-medium text-indigo-100">Total Price</p>
                <p class="mt-2 text-4xl font-bold tracking-tight">&pound;{{ number_format($breakdown['total_price'], 2) }}</p>
            </article>

            <section class="grid grid-cols-1 gap-3 sm:grid-cols-3 xl:grid-cols-1">
                <button
                    type="button"
                    wire:click="saveQuote"
                    class="inline-flex items-center justify-center rounded-xl bg-zinc-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-800 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-100"
                >
                    Save Quote
                </button>

                <button
                    type="button"
                    wire:click="prepareWhatsApp"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-green-500 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-600 disabled:opacity-60"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.122 1.531 5.855L0 24l6.293-1.508A11.955 11.955 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.885 0-3.65-.502-5.18-1.381l-.371-.221-3.838.92.96-3.736-.242-.385A9.956 9.956 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                    </svg>
                    Send via WhatsApp
                </button>

                <button
                    type="button"
                    wire:click="downloadPdf"
                    class="inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-white px-5 py-3 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800"
                >
                    Download PDF
                </button>

                <button
                    type="button"
                    wire:click="saveTemplate"
                    class="inline-flex items-center justify-center rounded-xl border border-zinc-200 bg-white px-5 py-3 text-sm font-semibold text-zinc-700 shadow-sm transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800"
                >
                    Save as Template
                </button>
            </section>
        </div>
    </section>

    {{-- WhatsApp Modal --}}
    <template x-teleport="body">
        <div
            x-show="waOpen"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            x-on:keydown.escape.window="waOpen = false"
        >
            <div
                x-show="waOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                x-on:click.outside="waOpen = false"
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
