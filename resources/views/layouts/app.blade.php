<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full antialiased">
<head>
    @php
        $layoutAppearance = data_get($layoutState ?? [], 'appearance')
            ?: app(\App\Services\LayoutConfigService::class)->getPublishedState()['appearance'];
        $layoutCssVariables = app(\App\Services\LayoutConfigService::class)->getAppearanceCssVariables($layoutAppearance);
    @endphp
    @php
        $usesLivewire = $usesLivewire ?? request()->routeIs([
            'products.index',
            'products.category',
            'products.show',
            'cart',
            'checkout',
            'checkout.*',
            'account.favorites',
        ]);
    @endphp
    <script>
        (function () {
            var k = 'rg-theme';
            var s = localStorage.getItem(k);
            var dark = false;
            if (s === 'dark') dark = true;
            else if (s === 'light') dark = false;
            else dark = false;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>
    <script>
        window.scrollRail = window.scrollRail || function () {
            return {
                canPrev: false,
                canNext: true,
                init: function () {
                    var track = this.$refs.track;
                    if (!track) return;

                    var sync = this.sync.bind(this, track);
                    sync();
                    track.addEventListener('scroll', sync, { passive: true });
                    window.addEventListener('resize', sync, { passive: true });
                },
                sync: function (track) {
                    var target = track || this.$refs.track;
                    if (!target) return;

                    this.canPrev = target.scrollLeft > 8;
                    this.canNext = target.scrollLeft + target.clientWidth < target.scrollWidth - 8;
                },
                scrollBy: function (direction) {
                    var track = this.$refs.track;
                    if (!track) return;

                    track.scrollBy({
                        left: track.clientWidth * 0.88 * direction,
                        behavior: 'smooth'
                    });
                },
                scrollPrev: function () {
                    this.scrollBy(-1);
                },
                scrollNext: function () {
                    this.scrollBy(1);
                }
            };
        };
    </script>
    @include('layouts.partials.meta')
    @php
        $gaId = trim((string) (\App\Models\Setting::get('seo', 'google_analytics_id')
            ?: config('services.google_analytics.measurement_id')));
        $gscCode = \App\Models\Setting::get('seo', 'google_search_console_code');
    @endphp
    @if($gaId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ e($gaId) }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ e($gaId) }}', {
            anonymize_ip: true,
            cookie_flags: 'SameSite=None;Secure'
        });
    </script>
    @endif
    @if($gscCode)
    <meta name="google-site-verification" content="{{ e($gscCode) }}">
    @endif
    @php
        $fontStylesheet = 'https://fonts.googleapis.com/css2?family=Great+Vibes&family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Poppins:wght@400;500;600;700&display=swap';
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="{{ $fontStylesheet }}" as="style">
    <link href="{{ $fontStylesheet }}" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="{{ $fontStylesheet }}" rel="stylesheet"></noscript>
    <style>
        :root {
            @foreach($layoutCssVariables as $cssKey => $cssValue)
                {{ $cssKey }}: {{ $cssValue }};
            @endforeach
        }
    </style>
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if ($usesLivewire)
        @livewireStyles
    @endif
</head>
<body data-livewire="{{ $usesLivewire ? 'true' : 'false' }}" @class([
    'min-h-full text-rg-darkText antialiased leading-relaxed dark:text-zinc-100',
    'rg-home-sales-skin' => request()->routeIs('home'),
]) style="background-color: var(--rg-surface-bg); font-family: var(--rg-font-family);">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-full focus:bg-rg-deepPurple focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        {{ __('Ana içeriğe geç') }}
    </a>
    <div class="rg-shell-glow relative isolate flex min-h-full flex-col overflow-x-clip">
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[40rem] bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.95),_rgba(250,248,246,0))] dark:bg-[radial-gradient(circle_at_top,_rgba(40,24,48,0.55),_rgba(17,11,22,0))]"></div>
        <div class="pointer-events-none absolute inset-x-0 top-[34rem] -z-10">
            <div class="rg-soft-divider"></div>
        </div>

        @include('layouts.partials.announcement-bar-dynamic')
        <div class="sticky top-0 z-50 border-b border-black/[0.07] bg-[#faf6f1]/88 shadow-[0_10px_40px_-18px_rgba(42,31,53,0.14)] backdrop-blur-xl backdrop-saturate-150 dark:border-white/10 dark:bg-[#120d18]/88 dark:shadow-[0_14px_48px_-18px_rgba(0,0,0,0.55)]">
            @include('layouts.partials.header')
            @include('layouts.partials.nav')
        </div>

        @stack('before_main')

        <main id="main-content" class="relative mx-auto w-full flex-1 px-4 pb-16 pt-7 sm:px-6 md:pt-10 lg:pb-24 lg:pt-11" style="max-width: var(--rg-content-width);">
        @yield('content')
    </main>

        @include('layouts.partials.footer')
        <x-guest-loyalty-prompt />
        @include('cookie-consent')
    </div>
    @if ($usesLivewire)
        @livewireScriptConfig
    @endif
    @stack('scripts')
</body>
</html>
