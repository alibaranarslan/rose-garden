<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased">
<head>
    <script>
        (function () {
            var k = 'rg-theme';
            var s = localStorage.getItem(k);
            var dark = false;
            if (s === 'dark') dark = true;
            else if (s === 'light') dark = false;
            else dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
    @include('layouts.partials.meta')
    @php
        $checkoutFontStylesheet = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap';
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="{{ $checkoutFontStylesheet }}" as="style">
    <link href="{{ $checkoutFontStylesheet }}" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="{{ $checkoutFontStylesheet }}" rel="stylesheet"></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body data-livewire="true" class="rg-checkout-shell min-h-full bg-rg-cream font-sans text-rg-darkText antialiased leading-relaxed dark:bg-[#1a0f22] dark:text-zinc-100">
    <div class="relative isolate min-h-full overflow-x-clip">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[22rem] bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.96),rgba(250,248,244,0))] dark:bg-[radial-gradient(circle_at_top,rgba(55,38,67,0.34),rgba(26,15,34,0))]"></div>

        <header class="border-b border-rg-lightLavender/80 bg-[linear-gradient(135deg,rgba(255,255,255,0.94),rgba(250,245,249,0.9))] shadow-[0_8px_28px_rgba(34,24,40,0.06)] backdrop-blur-xl dark:border-white/12 dark:bg-[linear-gradient(135deg,rgba(18,11,24,0.98),rgba(31,20,41,0.94))] dark:shadow-[0_14px_36px_rgba(7,4,11,0.34)]">
            <div class="mx-auto flex max-w-5xl flex-col gap-3 px-4 py-4 md:px-6 md:py-5">
                <div class="flex items-center justify-between gap-3">
                    <a href="{{ \App\Support\StorefrontLocale::route('home') }}" class="inline-flex min-w-0 shrink items-center rounded-full border border-black/6 bg-white/92 px-3 py-2 shadow-sm transition hover:border-rg-purple/25 dark:border-white/12 dark:bg-[#1f1429] dark:shadow-[0_12px_28px_rgba(0,0,0,0.3)]">
                        <x-site-logo variant="adaptive" type="wordmark" placement="header" loading="eager" class="drop-shadow-sm dark:drop-shadow-[0_2px_10px_rgba(255,255,255,0.08)]" />
                    </a>
                    <div class="flex shrink-0 items-center gap-2">
                        <a href="{{ \App\Support\StorefrontLocale::route('cart') }}" class="hidden rounded-full border border-rg-lightLavender/80 bg-white/78 px-3 py-1.5 text-xs font-semibold text-rg-deepPurple shadow-sm transition hover:border-rg-purple/30 hover:text-rg-purple dark:border-white/12 dark:bg-white/10 dark:text-white/84 dark:hover:text-white sm:inline-flex">
                            {{ __('Sepete dön') }}
                        </a>
                        <div class="rg-checkout-security-badge flex items-center gap-2 rounded-full border border-emerald-200/80 bg-emerald-50/90 px-3 py-1.5 text-xs font-semibold text-emerald-800 shadow-sm dark:border-emerald-500/30 dark:bg-emerald-950/40 dark:text-emerald-200">
                            <svg class="h-4 w-4 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <span>{{ __('Güvenli ödeme') }}</span>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-rg-grayText dark:text-white/78">{{ __('Bilgiler, teslimat ve ödeme adım adım tamamlanır.') }}</p>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-4 py-6 md:px-6 md:py-8">
            @yield('content')
        </main>

        @include('cookie-consent')
        @livewireScriptConfig
        @stack('scripts')
    </div>
</body>
</html>
