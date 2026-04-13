<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('Welcome') }} - {{ config('app.name', 'FlashQuote') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="stylesheet" href="//fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;family=Inter+Tight:wght@300;500;600&amp;display=swap" nonce="">

        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>
        
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Inter', sans-serif; }
            [x-cloak] { display: none !important; }
            .bg-emerald-gradient {
                background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            }
            .reveal {
                opacity: 0;
                transform: translateY(20px);
                transition: all 0.6s ease-out;
            }
            .reveal.active {
                opacity: 1;
                transform: translateY(0);
            }
        </style>
    </head>
    <body class="min-h-screen bg-[#f4f7f9] text-[#2b2f31] font-sans selection:bg-emerald-100 selection:text-emerald-900" x-data="{ scrolled: false, mobileMenu: false }" @scroll.window="scrolled = (window.pageYOffset > 20)">
        {{-- Navigation --}}
        <nav class="fixed top-0 w-full z-50 px-6 py-4">
            <div id="welcome-nav-shell" class="max-w-5xl mx-auto bg-white/80 backdrop-blur-md shadow-xl shadow-emerald-900/5 px-4 sm:px-6 py-3 rounded-4xl border border-white/20">
                <div class="flex justify-between items-center gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="lucide lucide-trending-up text-white w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                            </svg>
                        </div>
                        <a href="{{ url('/') }}">
                            <span class="text-xl font-bold tracking-tight text-slate-900">FlashQuote</span>
                        </a>
                    </div>
                
                    <div class="hidden md:flex gap-8 items-center">
                        <!--<a href="{{ url('/') }}/pricing" class="text-sm font-bold text-emerald-600 border-b-2 border-emerald-500 pb-0.5">Pricing</a>-->
                    </div>

                    <div class="flex items-center gap-3">
                        <button onclick="scrollToForm()" class="hidden md:block bg-gradient-to-r cursor-pointer from-[#00684e] to-[#74f3c6] text-white px-5 py-2 rounded-full text-sm font-bold hover:scale-[1.02] transition-transform active:scale-95 shadow-lg shadow-emerald-900/10">
                            Get Early Access
                        </button>
                        <button
                            id="welcome-mobile-menu-toggle"
                            type="button"
                            class="md:hidden inline-flex h-11 w-11 cursor-pointer items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition-colors hover:border-emerald-200 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            aria-expanded="false"
                            aria-controls="welcome-mobile-menu"
                            aria-label="Open navigation menu"
                        >
                            <svg id="welcome-mobile-menu-open-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true">
                                <line x1="4" x2="20" y1="12" y2="12"></line>
                                <line x1="4" x2="20" y1="6" y2="6"></line>
                                <line x1="4" x2="20" y1="18" y2="18"></line>
                            </svg>
                            <svg id="welcome-mobile-menu-close-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden h-5 w-5" aria-hidden="true">
                                <path d="M18 6 6 18"></path>
                                <path d="m6 6 12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="welcome-mobile-menu" class="hidden md:hidden pt-4">
                    <div class="rounded-3xl border border-emerald-100 bg-white px-5 py-5 shadow-lg shadow-emerald-900/5">
                        <div class="flex flex-col gap-4">
                            <a href="#" class="text-sm font-bold text-emerald-600" data-mobile-nav-link>Pricing</a>
                        </div>
                        <button onclick="scrollToForm()" class="mt-5 w-full bg-linear-to-r cursor-pointer from-[#00684e] to-[#74f3c6] cursor-pointer text-white px-5 py-3 rounded-full text-sm font-bold shadow-lg shadow-emerald-900/10">
                            Get Early Access
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <!-- Hero Section -->
            <section class="pt-40 pb-20 px-6">
                <div class="max-w-4xl mx-auto text-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#74f3c6]/30 text-primary text-xs font-bold mb-6 uppercase tracking-widest reveal">
                        Pricing built for trades.
                    </div>
                    <h1 class="text-5xl md:text-7xl font-extrabold tracking-tighter text-[#2b2f31] mb-6 leading-[0.95] reveal">
                        Simple Pricing That <span class="text-[#00684e] italic">Pays for Itself.</span>
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-600 max-w-2xl mx-auto mb-10 leading-tight reveal">
                        One job covers your monthly cost. No complex tiers, just everything you need to grow your business.
                    </p>
                </div>
            </section>

            <!-- Pricing Bento Grid -->
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-8 items-stretch">
                <!-- Main Plan Card -->
                <div class="md:col-span-7 lg:col-span-8 bg-white rounded-[2rem] p-8 md:p-12 shadow-xl shadow-primary/5 relative overflow-hidden flex flex-col justify-between group">
                    <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="rotate-12 group-hover:rotate-0 transition-transform duration-700 text-primary"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
                    </div>
                    
                    <div>
                        <h2 class="font-bold text-3xl mb-2">FlashQuote</h2>
                        <div class="flex items-baseline gap-2 mb-8">
                            <span class="text-5xl font-extrabold tracking-tighter text-on-surface">£20</span>
                            <span class="text-on-surface-variant font-medium">/ month</span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8 mb-12">
                            <div class="flex items-center gap-3">
                                <div class="bg-[#c0f5e4] p-1 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-[#00684e]"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                                <span class="font-label font-semibold text-on-surface-variant text-sm">Send quotes in minutes</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-[#c0f5e4] p-1 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-[#00684e]"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                                <span class="font-label font-semibold text-on-surface-variant text-sm">Track jobs simply</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-[#c0f5e4] p-1 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-[#00684e]"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                                <span class="font-label font-semibold text-on-surface-variant text-sm">Automatic review requests</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-[#c0f5e4] p-1 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-[#00684e]"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                                <span class="font-label font-semibold text-on-surface-variant text-sm">Follow-up reminders</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="bg-[#c0f5e4] p-1 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-primary"><polyline points="20 6 9 17 4 12"/></svg>
                                </div>
                                <span class="font-label font-semibold text-on-surface-variant text-sm">Unlimited jobs</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-6 pt-8 border-t border-surface-container">
                        <button class="w-full sm:w-auto bg-[#00684e] text-[#c6ffe6] px-8 py-4 rounded-xl font-bold text-lg shadow-xl shadow-primary/10 hover:scale-[1.02] hover:-translate-y-0.5 active:scale-[0.98] transition-all">
                            Start Free Early Access
                        </button>
                        <p class="text-sm text-on-surface-variant font-label italic">No payment required today</p>
                    </div>
                </div>

                <!-- Founder Offer Card -->
                <div class="md:col-span-5 lg:col-span-4 bg-[#0b0f10] rounded-[2rem] p-8 md:p-10 flex flex-col justify-between text-white shadow-2xl relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-primary/20 blur-[80px] rounded-full"></div>
                    
                    <div>
                        <div class="bg-[#09221e] text-[#74f3c6] inline-block px-3 py-1 rounded-full font-label text-[10px] uppercase tracking-widest font-bold mb-6">Founder Offer</div>
                        <h3 class="text-2xl font-bold mb-4">Early Access Pricing</h3>
                        <div class="flex items-baseline gap-2 mb-6">
                            <span class="text-4xl font-extrabold tracking-tighter">£10</span>
                            <span class="text-white/60 font-medium">/ month</span>
                        </div>
                        <p class="text-sm leading-relaxed mb-8 text-white/70">
                            For early users helping shape the product. Lock in this price for life while we're in early access.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex gap-3 items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#74f3c6]"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 11 11 13 15 9"/></svg>
                            <p class="text-sm font-medium text-white/90">Life-time price lock</p>
                        </div>
                        <div class="flex gap-3 items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#74f3c6]"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            <p class="text-sm font-medium text-white/90">Direct access to founders</p>
                        </div>
                        <div class="flex gap-3 items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#74f3c6]"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                            <p class="text-sm font-medium text-white/90">Priority feature requests</p>
                        </div>
                    </div>

                    <button class="mt-10 w-full border border-[#74f3c6] text-[#74f3c6] px-6 py-3 rounded-xl font-bold text-sm hover:bg-[#005a43] transition-all">
                        Claim Founder Access
                    </button>
                </div>
            </div>

            <!-- Risk Reversal Section -->
            <section class="max-w-5xl mx-auto mt-20 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-[#edf1f3] p-8 rounded-2xl flex flex-col items-center text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#00684e] mb-4"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="10" y1="14" x2="14" y2="18"/><line x1="14" y1="14" x2="10" y2="18"/></svg>
                    <h4 class="font-bold text-lg mb-2">Cancel anytime</h4>
                    <p class="text-sm text-[#585c5e]">No long-term commitments. Stay because you love us, not because you have to.</p>
                </div>
                <div class="bg-[#edf1f3] p-8 rounded-2xl flex flex-col items-center text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#00684e] mb-4"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg>
                    <h4 class="font-bold text-lg mb-2">No contracts</h4>
                    <p class="text-sm text-[#585c5e]">Simple, month-to-month billing. Honest service for honest tradespeople.</p>
                </div>
                <div class="bg-[#edf1f3] p-8 rounded-2xl flex flex-col items-center text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#00684e] mb-4"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    <h4 class="font-bold text-lg mb-2">No setup fees</h4>
                    <p class="text-sm text-[#585c5e]">Get started for £0. We help you import your contacts and templates for free.</p>
                </div>
            </section>

            <!-- Value Section -->
            <section class="max-w-6xl mx-auto mt-32 relative">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                    <div class="relative rounded-[2.5rem] overflow-hidden aspect-video shadow-2xl">
                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBhToZY-5AnaWeQAZG3Yx7pGtwwIeNjD0qxZhVEdT8nYjxuqPaIS1jzUAl03sQaDKVsZXuqmD-6YVeGQ_w7pdtn_N0e-HvYewqXJ_Hb0haFnTCVJf408SzSJA0b9qVl-iwdi1uH_Eoa2j275ZpyTDs0UdrNFZTwY1VKZoeDk3b6xMVZoR1L8Uwl3OxA1yyux0QDD7gYs2KoKlFoSCFqyySnm7wYJRpR59kJzhxTe8CCNlQWMcBZntYGkyBde7LXJhJhtpB6qL3fC_SN" alt="Modern tradesperson">
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/40 to-transparent"></div>
                        <div class="absolute bottom-8 left-8 bg-white/90 backdrop-blur p-6 rounded-2xl max-w-xs shadow-xl">
                            <div class="flex items-center gap-1 text-primary mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-[#00684e]"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-[#00684e]"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-[#00684e]"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-[#00684e]"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-[#00684e]"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            </div>
                            <p class="text-sm italic text-on-surface">"Won two boiler installs in my first week because I sent the quote before leaving their driveway."</p>
                            <p class="text-xs font-bold font-label mt-3 text-[#00684e] uppercase tracking-widest">— Dave, Gas Engineer</p>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-4xl font-extrabold text-on-surface tracking-tight mb-8">Why it’s worth it</h2>
                        <div class="space-y-10">
                            <div class="flex gap-6">
                                <div class="bg-[#c0f5e4] w-12 h-12 rounded-xl flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#00684e]"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xl font-bold mb-2">Win 1 Extra Job</h5>
                                    <p class="text-on-surface-variant leading-relaxed">Most trades win an extra job worth £500+ every month just by responding faster than the competition. QuoteFlow pays for itself 25x over.</p>
                                </div>
                            </div>
                            <div class="flex gap-6">
                                <div class="bg-[#c0f5e4] w-12 h-12 rounded-xl flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#00684e]"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xl font-bold mb-2">Better Reviews</h5>
                                    <p class="text-on-surface-variant leading-relaxed">Automated review requests help you build a 5-star reputation on Google and Checkatrade. More trust means you can charge more per job.</p>
                                </div>
                            </div>
                            <div class="flex gap-6">
                                <div class="bg-[#c0f5e4] w-12 h-12 rounded-xl flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[#00684e]"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                </div>
                                <div>
                                    <h5 class="text-xl font-bold mb-2">Save 5+ Hours Weekly</h5>
                                    <p class="text-on-surface-variant leading-relaxed">Stop spending your evenings on paperwork. Our templates let you send professional quotes in 90 seconds. Reclaim your weekends.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Final CTA Section -->
            <section class="max-w-5xl mx-auto mt-40 mb-20 text-center bg-gradient-to-br from-[#def7f0] to-[#e4e9eb] p-12 md:p-20 rounded-[3rem] border border-primary-container/30 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full pointer-events-none opacity-10">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] border-[1px] border-primary rounded-full"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] border-[1px] border-primary rounded-full"></div>
                </div>
                
                <h2 class="text-4xl md:text-5xl font-extrabold text-on-surface mb-8 tracking-tighter">Ready to streamline your business?</h2>
                <p class="text-xl text-on-surface-variant mb-12 max-w-2xl mx-auto">Join hundreds of tradespeople winning more work with less stress. Free trial, no strings attached.</p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    <button class="bg-[#00684e] text-[#c6ffe6] px-10 py-5 rounded-full font-bold text-xl shadow-2xl shadow-primary/20 hover:scale-105 hover:-translate-y-1 transition-all">
                        Start Free Early Access
                    </button>
                </div>
                <p class="mt-8 text-sm font-label font-bold text-primary tracking-widest uppercase">Limited time founder pricing available</p>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="bg-slate-50 py-8 px-8 border-t border-slate-200">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-2">
                    <span class="text-lg font-bold tracking-tight text-slate-900">FlashQuote</span>
                </div>
                <div class="text-xs font-bold uppercase tracking-widest text-slate-400">
                    © {{ date('Y') }} FlashQuote. All rights reserved.
                </div>
            </div>
        </footer>

        <!-- Scroll Animation Script -->
        <script>
            const observerOptions = {
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
        </script>
    </body>
</html>