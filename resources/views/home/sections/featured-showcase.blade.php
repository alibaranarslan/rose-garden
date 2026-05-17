@php
    if (! $featuredShowcase) {
        return;
    }

    $locale = app()->getLocale();
    $title = data_get($settings, "title_override.$locale")
        ?: (filled(data_get($homeContent, 'showcase_heading')) ? data_get($homeContent, 'showcase_heading') : $featuredShowcase->name);
    $subtitle = data_get($settings, "subtitle_override.$locale")
        ?: (filled(data_get($homeContent, 'showcase_body')) ? data_get($homeContent, 'showcase_body') : \Illuminate\Support\Str::limit(strip_tags((string) data_get($featuredShowcase, 'short_description', data_get($featuredShowcase, 'description', ''))), 150));
    $showcaseImage = \App\Support\StorefrontImage::publicImgSrc(\App\Support\StorefrontImage::resolveProduct(
        $featuredShowcase->primaryImage,
        $featuredShowcase->slug,
        $featuredShowcase->name,
    ));
    $priceDisplay = $featuredShowcase->cardPriceDisplay();
    $categoryName = data_get($featuredShowcase, 'categories.0.name');
    $showcaseCompanions = collect($showcaseCompanions ?? [])->take(2);
@endphp

<section class="rg-section">
    <article class="overflow-hidden rounded-[2rem] border border-black/[0.06] bg-white/90 shadow-[0_20px_54px_rgba(34,24,40,0.08)] dark:border-white/10 dark:bg-[#1f1826]">
        <div class="grid gap-0 lg:grid-cols-[minmax(0,0.96fr)_minmax(0,1.04fr)] lg:items-stretch">
            <div class="flex flex-col px-5 py-6 md:px-6 md:py-7">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="rg-kicker">{{ __('Seçili Vitrin') }}</span>
                        <h2 class="mt-3 text-balance font-display text-3xl leading-[1.08] text-rg-deepPurple dark:text-white md:text-[2.35rem]">{{ $title }}</h2>
                    </div>
                    @if ($categoryName)
                        <span class="hidden rounded-full border border-black/6 bg-rg-cream/80 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-rg-deepPurple dark:border-white/10 dark:bg-white/8 dark:text-white/86 md:inline-flex">
                            {{ $categoryName }}
                        </span>
                    @endif
                </div>

                <p class="mt-4 max-w-2xl text-sm leading-[1.8] text-rg-grayText dark:text-white/84 md:text-[15px]">{{ $subtitle }}</p>

                <div class="mt-5 flex flex-wrap items-baseline gap-x-3 gap-y-2 rounded-[1.35rem] border border-black/6 bg-rg-cream/55 px-4 py-4 dark:border-white/10 dark:bg-white/8">
                    @if ($priceDisplay)
                        <span class="text-2xl font-semibold tabular-nums text-rg-deepPurple dark:text-white">&#8378; {{ number_format($priceDisplay['current'], 0, ',', '.') }}</span>
                        @if (! empty($priceDisplay['compare']))
                            <span class="text-sm text-rg-grayText line-through dark:text-zinc-500">&#8378; {{ number_format($priceDisplay['compare'], 0, ',', '.') }}</span>
                        @endif
                    @endif
                    <span class="text-xs font-semibold uppercase tracking-[0.18em] text-rg-grayText dark:text-white/72">{{ __('Siparişe özel hazırlanır') }}</span>
                    @if ($showcaseCompanions->isNotEmpty())
                        <span class="text-xs font-semibold uppercase tracking-[0.18em] text-rg-midPurple dark:text-rg-lavender/78">{{ __('Daha fazla ürün proof') }}</span>
                    @endif
                </div>

                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ \App\Support\StorefrontLocale::route('products.show', ['slug' => $featuredShowcase->slug]) }}" class="inline-flex items-center justify-center rounded-full bg-rg-deepPurple px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-rg-purple">
                        {{ __('Ürünü incele') }}
                    </a>
                    <a href="{{ \App\Support\StorefrontLocale::route('products.index', ['sort' => 'best_sellers']) }}" class="rg-button-secondary">
                        {{ __('Çok satanlara geç') }}
                    </a>
                </div>

                @if ($showcaseCompanions->isNotEmpty())
                    <div class="mt-6 rounded-[1.5rem] border border-black/6 bg-white/80 p-4 dark:border-white/10 dark:bg-[#17111d]">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Aynı akışta') }}</p>
                                <h3 class="mt-1 text-sm font-semibold text-rg-deepPurple dark:text-white">{{ __('Bu vitrine yakın ürünler') }}</h3>
                            </div>
                            <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="text-xs font-semibold uppercase tracking-[0.18em] text-rg-grayText transition-colors hover:text-rg-purple dark:text-white/72 dark:hover:text-rg-lavender">
                                {{ __('Tümünü aç') }}
                            </a>
                        </div>

                        <div class="grid gap-3">
                            @foreach ($showcaseCompanions as $product)
                                <x-product-card-mini :product="$product" />
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="relative border-t border-black/6 bg-rg-lightLavender/25 dark:border-white/10 dark:bg-white/6 lg:border-l lg:border-t-0">
                <a href="{{ \App\Support\StorefrontLocale::route('products.show', ['slug' => $featuredShowcase->slug]) }}" class="block aspect-[5/4] overflow-hidden bg-rg-lightLavender/30 dark:bg-white/8 lg:h-full lg:min-h-[32rem] lg:aspect-auto">
                    <img src="{{ $showcaseImage }}" alt="{{ $featuredShowcase->name }}" loading="lazy" class="h-full w-full object-cover object-center transition-transform duration-700 hover:scale-[1.02]">
                </a>

                <div class="pointer-events-none absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-[#1d131f]/65 to-transparent"></div>

                <div class="absolute inset-x-4 bottom-4 flex flex-wrap items-end justify-between gap-3 rounded-[1.4rem] border border-white/25 bg-[#1f1622]/74 px-4 py-4 text-white shadow-[0_20px_44px_rgba(18,10,20,0.25)] backdrop-blur-md">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-white/72">{{ __('Satın alma yönü') }}</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ __('Vitrinden PDP’ye tek adımda geçiş') }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full border border-white/18 bg-white/10 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.18em] text-white/88">
                        {{ __('Gerçek ürün') }}
                    </span>
                </div>
            </div>
        </div>
    </article>
</section>
