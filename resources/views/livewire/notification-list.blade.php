<section class="rounded-xl bg-white shadow-sm ring-1 ring-zinc-100 dark:bg-zinc-900 dark:ring-zinc-800">
    <button
        wire:click="toggle"
        type="button"
        class="flex w-full items-center justify-between px-5 py-4 text-left"
    >
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-zinc-500 dark:text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">Notifications</span>
            @if ($unreadCount > 0)
                <span class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-bold text-white">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" @class(['h-4 w-4 text-zinc-400 transition-transform duration-200', 'rotate-180' => $expanded]) fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    @if ($expanded)
        <div class="border-t border-zinc-100 dark:border-zinc-800">
            @if ($notifications->isEmpty())
                <p class="px-5 py-6 text-center text-sm text-zinc-400 dark:text-zinc-500">No notifications yet.</p>
            @else
                @if (auth()->user()->unreadNotifications()->count() > 0)
                    <div class="flex justify-end px-5 py-2">
                        <button
                            wire:click="markAllRead"
                            type="button"
                            class="text-xs font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                        >
                            Mark all as read
                        </button>
                    </div>
                @endif

                <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($notifications as $notification)
                        @php
                            $data     = $notification->data;
                            $isUnread = is_null($notification->read_at);
                        @endphp
                        <li
                            wire:key="notif-{{ $notification->id }}"
                            wire:click="openQuote({{ $data['quote_id'] ?? 0 }})"
                            @class(['flex cursor-pointer items-start gap-3 px-5 py-3 transition hover:bg-zinc-50 dark:hover:bg-zinc-800', 'bg-indigo-50/70 dark:bg-indigo-500/10' => $isUnread])
                        >
                            <div @class([
                                'mt-0.5 flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full',
                                'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' => ($data['decision'] ?? '') === 'accepted',
                                'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-300' => ($data['decision'] ?? '') === 'declined',
                                'bg-zinc-100 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-300' => ! in_array($data['decision'] ?? '', ['accepted', 'declined']),
                            ])>
                                @if (($data['decision'] ?? '') === 'accepted')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="text-sm text-zinc-800 dark:text-zinc-200">
                                    <span class="font-medium">{{ $data['customer_name'] ?? 'A customer' }}</span>
                                    has <span class="font-medium">{{ $data['decision'] ?? 'responded to' }}</span>
                                    the quote for <span class="font-medium">{{ $data['job_name'] ?? 'your job' }}</span>.
                                </p>
                                @if (! empty($data['reason']))
                                    <p class="mt-1 text-xs italic text-zinc-500 dark:text-zinc-400">
                                        "{{ $data['reason'] }}"
                                    </p>
                                @endif
                                <p class="mt-0.5 text-xs text-zinc-400 dark:text-zinc-500">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>

                            @if ($isUnread)
                                <button
                                    wire:click="markRead('{{ $notification->id }}')"
                                    @click.stop
                                    type="button"
                                    class="flex-shrink-0 text-base font-medium leading-none text-zinc-300 hover:text-zinc-500 dark:text-zinc-600 dark:hover:text-zinc-300"
                                    title="Mark as read"
                                >
                                    &times;
                                </button>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif
</section>
