<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $seoDefaultDesc = \App\Models\Setting::get('seo', 'meta_description_default', '');
    $seoDefaultOg = \App\Models\Setting::get('seo', 'og_default_image', asset('images/product-placeholder.svg'));
    $resolvedTitle = $metaTitle ?? null;
    $resolvedDescription = $metaDescription ?? $seoDefaultDesc;
    $resolvedImage = $ogImage ?? $seoDefaultOg;
    $branding = \App\Support\SiteBranding::current();

    $brandingPublic = public_path('images/branding');
    $hasBrandingFaviconSvg = is_file($brandingPublic.DIRECTORY_SEPARATOR.'favicon.svg');
    $hasBrandingFaviconPng = is_file($brandingPublic.DIRECTORY_SEPARATOR.'favicon.png');
    $hasBrandingFaviconDarkPng = is_file($brandingPublic.DIRECTORY_SEPARATOR.'favicon-dark.png');
    $hasBrandingAppleTouch = is_file($brandingPublic.DIRECTORY_SEPARATOR.'apple-touch-icon.png');

    $brandingAsset = static function (string $relativePath): string {
        $path = public_path($relativePath);
        $v = is_file($path) ? filemtime($path) : 0;

        return $v > 0 ? asset($relativePath).'?v='.$v : asset($relativePath);
    };
@endphp
<x-seo-meta
    :title="$resolvedTitle"
    :description="$resolvedDescription"
    :image="$resolvedImage"
    :type="$ogType ?? 'website'"
    :canonical="$canonical ?? null"
    :noindex="$noindex ?? false"
/>
<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
@if (data_get($branding, 'uses_custom_favicon'))
    <link rel="icon" href="{{ $branding['favicon_url'] }}" sizes="any">
    <link rel="icon" href="{{ $branding['favicon_url'] }}" sizes="32x32">
    <link rel="apple-touch-icon" href="{{ $branding['favicon_url'] }}">
@elseif ($hasBrandingFaviconSvg && ! $hasBrandingFaviconPng)
    <link rel="icon" type="image/svg+xml" href="{{ $brandingAsset('images/branding/favicon.svg') }}">
@elseif ($hasBrandingFaviconPng)
    <link rel="icon" type="image/png" href="{{ $brandingAsset('images/branding/favicon.png') }}" sizes="32x32">
@endif
@if (! data_get($branding, 'uses_custom_favicon') && $hasBrandingFaviconDarkPng)
    <link rel="icon" type="image/png" href="{{ $brandingAsset('images/branding/favicon-dark.png') }}" sizes="32x32" media="(prefers-color-scheme: dark)">
@endif
@if (! data_get($branding, 'uses_custom_favicon') && $hasBrandingAppleTouch)
    <link rel="apple-touch-icon" href="{{ $brandingAsset('images/branding/apple-touch-icon.png') }}" sizes="180x180">
@endif
<meta name="theme-color" content="#FAF7F5" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#2D0A3E" media="(prefers-color-scheme: dark)">
@stack('schema')
