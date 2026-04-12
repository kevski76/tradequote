<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlashQuote | High-Speed Fencing Quotes</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
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
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trending-up text-white w-5 h-5" aria-hidden="true">
                            <path d="M16 7h6v6"></path><path d="m22 7-8.5 8.5-5-5L2 17"></path>
                        </svg>
                    </div>
                    <a href="{{ url('/') }}">
                        <span class="text-xl font-bold tracking-tight text-slate-900">FlashQuote</span>
                    </a>
                </div>
            
                <div class="hidden md:flex gap-8 items-center">
                    <a href="#" class="text-sm font-bold text-emerald-600 border-b-2 border-emerald-500 pb-0.5">Features</a>
                    <a href="" onclick="scrollToHowItWorks(event)" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">How it Works</a>
                    <a href="{{ url('/') }}/pricing" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Pricing</a>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="scrollToForm()" class="hidden md:block bg-gradient-to-r from-[#00684e] to-[#74f3c6] cursor-pointer text-white px-5 py-2 rounded-full text-sm font-bold hover:scale-[1.02] transition-transform active:scale-95 shadow-lg shadow-emerald-900/10">
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
                        <a href="#" class="text-sm font-bold text-emerald-600" data-mobile-nav-link>Features</a>
                        <a href="#" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors" data-mobile-nav-link>How it Works</a>
                        <a href="#" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors" data-mobile-nav-link>Pricing</a>
                    </div>
                    <button onclick="scrollToForm()" class="mt-5 w-full bg-linear-to-r from-[#00684e] to-[#74f3c6] cursor-pointer text-white px-5 py-3 rounded-full text-sm font-bold shadow-lg shadow-emerald-900/10">
                        Get Early Access
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-32 md:pt-48 overflow-hidden">
        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto pb-20 px-6">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold mb-6 tracking-wide uppercase text-xs font-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-rocket"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-5c1.62-2.2 5-3 5-3"/><path d="M12 15v5s3.03-.55 5-2c2.2-1.62 3-5 3-5"/></svg>
                        Fencing Contractors First
                    </div>
                    <h1 class="text-5xl md:text-7xl font-black tracking-tighter leading-[0.95] mb-8">
                        Get More Fencing Jobs With <span class="text-emerald-600">Faster Quotes</span>
                    </h1>
                    <p class="text-xl text-slate-600 leading-relaxed mb-10 max-w-lg">
                        Send quotes in minutes and automatically ask for 5-star reviews after every job — no chasing, no extra admin.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 mb-12">
                        <button onclick="scrollToForm()" class="bg-gradient-to-r from-[#00684e] to-[#74f3c6] cursor-pointer text-white px-8 py-4 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-emerald-900/20 
                        hover:scale-[1.02] transition-all hover:shadow-emerald-900/30">
                            Get Free Early Access
                        </button>
                        <div class="flex items-center gap-3 px-4">
                            <div class="flex -space-x-2">
                                <img class="w-8 h-8 rounded-full border-2 border-background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDAzPeG4-BrXyROTT3iY5TBe7jmOYcfbtF9apSNmcC9QSbNVggZu-quK1FMIbZIy67KyVCHl3qunXTmYJZygBwGokJAhHAgmfBM3W39x6TAqrkM2r3kI3qUZB979HOHlgPEtovRhynJmIVqIEWpQ1EzV4us6sKLrXIXa8-s1DT3XCaRlHFwXmPQ2Tl7xYba4d_9eXKt2WViW2xLjx2a-4NQ8nU9ar9jw4EaODt61hizM9BNrPu2Xtr3E-eWw6oEXNL4KS7uothCkm7R" alt="Contractor 1" referrerPolicy="no-referrer">
                                <img class="w-8 h-8 rounded-full border-2 border-background" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBHtaAJYg_514-zLOWcZ4Myv1EfZhhN-IW_QIA_vWRuPM3to0QhoVYXpJn72NZ_ykmNtBQ1hBtJ6QzvVUHd5EvWprTYmpFQ1bnkG6uNYZIN610JEmGMbVMrgZc_bqd7nX76Narpx_UfFaz2JiZ6ppKNbjH_6sWb7PQ8tsMczWOD2YRAO-yE4LcmPW-D-UGoXeRTG7c2SRA-Q5aJlruDL7f2KwvUpSFxoqjHgJhJKPetChzGVdjK7iCcUVMB8xmPjqgzklsj4oSv3b29" alt="Contractor 2" referrerPolicy="no-referrer">
                            </div>
                            <span class="text-xs font-label text-on-surface-variant font-bold leading-none">Joined by 20+ local contractors</span>
                        </div>
                        <!--
                        <button class="bg-slate-100 text-slate-900 px-8 py-4 rounded-xl font-bold text-lg transition-all hover:bg-slate-200 active:scale-95">
                            Get early access (free)
                        </button> 
                        -->
                    </div>
                    <!--
                    <div class="flex items-center gap-4">
                        <div class="flex -space-x-3">
                            <img src="https://picsum.photos/seed/u1/100/100" class="w-10 h-10 rounded-full border-2 border-white object-cover" alt="User">
                            <img src="https://picsum.photos/seed/u2/100/100" class="w-10 h-10 rounded-full border-2 border-white object-cover" alt="User">
                            <img src="https://picsum.photos/seed/u3/100/100" class="w-10 h-10 rounded-full border-2 border-white object-cover" alt="User">
                        </div>
                        <p class="text-sm font-medium text-slate-500">
                            Trusted by <span class="text-slate-900 font-bold">50+</span> local fencing pros
                        </p>
                    </div> 
                    -->
                </div>

                <div class="relative">
                    <div class="absolute -inset-4 bg-emerald-100 rounded-[3rem] blur-3xl opacity-50 -z-10"></div>
                    <div class="bg-slate-900 p-4 rounded-[3rem] shadow-2xl max-w-sm mx-auto lg:ml-auto transform rotate-2 hover:rotate-0 transition-transform duration-500">
                        <div class="bg-white rounded-[2.5rem] overflow-hidden aspect-[9/19] relative border-4 border-slate-900">
                            <img src="{{ url('/') }}/images/mobile-phone-quote.jpg" alt="App Preview" class="w-full h-full object-cover opacity-90">
                            <div class="absolute inset-x-0 bottom-0 bg-white p-6 pt-10">
                                <div class="w-12 h-1.5 bg-emerald-100 rounded-full mb-4"></div>
                                <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">New Quote</p>
                                <p class="text-2xl font-black text-slate-900">Total: £2,450.00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Problem Section -->
        <section class="py-24 px-6 bg-[#eef1f3]">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-3xl lg:text-5xl font-extrabold text-on-surface tracking-tight mb-4">You’re probably losing jobs without realising it</h2>
                    <div class="w-20 h-1.5 bg-emerald-700 mx-auto rounded-full"></div>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @php
                        $problems = [
                            ['icon' => 'timer', 'title' => 'Quotes take too long', 'desc' => "Spent your whole evening at the laptop instead of with your family? That's profit leaking away."],
                            ['icon' => 'zap', 'title' => 'Slow replies lose work', 'desc' => "Customers go with whoever replies first. If you're not fast, you're last."],
                            ['icon' => 'bell', 'title' => 'Forgotten reviews', 'desc' => "You finish the job, say goodbye, and forget to ask. That's a missed marketing opportunity."],
                            ['icon' => 'users', 'title' => 'Competitors winning', 'desc' => "The guy with 50 reviews will always win against the guy with 5, even if your work is better."]
                        ];
                    @endphp
                    @foreach ($problems as $problem)
                        <div class="p-8 rounded-2xl bg-white border-b-4 border-red-100 hover:border-red-800 transition-all hover:-translate-y-1">
                            <div class="text-red-800 mb-4">
                                @if($problem['icon'] == 'timer')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                @elseif($problem['icon'] == 'zap')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                @elseif($problem['icon'] == 'bell')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="19" cy="11" r="4"/></svg>
                                @endif
                            </div>
                            <h3 class="text-lg font-bold mb-3">{{ $problem['title'] }}</h3>
                            <p class="text-on-surface-variant text-sm leading-relaxed">{{ $problem['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Solution Section -->
        <section id="features" class="bg-white py-24 px-6 overflow-hidden">
            <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-12 items-center">
                <div class="lg:w-1/2">
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-on-surface tracking-tight mb-8">
                        One simple tool to fix <br/><span class="text-emerald-600 italic">both problems</span>
                    </h2>
                    <div class="space-y-6">
                        @php
                            $solutions = [
                                ['title' => 'Send clean, professional quotes in minutes', 'desc' => 'Simple mobile interface built for the job site, not the office.'],
                                ['title' => 'Mark jobs as complete in one click', 'desc' => 'Tells the system the work is done and triggers the automation.'],
                                ['title' => 'Automatically send review requests', 'desc' => 'The software does the "awkward" asking for you via SMS or email.'],
                                ['title' => 'Build your reputation without thinking', 'desc' => 'Watch your Google reviews climb while you work.']
                            ];
                        @endphp
                        @foreach ($solutions as $sol)
                            <div class="flex gap-4 items-start">
                                <div class="w-10 h-10 rounded-full bg-[#dbe8e8] flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#00684e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-xl font-bold mb-1">{{ $sol['title'] }}</h4>
                                    <p class="text-on-surface-variant">{{ $sol['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="lg:w-1/2 grid grid-cols-2 gap-4">
                    <div class="space-y-4 translate-y-8">
                        <div class="bg-[#0b0f10] p-4 rounded-2xl shadow-xl border border-white/5">
                            <img class="rounded-lg mb-4" src="{{ url('/') }}/images/mobile-img2.png" alt="App UI" referrerPolicy="no-referrer">
                            <div class="h-2 w-1/2 bg-white/10 rounded mb-2"></div>
                            <div class="h-2 w-3/4 bg-white/10 rounded"></div>
                        </div>
                        <div class="bg-emerald-700 p-6 rounded-2xl shadow-xl text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                            <p class="font-bold">WhatsApp Review Request</p>
                            <p class="text-xs opacity-80">Sent on job completion.</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-white p-6 rounded-2xl shadow-xl border border-slate-100">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-full bg-slate-200"></div>
                                <div class="h-3 w-20 bg-slate-100 rounded"></div>
                            </div>
                            <p class="text-sm font-bold text-slate-800">New Quote Sent!</p>
                            <p class="text-xs text-slate-500 mb-4">Residential Fence Replacement - £2,450</p>
                            <div class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded w-fit uppercase">Pending Approval</div>
                        </div>
                        <div class="aspect-square bg-slate-200 rounded-2xl overflow-hidden relative group">
                            <img class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700" src="{{ url('/') }}/images/fence-detail.png" alt="Fence detail" referrerPolicy="no-referrer">
                            <div class="absolute inset-0 bg-emerald-100/5"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works -->
        <section id="how-it-works" class="py-24 px-6 bg-[#0b0f10] text-white">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-20">
                    <h2 class="text-3xl lg:text-5xl font-extrabold tracking-tight mb-4">How It Works</h2>
                    <p class="text-slate-400">Four simple steps to a more profitable business.</p>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @php
                        $steps = [
                            ['title' => 'Create and send your quote', 'desc' => 'Add materials, labor, and details in 60 seconds on your phone.'],
                            ['title' => 'Complete the job', 'desc' => 'Do what you do best—build great fences. No app tracking needed here.'],
                            ['title' => 'Tap "Request Review"', 'desc' => 'One tap in FlashQuote when you\'re packing up the tools.'],
                            ['title' => 'Customer gets a message', 'desc' => 'They receive a simple link to leave a review while they\'re happy with the work.']
                        ];
                    @endphp
                    @foreach ($steps as $index => $step)
                        <div class="bg-white/5 border border-white/10 p-8 rounded-3xl relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 text-white/5 font-black text-9xl">{{ $index + 1 }}</div>
                            <div class="text-emerald-300 mb-6">
                                @if($index == 0) <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                @elseif($index == 1) <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 22 1-1h3l9-9"/><path d="M3 21v-3l9-9"/><path d="m15 6 3.4-3.4a2.1 2.1 0 1 1 3 3L18 9l-3-3Z"/></svg>
                                @elseif($index == 2) <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="M2 12h2"/><path d="M20 12h2"/></svg>
                                @else <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                @endif
                            </div>
                            <h3 class="text-xl font-bold mb-3">{{ $step['title'] }}</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">{{ $step['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-16 text-center">
                    <div onclick="scrollToForm()" class="inline-block px-10 py-4 bg-emerald-300 text-emerald-800 font-black text-2xl rounded-2xl transform -rotate-1 hover:rotate-0 transition-transform cursor-default">
                        That’s it.
                    </div>
                </div>
            </div>
        </section>

        <!-- Outcome Section -->
        <section class="py-24 px-6">
            <div class="max-w-7xl mx-auto bg-white rounded-[3rem] p-8 lg:p-20 shadow-xl border border-slate-100 overflow-hidden relative">
                <div class="absolute top-0 right-0 w-1/3 h-full bg-primary/5 -skew-x-12 translate-x-1/2"></div>
                <div class="relative z-10">
                    <h2 class="text-3xl lg:text-5xl font-extrabold text-on-surface mb-12 tracking-tight max-w-2xl">What this actually does for your business</h2>
                    <div class="grid md:grid-cols-3 gap-12">
                        @php
                            $outcomes = [
                                ['num' => '01', 'title' => 'More reviews → more trust', 'desc' => 'A profile full of 5-star reviews makes you the obvious choice for new enquiries.'],
                                ['num' => '02', 'title' => 'More trust → more enquiries', 'desc' => 'Better online reputation means the phone rings more without spending on ads.'],
                                ['num' => '03', 'title' => 'Faster quotes → more jobs won', 'desc' => 'Beat the competition by getting your professional quote in their inbox first.']
                            ];
                        @endphp
                        @foreach ($outcomes as $outcome)
                            <div class="space-y-4">
                                <div class="text-6xl font-black text-emerald-100">{{ $outcome['num'] }}</div>
                                <h4 class="text-xl font-bold">{{ $outcome['title'] }}</h4>
                                <p class="text-on-surface-variant leading-relaxed">{{ $outcome['desc'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonial Section -->
        <section class="py-24 px-6 bg-slate-100">
            <div class="max-w-4xl mx-auto text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="rgba(0,104,78,0.1)" stroke="#00684e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-8"><path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/><path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/></svg>
                <blockquote class="text-3xl lg:text-4xl font-extrabold text-on-surface leading-tight mb-10">
                    “We used to forget to ask for reviews — now they just come in automatically.”
                </blockquote>
                <!--
                <div class="flex flex-col items-center gap-4">
                    <div class="w-16 h-16 rounded-full border-4 border-white shadow-lg overflow-hidden">
                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCxnuB8N8jK9tJAv44YpZ0Yg6N5ezSxtiSa-IBw8zFAcGgnFz1q-b1dx-z88-8SH-X-cds3FAE9eymCIcrPfHX7PonwLVRStn-glCYw4RgvVgDdkTssQZOp0H-diS3oUouQpPdVHghqWNl_j1didaD2ZIY-u0JrTS9f2oSXJ7EEP4hJcsla_5KiKCgnjMnuh7HGQdKQ2Nlrd9G5O78vZqQy8NTLxmA62WELfc1Et1-zuW9M2P5Oc3YgsfHHd9iL82fVy55gkzEqMaO-" alt="Dave Henderson" referrerPolicy="no-referrer">
                    </div>
                    <div>
                        <p class="font-bold text-on-surface">Dave Henderson</p>
                        <p class="text-sm text-on-surface-variant font-label font-semibold">Owner, Henderson Fencing & Landscapes</p>
                    </div>
                </div> 
                -->
            </div>
        </section>

        <!-- Problem Section -->
        <section class="py-24 bg-white reveal">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 gap-20 items-center">
                    <div class="space-y-10">
                        <div class="flex gap-6">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center font-bold text-lg">1</div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 mb-1">You measure up during the day</h3>
                                <p class="text-slate-600">Walking boundaries, taking notes on scraps of paper.</p>
                            </div>
                        </div>
                        <div class="flex gap-6">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center font-bold text-lg">2</div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 mb-1">Go home and write quotes later</h3>
                                <p class="text-slate-600">Spent your evening on a laptop instead of with family.</p>
                            </div>
                        </div>
                        <div class="flex gap-6">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center font-bold text-lg">3</div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 mb-1">Customers chase you</h3>
                                <p class="text-slate-600">"Just checking if you've had a chance to look at that price yet?"</p>
                            </div>
                        </div>
                        <div class="flex gap-6">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center font-bold text-lg">4</div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 mb-1">Or worse — go with someone faster</h3>
                                <p class="text-slate-600">The competition sent their quote 2 hours after visiting.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <h2 class="text-4xl font-extrabold text-slate-900 leading-tight">
                            Quoting fencing jobs shouldn't take all night
                        </h2>
                        <p class="text-lg text-slate-600 leading-relaxed">
                            Speed wins fencing jobs. Most lads just don't have it. We built FlashQuote to give you that edge without the late-night admin.
                        </p>
                        <div class="bg-emerald-50 border border-emerald-100 p-8 rounded-2xl">
                            <p class="text-emerald-800 font-semibold italic text-lg leading-relaxed">
                                "I was losing 2 hours every evening typing up quotes. Now I send them from my van before I've even started the engine."
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section id="features" class="py-24 bg-slate-50 reveal">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-extrabold text-slate-900 mb-4">Built specifically for fencing contractors</h2>
                    <p class="text-lg text-slate-600">No spreadsheets. No guesswork. No delays.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                        <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="text-emerald-600 w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Length & Height</h4>
                        <p class="text-slate-600 text-sm leading-relaxed">Enter total fence length (e.g. 15m) and preferred heights.</p>
                    </div>
                    <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                        <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="text-emerald-600 w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Auto-BOM</h4>
                        <p class="text-slate-600 text-sm leading-relaxed">Automatically calculates panels, posts & materials based on your spec.</p>
                    </div>
                    <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                        <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="text-emerald-600 w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Smart Markup</h4>
                        <p class="text-slate-600 text-sm leading-relaxed">Adds your specific labor rates + your material markup automatically.</p>
                    </div>
                    <div class="bg-white p-8 rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300">
                        <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="text-emerald-600 w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 mb-3">Instant Quote</h4>
                        <p class="text-slate-600 text-sm leading-relaxed">Generates a clean, professional PDF quote instantly to WhatsApp or Email.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it Works -->
        <section id="how-it-works" class="py-24 bg-white reveal">
            <div class="max-w-7xl mx-auto px-6">
                <h2 class="text-4xl font-extrabold text-slate-900 text-center mb-20">From measurement to quote in seconds</h2>
                
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12">
                    <div class="group">
                        <div class="relative h-64 rounded-3xl overflow-hidden mb-8">
                            <img src="{{ url('/') }}/images/tape-measure-1.jpg" alt="Measure" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        </div>
                        <div class="flex gap-4">
                            <span class="text-4xl font-black text-emerald-100 leading-none">01</span>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 mb-1">Enter fence length</h4>
                                <p class="text-sm text-slate-600 leading-relaxed">Just type it in as you walk the line.</p>
                            </div>
                        </div>
                    </div>
                    <div class="group">
                        <div class="relative h-64 rounded-3xl overflow-hidden mb-8">
                            <img src="{{ url('/') }}/images/fence-gate.jpg" alt="Extras" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        </div>
                        <div class="flex gap-4">
                            <span class="text-4xl font-black text-emerald-100 leading-none">02</span>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 mb-1">Select extras</h4>
                                <p class="text-sm text-slate-600 leading-relaxed">Gravel boards, gates, removal of old fence.</p>
                            </div>
                        </div>
                    </div>
                    <div class="group">
                        <div class="relative h-64 rounded-3xl overflow-hidden mb-8">
                            <img src="{{ url('/') }}/images/calculator.jpg" alt="Price" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        </div>
                        <div class="flex gap-4">
                            <span class="text-4xl font-black text-emerald-100 leading-none">03</span>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 mb-1">Get instant price</h4>
                                <p class="text-sm text-slate-600 leading-relaxed">Real-time cost updates as you toggle options.</p>
                            </div>
                        </div>
                    </div>
                    <div class="group">
                        <div class="relative h-64 rounded-3xl overflow-hidden mb-8">
                            <img src="{{ url('/') }}/images/send-on-the-spot.jpg" alt="Send" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                        </div>
                        <div class="flex gap-4">
                            <span class="text-4xl font-black text-emerald-100 leading-none">04</span>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900 mb-1">Send on the spot</h4>
                                <p class="text-sm text-slate-600 leading-relaxed">Professional PDF sent before you leave.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Value Proposition Card -->
        <section class="py-24 reveal">
            <div class="max-w-5xl mx-auto px-6">
                <div class="bg-slate-900 rounded-[3rem] p-12 md:p-24 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-[100px] -mr-48 -mt-48"></div>
                    <div class="absolute bottom-0 left-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-[100px] -ml-48 -mb-48"></div>
                    
                    <div class="relative z-10">
                        <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-8 leading-tight">
                            One extra job a month pays for this
                        </h2>
                        <p class="text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">
                            If faster quotes win you just one more fencing job, this pays for itself ten times over. Stop leaving money on the table.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Early Access Form -->
        <section class="py-24 bg-slate-50 reveal">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 gap-20 items-start">
                    <div class="space-y-8">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs tracking-wider uppercase">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                            Early Access Open
                        </div>
                        <h2 class="text-4xl font-extrabold text-slate-900">Built with local fencing contractors</h2>
                        <p class="text-lg text-slate-600 leading-relaxed">
                            I'm working with a small group of fencing contractors to get this right before launch. This isn't a generic tool—it's built for the way you actually work.
                        </p>
                        <!--
                        <div class="flex items-center gap-6 p-6 bg-white rounded-2xl shadow-sm">
                            <img src="https://picsum.photos/seed/james/100/100" alt="Founder" class="w-16 h-16 rounded-full object-cover">
                            <div>
                                <p class="font-bold text-slate-900">Message from James</p>
                                <p class="text-sm text-slate-500">Founder, QuoteFlow</p>
                            </div>
                        </div> 
                        -->
                    </div>

                    @livewire('early-access-registration')
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-24 px-6 bg-[#00684e] text-white overflow-hidden relative">
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>
            </div>
            <div class="max-w-5xl mx-auto text-center relative z-10">
                <h2 class="text-4xl lg:text-6xl font-extrabold mb-8 tracking-tighter">
                    Get early access <br/><span class="text-[#74f3c6]">(free while we’re building)</span>
                </h2>
                <p class="text-xl text-[#74f3c6] max-w-2xl mx-auto mb-12 leading-relaxed">
                    I’m working directly with a small number of fencing contractors to shape this. Join now and I’ll set everything up with you.
                </p>
                <div class="flex flex-col items-center gap-6">
                    <button onclick="scrollToForm()" class="bg-[#74f3c6] text-[#00684e] px-12 py-5 rounded-2xl text-xl font-black shadow-2xl hover:scale-105 active:scale-95 transition-all">
                        Get Started
                    </button>
                    <p class="flex items-center gap-2 text-sm text-white font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="lucide lucide-lock"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        No credit card required. Only 5 spots left.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="py-12 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-emerald-600 rounded flex items-center justify-center">
                        <svg class="text-white w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <span class="text-lg font-bold tracking-tight text-slate-900">FlashQuote</span>
                </div>
                <!--
                <div class="flex flex-wrap justify-center gap-8">
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-emerald-600 transition-colors">Terms</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-emerald-600 transition-colors">Privacy</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-emerald-600 transition-colors">Contact</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-emerald-600 transition-colors">Twitter</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-emerald-600 transition-colors">LinkedIn</a>
                </div>
                -->
                
                <p class="text-sm text-slate-400">
                    © {{ date('Y') }} FlashQuote. Built for Fencing Professionals.
                </p>
            </div>
        </div>
    </footer>
    <script>
        function scrollToForm() {
            const el = document.getElementById('earlyAccessForm');
            const navHeight = document.querySelector('nav').offsetHeight;
            const top = el.getBoundingClientRect().top + window.pageYOffset - navHeight - 24;
            window.scrollTo({ top, behavior: 'smooth' });
        }

        function scrollToHowItWorks(event) {
            event.preventDefault();
            const el = document.getElementById('how-it-works');
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