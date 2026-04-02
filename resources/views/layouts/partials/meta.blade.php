<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
    $seoDefaultDesc = \App\Models\Setting::get('seo', 'meta_description_default', '');
    $seoDefaultOg = \App\Models\Setting::get('seo', 'og_default_image', asset('images/og-default.jpg'));
    $resolvedTitle = $metaTitle ?? null;
    $resolvedDescription = $metaDescription ?? $seoDefaultDesc;
    $resolvedImage = $ogImage ?? $seoDefaultOg;
@endphp
<x-seo-meta
    :title="$resolvedTitle"
    :description="$resolvedDescription"
    :image="$resolvedImage"
    :type="$ogType ?? 'website'"
    :canonical="$canonical ?? null"
    :noindex="$noindex ?? false"
/>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/branding/favicon.svg') }}">
<link rel="icon" type="image/png" href="{{ asset('images/branding/favicon.png') }}" sizes="32x32">
<link rel="apple-touch-icon" href="{{ asset('images/branding/favicon.svg') }}">
@stack('schema')
