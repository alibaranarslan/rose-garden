@props([
    'product',
    'interactive' => true,
    'eagerImage' => false,
    /** @var 'catalog'|'showcase'|'explore' */
    'layout' => 'catalog',
    'imageAlternateProducts' => null,
])

@php
    $isExplore = $layout === 'explore';
    $isShowcase = $layout === 'showcase';
    $id = data_get($product, 'id');
    $slug = data_get($product, 'slug');
    $name = data_get($product, 'name', '');
    $salePrice = data_get($product, 'sale_price');
    $isNew = (bool) data_get($product, 'is_new', false);
    $path = $product instanceof \App\Models\Product
        ? $product->primaryImage
        : (data_get($product, 'image') ?? data_get($product, 'images.0.image_path'));
    $image = \App\Support\StorefrontImage::resolveProduct($path, $slug, $name);
    $imageSrc = \App\Support\StorefrontImage::publicImgSrc($image);
    $imageOptimizedSrc = \App\Support\StorefrontImage::optimizedImgSrc($imageSrc, $isExplore ? 640 : 960);
    $imageSrcset = \App\Support\StorefrontImage::optimizedImgSrcset($imageSrc, $isExplore ? [320, 480, 640] : [320, 640, 960]);
    $imageSizes = $isExplore
        ? '(min-width: 768px) 18rem, 46vw'
        : ($isShowcase ? '(min-width: 1024px) 26rem, 100vw' : '(max-width: 767px) 46vw, (min-width: 1280px) 18rem, 33vw');
    $imageFallbackSrc = \App\Support\StorefrontImage::productPlaceholderImgSrc();
    $imgLoading = $eagerImage ? 'eager' : 'lazy';
    $imgFetchPriority = $eagerImage ? 'high' : 'low';
    $categoryName = data_get($product, 'categories.0.name');
    $description = trim((string) data_get($product, 'short_description'));
    if ($description === '') {
        $description = \Illuminate\Support\Str::limit(
            strip_tags((string) data_get($product, 'description', '')),
            $isShowcase ? 140 : 96
        );
    }

    $cardPrice = $product instanceof \App\Models\Product
        ? $product->cardPriceDisplay()
        : ['current' => (float) data_get($product, 'price', 0), 'compare' => ! empty($salePrice) ? (float) data_get($product, 'price', 0) : null, 'show_from' => false];

    $rgBadge = 'rounded-full px-2.5 py-0.5 text-[10px] sm:text-[11px] font-semibold tracking-wide';
    $useCoverImage = ! $interactive || $isShowcase;
@endphp

