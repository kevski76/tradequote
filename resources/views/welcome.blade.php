<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ __('Welcome') }} - {{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&amp;display=swap" nonce="">
        <link rel="stylesheet" href="//fonts.googleapis.com/css2?family=Inter:wght@400;500&amp;family=Inter+Tight:wght@300;500;600&amp;display=swap" nonce="">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Google+Sans+Text:ital,wght@0,400;0,500;0,600;1,400;1,500&amp;display=block" nonce="">

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f4f7f9] text-[#2b2f31] font-sans selection:bg-emerald-100 selection:text-emerald-900">
        {{-- Navigation --}}
        <nav class="fixed top-0 w-full z-50 px-6 py-4">
            <div class="max-w-5xl mx-auto bg-white/80 backdrop-blur-md shadow-xl shadow-emerald-900/5 flex justify-between items-center px-6 py-3 rounded-full border border-white/20">
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

                <div class="flex items-center gap-4">
                    <button class="hidden sm:block bg-gradient-to-r from-[#00684e] to-[#74f3c6] text-white px-5 py-2 rounded-full text-sm font-bold hover:scale-[1.02] transition-transform active:scale-95 shadow-lg shadow-emerald-900/10">
                        Get Early Access
                    </button>
                </div>
            </div>
        </nav>

        {{-- Hero Section --}}
        <section class="pt-40 pb-20 px-6 max-w-7xl mx-auto overflow-hidden">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold tracking-widest uppercase"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles w-3 h-3" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>Beta Access Now Open</div>
                    <h1 class="text-5xl md:text-7xl font-black tracking-tighter leading-[0.95]">
                        Win the job before you leave the <span class="text-[#00684e]">driveway</span>
                    </h1>
                    <p class="text-xl text-slate-600 max-w-lg leading-relaxed">
                        Create and send a professional quote in under 60 seconds — straight from your phone.
                    </p>
                    <div class="flex flex-wrap gap-4 pt-4">
                        <button class="bg-gradient-to-r from-[#00684e] to-[#74f3c6] text-white px-8 py-4 rounded-xl font-bold flex items-center gap-2 shadow-lg shadow-emerald-900/20 hover:scale-[1.02] transition-all hover:shadow-emerald-900/30">Watch 30-sec demo<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-play w-5 h-5" aria-hidden="true"><path d="M9 9.003a1 1 0 0 1 1.517-.859l4.997 2.997a1 1 0 0 1 0 1.718l-4.997 2.997A1 1 0 0 1 9 14.996z"></path><circle cx="12" cy="12" r="10"></circle></svg></button>
                        <button class="bg-white text-slate-800 px-8 py-4 rounded-xl font-bold hover:bg-slate-50 transition-all border border-slate-200">
                            Get early access (free)
                        </button>
                    </div>
                </div>
                
                <div class="relative">
                    <div class="absolute -inset-4 bg-emerald-500/10 blur-3xl rounded-full"></div>
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl border-4 border-white">
                        <img src="https://picsum.photos/seed/quotes/800/1000" alt="App Interface" class="w-full h-auto">
                    </div>
                </div>
            </div>
        </section>

        <section class="py-24 bg-slate-100/50"><div class="max-w-7xl mx-auto px-6 text-center"><div style="opacity: 1; transform: none;"><h2 class="text-4xl font-bold mb-4">See it in action</h2><p class="text-slate-600 text-lg max-w-2xl mx-auto mb-16">No forms. No paperwork. Just enter the job and send the quote.</p></div><div class="relative max-w-4xl mx-auto rounded-[2rem] overflow-hidden bg-slate-900 shadow-2xl aspect-video group cursor-pointer" style="opacity: 1; transform: none;"><img alt="Dashboard" class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-700" referrerpolicy="no-referrer" src="https://picsum.photos/seed/dashboard/1200/800"><div class="absolute inset-0 flex items-center justify-center"><div class="w-20 h-20 bg-[#00684e] rounded-full flex items-center justify-center text-white shadow-xl" tabindex="0" style="transform: none;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-play w-10 h-10 fill-current" aria-hidden="true"><path d="M9 9.003a1 1 0 0 1 1.517-.859l4.997 2.997a1 1 0 0 1 0 1.718l-4.997 2.997A1 1 0 0 1 9 14.996z"></path><circle cx="12" cy="12" r="10"></circle></svg></div></div></div></div></section>

        {{-- Problem Section --}}
        <section class="py-28 bg-[#0b0f10] text-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 gap-20 items-center">
                    <div class="space-y-12">
                        <h2 class="text-4xl md:text-5xl font-extrabold leading-tight">
                            Most trades lose <br/><span class="text-red-400">jobs here</span>
                        </h2>
                        
                        <div class="space-y-6">
                            @foreach([
                                ['id' => '01', 'text' => 'You finish the job visit'],
                                ['id' => '02', 'text' => 'Say “I’ll send a quote later”'],
                                ['id' => '03', 'text' => 'Get busy with life & work'],
                                ['id' => '04', 'text' => 'Customer goes with someone faster', 'highlight' => true],
                            ] as $item)
                                <div class="flex items-center gap-6 p-6 rounded-2xl bg-white/5 border border-white/10 {{ isset($item['highlight']) ? 'border-red-400/30 bg-red-400/5' : '' }}">
                                    <span class="text-3xl font-bold {{ isset($item['highlight']) ? 'text-red-400' : 'text-slate-500' }}">{{ $item['id'] }}</span>
                                    <p class="text-lg {{ isset($item['highlight']) ? 'font-bold' : '' }}">{{ $item['text'] }}</p>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-2xl font-bold italic text-slate-400" style="opacity: 1; transform: none;">"Speed wins. Delays lose work."</p>
                    </div>
                    
                    <div class="hidden lg:block">
                        <img src="https://picsum.photos/seed/worker/800/1000?grayscale" alt="Exhausted worker" class="rounded-3xl opacity-60">
                    </div>
                </div>
            </div>
        </section>

        {{-- Solution Section --}}
        <section class="py-28">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-20">
                    <h2 class="text-5xl font-extrabold tracking-tight mb-6" style="opacity: 1; transform: none;">Quote it there and then</h2>
                    <div class="h-1.5 bg-[#00684e] mx-auto rounded-full" style="width: 96px;"></div>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white p-10 rounded-3xl shadow-sm hover:shadow-xl transition-all border-b-4 border-[#00684e]" style="opacity: 1; transform: none;">
                        <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center text-[#00684e] mb-8">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ruler w-8 h-8" aria-hidden="true">
                                <path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z"></path>
                                <path d="m14.5 12.5 2-2"></path>
                                <path d="m11.5 9.5 2-2"></path>
                                <path d="m8.5 6.5 2-2"></path>
                                <path d="m17.5 15.5 2-2"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Enter measurements</h3>
                        <p class="text-slate-600">Simply tap in dimensions or select materials from your custom list.</p>
                    </div>
                    <div class="bg-white p-10 rounded-3xl shadow-sm hover:shadow-xl transition-all border-b-4 border-[#00684e]" style="opacity: 1; transform: none;">
                        <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center text-[#00684e] mb-8">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calculator w-8 h-8" aria-hidden="true">
                                <rect width="16" height="20" x="4" y="2" rx="2"></rect>
                                <line x1="8" x2="16" y1="6" y2="6"></line>
                                <line x1="16" x2="16" y1="14" y2="18"></line>
                                <path d="M16 10h.01"></path>
                                <path d="M12 10h.01"></path>
                                <path d="M8 10h.01"></path>
                                <path d="M12 14h.01"></path>
                                <path d="M8 14h.01"></path>
                                <path d="M12 18h.01"></path>
                                <path d="M8 18h.01"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Price is calculated instantly</h3>
                        <p class="text-slate-600">Your profit margins and labor costs are baked in automatically.</p>
                    </div>
                    <div class="bg-white p-10 rounded-3xl shadow-sm hover:shadow-xl transition-all border-b-4 border-[#00684e]" style="opacity: 1; transform: none;">
                        <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center text-[#00684e] mb-8">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send w-8 h-8" aria-hidden="true">
                                <path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"></path>
                                <path d="m21.854 2.147-10.94 10.939"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4">Send quote on the spot</h3>
                        <p class="text-slate-600">Email or WhatsApp a professional PDF before you start the van.</p>
                    </div>
                </div>
                <p class="text-center mt-16 text-2xl font-extrabold text-[#00684e]" style="opacity: 1; transform: none;">Done in under a minute.</p>
            </div>
        </section>
        <section class="py-32 bg-[#00684e] relative overflow-hidden"><div class="max-w-7xl mx-auto px-6 text-center relative z-10" style="opacity: 1; transform: none;"><h2 class="text-4xl md:text-7xl font-black text-white tracking-tighter uppercase leading-none">One extra job a month <br class="hidden md:block"> pays for this</h2></div><div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none"><div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-white rounded-full blur-[120px]"></div></div></section>    
        {{-- Early Access Section --}}
        <section class="py-28"><div class="max-w-7xl mx-auto px-6"><div class="grid lg:grid-cols-2 gap-12"><div class="space-y-8"><h2 class="text-4xl font-bold" style="opacity: 1; transform: none;">Built with local trades</h2><p class="text-xl text-slate-600 leading-relaxed" style="opacity: 1; transform: none;">I’m working with a small group of landscapers &amp; fencing contractors to get this right before launch. This isn't corporate software — it's built by the people who use it.</p><div class="flex items-center gap-4 p-4 bg-slate-100 rounded-2xl" style="opacity: 1; transform: none;"><div class="flex -space-x-3"><img alt="User" class="w-10 h-10 rounded-full border-2 border-white" referrerpolicy="no-referrer" src="https://i.pravatar.cc/100?img=11"><img alt="User" class="w-10 h-10 rounded-full border-2 border-white" referrerpolicy="no-referrer" src="https://i.pravatar.cc/100?img=12"><img alt="User" class="w-10 h-10 rounded-full border-2 border-white" referrerpolicy="no-referrer" src="https://i.pravatar.cc/100?img=13"></div><p class="text-sm font-bold text-slate-500 uppercase tracking-widest">JOIN 120+ TRADES ALREADY TESTING</p></div></div><div class="bg-white p-8 md:p-12 rounded-[2rem] shadow-2xl relative overflow-hidden" style="opacity: 1; transform: none;"><div class="absolute top-0 right-0 p-4 opacity-10"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-pen w-32 h-32 rotate-12 text-[#00684e]" aria-hidden="true"><path d="M12.5 22H18a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v9.5"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M13.378 15.626a1 1 0 1 0-3.004-3.004l-5.01 5.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z"></path></svg></div><form class="space-y-6 relative z-10"><div class="grid md:grid-cols-2 gap-6"><div class="space-y-2"><label class="text-xs font-bold uppercase tracking-widest text-slate-500">Name</label><input class="w-full bg-slate-50 border-none rounded-xl p-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all" placeholder="John Doe" type="text"></div><div class="space-y-2"><label class="text-xs font-bold uppercase tracking-widest text-slate-500">Trade</label><input class="w-full bg-slate-50 border-none rounded-xl p-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all" placeholder="e.g. Landscaper" type="text"></div></div><div class="space-y-2"><label class="text-xs font-bold uppercase tracking-widest text-slate-500">Email</label><input class="w-full bg-slate-50 border-none rounded-xl p-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all" placeholder="john@example.com" type="email"></div><div class="space-y-2"><label class="text-xs font-bold uppercase tracking-widest text-slate-500">How do you currently quote jobs?</label><textarea class="w-full bg-slate-50 border-none rounded-xl p-4 focus:ring-2 focus:ring-emerald-500 outline-none transition-all" placeholder="Paper, Excel, or mental math..." rows="3"></textarea></div><button class="w-full bg-gradient-to-r from-[#00684e] to-[#74f3c6] text-white py-5 rounded-xl font-bold text-lg shadow-xl hover:scale-[1.01] active:scale-95 transition-all">Get early access</button></form></div></div></div></section>

        {{-- Footer --}}
        <footer class="bg-slate-50 pt-20 pb-12 px-8 border-t border-slate-200">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="text-lg font-bold text-emerald-900">QuoteFlow</div>
                <div class="text-xs font-bold uppercase tracking-widest text-slate-400">
                    © {{ date('Y') }} QuoteFlow. All rights reserved.
                </div>
            </div>
        </footer>
    </body>
</html>
