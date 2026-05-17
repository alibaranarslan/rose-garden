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
        <div class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <span class="rg-kicker">{{ __('Ürün Seçkisi') }}</span>
                <h2 class="mt-3 text-balance font-display text-3xl text-rg-deepPurple dark:text-white md:text-4xl">{{ $resolvedTitle }}</h2>
                @if ($resolvedSubtitle)
                    <p class="mt-3 text-pretty text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ $resolvedSubtitle }}</p>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="rounded-full border border-black/6 bg-white/82 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-rg-grayText dark:border-white/10 dark:bg-white/8 dark:text-white/72">
                    {{ trans_choice(':count ürün', $renderableProducts->count(), ['count' => $renderableProducts->count()]) }}
                </span>
                <a href="{{ $routeUrl }}" class="rg-inline-link">{{ $routeLabel }}</a>
            </div>
        </div>

        <x-product-rail :products="$renderableProducts" :interactive="false" />
    </section>
@endif
