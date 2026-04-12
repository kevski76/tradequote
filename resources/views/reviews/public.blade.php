<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How Was Your Experience?</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 dark:bg-zinc-800 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-xl items-center px-4 py-10 sm:px-6">
        <section class="w-full rounded-3xl bg-white/90 p-6 shadow-lg ring-1 ring-zinc-200 backdrop-blur sm:p-8">
            @if($quote->customer_name)
                <p class="text-sm font-medium text-zinc-500">Hi {{ $quote->customer_name }},</p>
            @endif

            <h1 class="mt-2 text-xl font-bold leading-tight text-zinc-900 sm:text-2xl">
                Thanks for choosing {{ $organisation->name }}
            </h1>
            <p class="mt-3 text-sm text-zinc-600 sm:text-base">We’d really appreciate a quick review</p>

            <div class="mt-8 space-y-4">
                @if($googleReviewUrl !== '')
                    <a
                        href="{{ $googleReviewUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-6 py-5 text-center text-lg font-semibold text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        Leave a Google Review
                    </a>
                @else
                    <div class="flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-6 py-5 text-center text-lg font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                        Leave a Google Review
                    </div>
                @endif
                <div class="w-full text-center">
                    <a href="{{ route('review.feedback', ['uuid' => $quote->uuid]) }}" class="text-center text-base text-zinc-800 hover:underline italic transition hover:text-zinc-700">
                        Had an issue? Please let us know
                    </a>
                </div>

            </div>

            <p class="mt-6 text-center text-xs text-zinc-500">No login required.</p>
        </section>
    </main>
</body>
</html>
