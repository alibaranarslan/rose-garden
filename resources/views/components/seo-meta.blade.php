@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'canonical' => null,
    'noindex' => false,
])

@php
    $branding = \App\Support\SiteBranding::current();
    $siteName = $branding['site_name'] ?? config('app.name');
    $titleSuffix = \App\Models\Setting::get('seo', 'meta_title_suffix', '| Rose Garden');
    $defaultDescription = \App\Models\Setting::get('seo', 'meta_description_default', '')
        ?: 'Rose Garden; Adıyaman için taze çiçek, butik çikolata, özel gün hediyeleri ve güvenli online sipariş deneyimi sunar.';
    $defaultImage = \App\Models\Setting::get('seo', 'og_default_image', '');
    $canonicalDomain = trim((string) \App\Models\Setting::get('seo', 'canonical_domain', ''));

    if ($canonicalDomain !== '') {
        if (! str_starts_with($canonicalDomain, 'http://') && ! str_starts_with($canonicalDomain, 'https://')) {
            $canonicalDomain = 'https://'.$canonicalDomain;
        }

        $canonicalParts = parse_url($canonicalDomain);

        if (is_array($canonicalParts) && ! empty($canonicalParts['host'])) {
            $scheme = $canonicalParts['scheme'] ?? 'https';
            $port = isset($canonicalParts['port']) ? ':'.$canonicalParts['port'] : '';
            $canonicalDomain = "{$scheme}://{$canonicalParts['host']}{$port}";
        } else {
            $canonicalDomain = '';
        }
    }

    $rootUrl = $canonicalDomain !== '' ? $canonicalDomain : url('/');

    $titleWasProvided = trim((string) $title) !== '';
    $rawTitle = trim((string) ($titleWasProvided ? $title : $siteName));
    $titleSuffix = trim((string) $titleSuffix);
    $siteLabelFromSuffix = trim((string) preg_replace('/^[\|\-\x{2013}\x{2014}\s]+/u', '', $titleSuffix));
    $rawTitleLower = mb_strtolower($rawTitle);
    $suffixLower = mb_strtolower($titleSuffix);
    $siteLabelLower = mb_strtolower($siteLabelFromSuffix);
    $alreadyHasSuffix = $titleSuffix !== '' && str_ends_with($rawTitleLower, $suffixLower);
    $alreadyHasSiteLabel = $siteLabelFromSuffix !== ''
        && (
            $rawTitleLower === $siteLabelLower
            || preg_match('/[\|\-\x{2013}\x{2014}]\s*'.preg_quote($siteLabelFromSuffix, '/').'$/iu', $rawTitle) === 1
        );
    $metaTitle = $titleWasProvided && $titleSuffix !== '' && ! $alreadyHasSuffix && ! $alreadyHasSiteLabel
        ? trim($rawTitle.' '.$titleSuffix)
        : $rawTitle;
    $metaDescription = $description ?: $defaultDescription;
    $metaImage = $image ?: $defaultImage;
    $metaImageUrl = $metaImage ? (str_starts_with($metaImage, 'http') ? $metaImage : asset($metaImage)) : null;
    $canonicalUrl = $canonical ?? \App\Support\StorefrontLocale::currentRequestUrl(
        \App\Support\StorefrontLocale::current(),
        false,
        $rootUrl
    );
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $metaDescription), 160) }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

@if($noindex)
<meta name="robots" content="noindex, nofollow">
@endif

<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $metaDescription), 200) }}">
@if($metaImageUrl)
<meta property="og:image" content="{{ $metaImageUrl }}">
@endif
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="{{ \App\Support\StorefrontLocale::ogLocale() }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $metaDescription), 200) }}">
@if($metaImageUrl)
<meta name="twitter:image" content="{{ $metaImageUrl }}">
@endif

@php
    $defaultLocale = \App\Support\StorefrontLocale::default();
@endphp
@foreach (\App\Support\StorefrontLocale::codes() as $lang)
<link rel="alternate" hreflang="{{ $lang }}" href="{{ \App\Support\StorefrontLocale::currentRequestUrl($lang, $lang !== $defaultLocale, $rootUrl) }}">
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ \App\Support\StorefrontLocale::currentRequestUrl($defaultLocale, false, $rootUrl) }}">
