<div
    id="earlyAccessForm"
    class="scroll-mt-36 relative"
    x-data="{ showModal: false }"
    @early-access-submitted.window="showModal = true"
>
    {{-- Card --}}
    <div class="bg-white p-8 md:p-12 rounded-[2rem] shadow-2xl relative overflow-hidden">

        {{-- Decorative background icon --}}
        <div class="absolute top-0 right-0 p-4 opacity-10" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-pen w-32 h-32 rotate-12 text-[#00684e]">
                <path d="M12.5 22H18a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v9.5"></path>
                <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                <path d="M13.378 15.626a1 1 0 1 0-3.004-3.004l-5.01 5.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z"></path>
            </svg>
        </div>

        @if ($submitted)
            {{-- Inline success state --}}
            <div class="relative z-10 text-center py-8 space-y-5">
                <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-extrabold text-slate-900">You're on the list!</h3>
                <p class="text-slate-500 leading-relaxed">Thanks for registering — we'll be in touch soon. Keep an eye on your inbox.</p>
            </div>
        @else
            {{-- Registration form --}}
            <form wire:submit="submit" class="space-y-6 relative z-10" novalidate>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-500" for="ea-name">Name</label>
                        <input
                            id="ea-name"
                            wire:model="name"
                            type="text"
                            placeholder="John Doe"
                            autocomplete="name"
                            class="w-full bg-slate-50 rounded-xl p-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all border @error('name') border-red-700 bg-red-50 @else border-transparent @enderror"
                        >
                        @error('name')
                            <p class="text-xs text-red-700 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-widest text-slate-500" for="ea-business">Business name</label>
                        <input
                            id="ea-business"
                            wire:model="business_name"
                            type="text"
                            placeholder="JD Fencing Ltd"
                            autocomplete="organization"
                            class="w-full bg-slate-50 rounded-xl p-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all border @error('business_name') border-red-700 bg-red-50 @else border-transparent @enderror"
                        >
                        @error('business_name')
                            <p class="text-xs text-red-700 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500" for="ea-email">Email</label>
                    <input
                        id="ea-email"
                        wire:model="email"
                        type="email"
                        placeholder="john@example.com"
                        autocomplete="email"
                        class="w-full bg-slate-50 rounded-xl p-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all border @error('email') border-red-700 bg-red-50 @else border-transparent @enderror"
                    >
                    @error('email')
                        <p class="text-xs text-red-700 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-widest text-slate-500" for="ea-quoting">How do you currently quote jobs? <span class="normal-case font-normal text-slate-400">(optional)</span></label>
                    <textarea
                        id="ea-quoting"
                        wire:model="quoting_method"
                        rows="3"
                        placeholder="Paper, Excel, or mental math..."
                        class="w-full bg-slate-50 border border-transparent rounded-xl p-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all resize-none"
                    ></textarea>
                    @error('quoting_method')
                        <p class="text-xs text-red-700 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-75 cursor-not-allowed scale-100"
                    class="w-full bg-gradient-to-r from-[#00684e] to-[#74f3c6] text-white py-5 rounded-xl font-bold text-lg shadow-xl hover:scale-[1.01] active:scale-95 transition-all"
                >
                    <span wire:loading.remove>Get early access</span>
                    <span wire:loading class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 12h-4z"></path>
                        </svg>
                        Sending...
                    </span>
                </button>

                <p class="text-center text-xs text-slate-400">
                    Limited to 5 spots, get in quick. No credit card required.
                </p>

            </form>
        @endif
    </div>

    {{-- Thank you modal --}}
    <div
        x-cloak
        x-show="showModal"
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-label="Registration successful"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="showModal = false"
            aria-hidden="true"
        ></div>

        {{-- Modal panel --}}
        <div
            class="relative bg-white rounded-3xl p-10 md:p-14 max-w-md w-full shadow-2xl text-center space-y-6"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-4"
        >
            <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <div class="space-y-3">
                <h3 class="text-3xl font-extrabold text-slate-900">You're on the list! 🎉</h3>
                <p class="text-lg text-slate-500 leading-relaxed">
                    Thanks for registering. We'll be in touch soon — keep an eye on your inbox.
                </p>
            </div>

            <button
                @click="showModal = false"
                class="w-full bg-gradient-to-r from-[#00684e] to-[#74f3c6] text-white py-4 rounded-xl font-bold text-base shadow-lg hover:scale-[1.01] active:scale-95 transition-all"
            >
                Done
            </button>
        </div>
    </div>
</div>
