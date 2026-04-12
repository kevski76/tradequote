<div>
    @if ($currentStatus === 'accepted')
        <div class="mt-6 rounded-xl bg-emerald-50 px-5 py-4 ring-1 ring-emerald-200">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 text-emerald-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-emerald-800">You have accepted this quote</p>
                    <p class="mt-0.5 text-xs text-emerald-600">The trade professional has been notified.</p>
                </div>
            </div>
        </div>

    @elseif ($currentStatus === 'declined')
        <div class="mt-6 rounded-xl bg-red-50 px-5 py-4 ring-1 ring-red-200">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm4.707-11.293a1 1 0 00-1.414 0L10 9.586 6.707 6.293a1 1 0 00-1.414 1.414L8.586 11l-3.293 3.293a1 1 0 101.414 1.414L10 12.414l3.293 3.293a1 1 0 001.414-1.414L11.414 11l3.293-3.293a1 1 0 000-1.414z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-red-800">You have declined this quote</p>
                    <p class="mt-0.5 text-xs text-red-600">The trade professional has been notified.</p>
                </div>
            </div>
        </div>

    @else
        <div class="mt-6 rounded-xl bg-white p-5 ring-1 ring-zinc-100">
            <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-zinc-400">Respond to this Quote</h3>

            @if ($showDeclineModal)
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-zinc-700">
                        Reason for rejection
                        <span class="ml-1 text-xs font-normal text-zinc-400">(optional)</span>
                    </label>
                    <textarea
                        wire:model="rejectionReason"
                        rows="3"
                        maxlength="500"
                        placeholder="Let the trade professional know why you're declining…"
                        class="block w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-zinc-900 placeholder-zinc-400 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    ></textarea>

                    <div class="flex items-center gap-3">
                        <button
                            wire:click="reject"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="reject">Confirm Decline</span>
                            <span wire:loading wire:target="reject">Sending…</span>
                        </button>
                        <button
                            wire:click="closeDeclineModal"
                            type="button"
                            class="text-sm font-medium text-zinc-500 hover:text-zinc-700"
                        >
                            Cancel
                        </button>
                    </div>

                    @error('rejectionReason')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @else
                <div class="flex flex-col gap-3 sm:flex-row">
                    <button
                        wire:click="accept"
                        wire:loading.attr="disabled"
                        type="button"
                        class="inline-flex flex-1 items-center justify-center rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-60"
                    >
                        <span wire:loading.remove wire:target="accept">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            Accept Quote
                        </span>
                        <span wire:loading wire:target="accept">Processing…</span>
                    </button>

                    <button
                        wire:click="openDeclineModal"
                        type="button"
                        class="inline-flex flex-1 items-center justify-center rounded-lg border border-red-200 bg-white px-5 py-3 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Decline Quote
                    </button>
                </div>
            @endif
        </div>
    @endif
</div>
