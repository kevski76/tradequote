<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Private Feedback</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 dark:bg-zinc-800 antialiased">
    <main class="mx-auto flex min-h-screen w-full max-w-xl items-center px-4 py-8 sm:px-6">
        <section class="w-full rounded-3xl bg-white/90 p-6 shadow-lg ring-1 ring-zinc-200 backdrop-blur sm:p-8">
            <h1 class="text-2xl font-bold text-zinc-900 sm:text-3xl">Send private feedback</h1>
            <p class="mt-2 text-sm text-zinc-600">Your message goes directly to our team.</p>

            @if(session('feedback_submitted'))
                <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    Thank you. Your feedback has been sent.
                </div>
            @endif

            @if($errors->has('message'))
                <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
                    {{ $errors->first('message') }}
                </div>
            @endif

            <form method="POST" action="{{ route('review.feedback.submit', ['uuid' => $quote->uuid]) }}" class="mt-6 space-y-4">
                @csrf

                <input
                    type="text"
                    name="website"
                    tabindex="-1"
                    autocomplete="off"
                    class="pointer-events-none absolute -left-2500 opacity-0"
                    aria-hidden="true"
                >

                <div>
                    <label for="name" class="text-sm font-medium text-zinc-700">Name (optional)</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $quote->customer_name) }}"
                        class="mt-1.5 w-full rounded-xl border border-zinc-200 px-4 py-3 text-base text-zinc-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                    >
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="text-sm font-medium text-zinc-700">Email (optional)</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        class="mt-1.5 w-full rounded-xl border border-zinc-200 px-4 py-3 text-base text-zinc-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                    >
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="text-sm font-medium text-zinc-700">Phone (optional)</label>
                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        value="{{ old('phone', $quote->customer_phone) }}"
                        class="mt-1.5 w-full rounded-xl border border-zinc-200 px-4 py-3 text-base text-zinc-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                    >
                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="message" class="text-sm font-medium text-zinc-700">Your feedback</label>
                    <textarea
                        id="message"
                        name="message"
                        rows="5"
                        required
                        class="mt-1.5 w-full rounded-xl border border-zinc-200 px-4 py-3 text-base text-zinc-900 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                    >{{ old('message') }}</textarea>
                    @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <button
                    type="submit"
                    class="mt-2 flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-6 py-5 text-center text-lg font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                >
                    Send private feedback
                </button>
            </form>

            <a
                href="{{ route('review.public', ['uuid' => $quote->uuid]) }}"
                class="mt-4 block text-center text-sm font-medium text-zinc-600 hover:text-zinc-900"
            >
                Back
            </a>

            <p class="mt-6 text-center text-xs text-zinc-500">No login required.</p>
        </section>
    </main>
</body>
</html>
