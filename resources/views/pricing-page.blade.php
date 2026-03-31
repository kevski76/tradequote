<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing | QuoteFlow - Professional Fencing Quotes</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Manrope:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols for Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#00684e',
                        'primary-light': '#74f3c6',
                        'surface': '#f4f7f9',
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    }
                }
            }
        }
    </script>

    <style>
        .ai-glow {
            background: linear-gradient(45deg, #00684e, #74f3c6);
        }
        /* Animation classes */
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
<body class="bg-surface text-[#2b2f31] font-sans antialiased">

    <!-- Navigation -->
    <header class="fixed top-4 left-1/2 -translate-x-1/2 w-[95%] max-w-7xl rounded-full px-6 py-3 z-50 bg-white/80 backdrop-blur-xl shadow-[0_12px_24px_rgba(0,104,78,0.08)] flex justify-between items-center transition-all duration-300">
        <div class="text-xl font-extrabold tracking-tighter text-primary">
            QuoteFlow
        </div>
        <nav class="hidden md:flex items-center gap-8">
            <a href="#features" class="text-sm font-semibold hover:text-primary transition-colors">Features</a>
            <a href="#how-it-works" class="text-sm font-semibold hover:text-primary transition-colors">How it Works</a>
            <a href="#pricing" class="text-sm font-semibold text-primary border-b-2 border-primary pb-1">Pricing</a>
        </nav>
        <button class="bg-primary text-white px-6 py-2 rounded-full font-bold text-sm tracking-tight shadow-lg shadow-primary/20 transition-transform hover:scale-105 active:scale-95">
            Get Early Access
        </button>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="pt-40 pb-20 px-6">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#74f3c6]/30 text-primary text-xs font-bold mb-6 uppercase tracking-widest reveal">
                    <span class="material-symbols-outlined text-sm">timer</span> Speed is the new professional
                </div>
                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tighter text-[#2b2f31] mb-6 leading-[0.95] reveal">
                    Win the job before you leave the driveway.
                </h1>
                <p class="text-xl md:text-2xl text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed reveal">
                    Create professional fencing quotes in under 60 seconds — no paperwork, no delays.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 reveal">
                    <button class="ai-glow text-white px-8 py-4 rounded-xl font-bold text-lg shadow-xl shadow-primary/20 transition-transform hover:scale-105 active:scale-95">
                        Start 7-Day Free Trial
                    </button>
                    <div class="flex items-center gap-2 text-gray-500 font-semibold text-sm">
                        <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1">check_circle</span>
                        No credit card required
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section id="features" class="pb-20 px-6">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-6">
                @php
                    $features = [
                        ['icon' => 'shutter_speed', 'title' => 'On-site speed', 'desc' => 'Quote jobs on-site in under 1 minute. No more evening admin.'],
                        ['icon' => 'calculate', 'title' => 'Auto calculations', 'desc' => 'Automatically calculate materials + labour costs instantly.'],
                        ['icon' => 'send', 'title' => 'Instant Delivery', 'desc' => 'Send professional quotes instantly via WhatsApp or Email.'],
                        ['icon' => 'content_copy', 'title' => 'Smart Templates', 'desc' => 'Save repeat jobs as templates to save even more time.']
                    ];
                @endphp

                @foreach($features as $feature)
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-all duration-300 reveal">
                    <div class="w-12 h-12 rounded-2xl bg-[#74f3c6]/20 flex items-center justify-center text-primary mb-6">
                        <span class="material-symbols-outlined">{{ $feature['icon'] }}</span>
                    </div>
                    <h3 class="font-bold text-lg mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $feature['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-24 px-6 bg-[#edf1f3]">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-extrabold tracking-tight mb-4 reveal">Simple, Fair Pricing</h2>
                    <p class="text-gray-500 font-semibold reveal">Everything you need to grow your fencing business.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-end">
                    <!-- Solo Plan -->
                    <div class="bg-white p-10 rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full reveal">
                        <div class="mb-8">
                            <h3 class="text-xl font-bold mb-2">SOLO</h3>
                            <p class="text-gray-500 text-sm font-semibold mb-6">For individual trades</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold">£29</span>
                                <span class="text-gray-500 font-semibold">/mo</span>
                            </div>
                        </div>
                        <ul class="space-y-4 mb-10 flex-grow">
                            <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-primary text-lg">check</span> 1 trade module (Fencing)</li>
                            <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-primary text-lg">check</span> Up to 5 templates</li>
                            <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-primary text-lg">check</span> PDF quotes</li>
                            <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-primary text-lg">check</span> Basic branding</li>
                        </ul>
                        <button class="w-full py-4 rounded-xl font-bold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all duration-200 active:scale-95">
                            Start Free Trial
                        </button>
                    </div>

                    <!-- Pro Plan -->
                    <div class="bg-[#0b0f10] p-10 rounded-[2.5rem] shadow-2xl shadow-primary/10 relative transform md:scale-105 z-10 flex flex-col reveal">
                        <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary-light text-primary text-[10px] font-extrabold tracking-widest uppercase px-4 py-1.5 rounded-full">
                            MOST POPULAR
                        </div>
                        <div class="mb-8">
                            <h3 class="text-xl font-bold mb-2 text-white">PRO</h3>
                            <p class="text-gray-400 text-sm font-semibold mb-6">For growing businesses</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-5xl font-extrabold text-white">£59</span>
                                <span class="text-gray-400 font-semibold">/mo</span>
                            </div>
                        </div>
                        <ul class="space-y-4 mb-10 flex-grow">
                            <li class="flex items-center gap-3 text-sm font-semibold text-white"><span class="material-symbols-outlined text-primary-light text-lg">verified</span> Unlimited modules</li>
                            <li class="flex items-center gap-3 text-sm font-semibold text-white"><span class="material-symbols-outlined text-primary-light text-lg">verified</span> Unlimited templates</li>
                            <li class="flex items-center gap-3 text-sm font-semibold text-white"><span class="material-symbols-outlined text-primary-light text-lg">verified</span> Full branding (logo, colours)</li>
                            <li class="flex items-center gap-3 text-sm font-semibold text-white"><span class="material-symbols-outlined text-primary-light text-lg">verified</span> Faster quoting workflow</li>
                        </ul>
                        <button class="w-full py-4 rounded-xl font-bold ai-glow text-white shadow-lg shadow-primary/20 transition-transform hover:scale-105 active:scale-95">
                            Start Free Trial
                        </button>
                    </div>

                    <!-- Authority Plan -->
                    <div class="bg-white p-10 rounded-3xl shadow-sm border border-gray-100 flex flex-col h-full reveal">
                        <div class="mb-8">
                            <h3 class="text-xl font-bold mb-2">AUTHORITY</h3>
                            <p class="text-gray-500 text-sm font-semibold mb-6">For business growth</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-4xl font-extrabold">£199</span>
                                <span class="text-gray-500 font-semibold">/mo</span>
                            </div>
                        </div>
                        <ul class="space-y-4 mb-10 flex-grow">
                            <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-primary text-lg">check</span> Everything in Pro</li>
                            <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-primary text-lg">check</span> Google review automation</li>
                            <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-primary text-lg">check</span> Google Business posting</li>
                            <li class="flex items-center gap-3 text-sm font-semibold"><span class="material-symbols-outlined text-primary text-lg">check</span> Done-for-you growth tools</li>
                        </ul>
                        <button class="w-full py-4 rounded-xl font-bold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-all duration-200 active:scale-95">
                            Start Free Trial
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trust Section -->
        <section class="py-20 px-6">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 text-center items-center">
                <div class="flex flex-col items-center reveal">
                    <span class="material-symbols-outlined text-3xl text-primary mb-4">location_on</span>
                    <p class="font-bold text-xl text-[#2b2f31]">Built for UK trades</p>
                </div>
                <div class="flex flex-col items-center reveal">
                    <span class="material-symbols-outlined text-3xl text-primary mb-4">verified_user</span>
                    <p class="font-bold text-xl text-[#2b2f31]">No contracts — cancel anytime</p>
                </div>
                <div class="flex flex-col items-center reveal">
                    <span class="material-symbols-outlined text-3xl text-primary mb-4">group</span>
                    <p class="font-bold text-xl text-[#2b2f31]">Used by local fencing contractors</p>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="py-24 px-6 bg-white">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl font-extrabold tracking-tight mb-12 text-center reveal">Frequently Asked Questions</h2>
                <div class="grid gap-6">
                    <div class="bg-[#f4f7f9] p-8 rounded-2xl reveal">
                        <h4 class="text-lg font-bold mb-3 flex items-center gap-3 text-[#2b2f31]">
                            <span class="material-symbols-outlined text-primary">help</span>
                            Will this actually save me time?
                        </h4>
                        <p class="text-gray-600 font-semibold leading-relaxed">
                            Most users create quotes in under 60 seconds after setup. The system calculates everything automatically so you just plug in the measurements and go.
                        </p>
                    </div>
                    <div class="bg-[#f4f7f9] p-8 rounded-2xl reveal">
                        <h4 class="text-lg font-bold mb-3 flex items-center gap-3 text-[#2b2f31]">
                            <span class="material-symbols-outlined text-primary">psychology</span>
                            Do I need to be techy?
                        </h4>
                        <p class="text-gray-600 font-semibold leading-relaxed">
                            No — if you can use WhatsApp, you can use this. We designed the interface to be as simple as possible for working professionals on site.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-24 px-6">
            <div class="max-w-7xl mx-auto relative overflow-hidden rounded-[3rem] bg-[#0b0f10] p-12 md:p-24 text-center reveal">
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 50%, #74f3c6 0%, transparent 40%)"></div>
                <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6 tracking-tighter relative z-10">Ready to stop working evenings?</h2>
                <p class="text-gray-400 text-lg mb-10 max-w-xl mx-auto font-semibold relative z-10">Join hundreds of fencing contractors who are winning more jobs with professional, instant quotes.</p>
                <button class="ai-glow text-white px-10 py-5 rounded-2xl font-bold text-xl shadow-2xl shadow-primary/30 relative z-10 transition-transform hover:scale-105 active:scale-95">
                    Start Your 7-Day Free Trial
                </button>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-[#f4f7f9] w-full py-12 px-8 border-t border-primary/10">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="text-lg font-bold text-[#2b2f31]">QuoteFlow</div>
            <div class="flex gap-8">
                <a href="#" class="text-gray-500 hover:text-primary underline decoration-2 underline-offset-4 transition-all duration-200 font-semibold text-sm">Terms</a>
                <a href="#" class="text-gray-500 hover:text-primary underline decoration-2 underline-offset-4 transition-all duration-200 font-semibold text-sm">Privacy</a>
                <a href="#" class="text-gray-500 hover:text-primary underline decoration-2 underline-offset-4 transition-all duration-200 font-semibold text-sm">Twitter</a>
                <a href="#" class="text-gray-500 hover:text-primary underline decoration-2 underline-offset-4 transition-all duration-200 font-semibold text-sm">LinkedIn</a>
            </div>
            <div class="text-gray-400 font-semibold text-sm">© 2024 QuoteFlow. All rights reserved.</div>
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