{{-- Legacy homepage reference view only. Live homepage ownership is home.layout-studio via StorefrontHomeController@index. --}}
@extends('layouts.app')

@php
    $heroProduct = $heroSpotlightProduct ?? $featuredProducts->first() ?? $newProducts->first() ?? $bestSellers->first();
    $heroHeading = filled(data_get($homeContent, 'hero_heading'))
        ? data_get($homeContent, 'hero_heading')
        : __('Adıyaman’da butik çiçek ve saksı bitki seçkisi, daha sakin ve profesyonel bir mağaza ritmiyle sunuluyor.');
    $heroSubheading = filled(data_get($homeContent, 'hero_subheading'))
        ? data_get($homeContent, 'hero_subheading')
        : __('Rose Garden vitrini yalnızca yerel ürün görselleriyle çalışır. Buketler ve saksı bitkileri; hazırlık kalitesi, teslimat akışı ve jest etkisi birlikte düşünülerek sunulur.');

    $heroHighlights = collect(data_get($homeContent, 'hero_highlights', []))
        ->filter(fn ($item) => filled(data_get($item, 'label')) && filled(data_get($item, 'value')))
        ->take(2)
        ->values();

    if ($heroHighlights->isEmpty()) {
        $heroHighlights = collect([
            ['label' => __('Canlı Katalog'), 'value' => __('Yerel ürün görselleriyle kurulan, teslime hazır vitrin.')],
            ['label' => __('Butik Akış'), 'value' => __('Hazırlık, not kartı ve teslimat dili tek ritimde ilerler.')],
        ]);
    }

    $collectionHeading = filled(data_get($homeContent, 'home_intro_heading'))
        ? data_get($homeContent, 'home_intro_heading')
        : __('Teslime hazır bir katalog, sade bir keşif akışıyla daha güçlü görünür.');
    $collectionBody = filled(data_get($homeContent, 'home_intro_body'))
        ? data_get($homeContent, 'home_intro_body')
        : __('Ana sayfa; kategori keşfi, editoryal seçki ve ticari vitrin akışlarını birbirinden ayıracak şekilde yeniden dengelendi. Böylece ziyaretçi hem marka tonunu hisseder hem de ürün seçimine daha rahat yaklaşır.');

    $collectionPoints = collect(data_get($homeContent, 'home_intro_points', []))
        ->filter(fn ($item) => filled(data_get($item, 'title')) && filled(data_get($item, 'text')))
        ->take(3)
        ->values();

    if ($collectionPoints->isEmpty()) {
        $collectionPoints = collect([
            ['title' => __('Teslim Hissi'), 'text' => __('Vitrin artık müşteriye sunulacak gerçek katalog ölçeğinde, daha net karar alanlarıyla çalışıyor.')],
            ['title' => __('Admin Kontrolü'), 'text' => __('Galeri, highlight kartları, varyantlar ve etiketler panelden yönetilebilir kurguda korunuyor.')],
            ['title' => __('Kategori Derinliği'), 'text' => __('Buketler ve saksı bitkileri alt kataloglarla birlikte daha okunaklı biçimde keşfediliyor.')],
        ]);
    }

    $showcaseProduct = $featuredShowcase ?? null;
    $showcaseImage = $showcaseProduct
        ? \App\Support\StorefrontImage::publicImgSrc(\App\Support\StorefrontImage::resolveProduct(
            $showcaseProduct->primaryImage,
            $showcaseProduct->slug,
            $showcaseProduct->name,
        ))
        : \App\Support\StorefrontImage::productPlaceholderImgSrc();
    $showcasePrice = $showcaseProduct?->cardPriceDisplay();
    $showcaseBody = filled(data_get($homeContent, 'showcase_body'))
        ? data_get($homeContent, 'showcase_body')
        : \Illuminate\Support\Str::limit(strip_tags((string) data_get($showcaseProduct, 'short_description', data_get($showcaseProduct, 'description', ''))), 170);

    $bestSellersHeading = filled(data_get($homeContent, 'best_sellers_heading'))
        ? data_get($homeContent, 'best_sellers_heading')
        : __('Kararı hızlandıran çok satan seçimler');
    $bestSellersBody = filled(data_get($homeContent, 'best_sellers_body'))
        ? data_get($homeContent, 'best_sellers_body')
        : __('En çok ilgi gören yerel ürünler; kararsız müşteriler için daha hızlı okunabilen ikinci bir ticari vitrin oluşturur.');

    $trustAccentProducts = $featuredProducts
        ->concat($newProducts)
        ->concat($bestSellers)
        ->unique('id')
        ->take(4)
        ->values();

    $blogCards = $blogPosts->map(function ($post) {
        $coverImage = \App\Support\StorefrontImage::publicImgSrc(\App\Support\StorefrontImage::resolveBlogPostCoverUrl($post));

        return [
            'slug' => $post->slug,
            'title' => $post->title,
            'excerpt' => $post->excerpt,
            'cover_image' => $coverImage,
            'cover_illustration' => \App\Support\StorefrontImage::isBlogDecorativeCover($coverImage),
            'published_label' => $post->published_at
                ? $post->published_at->locale(app()->getLocale())->translatedFormat('j F Y')
                : '',
            'published_at' => $post->published_at?->toDateString(),
        ];
    });

    $leadBlog = $blogCards->first();
    $secondaryBlogs = $blogCards->slice(1, 2)->values();
