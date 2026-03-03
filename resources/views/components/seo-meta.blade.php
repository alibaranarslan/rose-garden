@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'canonical' => null,
    'noindex' => false,
])

@php
    $siteSettings = \Illuminate\Support\Facades\Cache::remember('rg_site_settings_flat', 3600, fn () => \App\Models\Setting::query()
        ->pluck('value', 'key'));
    $siteName = $siteSettings->get('site_name', config('app.name'));
    $defaultDescription = $siteSettings->get('meta_description', '');
    $defaultImage = $siteSettings->get('og_image', '');

    $metaTitle = $title ? $title . ' | ' . $siteName : $siteName;
    $metaDescription = $description ?? $defaultDescription;
    $metaImage = $image ?? $defaultImage;
    $metaImageUrl = $metaImage ? (str_starts_with($metaImage, 'http') ? $metaImage : asset($metaImage)) : null;
    $canonicalUrl = $canonical ?? request()->url();
    $locale = app()->getLocale() === 'tr' ? 'tr_TR' : (app()->getLocale() === 'en' ? 'en_US' : 'ku_TR');
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $metaDescription), 160) }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

@if($noindex)
<meta name="robots" content="noindex, nofollow">
@endif

<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $metaDescription), 200) }}">
<meta property="og:image" content="{{ $metaImageUrl }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="{{ $locale }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $metaDescription), 200) }}">
<meta name="twitter:image" content="{{ $metaImageUrl }}">

@foreach (['tr', 'en', 'ku'] as $lang)
<link rel="alternate" hreflang="{{ $lang }}" href="{{ url('/' . $lang . '/' . ltrim(request()->path(), '/')) }}">
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ url('/tr/' . ltrim(request()->path(), '/')) }}">
