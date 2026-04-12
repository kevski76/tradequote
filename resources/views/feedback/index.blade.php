<x-layouts::app :title="__('Private Feedback')">
    <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:px-10">
        <section class="space-y-1">
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">Private Feedback</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Messages submitted through the public review page.</p>
        </section>

        <section class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
            @if ($feedback->isEmpty())
                <div class="px-6 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    No private feedback submissions yet.
                </div>
            @else
                <table class="min-w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                    <thead class="bg-zinc-50 dark:bg-zinc-950/40">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Submitted</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Quote</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Customer</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-500 dark:text-zinc-400">Message</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach ($feedback as $item)
                            <tr>
                                <td class="px-4 py-4 text-zinc-600 dark:text-zinc-300">
                                    {{ $item->created_at?->format('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-4 text-zinc-700 dark:text-zinc-200">
                                    <div class="font-medium">#{{ $item->quote_id ?: 'N/A' }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $item->quote_uuid }}</div>
                                </td>
                                <td class="px-4 py-4 text-zinc-700 dark:text-zinc-200">
                                    <div>{{ $item->customer_name !== '' ? $item->customer_name : 'Not provided' }}</div>
                                    @if ($item->customer_email)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $item->customer_email }}</div>
                                    @endif
                                    @if ($item->customer_phone)
                                        <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $item->customer_phone }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-zinc-800 dark:text-zinc-100">
                                    <p class="max-w-2xl whitespace-pre-line">{{ $item->message }}</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        @if ($feedback->hasPages())
            <section>
                {{ $feedback->links() }}
            </section>
        @endif
    </div>
</x-layouts::app>
