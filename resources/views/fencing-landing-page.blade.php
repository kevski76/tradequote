<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuoteFlow | High-Speed Fencing Quotes</title>

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
                    <span class="text-xl font-bold tracking-tight text-slate-900">QuoteFlow</span>
                </div>
            
                <div class="hidden md:flex gap-8 items-center">
                    <a href="#" class="text-sm font-bold text-emerald-600 border-b-2 border-emerald-500 pb-0.5">Features</a>
                    <a href="#" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">How it Works</a>
                    <a href="#" class="text-sm font-semibold text-slate-600 hover:text-emerald-600 transition-colors">Pricing</a>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="scrollToForm()" class="hidden md:block bg-gradient-to-r from-[#00684e] to-[#74f3c6] cursor-pointer text-white px-5 py-2 rounded-full text-sm font-bold hover:scale-[1.02] transition-transform active:scale-95 shadow-lg shadow-emerald-900/10">
                        Get Early Access
                    </button>
                    <button
                        id="welcome-mobile-menu-toggle"
                        type="button"
                        class="md:hidden inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition-colors hover:border-emerald-200 hover:text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500"
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

    <main class="pt-32 pb-20 md:pt-48 md:pb-32 overflow-hidden">
        <!-- Hero Section -->
        <section class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="max-w-2xl">
                    <h1 class="text-5xl md:text-7xl font-black tracking-tighter leading-[0.95] mb-8">
                        Quote fencing jobs before you leave the <span class="text-emerald-600">driveway</span>
                    </h1>
                    <p class="text-xl text-slate-600 leading-relaxed mb-10 max-w-lg">
                        Measure the job, generate the price, and send a professional quote in under 60 seconds.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 mb-12">
                        <button onclick="scrollToForm()" class="bg-gradient-to-r from-[#00684e] to-[#74f3c6] cursor-pointer text-white px-8 py-4 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-emerald-900/20 
                        hover:scale-[1.02] transition-all hover:shadow-emerald-900/30">
                            Get early access (free)
                        </button>
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
                            Speed wins fencing jobs. Most lads just don't have it. We built QuoteFlow to give you that edge without the late-night admin.
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

                    <div id="earlyAccessForm" class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-2xl shadow-emerald-900/5">
                        <form class="space-y-6" onsubmit="event.preventDefault(); alert('Thanks for your interest!');">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700">Name</label>
                                    <input type="text" placeholder="John Doe" class="w-full bg-slate-50 border-none rounded-xl px-4 py-4 focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-slate-700">Business name</label>
                                    <input type="text" placeholder="JD Fencing Ltd" class="w-full bg-slate-50 border-none rounded-xl px-4 py-4 focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700">Email</label>
                                <input type="email" placeholder="john@jdfencing.com" class="w-full bg-slate-50 border-none rounded-xl px-4 py-4 focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700">How do you currently quote? (Optional)</label>
                                <textarea rows="3" placeholder="Pen and paper, Excel, etc..." class="w-full bg-slate-50 border-none rounded-xl px-4 py-4 focus:ring-2 focus:ring-emerald-500 transition-all outline-none resize-none"></textarea>
                            </div>
                            <button class="w-full bg-gradient-to-r from-[#00684e] to-[#74f3c6] cursor-pointer text-white py-5 rounded-xl font-bold text-lg shadow-xl hover:scale-[1.01] active:scale-95 transition-all">
                                Get early access
                            </button>
                            <p class="text-center text-xs text-slate-400">
                                Limited to 5 spots, get in quick. No credit card required.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-32 bg-emerald-900 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#74f3c6 1px, transparent 1px); background-size: 30px 30px;"></div>
            <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
                <h2 class="text-4xl md:text-6xl font-extrabold text-white mb-12 leading-tight">
                    Try it. Break it. Tell me what's missing.
                </h2>
                <button onclick="scrollToForm()" class="bg-emerald-400 text-emerald-950 cursor-pointer px-12 py-6 rounded-full text-xl font-black shadow-2xl hover:bg-white transition-all active:scale-95">
                    Get Started
                </button>
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
                    <span class="text-lg font-bold tracking-tight text-slate-900">QuoteFlow</span>
                </div>
                
                <div class="flex flex-wrap justify-center gap-8">
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-emerald-600 transition-colors">Terms</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-emerald-600 transition-colors">Privacy</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-emerald-600 transition-colors">Contact</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-emerald-600 transition-colors">Twitter</a>
                    <a href="#" class="text-sm font-medium text-slate-500 hover:text-emerald-600 transition-colors">LinkedIn</a>
                </div>
                
                <p class="text-sm text-slate-400">
                    © {{ date('Y') }} QuoteFlow. Built for Fencing Professionals.
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