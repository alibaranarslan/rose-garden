@extends('layouts.app')

@section('content')
    @php
        $occasionTitle = $occasion->getTranslation('name', app()->getLocale());
        $occasionCategory = $occasion->category?->getTranslation('name', app()->getLocale());
        $occasionDate = $occasion->nextOccurrence()->locale(app()->getLocale())->translatedFormat('d F');
        $occasionDayLabel = $occasion->isToday()
            ? __('Bugün için hazır')
            : __(':count gün kaldı', ['count' => $occasion->daysUntil()]);

        $occasionVisuals = collect(
            \App\Support\StorefrontImage::specialOccasionGallery(
                $occasion->slug,
                $occasionTitle,
                $occasionCategory,
                $occasion->category?->slug,
            )
        )
            ->map(fn (string $path) => \App\Support\StorefrontImage::publicImgSrc($path))
            ->filter()
            ->values();

        if ($occasionVisuals->isEmpty()) {
            $occasionVisuals = collect([\App\Support\StorefrontImage::productPlaceholderImgSrc()]);
        }

        $primaryVisual = $occasionVisuals->get(0);
        $secondaryVisual = $occasionVisuals->get(1, $primaryVisual);
        $tertiaryVisual = $occasionVisuals->get(2, $secondaryVisual);

        $occasionSummary = $occasionCategory
            ? __(':category kategorisindeki seçili ürünler ve bu güne özel atanan tasarımlar, aynı koleksiyonda bir araya gelir.', ['category' => $occasionCategory])
            : __('Bu özel gün için seçilmiş çiçek ve hediye tasarımları birlikte sunulur.');

        $curationNotes = [
            __('Kart mesajını kısa ve kişisel tutmak, aranjmanın etkisini daha sıcak bir jeste dönüştürür.'),
            __('Aynı gün teslim planlanan siparişlerde ilk vitrindeki ürünler hızlı karar için ideal bir başlangıç sunar.'),
            __('Çikolata ya da hediye eşliği, seçimi daha tamamlanmış ve zarif bir sunuma taşır.'),
        ];
    @endphp

    <x-breadcrumb :items="[
        ['label' => __('Anasayfa'), 'url' => \App\Support\StorefrontLocale::route('home')],
        ['label' => __('Özel Günler'), 'url' => \App\Support\StorefrontLocale::route('special-occasions.index')],
        ['label' => $occasionTitle, 'url' => null],
    ]" />

    <div class="space-y-8 md:space-y-10">
        <section class="rg-occasion-stage rg-occasion-theme rg-occasion-theme--{{ $occasion->slug }}">
            <div class="grid gap-6 xl:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)] xl:items-center">
                <div class="space-y-5">
                    <span class="rg-kicker">{{ __('Özel Gün Koleksiyonu') }}</span>

                    <div class="max-w-2xl space-y-3">
                        <h1 class="font-display text-4xl leading-tight text-rg-copy-strong dark:text-white md:text-5xl xl:text-[3.25rem]">
                            {{ $occasionTitle }}
                        </h1>
                        <p class="max-w-xl text-sm leading-7 text-rg-copy-muted dark:text-white/78 md:text-[15px]">
                            {{ $occasionSummary }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2.5">
                        <span class="rg-occasion-chip">{{ $occasionDate }}</span>
                        <span class="rg-occasion-chip">{{ $occasionDayLabel }}</span>
                        <span class="rg-occasion-chip">{{ trans_choice(':count seçili tasarım', $products->total(), ['count' => $products->total()]) }}</span>

                        @if ($occasion->loyalty_multiplier > 1)
                            <span class="rg-occasion-chip">
                                {{ __('Paraçiçek x:multiplier', ['multiplier' => rtrim(rtrim((string) $occasion->loyalty_multiplier, '0'), '.')]) }}
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="#occasion-products"
                           class="inline-flex items-center justify-center rounded-full bg-rg-purple px-5 py-2.5 text-sm font-semibold text-white transition-all duration-200 hover:bg-rg-darkPlum">
                            {{ __('Koleksiyonu İncele') }}
                        </a>
                        <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.index') }}" class="rg-button-secondary">
                            {{ __('Diğer Özel Günler') }}
                        </a>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rg-occasion-stage-stat">
                            <span class="text-[11px] uppercase tracking-[0.24em] text-rg-copy-soft dark:text-white/48">{{ __('Seçim Dili') }}</span>
                            <strong class="mt-3 block text-base text-rg-copy-strong dark:text-white">{{ __('Daha rafine, daha net bir vitrin') }}</strong>
                            <p class="mt-2 text-sm leading-6 text-rg-copy-muted dark:text-white/84">
                                {{ __('Aynı günün ruhuna uygun ürünleri sade ve net bir seçkiyle inceleyebilirsiniz.') }}
                            </p>
                        </div>

                        <div class="rg-occasion-stage-stat">
                            <span class="text-[11px] uppercase tracking-[0.24em] text-rg-copy-soft dark:text-white/48">{{ __('Teslimat Ritmi') }}</span>
                            <strong class="mt-3 block text-base text-rg-copy-strong dark:text-white">{{ __('Aynı gün teslimata uygun') }}</strong>
                            <p class="mt-2 text-sm leading-6 text-rg-copy-muted dark:text-white/84">
                                {{ __('Hızlı seçilebilen ürünler üst vitrinde öne çıkar; tüm koleksiyon aşağıda sakin bir grid içinde devam eder.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-[minmax(0,1.08fr)_minmax(0,0.92fr)]">
                    <a href="#occasion-products" class="rg-occasion-photo-card rg-occasion-photo-card--hero">
                        <img src="{{ $primaryVisual }}" alt="{{ $occasionTitle }}" class="h-full w-full object-cover object-center">
                        <div class="rg-occasion-photo-card__content">
                            <span class="rg-occasion-photo-card__eyebrow">{{ __('Seçili Vitrin') }}</span>
                            <h2 class="font-display text-[2rem] leading-tight text-white md:text-[2.3rem]">
                                {{ __('Kutlamaya hazır butik seçimler') }}
                            </h2>
                            <p class="max-w-sm text-sm leading-6 text-white/78">
                                {{ __('Bu sayfada yer alan ürünler, hem günün tonuna hem de teslim beklentisine uyacak şekilde birlikte kurgulanır.') }}
                            </p>
                        </div>
                    </a>

                    <div class="grid gap-4">
                        <div class="overflow-hidden rounded-[1.45rem] border border-black/6 bg-white/65 shadow-[0_14px_30px_rgba(34,24,40,0.06)] dark:border-white/10 dark:bg-white/10">
                            <div class="aspect-[1.08/1]">
                                <img src="{{ $secondaryVisual }}" alt="{{ $occasionTitle }}" class="h-full w-full object-cover object-center">
                            </div>
                        </div>

                        <div class="rg-surface-soft px-5 py-5">
                            <span class="rg-kicker">{{ __('Koleksiyon Notu') }}</span>
                            <h2 class="mt-3 font-display text-2xl text-rg-copy-strong dark:text-white">
                                {{ __('Duygu, teslimat ve sunum aynı çizgide') }}
                            </h2>
                            <p class="mt-3 text-sm leading-7 text-rg-copy-muted dark:text-white/86">
                                {{ __('Çiçek, hediye ve not seçeneklerini aynı sayfada inceleyerek daha rahat karar verebilirsiniz.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if ($featuredProducts->isNotEmpty())
            <section class="space-y-4">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div class="space-y-2">
                        <span class="rg-kicker">{{ __('Hızlı Seçim') }}</span>
                        <div>
                            <h2 class="font-display text-2xl text-rg-copy-strong dark:text-white md:text-[2rem]">
                                {{ $occasionTitle }} {{ __('için öne çıkanlar') }}
                            </h2>
                            <p class="max-w-2xl text-sm leading-7 text-rg-copy-muted dark:text-white/72">
                                {{ __('En çok tercih edilen seçenekleri önce görün, ardından tüm koleksiyonu inceleyin.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <x-product-rail :products="$featuredProducts" :interactive="false" card-width="w-[78vw] min-[480px]:w-[16.5rem] md:w-[17.5rem] lg:w-[18rem]" />
            </section>
        @endif

        @if ($products->isEmpty())
            <div class="rg-surface px-6 py-10 text-center">
                <p class="mx-auto mb-5 max-w-xl text-sm leading-7 text-rg-copy-muted dark:text-white/86">
                    {{ __('Bu özel gün için henüz ürün ataması bulunmuyor. Tüm koleksiyondan uygun bir seçimle ilerleyebilirsiniz.') }}
                </p>
                <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}"
                   class="inline-flex items-center justify-center rounded-full bg-rg-purple px-5 py-2.5 text-sm font-semibold text-white transition-all duration-200 hover:bg-rg-darkPlum">
                    {{ __('Tüm Ürünler') }}
                </a>
            </div>
        @else
            <section id="occasion-products" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_19rem] xl:items-start">
                <div class="space-y-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                        <div class="space-y-2">
                            <span class="rg-kicker">{{ __('Koleksiyon Akışı') }}</span>
                            <h2 class="font-display text-2xl text-rg-copy-strong dark:text-white md:text-[2rem]">
                                {{ $occasionTitle }} {{ __('için seçilen tüm tasarımlar') }}
                            </h2>
                        </div>

                        <p class="max-w-xl text-sm leading-7 text-rg-copy-muted dark:text-white/86">
                            {{ __(':count ürün, bu özel gün için hazırlanmış butik seçimler içinde birlikte sunulur.', ['count' => $products->total()]) }}
                        </p>
                    </div>

                    <div class="rg-surface-soft grid gap-4 px-5 py-5 md:grid-cols-[minmax(0,1fr)_10rem] md:items-center">
                        <div class="space-y-2">
                            <span class="rg-kicker">{{ __('Editoryal Seçim') }}</span>
                            <p class="text-sm leading-7 text-rg-copy-muted dark:text-white/86">
                                {{ __('Grid alanı, hızla karar verilebilen ürünlerle daha rafine sunum isteyen tasarımları aynı ritimde bir araya getirir. Üstteki vitrin karar vermeyi hızlandırır; aşağıdaki grid ise tüm seçenekleri sakin biçimde tamamlar.') }}
                            </p>
                        </div>

                        <div class="overflow-hidden rounded-[1.25rem] border border-black/6 bg-white/60 shadow-[0_12px_24px_rgba(34,24,40,0.05)] dark:border-white/10 dark:bg-white/10">
                            <div class="aspect-[1/1]">
                                <img src="{{ $tertiaryVisual }}" alt="{{ $occasionTitle }}" class="h-full w-full object-cover object-center">
                            </div>
                        </div>
                    </div>

                    <x-product-grid>
                        @foreach ($products as $product)
                            <x-product-card
                                :product="$product"
                                :interactive="false"
                                :image-alternate-products="$products->getCollection()" />
                        @endforeach
                    </x-product-grid>

                    {{ $products->links('vendor.pagination.rg-catalog') }}
                </div>

                <aside class="space-y-4">
                    <div class="rg-surface overflow-hidden">
                        <div class="aspect-[1.08/0.92]">
                            <img src="{{ $secondaryVisual }}" alt="{{ $occasionTitle }}" class="h-full w-full object-cover object-center">
                        </div>
                        <div class="px-5 py-5">
                            <span class="rg-kicker">{{ __('Sunum Notu') }}</span>
                            <p class="mt-3 text-sm leading-7 text-rg-copy-muted dark:text-white/86">
                                {{ __('Daha şık bir sonuç için ürüne kısa bir not ve sade bir hediye eşliği eklemek çoğu zaman yeterlidir.') }}
                            </p>
                        </div>
                    </div>

                    <div class="rg-surface px-5 py-5">
                        <div class="space-y-3">
                            <span class="rg-kicker">{{ __('Jest Notları') }}</span>
                            <h2 class="font-display text-2xl text-rg-copy-strong dark:text-white">{{ __('Seçimi tamamlayan küçük kararlar') }}</h2>
                        </div>

                        <ul class="mt-4 space-y-3 text-sm leading-7 text-rg-copy-muted dark:text-white/86">
                            @foreach ($curationNotes as $note)
                                <li>{{ $note }}</li>
                            @endforeach
                        </ul>
                    </div>

                    @if ($relatedOccasions->isNotEmpty())
                        <div class="rg-surface px-5 py-5">
                            <div class="space-y-3">
                                <span class="rg-kicker">{{ __('Diğer Tarihler') }}</span>
                                <h2 class="font-display text-2xl text-rg-copy-strong dark:text-white">{{ __('Sıradaki özel günler') }}</h2>
                            </div>

                            <div class="mt-4 space-y-3">
                                @foreach ($relatedOccasions as $relatedOccasion)
                                    @php
                                        $relatedTitle = $relatedOccasion->getTranslation('name', app()->getLocale());
                                    @endphp

                                    <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $relatedOccasion->slug]) }}"
                                       class="rg-occasion-mini-card rg-occasion-theme rg-occasion-theme--{{ $relatedOccasion->slug }}">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="space-y-1">
                                                <p class="text-[11px] uppercase tracking-[0.24em] text-rg-copy-soft dark:text-white/48">
                                                    {{ $relatedOccasion->nextOccurrence()->locale(app()->getLocale())->translatedFormat('d F') }}
                                                </p>
                                                <h3 class="text-base font-semibold text-rg-copy-strong dark:text-white">{{ $relatedTitle }}</h3>
                                                <p class="text-sm text-rg-copy-muted dark:text-white/86">
                                                    {{ $relatedOccasion->isToday()
                                                        ? __('Bugün için hazır')
                                                        : __(':count gün kaldı', ['count' => $relatedOccasion->daysUntil()]) }}
                                                </p>
                                            </div>
                                            <span class="rg-occasion-dot"></span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </aside>
            </section>
        @endif
    </div>
@endsection
