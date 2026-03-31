<div class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-4 py-6 sm:px-6 lg:px-10">
    <section class="space-y-2">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Create Quote</h1>
    </section>

    @if ($notice)
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
            {{ $notice }}
        </div>
    @endif

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
</div>
