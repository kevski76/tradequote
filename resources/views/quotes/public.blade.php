<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Quote{{ isset($quote) && $quote->customer_name ? ' for ' . $quote->customer_name : '' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-50 antialiased">

    <div class="mx-auto max-w-xl px-4 py-12">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-indigo-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            @if($quote->customer_name)
                <p class="text-sm font-medium text-zinc-500">Hi {{ $quote->customer_name }},</p>
            @endif
            <h1 class="mt-1 text-2xl font-bold text-zinc-900">Your Quote</h1>
            @if($quote->job_name)
                <p class="mt-1 text-sm text-zinc-500">{{ $quote->job_name }}</p>
            @endif
        </div>

        {{-- Breakdown card --}}
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-zinc-100">
            <div class="p-6">
                <h2 class="mb-4 text-xs font-semibold uppercase tracking-wide text-zinc-400">Breakdown</h2>

                <div class="space-y-2 text-sm">
                    @if(isset($breakdown['posts_qty']))
                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2">
                        <span class="text-zinc-600">Posts ({{ number_format($breakdown['posts_qty']) }})</span>
                        <span class="font-semibold text-zinc-900">&pound;{{ number_format($breakdown['posts_price'] ?? 0, 2) }}</span>
                    </div>
                    @endif

                    @if(isset($breakdown['boards_qty']))
                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2">
                        <span class="text-zinc-600">Boards / Panels ({{ number_format($breakdown['boards_qty']) }})</span>
                        <span class="font-semibold text-zinc-900">&pound;{{ number_format($breakdown['boards_price'] ?? 0, 2) }}</span>
                    </div>
                    @endif

                    @if(isset($breakdown['materials_cost']))
                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2">
                        <span class="text-zinc-600">Materials (inc. waste)</span>
                        <span class="font-semibold text-zinc-900">&pound;{{ number_format($breakdown['materials_cost'], 2) }}</span>
                    </div>
                    @endif

                    @if(isset($breakdown['labour_cost']))
                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2">
                        <span class="text-zinc-600">Labour</span>
                        <span class="font-semibold text-zinc-900">&pound;{{ number_format($breakdown['labour_cost'], 2) }}</span>
                    </div>
                    @endif

                    @if(isset($breakdown['vat_amount']))
                    <div class="flex items-center justify-between rounded-lg bg-zinc-50 px-3 py-2">
                        <span class="text-zinc-600">VAT ({{ number_format($breakdown['vat_rate'] ?? 20, 1) }}%)</span>
                        <span class="font-semibold text-zinc-900">&pound;{{ number_format($breakdown['vat_amount'], 2) }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Total --}}
            <div class="rounded-b-2xl bg-indigo-600 px-6 py-5">
                <p class="text-sm font-medium text-indigo-200">Total Price</p>
                <p class="mt-1 text-4xl font-bold tracking-tight text-white">
                    &pound;{{ number_format($quote->total_price, 2) }}
                </p>
            </div>
        </div>

        @if($quote->payment_terms)
        <div class="mt-6 rounded-xl bg-white p-5 ring-1 ring-zinc-100">
            <h3 class="mb-2 text-xs font-semibold uppercase tracking-wide text-zinc-400">Payment Terms</h3>
            <p class="text-sm text-zinc-600">{{ $quote->payment_terms }}</p>
        </div>
        @endif

        <p class="mt-8 text-center text-xs text-zinc-400">
            Quote valid for 14 days &middot; Generated {{ $quote->created_at?->format('j M Y') }}
        </p>

    </div>

</body>
</html>
