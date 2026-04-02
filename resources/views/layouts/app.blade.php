<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    @include('layouts.partials.meta')
    @php
        $gaId = \App\Models\Setting::get('seo', 'google_analytics_id');
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=Great+Vibes&family=Nunito:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-rg-cream font-sans text-rg-darkText antialiased">
    @include('layouts.partials.announcement-bar')
    @include('layouts.partials.header')
    @include('layouts.partials.nav')

    <main class="max-w-7xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    @include('layouts.partials.footer')
    @include('cookie-consent')
    @livewireScripts
    @stack('scripts')
</body>
</html>
