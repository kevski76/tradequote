<div class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-4 py-6 sm:px-6 lg:px-10">
    <section class="space-y-2">
        <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Dashboard</h1>
        <p class="text-sm text-zinc-600 dark:text-zinc-400">Quickly create and manage your quotes</p>
    </section>

    @if ($notice)
        <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-700 dark:border-indigo-500/40 dark:bg-indigo-500/10 dark:text-indigo-200">
            {{ $notice }}
        </div>
    @endif

    <section>
        <a
            href="{{ route('quotes.create') }}"
            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-8 py-4 text-base font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900"
        >
            Create New Quote 
        </a>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <article class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
            <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Quotes This Week</p>
            <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($quotesThisWeek) }}</p>
        </article>

        <article class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
            <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Total Value Quoted (&pound;)</p>
            <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">&pound;{{ number_format($totalValueQuoted, 0) }}</p>
        </article>

        <article class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
            <p class="text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Accepted Jobs</p>
            <p class="mt-2 text-3xl font-semibold text-zinc-900 dark:text-zinc-100">{{ number_format($acceptedJobs) }}</p>
        </article>
    </section>

    <section class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Recent Quotes</h2>
            <span class="text-xs text-zinc-500 dark:text-zinc-400">Showing latest {{ $recentQuoteRows->count() }}</span>
        </div>

        @if ($recentQuoteRows->isEmpty())
            <div class="rounded-xl bg-white p-8 text-center shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
                <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">Create your first quote in under 60 seconds</p>
                <a
                    href="{{ route('quotes.create') }}"
                    class="mt-4 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                >
                    Create New Quote
                </a>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
                <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                    <thead class="bg-zinc-50 dark:bg-zinc-950/40">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Job Name / Customer</th>
                            <th scope="col" class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Total Price</th>
                            <th scope="col" class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Status</th>
                            <th scope="col" class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Date</th>
                            <th scope="col" class="px-4 py-3 text-right font-medium text-zinc-500 dark:text-zinc-400">PDF</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach ($recentQuoteRows as $quote)
                            <tr
                                wire:click="openQuote({{ $quote['id'] }})"
                                class="cursor-pointer transition hover:bg-zinc-50 dark:hover:bg-zinc-800/60"
                            >
                                <td class="px-4 py-4">
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $quote['job_name'] }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $quote['customer_name'] }}</p>
                                </td>
                                <td class="px-4 py-4 font-medium text-zinc-900 dark:text-zinc-100">&pound;{{ number_format($quote['total_price'], 0) }}</td>
                                <td class="px-4 py-4">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-zinc-100 text-zinc-700 dark:bg-zinc-700/60 dark:text-zinc-100' => $quote['status'] === 'draft',
                                        'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' => $quote['status'] === 'sent',
                                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' => $quote['status'] === 'accepted',
                                    ])>
                                        {{ ucfirst($quote['status']) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-zinc-600 dark:text-zinc-300">{{ optional($quote['created_at'])->format('d M Y') }}</td>
                                <td class="px-4 py-4 text-right">
                                    <a
                                        href="{{ route('quotes.pdf', ['quote' => $quote['id']]) }}"
                                        x-on:click.stop
                                        class="inline-flex items-center justify-center rounded-lg border border-zinc-200 px-3 py-1.5 text-xs font-medium text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
                                    >
                                        {{ $quote['has_pdf_snapshot'] ? 'View PDF' : 'Generate PDF' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    <section class="space-y-4">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Saved Templates</h2>

        @if ($templates->isEmpty())
            <div class="rounded-xl bg-white p-5 text-sm text-zinc-600 shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:text-zinc-300 dark:ring-zinc-800">
                No templates saved yet.
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($templates as $template)
                    <article class="flex flex-col justify-between rounded-xl bg-white p-5 shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
                        <div>
                            <p class="text-base font-semibold text-zinc-900 dark:text-zinc-100">{{ $template->name ?: 'Untitled Template' }}</p>
                            @if ($template->variant_key)
                                <p class="mt-1 text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ str_replace('_', ' ', $template->variant_key) }}</p>
                            @endif
                        </div>

                        <button
                            type="button"
                            wire:click="useTemplate({{ $template->id }})"
                            class="mt-4 inline-flex items-center justify-center rounded-xl border border-zinc-200 px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800"
                        >
                            Use Template
                        </button>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    @if (! $isPro)
        <section x-data="{ open: true }" x-show="open" class="rounded-xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm dark:border-indigo-500/30 dark:bg-indigo-500/10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm font-medium text-indigo-900 dark:text-indigo-100">Add your logo to quotes</p>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        wire:click="createQuote"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500"
                    >
                        Upgrade to Pro
                    </button>
                    <button
                        type="button"
                        x-on:click="open = false"
                        class="rounded-lg px-2 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100 dark:text-indigo-200 dark:hover:bg-indigo-500/20"
                    >
                        Dismiss
                    </button>
                </div>
            </div>
        </section>
    @endif
</div>
