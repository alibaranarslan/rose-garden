@php
    $locale = app()->getLocale();
    $resolvedTitle = data_get($settings, "title_override.$locale") ?: $title;
    $resolvedSubtitle = data_get($settings, "subtitle_override.$locale") ?: ($subtitle ?? null);
    $renderableProducts = collect($products)
        ->filter(function ($product) {
            $name = trim((string) data_get($product, 'name'));
            $slug = trim((string) data_get($product, 'slug'));

            return $name !== '' && $slug !== '';
        })
        ->values();
@endphp

@if ($renderableProducts->isNotEmpty())
    <section class="rg-section">
        <div class="mb-4 flex items-end justify-between gap-4">
            <div class="max-w-3xl">
                <h2 class="text-balance font-display text-2xl text-rg-deepPurple dark:text-white md:text-3xl">{{ $resolvedTitle }}</h2>
                @if ($resolvedSubtitle)
                    <p class="mt-2 text-pretty text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ $resolvedSubtitle }}</p>
                @endif
            </div>
            <a href="{{ $routeUrl }}" class="rg-inline-link shrink-0">{{ $routeLabel }}</a>
        </div>

        <x-product-rail :products="$renderableProducts" :interactive="true" />
    </section>
@endif
