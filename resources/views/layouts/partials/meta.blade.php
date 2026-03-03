<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<x-seo-meta
    :title="$metaTitle ?? null"
    :description="$metaDescription ?? null"
    :image="$ogImage ?? null"
    :type="$ogType ?? 'website'"
    :canonical="$canonical ?? null"
    :noindex="$noindex ?? false"
/>
<link rel="icon" type="image/svg+xml" href="{{ asset('images/branding/favicon.svg') }}">
<link rel="icon" type="image/png" href="{{ asset('images/branding/favicon.png') }}" sizes="32x32">
<link rel="apple-touch-icon" href="{{ asset('images/branding/favicon.svg') }}">
@stack('schema')