@if ($isExplore)
    <article class="group relative h-full w-full min-h-0 overflow-hidden rounded-[1.2rem] border border-black/[0.07] bg-gradient-to-b from-white to-rg-cream/95 shadow-card-soft ring-1 ring-black/[0.03] transition-[transform,box-shadow] duration-300 ease-rg-out dark:border-white/12 dark:from-[#2a2433] dark:to-[#1c1622] dark:shadow-[0_18px_40px_-14px_rgba(0,0,0,0.5)] dark:ring-white/[0.05] sm:rounded-[1.35rem] sm:hover:-translate-y-0.5 sm:hover:shadow-card-soft-hover">
        <a href="{{ \App\Support\StorefrontLocale::route('products.show', ['slug' => $slug]) }}" class="relative block h-full w-full focus-visible:outline-none">
            <img
                src="{{ $imageOptimizedSrc }}"
                @if ($imageSrcset !== '') srcset="{{ $imageSrcset }}" sizes="{{ $imageSizes }}" @endif
                alt="{{ $name }}"
                loading="{{ $imgLoading }}"
                fetchpriority="{{ $imgFetchPriority }}"
                decoding="async"
                onerror='this.onerror=null;this.src={{ json_encode($imageFallbackSrc) }}'
                class="absolute inset-0 z-0 h-full w-full object-cover object-center transition-transform duration-500 ease-rg-out group-hover:scale-[1.04]"
            >
            <div class="pointer-events-none absolute inset-0 z-[1] bg-[linear-gradient(180deg,rgba(42,31,53,0.06)_0%,rgba(0,0,0,0.18)_32%,rgba(12,8,18,0.88)_100%)] dark:bg-[linear-gradient(180deg,rgba(0,0,0,0.2)_0%,rgba(0,0,0,0.55)_100%)]"></div>
            <div class="pointer-events-none absolute inset-x-0 bottom-0 z-[1] h-[52%] bg-gradient-to-t from-black/82 via-black/35 to-transparent sm:h-[48%]"></div>

            <div class="absolute left-2 top-2 z-10 flex max-w-[calc(100%-1rem)] flex-wrap gap-1.5">
                @if (data_get($product, 'stock_status') !== 'in_stock')
                    <span class="{{ $rgBadge }} bg-black/60 text-white backdrop-blur-sm">{{ __('Tükendi') }}</span>
                @endif
                @if (! empty($salePrice))
                    @php
                        $listForDiscount = (float) data_get($product, 'price', 0);
                        $discount = $listForDiscount > 0 ? max(1, round((($listForDiscount - (float) $salePrice) / $listForDiscount) * 100)) : 0;
                    @endphp
                    <span class="{{ $rgBadge }} bg-rg-rosePink/95 text-rg-deepPurple shadow-sm">%{{ $discount }}</span>
                @endif
                @if ($isNew)
                    <span class="{{ $rgBadge }} bg-rg-leafGreen/95 text-white shadow-sm">{{ __('Yeni') }}</span>
                @endif
            </div>

            <div class="absolute inset-x-0 bottom-0 z-10 p-2.5 sm:p-3">
                <h3 class="line-clamp-2 text-[11px] font-semibold leading-snug tracking-tight text-white drop-shadow-[0_1px_2px_rgba(0,0,0,0.35)] sm:text-xs">
                    {{ $name }}
                </h3>
                <div class="mt-1 flex flex-wrap items-baseline gap-x-2 gap-y-0.5 text-base tabular-nums sm:text-lg">
                    <span class="font-bold text-white drop-shadow-[0_1px_3px_rgba(0,0,0,0.45)]">₺ {{ number_format($cardPrice['current'], 0, ',', '.') }}</span>
                    @if (! empty($cardPrice['compare']))
                        <span class="text-sm tabular-nums text-white/85 line-through decoration-2 decoration-white/55 opacity-95 sm:text-base">₺ {{ number_format($cardPrice['compare'], 0, ',', '.') }}</span>
                    @endif
                    @if (! empty($cardPrice['show_from']))
                        <span class="w-full text-[10px] font-medium leading-tight text-white/82 sm:text-[11px]">{{ __('den başlayan') }}</span>
                    @endif
                </div>
            </div>
        </a>
    </article>
