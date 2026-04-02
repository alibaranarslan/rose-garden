@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
    'canonical' => null,
    'noindex' => false,
])

@php
    $siteName = \App\Models\Setting::get('general', 'site_name', config('app.name'));
    $titleSuffix = \App\Models\Setting::get('seo', 'meta_title_suffix', '| Rose Garden');
    $defaultDescription = \App\Models\Setting::get('seo', 'meta_description_default', '');
    $defaultImage = \App\Models\Setting::get('seo', 'og_default_image', '');
    $canonicalDomain = rtrim((string) \App\Models\Setting::get('seo', 'canonical_domain', ''), '/');

    $rawTitle = $title ?: $siteName;
    $metaTitle = trim($rawTitle . ' ' . $titleSuffix);
    $metaDescription = $description ?: $defaultDescription;
    $metaImage = $image ?: $defaultImage;
    $metaImageUrl = $metaImage ? (str_starts_with($metaImage, 'http') ? $metaImage : asset($metaImage)) : null;
    $canonicalUrl = $canonical ?? ($canonicalDomain ? $canonicalDomain . request()->getPathInfo() : request()->url());
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
@if($metaImageUrl)
<meta property="og:image" content="{{ $metaImageUrl }}">
@endif
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="{{ $locale }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags((string) $metaDescription), 200) }}">
@if($metaImageUrl)
<meta name="twitter:image" content="{{ $metaImageUrl }}">
@endif

@php
    $rawPath = request()->path();
    $pathWithoutLocale = preg_replace('#^(tr|en|ku)(/|$)#', '', $rawPath);
    $defaultLocale = config('app.locale', 'tr');
@endphp
@foreach (['tr', 'en', 'ku'] as $lang)
@if ($lang === $defaultLocale)
<link rel="alternate" hreflang="{{ $lang }}" href="{{ url('/' . ltrim($pathWithoutLocale, '/')) }}">
@else
<link rel="alternate" hreflang="{{ $lang }}" href="{{ url('/' . $lang . '/' . ltrim($pathWithoutLocale, '/')) }}">
@endif
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ url('/' . ltrim($pathWithoutLocale, '/')) }}">