@endphp

@push('before_main')
    <x-store-hero
        :featured-product="$heroProduct"
        :heading="$heroHeading"
        :subheading="$heroSubheading"
        :spotlight-eyebrow="$heroSpotlightEyebrow"
        :spotlight-summary="$heroSpotlightSummary"
        :highlights="$heroHighlights"
    />
@endpush

@section('content')
    <section class="rg-section">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,0.68fr)_minmax(0,1.32fr)] lg:items-start">
            <div class="rg-surface px-5 py-6 md:px-6 md:py-7">
                <span class="rg-kicker">{{ __('Koleksiyon Akışı') }}</span>
                <h2 class="mt-4 max-w-2xl text-balance font-display text-4xl leading-[1.08] text-rg-deepPurple dark:text-white md:text-[2.8rem]">
                    {{ $collectionHeading }}
                </h2>
                <p class="mt-4 max-w-xl text-pretty text-base leading-[1.75] text-rg-grayText dark:text-zinc-200">
                    {{ $collectionBody }}
                </p>

                <div class="mt-6 space-y-3">
                    @foreach ($collectionPoints as $point)
                        <div class="rounded-[1.35rem] border border-black/6 bg-rg-cream/70 px-4 py-4 dark:border-white/10 dark:bg-[#252030]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-[#e8dcf8]">{{ data_get($point, 'title') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-zinc-200">{{ data_get($point, 'text') }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
                <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="inline-flex items-center justify-center rounded-full bg-rg-deepPurple px-5 py-3 text-sm font-semibold text-white transition-colors duration-200 hover:bg-rg-purple">
                        {{ __('Tüm koleksiyonu aç') }}
                    </a>
                <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.index') }}" class="rg-button-secondary">
                        {{ __('Özel gün seçkilerine geç') }}
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4">
                @foreach ($categories as $category)
                    <x-category-card :category="$category" :featured="$loop->first" />
                @endforeach
            </div>
        </div>
    </section>

    @if ($showcaseProduct)
        <section class="rg-section">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.12fr)_minmax(0,0.88fr)] lg:items-center">
                <article class="overflow-hidden rounded-[2rem] border border-black/[0.06] bg-white/88 shadow-[0_22px_58px_rgba(34,24,40,0.09)] dark:border-white/10 dark:bg-[#1f1826]">
                    <div class="grid gap-0 md:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
                        <div class="aspect-[5/6] overflow-hidden bg-rg-lightLavender/30 dark:bg-white/8">
                            <img src="{{ $showcaseImage }}" alt="{{ $showcaseProduct->name }}" loading="lazy" decoding="async" class="h-full w-full object-cover object-center">
                        </div>
                        <div class="flex h-full flex-col justify-between px-5 py-6 md:px-6 md:py-7">
                            <div>
                                <span class="rg-kicker">{{ __('Editoryal Seçki') }}</span>
                                <h2 class="mt-4 text-balance font-display text-3xl leading-[1.08] text-rg-deepPurple dark:text-white md:text-[2.45rem]">
                                    {{ $showcaseProduct->name }}
                                </h2>
                                <p class="mt-4 text-pretty text-sm leading-[1.8] text-rg-grayText dark:text-white/84 md:text-[15px]">
                                    {{ $showcaseBody }}
                                </p>
                            </div>

                            <div class="mt-6 space-y-4">
                                @if ($showcasePrice)
                                    <div class="rounded-[1.3rem] border border-black/6 bg-rg-cream/70 px-4 py-4 dark:border-white/12 dark:bg-[#17131f]">
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-[#dcc8ee]">{{ __('Fiyat') }}</p>
                                        <div class="mt-3 flex flex-wrap items-baseline gap-x-3 gap-y-1">
                                            <span class="text-3xl font-bold tabular-nums text-rg-deepPurple dark:text-white">₺ {{ number_format($showcasePrice['current'], 0, ',', '.') }}</span>
                                            @if (! empty($showcasePrice['compare']))
                                                <span class="text-base tabular-nums text-rg-grayText line-through decoration-2 decoration-rg-mauve/70 dark:text-zinc-400">₺ {{ number_format($showcasePrice['compare'], 0, ',', '.') }}</span>
                                            @endif
                                            @if (! empty($showcasePrice['show_from']))
                                                <span class="text-sm font-medium text-rg-grayText dark:text-white/80">{{ __('Başlayan fiyatlarla') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <a href="{{ \App\Support\StorefrontLocale::route('products.show', ['slug' => $showcaseProduct->slug]) }}" class="inline-flex items-center justify-center rounded-full bg-rg-deepPurple px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-rg-purple">
                                        {{ __('Ürünü incele') }}
                                    </a>
                <a href="{{ \App\Support\StorefrontLocale::route('products.index', ['sort' => 'best_sellers']) }}" class="rg-button-secondary">
                                        {{ __('Çok satanlara geç') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>

                <div class="rg-surface px-5 py-6 md:px-6 md:py-7">
                    <span class="rg-kicker">{{ __('Vitrin Rolü') }}</span>
                    <h2 class="mt-4 text-balance font-display text-3xl leading-[1.1] text-rg-deepPurple dark:text-white">
                        {{ __('Bu alan, hero’dan sonra tek bir güçlü ürünü sakin bir odakta tutar.') }}
                    </h2>
                    <p class="mt-4 text-sm leading-[1.85] text-rg-grayText dark:text-white/84 md:text-[15px]">
                        {{ __('Açılış yüzeyi marka vaadini taşır; editoryal seçki ise doğrudan ürün kararına yaklaşır. Böylece ana sayfa iki farklı “öne çıkan” bölümü aynı işi yapmadan birlikte taşır.') }}
                    </p>
                    <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                        <div class="rounded-[1.3rem] border border-black/6 bg-rg-cream/72 px-4 py-4 dark:border-white/10 dark:bg-[#241f2c]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Keşif') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ __('Kategori blokları daha geniş katalog resmi verir; editoryal seçki tek üründe karar hissi kurar.') }}</p>
                        </div>
                        <div class="rounded-[1.3rem] border border-black/6 bg-rg-cream/72 px-4 py-4 dark:border-white/10 dark:bg-[#241f2c]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Teslim Dili') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ __('Ürün notu, fiyat ve yönlendirme aynı blokta toplandığı için kullanıcı daha az dağılır.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($newProducts->isNotEmpty())
        <section class="rg-section">
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div class="max-w-3xl">
                    <span class="rg-kicker">{{ __('Yeni Gelenler') }}</span>
                    <h2 class="mt-3 text-balance font-display text-3xl text-rg-deepPurple dark:text-white md:text-4xl">
                        {{ __('Koleksiyona yeni eklenen ürünler') }}
                    </h2>
                </div>
                <a href="{{ \App\Support\StorefrontLocale::route('products.index', ['sort' => 'newest']) }}" class="rg-inline-link shrink-0">{{ __('Tamamını gör') }}</a>
            </div>

            <x-product-rail :products="$newProducts" :interactive="false" />
        </section>
    @endif

    @if ($bestSellers->isNotEmpty())
        <section class="rg-section">
            <div class="rg-surface px-5 py-6 md:px-6 md:py-7">
                <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div class="max-w-3xl">
                        <span class="rg-kicker">{{ __('Çok Satanlar') }}</span>
                        <h2 class="mt-3 text-balance font-display text-3xl text-rg-deepPurple dark:text-white md:text-4xl">{{ $bestSellersHeading }}</h2>
                    </div>
                    <p class="max-w-xl text-pretty text-sm leading-relaxed text-rg-grayText md:text-base dark:text-white/84">{{ $bestSellersBody }}</p>
                </div>

                <x-product-rail :products="$bestSellers" :interactive="false" />
            </div>
        </section>
    @endif

    @if ($activeOccasion)
        <section class="rg-section">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,0.82fr)_minmax(0,1.18fr)] lg:items-center">
                <div class="rg-surface px-5 py-6 md:px-6 md:py-7">
                    <span class="rg-kicker">{{ __('Yaklaşan Özel Gün') }}</span>
                    <h2 class="mt-3 text-balance font-display text-3xl text-rg-deepPurple dark:text-white md:text-4xl">
                        {{ $activeOccasion->name }}
                    </h2>
                    <p class="mt-4 text-pretty text-base leading-[1.75] text-rg-grayText dark:text-zinc-200">
                        {{ __('Yaklaşan tarih için uygun ürünleri, karar vermeyi kolaylaştıran daha sade bir vitrin yüzeyinde topladık.') }}
                    </p>
                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $activeOccasion->slug]) }}" class="inline-flex items-center justify-center rounded-full bg-rg-purple px-5 py-3 text-sm font-semibold text-white transition-colors duration-200 hover:bg-rg-darkPlum">
                            {{ __('Özel gün seçkisini aç') }}
                        </a>
                <a href="{{ \App\Support\StorefrontLocale::route('contact') }}" class="rg-button-secondary">
                            {{ __('Kişisel tasarım talep et') }}
                        </a>
                    </div>
                </div>

                <div class="overflow-hidden rounded-[1.85rem] border border-black/5 bg-[linear-gradient(135deg,rgba(255,255,255,0.94),rgba(246,236,242,0.98))] px-5 py-6 shadow-[0_18px_55px_rgba(34,24,40,0.08)] md:px-6 md:py-7 dark:border-white/10 dark:bg-[linear-gradient(135deg,rgba(38,24,42,0.92),rgba(31,20,36,0.96))]">
                    @if ($occasionProducts->isNotEmpty())
                        <x-product-rail :products="$occasionProducts" :interactive="false" card-width="w-[78vw] min-[480px]:w-[16rem] md:w-[17rem] lg:w-[18rem]" />
                    @else
                        <div class="rounded-[1.5rem] border border-black/6 bg-rg-cream/70 px-5 py-5 dark:border-white/10 dark:bg-[#252030]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-[#dcc8ee]">{{ __('Teslimat Notu') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-zinc-200">{{ __('Sipariş yoğunluğu olan tarihler için erken planlama önerilir.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <x-trust-badges :accent-products="$trustAccentProducts" :fallback-image-urls="[]" />

    @if ($leadBlog)
        <section class="rg-section">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,0.56fr)_minmax(0,1.44fr)] lg:items-start">
                <aside class="rg-surface px-5 py-6 md:px-6 md:py-7">
                    <span class="rg-kicker">{{ __('Marka Notu') }}</span>
                    <h2 class="mt-4 text-balance font-display text-3xl leading-[1.08] text-rg-deepPurple dark:text-white">
                        {{ __('Floral dili korurken daha sakin ve daha kurumsal bir yüzey kurduk.') }}
                    </h2>
                    <p class="mt-4 text-sm leading-[1.85] text-rg-grayText dark:text-white/84 md:text-[15px]">
                        {{ __('Renk, tipografi ve yüzey dili romantik ama kararsızlığa düşmeyen bir seviyeye çekildi. Böylece site hem estetik hem operasyonel açıdan güven veren bir vitrin gibi çalışıyor.') }}
                    </p>

                    <div class="mt-6 space-y-3">
                        <div class="rounded-[1.25rem] border border-black/6 bg-rg-cream/72 px-4 py-4 dark:border-white/10 dark:bg-[#241f2c]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Ton') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ __('Hero, katalog ve utility sayfalar artık birbirinden daha net ayrılıyor.') }}</p>
                        </div>
                        <div class="rounded-[1.25rem] border border-black/6 bg-rg-cream/72 px-4 py-4 dark:border-white/10 dark:bg-[#241f2c]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Teslim') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ __('Müşteri ürünü, destek bilgisini ve iletişim yolunu daha hızlı ayırt edebiliyor.') }}</p>
                        </div>
                    </div>
                </aside>

                <div class="space-y-5">
                    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                        <div>
                            <span class="rg-kicker">{{ __('Blog') }}</span>
                            <h2 class="mt-3 font-display text-3xl text-rg-deepPurple dark:text-white md:text-4xl">{{ __('Çiçek seçimi ve teslimat deneyimi üzerine notlar') }}</h2>
                        </div>
            <a href="{{ \App\Support\StorefrontLocale::route('blog.index') }}" class="rg-inline-link">{{ __('Tüm yazıları aç') }}</a>
                    </div>

                    <div class="grid gap-5 xl:grid-cols-[minmax(0,1.08fr)_minmax(0,0.92fr)]">
                        <article class="rg-surface flex h-full flex-col overflow-hidden">
                    <a href="{{ \App\Support\StorefrontLocale::route('blog.show', ['slug' => $leadBlog['slug']]) }}" class="block aspect-[16/10] overflow-hidden {{ $leadBlog['cover_illustration'] ? 'bg-[linear-gradient(135deg,#f8ede7,#f4e8f1)] dark:bg-[linear-gradient(135deg,#2d2133,#23182c)]' : 'bg-rg-lightLavender/45 dark:bg-[#2a2633]' }}">
                                <img
                                    src="{{ $leadBlog['cover_image'] }}"
                                    alt="{{ $leadBlog['title'] }}"
                                    loading="lazy"
                                    class="h-full w-full transition-transform duration-500 {{ $leadBlog['cover_illustration'] ? 'object-contain p-10 hover:scale-[1.02]' : 'object-cover object-center hover:scale-[1.04]' }}"
                                >
                            </a>
                            <div class="flex flex-1 flex-col p-5 sm:p-6">
                                <time datetime="{{ $leadBlog['published_at'] }}" class="text-xs font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender">
                                    {{ $leadBlog['published_label'] }}
                                </time>
                                <h3 class="mt-3 text-balance font-display text-[1.95rem] leading-tight text-rg-deepPurple dark:text-white">
                        <a href="{{ \App\Support\StorefrontLocale::route('blog.show', ['slug' => $leadBlog['slug']]) }}" class="transition-colors duration-200 hover:text-rg-purple dark:hover:text-rg-lavender">{{ $leadBlog['title'] }}</a>
                                </h3>
                                <p class="rg-copy-muted mt-3 flex-1 text-pretty text-sm leading-relaxed sm:text-[0.98rem]">{{ $leadBlog['excerpt'] }}</p>
                    <a href="{{ \App\Support\StorefrontLocale::route('blog.show', ['slug' => $leadBlog['slug']]) }}" class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-rg-deepPurple transition-colors duration-200 hover:text-rg-purple dark:text-white dark:hover:text-rg-lavender">
                                    {{ __('Devamını Oku') }}
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </article>

                        <div class="grid gap-5">
                            @foreach ($secondaryBlogs as $card)
                                <article class="rg-surface flex h-full overflow-hidden">
                    <a href="{{ \App\Support\StorefrontLocale::route('blog.show', ['slug' => $card['slug']]) }}" class="grid h-full w-full min-h-[15rem] md:grid-cols-[minmax(10rem,0.9fr)_minmax(0,1.1fr)]">
                                        <div class="overflow-hidden {{ $card['cover_illustration'] ? 'bg-[linear-gradient(135deg,#f8ede7,#f4e8f1)] dark:bg-[linear-gradient(135deg,#2d2133,#23182c)]' : 'bg-rg-lightLavender/45 dark:bg-[#2a2633]' }}">
                                            <img
                                                src="{{ $card['cover_image'] }}"
                                                alt="{{ $card['title'] }}"
                                                loading="lazy"
                                                class="h-full w-full transition-transform duration-500 {{ $card['cover_illustration'] ? 'object-contain p-8 hover:scale-[1.02]' : 'object-cover object-center hover:scale-[1.04]' }}"
                                            >
                                        </div>
                                        <div class="flex min-h-0 flex-1 flex-col p-5">
                                            <time datetime="{{ $card['published_at'] }}" class="text-xs font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender">
                                                {{ $card['published_label'] }}
                                            </time>
                                            <h3 class="mt-3 text-balance font-display text-2xl leading-snug text-rg-deepPurple dark:text-white">{{ $card['title'] }}</h3>
                                            <p class="rg-copy-muted mt-3 flex-1 text-pretty text-sm leading-relaxed">{{ $card['excerpt'] }}</p>
                                            <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-rg-deepPurple dark:text-white">
                                                {{ __('Yazıyı Aç') }}
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </span>
                                        </div>
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
@endsection

