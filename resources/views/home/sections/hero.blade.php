@php
    $locale = app()->getLocale();
    $heading = data_get($settings, "title_override.$locale")
        ?: (filled(data_get($homeContent, 'hero_heading')) ? data_get($homeContent, 'hero_heading') : __('Adıyaman’da butik çiçek ve saksı bitki vitrini.'));
    $subheading = data_get($settings, "subtitle_override.$locale")
        ?: (filled(data_get($homeContent, 'hero_subheading')) ? data_get($homeContent, 'hero_subheading') : __('Yerel ürünler, rafine sunum ve aynı gün teslimat akışı birlikte kurgulandı.'));
    $heroHighlights = collect(data_get($homeContent, 'hero_highlights', []))
        ->filter(fn ($item) => filled(data_get($item, 'label')) && filled(data_get($item, 'value')))
        ->take(2)
        ->values();
@endphp

<x-store-hero
    :featured-product="$heroProduct"
    :heading="$heading"
    :subheading="$subheading"
    :spotlight-eyebrow="data_get($heroSpotlight, 'eyebrow')"
    :spotlight-summary="data_get($heroSpotlight, 'summary')"
    :highlights="$heroHighlights"
/>
