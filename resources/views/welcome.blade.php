<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    <body class="min-h-screen scroll-smooth bg-[#f4f7f9] text-[#2b2f31] font-sans selection:bg-emerald-100 selection:text-emerald-900">
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
                        <!--<a href="{{ url('/') }}/pricing" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Pricing</a>-->
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
                            <!--<a href="#" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors" data-mobile-nav-link>Pricing</a>-->
                        </div>
                        <button onclick="scrollToForm()" class="mt-5 w-full bg-linear-to-r cursor-pointer from-[#00684e] to-[#74f3c6] cursor-pointer text-white px-5 py-3 rounded-full text-sm font-bold shadow-lg shadow-emerald-900/10">
                            Get Early Access
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Hero Section --}}
        <section class="pt-40 pb-20 px-6 max-w-7xl mx-auto overflow-hidden">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold tracking-widest uppercase">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles w-3 h-3" aria-hidden="true">
                            <path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path>
                            <path d="M20 2v4"></path>
                            <path d="M22 4h-4"></path>
                            <circle cx="4" cy="20" r="2"></circle>
                        </svg>Built for modern trades
                    </div>
                    <h1 class="text-5xl md:text-7xl font-black tracking-tighter leading-[0.95]">
                        Win More Jobs Without More <span class="text-[#00684e]">Admin</span>
                    </h1>
                    <p class="text-xl text-slate-600 max-w-lg leading-relaxed">
                        Simple tools for trades to send quotes faster, get more reviews, and grow their business — without complicated software.
                    </p>
                    <div class="flex flex-wrap gap-4 pt-4">
                        <button onclick="scrollToForm()" class="bg-gradient-to-r from-[#00684e] to-[#74f3c6] text-white px-8 py-4 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-emerald-900/20 
                        hover:scale-[1.02] transition-all hover:shadow-emerald-900/30">
                            Get early access (free)
                        </button>
                        <!--
                        <button class="bg-white text-slate-800 px-8 py-4 rounded-xl font-bold hover:bg-slate-50 transition-all border border-slate-200">
                            Get early access (free)
                        </button> 
                        -->
                    </div>
                </div>
                
                <div class="relative">
                    <div class="absolute -inset-4 bg-emerald-500/10 blur-3xl rounded-full"></div>
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl border-4 border-white bg-[#e8f6f0]">
                        <img id="app-interface" src="{{ asset('images/hero-phone-quote.svg') }}" alt="Hand holding a mobile phone displaying a quote interface" class="w-full h-auto">
                    </div>
                </div>
            </div>
        </section>

         <!-- Problem Section -->
        <section class="py-20 px-6 bg-[#eef1f3]">
            <div class="max-w-7xl mx-auto text-center space-y-4 mb-16  reveal" x-intersect="$el.classList.add('visible')">
                <h2 class="text-3xl md:text-4xl font-extrabold tracking-tight">Most trade software is overkill</h2>
                <p class="text-on-surface-variant font-label">Why settle for complex when you need simple?</p>
            </div>
            
            <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Card 1 -->
                <div class="bg-white p-8 rounded-2xl flex items-start gap-4 shadow-sm border border-black/5  reveal transition-all hover:-translate-y-1" x-intersect="$el.classList.add('visible')">
                    <div class="p-3 bg-red-50 text-red-500 rounded-full">
                        <i data-lucide="ban" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-2">Too many features you don't need</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed">Don't pay for bloat. Focus only on what moves the needle for your trade business.</p>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="bg-white p-8 rounded-2xl flex items-start gap-4 shadow-sm border border-black/5  reveal transition-all hover:-translate-y-1" x-intersect="$el.classList.add('visible')">
                    <div class="p-3 bg-red-50 text-red-500 rounded-full">
                        <i data-lucide="dollar-sign" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-2">Too expensive for small teams</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed">Enterprise pricing shouldn't apply to hard-working local teams.</p>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="bg-white p-8 rounded-2xl flex items-start gap-4 shadow-sm border border-black/5  reveal transition-all hover:-translate-y-1" x-intersect="$el.classList.add('visible')">
                    <div class="p-3 bg-red-50 text-red-500 rounded-full">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-2">Takes too long to learn</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed">You should be on the job site, not spending weeks in software training.</p>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="bg-white p-8 rounded-2xl flex items-start gap-4 shadow-sm border border-black/5  reveal transition-all hover:-translate-y-1" x-intersect="$el.classList.add('visible')">
                    <div class="p-3 bg-red-50 text-red-500 rounded-full">
                        <i data-lucide="trending-down" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-2">Still doesn't help you win more work</h3>
                        <p class="text-on-surface-variant text-sm leading-relaxed">Shiny buttons are useless if they don't help you close the next contract.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Solution Section -->
        <section class="py-32 px-6">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div class="relative order-2 lg:order-1 reveal">
                    <div class="bg-surface-variant rounded-3xl p-6 aspect-square max-w-lg mx-auto overflow-hidden">
                        <img 
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuD9K0p1G0WX8zooVP-dVNscGhaZgK_o9yGrR2GlbEgxspydK92WbSfHkUzMfWyhU6srndJeRBSTg26obtQuISjg-eoTiA20RazDwVuC6GXLnjFU7a6mgOFTShDp_hrnFpqwEC8cfF5bpM8n-KYUmgeQQC5EF8Sc8H_H5eCKoWrBHDOhHE95lCirLS8VndnPStC1Nz8StP-dcu1rslrmhOZwd9v1dTgH7Qq1-dMMnaNKOTiDcoHFNE93iACxWn89PGTtHEE0EnikmGFw" 
                            alt="Professional working" 
                            class="w-full h-full object-cover rounded-2xl shadow-xl"
                            referrerPolicy="no-referrer"
                        >
                    </div>
                    <div class="absolute -bottom-6 -right-6 bg-[#00684e] p-6 rounded-2xl shadow-2xl text-[#c6ffe6] transform transition-transform duration-700 delay-300" x-intersect="$el.classList.add('scale-110')">
                        <div class="text-3xl font-bold">100%</div>
                        <p class="text-xs font-label uppercase tracking-widest opacity-80">Built for trades</p>
                    </div>
                </div>
                
                <div class="space-y-10 order-1 lg:order-2 reveal">
                    <div class="space-y-4">
                        <h2 class="text-4xl font-extrabold tracking-tight">Built for how trades actually work</h2>
                        <p class="text-xl text-on-surface-variant">We removed the fluff and kept the essentials to help you scale.</p>
                    </div>
                    <ul class="space-y-6">
                        <li class="flex items-center gap-4 reveal">
                            <div class="p-2 bg-[#74f3c6] text-[#00684e] rounded-lg">
                                <i data-lucide="fast-forward" class="w-5 h-5"></i>
                            </div>
                            <span class="text-lg font-medium">Fast quoting from your phone</span>
                        </li>
                        <li class="flex items-center gap-4 reveal">
                            <div class="p-2 bg-[#74f3c6] text-[#00684e] rounded-lg">
                                <i data-lucide="check-square" class="w-5 h-5"></i>
                            </div>
                            <span class="text-lg font-medium">Simple job tracking that makes sense</span>
                        </li>
                        <li class="flex items-center gap-4 reveal">
                            <div class="p-2 bg-[#74f3c6] text-[#00684e] rounded-lg">
                                <i data-lucide="star" class="w-5 h-5"></i>
                            </div>
                            <span class="text-lg font-medium">Automatic review requests upon completion</span>
                        </li>
                        <li class="flex items-center gap-4 reveal">
                            <div class="p-2 bg-[#74f3c6] text-[#00684e] rounded-lg">
                                <i data-lucide="eye-off" class="w-5 h-5"></i>
                            </div>
                            <span class="text-lg font-medium">No fluff, no complexity, no distractions</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Bento Grid Section -->
        <section id="features" class="py-24 px-6 bg-white reveal">
            <div class="max-w-7xl mx-auto space-y-16">
                <div class="text-center space-y-4 reveal">
                    <h2 class="text-4xl font-extrabold tracking-tight">Focus on your craft, we'll handle the rest</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Benefit 1 -->
                    <div class="md:col-span-2 bg-[#f4f7f9] p-10 rounded-3xl relative overflow-hidden group border border-black/5 reveal transition-all hover:-translate-y-1">
                        <div class="relative z-10 space-y-4 max-w-sm">
                            <div class="text-[#00684e] font-bold text-3xl">Save Time</div>
                            <p class="text-[#585c5e]">Create and send professional quotes in minutes, not hours. Reclaim your evenings for what matters.</p>
                        </div>
                        <div class="absolute right-0 bottom-0 translate-y-1/4 translate-x-1/4 w-2/3 opacity-5 group-hover:opacity-10 transition-opacity">
                            <i data-lucide="timer" class="w-full h-full text-primary" stroke-width="1"></i>
                        </div>
                    </div>
                    
                    <!-- Benefit 2 -->
                    <div class="bg-[#00684e] text-[#c6ffe6] p-10 rounded-3xl flex flex-col justify-between shadow-xl shadow-primary/10 reveal transition-all hover:-translate-y-1">
                        <i data-lucide="message-square" class="w-12 h-12 fill-current mb-8"></i>
                        <div class="space-y-4">
                            <h3 class="text-2xl font-bold">Get More Reviews</h3>
                            <p class="text-[#c6ffe6]">Automatically request reviews after every job. Let your reputation do the selling for you.</p>
                        </div>
                    </div>
                    
                    <!-- Benefit 3 -->
                    <div class="md:col-span-3 bg-slate-950 text-white p-10 rounded-3xl flex flex-col md:flex-row items-center gap-12 overflow-hidden  reveal" x-intersect="$el.classList.add('visible')">
                        <div class="space-y-6 flex-1">
                            <h3 class="text-4xl font-bold">Win More Work</h3>
                            <p class="text-lg text-slate-400">Look more professional and respond faster than competitors. In a world of slow replies, speed is your secret weapon.</p>
                            <div class="flex flex-wrap gap-4">
                                <div class="bg-white/5 p-4 rounded-xl border border-white/10">
                                    <span class="text-[#74f3c6] block font-bold text-2xl">40%</span>
                                    <span class="text-[10px] font-label uppercase tracking-wider text-slate-500">Higher Close Rate</span>
                                </div>
                                <div class="bg-white/5 p-4 rounded-xl border border-white/10">
                                    <span class="text-[#74f3c6] block font-bold text-2xl">2.5x</span>
                                    <span class="text-[10px] font-label uppercase tracking-wider text-slate-500">Faster Response</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 w-full max-w-md">
                            <img 
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBs95hnO_aYLph-nu0PogSf3ZZ8FaAaEIrLzDjyWXNt8OG5xC5ufR2qQjK9xTQFEfZKUovIX8o-sPiEbP7qnarFx9XiYW3ddr0Q590wyB7WOZozcgWV6kKKJflXjA3_y7Ps-qunf6v50YqnYwsP_5gPt3XganS4o-L4a1sIVydQMbISO4iyTj64WU1Jki8kM2bGKqS44eXDZt0qkw7NDVWpRoiNdyGyN_hYKQXCtaZ2S8f5sJng-xu3im7XCRqkl4ZdWe4NKnTV2vZC" 
                                alt="Professional handshake" 
                                class="rounded-2xl shadow-2xl grayscale brightness-75 hover:grayscale-0 transition-all duration-700"
                                referrerpolicy="no-referrer"
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Who It's For -->
        <section class="py-32 px-6 reveal">
            <div class="max-w-7xl mx-auto flex flex-col items-center">
                <h2 class="text-3xl font-extrabold text-center mb-16 reveal">Perfect for small teams and solo pros</h2>
                <div class="flex flex-wrap justify-center gap-4">
                    <div class="px-8 py-4 bg-[#edf1f3] rounded-full font-bold text-[#585c5e] transition-all cursor-default border border-black/5 hover:bg-[#74f3c6] hover:text-[#005a43] hover:scale-105">Fencing contractors</div>
                    <div class="px-8 py-4 bg-[#edf1f3] rounded-full font-bold text-[#585c5e] transition-all cursor-default border border-black/5 hover:bg-[#74f3c6] hover:text-[#005a43] hover:scale-105">Landscapers</div>
                    <div class="px-8 py-4 bg-[#edf1f3] rounded-full font-bold text-[#585c5e] transition-all cursor-default border border-black/5 hover:bg-[#74f3c6] hover:text-[#005a43] hover:scale-105">Electricians</div>
                    <div class="px-8 py-4 bg-[#edf1f3] rounded-full font-bold text-[#585c5e] transition-all cursor-default border border-black/5 hover:bg-[#74f3c6] hover:text-[#005a43] hover:scale-105">Plumbers</div>
                    <div class="px-8 py-4 bg-[#edf1f3] rounded-full font-bold text-[#585c5e] transition-all cursor-default border border-black/5 hover:bg-[#74f3c6] hover:text-[#005a43] hover:scale-105">Small trade businesses</div>
                    <div class="px-8 py-4 bg-[#edf1f3] rounded-full font-bold text-[#585c5e] transition-all cursor-default border border-black/5 hover:bg-[#74f3c6] hover:text-[#005a43] hover:scale-105">Solo or small teams</div>
                </div>
            </div>
        </section>

        <!-- Final CTA Section -->
        <section class="mx-6 mb-24 reveal">
            <div class="max-w-7xl mx-auto bg-[#00684e] rounded-[2.5rem] p-12 md:p-24 text-center space-y-8 relative overflow-hidden shadow-2xl shadow-primary/20 reveal">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-primary-container/20 via-transparent to-transparent"></div>
                <div class="relative z-10 space-y-6">
                    <h2 class="text-4xl md:text-6xl font-extrabold text-[#c6ffe6] tracking-tighter">Start simple. Grow faster.</h2>
                    <p class="text-xl text-[#c6ffe6] max-w-2xl mx-auto">No complicated setup. No learning curve. Get started today and see the difference by your next quote.</p>
                    <div class="pt-6">
                        <button onclick="scrollToForm()" class="bg-slate-800 text-white px-10 py-5 rounded-2xl font-extrabold text-lg shadow-2xl transition-all flex items-center gap-2 mx-auto hover:scale-105 hover:shadow-black/50 active:scale-95">
                            Try It Free
                            <i data-lucide="chevron-right" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        {{-- Early Access Section --}}
        <section class="py-28 reveal">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 gap-12">
                    <div class="space-y-8">
                        <h2 class="text-4xl font-bold" style="opacity: 1; transform: none;">Built with local trades</h2>
                        <p class="text-xl text-slate-600 leading-relaxed" style="opacity: 1; transform: none;">
                            I’m working with a small group of landscapers &amp; fencing contractors to get this right before launch. 
                            This isn't corporate software — it's built by the people who use it.
                        </p>
                        <div class="flex items-center gap-4 p-4 bg-slate-100 rounded-2xl" style="opacity: 1; transform: none;">
                            <div class="flex -space-x-3">
                                <img alt="User" class="w-10 h-10 rounded-full border-2 border-white" referrerpolicy="no-referrer" src="https://i.pravatar.cc/100?img=11">
                                <img alt="User" class="w-10 h-10 rounded-full border-2 border-white" referrerpolicy="no-referrer" src="https://i.pravatar.cc/100?img=12">
                                <img alt="User" class="w-10 h-10 rounded-full border-2 border-white" referrerpolicy="no-referrer" src="https://i.pravatar.cc/100?img=13">
                            </div>
                            <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">JOIN 20+ TRADES ALREADY TESTING</p>
                        </div>
                    </div>
                    @livewire('early-access-registration')
                </div>
            </div>
        </section>

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
        <script>
            // Initialize Lucide Icons
            lucide.createIcons();

            function scrollToForm() {
                const el = document.getElementById('earlyAccessForm');
                const navHeight = document.querySelector('nav').offsetHeight;
                const top = el.getBoundingClientRect().top + window.pageYOffset - navHeight - 24;
                window.scrollTo({ top, behavior: 'smooth' });
            }

            // Simple scroll reveal logic
            document.addEventListener('DOMContentLoaded', () => {
                const navShell = document.getElementById('welcome-nav-shell');
                const mobileMenu = document.getElementById('welcome-mobile-menu');
                const mobileMenuToggle = document.getElementById('welcome-mobile-menu-toggle');
                const mobileMenuOpenIcon = document.getElementById('welcome-mobile-menu-open-icon');
                const mobileMenuCloseIcon = document.getElementById('welcome-mobile-menu-close-icon');

                const setMobileMenuState = (isOpen) => {
                    if (!mobileMenu || !mobileMenuToggle || !mobileMenuOpenIcon || !mobileMenuCloseIcon) {
                        return;
                    }

                    mobileMenu.classList.toggle('hidden', !isOpen);
                    mobileMenuOpenIcon.classList.toggle('hidden', isOpen);
                    mobileMenuCloseIcon.classList.toggle('hidden', !isOpen);
                    mobileMenuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    mobileMenuToggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
                };

                if (mobileMenuToggle) {
                    mobileMenuToggle.addEventListener('click', () => {
                        const isOpen = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
                        setMobileMenuState(!isOpen);
                    });
                }

                document.querySelectorAll('[data-mobile-nav-link]').forEach((link) => {
                    link.addEventListener('click', () => setMobileMenuState(false));
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        setMobileMenuState(false);
                    }
                });

                document.addEventListener('click', (event) => {
                    if (!navShell || mobileMenu?.classList.contains('hidden')) {
                        return;
                    }

                    if (!navShell.contains(event.target)) {
                        setMobileMenuState(false);
                    }
                });

                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 768) {
                        setMobileMenuState(false);
                    }
                });

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

                document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
            });
        </script>
    </body>
</html>