@else
    <article @class([
        'rg-product-card group relative flex h-full flex-col overflow-hidden rounded-[1.55rem] border border-black/5 bg-white/94 shadow-[0_14px_38px_rgba(34,24,40,0.07)] transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-[0_22px_48px_rgba(34,24,40,0.1)] dark:border-white/10 dark:bg-[#1e1a26]',
        'rg-product-card--catalog' => ! $isShowcase,
        'rg-product-card--showcase' => $isShowcase,
        'min-h-[26rem] sm:min-h-[28rem]' => ! $isShowcase,
    ])>
        <div class="absolute inset-x-0 top-0 z-10 flex items-start justify-between gap-2 p-2.5">
            <div class="flex flex-wrap gap-2">
                @if (data_get($product, 'stock_status') !== 'in_stock')
                    <span class="{{ $rgBadge }} bg-[#4b5563] text-white shadow-sm">{{ __('Tükendi') }}</span>
                @endif
                @if (! empty($salePrice))
                    @php
                        $listForDiscount = (float) data_get($product, 'price', 0);
                        $discount = $listForDiscount > 0 ? max(1, round((($listForDiscount - (float) $salePrice) / $listForDiscount) * 100)) : 0;
                    @endphp
                    <span class="{{ $rgBadge }} bg-rg-rosePink text-rg-deepPurple shadow-sm">%{{ $discount }}</span>
                @endif
                @if ($isNew)
                    <span class="{{ $rgBadge }} bg-rg-leafGreen text-white shadow-sm">{{ __('Yeni') }}</span>
                @endif
            </div>

            @if ($interactive)
                <div class="rounded-full bg-white/82 p-1 backdrop-blur dark:bg-[#1b1320]/78">
                    <livewire:favorite-toggle :product-id="$id" :key="'fav-'.$id" />
                </div>
            @endif
        </div>

        <a href="{{ \App\Support\StorefrontLocale::route('products.show', ['slug' => $slug]) }}" @class([
            'relative block shrink-0 overflow-hidden',
            'aspect-[1/1] bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.96),rgba(239,228,235,0.78)_62%,rgba(227,215,224,0.9))] dark:bg-[radial-gradient(circle_at_top,rgba(62,43,72,0.9),rgba(31,23,37,0.96)_60%,rgba(22,15,28,0.98))]' => ! $isShowcase,
            'aspect-[5/4] min-h-[13rem] bg-rg-lightLavender/45 dark:bg-[#2a2633]' => $isShowcase,
        ])>
            <img
                src="{{ $imageOptimizedSrc }}"
                @if ($imageSrcset !== '') srcset="{{ $imageSrcset }}" sizes="{{ $imageSizes }}" @endif
                alt="{{ $name }}"
                loading="{{ $imgLoading }}"
                fetchpriority="{{ $imgFetchPriority }}"
                decoding="async"
                onerror='this.onerror=null;this.src={{ json_encode($imageFallbackSrc) }}'
                class="absolute inset-0 h-full w-full transition-transform duration-700 group-hover:scale-[1.03] {{ $useCoverImage ? 'object-cover object-center' : 'object-contain object-center p-5 sm:p-6' }}"
            >
            <div @class([
                'pointer-events-none absolute inset-0',
                'bg-[linear-gradient(180deg,rgba(255,255,255,0)_55%,rgba(20,10,22,0.1)_100%)]' => ! $useCoverImage,
                'bg-[linear-gradient(180deg,rgba(255,255,255,0)_45%,rgba(20,10,22,0.3)_100%)]' => $useCoverImage,
            ])></div>
        </a>

        <div @class([
            'flex min-h-0 flex-1 flex-col',
            'p-4 md:p-5' => ! $isShowcase,
            'p-5 md:p-6' => $isShowcase,
        ])>
            <div class="min-h-0 space-y-2">
                @if ($categoryName)
                    <p class="rg-product-card-category text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ $categoryName }}</p>
                @endif

                <h3 @class([
                    'rg-product-card-title font-display leading-snug text-rg-deepPurple dark:text-white',
                    'line-clamp-2 min-h-[2.75rem] text-[1.35rem] sm:text-[1.45rem]' => ! $isShowcase,
                    'line-clamp-3 text-[1.45rem] sm:text-[1.55rem]' => $isShowcase,
                ])>
                    <a href="{{ \App\Support\StorefrontLocale::route('products.show', ['slug' => $slug]) }}" class="transition-colors duration-200 hover:text-rg-purple dark:hover:text-rg-lavender">{{ $name }}</a>
                </h3>

                @if ($description !== '')
                    <p @class([
                        'rg-product-card-description leading-relaxed text-rg-grayText dark:text-zinc-300',
                        'line-clamp-3 text-sm' => ! $isShowcase,
                        'line-clamp-3 text-[15px]' => $isShowcase,
                    ])>{{ $description }}</p>
                @endif
            </div>

            <div class="mt-4">
                @if ($interactive)
                    <livewire:add-to-cart :product-id="$id" layout="card" :key="'cart-'.$id" />
                @else
                    <div class="flex flex-col gap-3 rounded-[1.15rem] border border-black/6 bg-rg-cream/62 px-4 py-3.5 dark:border-white/12 dark:bg-[#16121d]">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                                <span class="text-xl font-bold tabular-nums text-rg-deepPurple dark:text-white">₺ {{ number_format($cardPrice['current'], 0, ',', '.') }}</span>
                                @if (! empty($cardPrice['compare']))
                                    <span class="text-sm tabular-nums text-rg-grayText line-through decoration-2 decoration-rg-mauve/70 opacity-90 dark:text-zinc-400 dark:decoration-rg-mauve/50">₺ {{ number_format($cardPrice['compare'], 0, ',', '.') }}</span>
                                @endif
                                @if (! empty($cardPrice['show_from']))
                                    <span class="basis-full text-xs font-semibold uppercase tracking-[0.16em] text-rg-grayText dark:text-zinc-400">{{ __('Başlayan fiyatlarla') }}</span>
                                @endif
                            </div>
                        </div>

                        <a href="{{ \App\Support\StorefrontLocale::route('products.show', ['slug' => $slug]) }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-rg-deepPurple/8 px-4 py-2.5 text-sm font-semibold text-rg-deepPurple transition-colors duration-200 hover:bg-rg-deepPurple/12 dark:bg-white/10 dark:text-zinc-100 dark:hover:bg-white/16 sm:w-fit sm:justify-start">
                            {{ __('Ürünü Gör') }}
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </article>
@endif
