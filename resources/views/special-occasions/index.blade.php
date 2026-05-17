@extends('layouts.app')

@section('content')
    @php
        $featuredOccasionTitle = $featuredOccasion?->getTranslation('name', app()->getLocale());
        $featuredOccasionCategory = $featuredOccasion?->category?->getTranslation('name', app()->getLocale());
        $featuredOccasionVisual = \App\Support\StorefrontImage::publicImgSrc(
            $featuredOccasion
                ? \App\Support\StorefrontImage::resolveSpecialOccasion(
                    $featuredOccasion->slug,
                    $featuredOccasionTitle,
                    $featuredOccasionCategory,
                    $featuredOccasion->category?->slug,
                )
                : (\App\Support\StorefrontImage::productVisualStrip(1)[0] ?? \App\Support\StorefrontImage::productPlaceholderImgSrc())
        );

        $timelineOccasions = $occasions->take(5);
        $spotlightOccasions = $occasions->take(3);

        $formatDate = fn ($item) => $item->nextOccurrence()
            ->locale(app()->getLocale())
            ->translatedFormat('d F');

        $daysLabel = function ($item) {
            if ($item->isToday()) {
                return __('Bugün');
            }

            return __(':count gün kaldı', ['count' => $item->daysUntil()]);
        };

        $occasionVisual = fn ($occasion) => \App\Support\StorefrontImage::publicImgSrc(\App\Support\StorefrontImage::resolveSpecialOccasion(
            $occasion->slug,
            $occasion->getTranslation('name', app()->getLocale()),
            $occasion->category?->getTranslation('name', app()->getLocale()),
            $occasion->category?->slug,
        ));

        $occasionSummary = function ($occasion) {
            $category = $occasion->category?->getTranslation('name', app()->getLocale());

            if ($category) {
                return __(':category çizgisindeki çiçek ve hediye seçimleri bu özel gün için birlikte sunulur.', ['category' => $category]);
            }

            return __('Çiçek, hediye notu ve seçili ürünlerle hazırlanmış canlı bir koleksiyon sunulur.');
        };

        $curationNotes = [
            [
                'label' => __('Teslimat'),
                'title' => __('Aynı gün için hazır seçimler'),
                'copy' => __('Zamanın önemli olduğu anlarda, hızlı karar verilebilen seçimler öne çıkar.'),
            ],
            [
                'label' => __('Sunum'),
                'title' => __('Daha rafine bir kutlama hissi'),
                'copy' => __('Her tarih için hazırlanan sayfalar, seçim sürecine daha sakin ve seçkin bir ritim kazandırır.'),
            ],
            [
                'label' => __('Katalog'),
                'title' => __('Gerçek ürün görselleriyle ilerler'),
                'copy' => __('Özel gün akışları dekoratif ikonlara değil, vitrindeki gerçek ürün fotoğraflarına dayanır.'),
            ],
        ];
    @endphp

    <x-breadcrumb :items="[
        ['label' => __('Anasayfa'), 'url' => \App\Support\StorefrontLocale::route('home')],
        ['label' => __('Özel Günler'), 'url' => null],
    ]" />

    <div class="space-y-8 md:space-y-10">
        <section class="rg-occasion-stage rg-occasion-theme {{ $featuredOccasion ? 'rg-occasion-theme--' . $featuredOccasion->slug : '' }}">
            <div class="grid gap-6 xl:grid-cols-[minmax(0,0.96fr)_minmax(0,1.04fr)] xl:items-center">
                <div class="space-y-5">
                    <span class="rg-kicker">{{ __('Özel Günler') }}</span>

                    <div class="max-w-2xl space-y-3">
                        <h1 class="font-display text-4xl leading-tight text-rg-copy-strong dark:text-white md:text-5xl xl:text-[3.4rem]">
                            {{ __('Kutlamaya değer her an için seçilmiş çiçek ve hediye koleksiyonları') }}
                        </h1>
                        <p class="max-w-xl text-sm leading-7 text-rg-copy-muted dark:text-white/78 md:text-[15px]">
                            {{ __('Sevgililer Günü’nden Anneler Günü’ne, yıl boyunca hatırlanmak istenen her tarih için çiçek ve hediye seçimleri tek bir akışta buluşur.') }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @if ($featuredOccasion)
                            <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $featuredOccasion->slug]) }}"
                               class="inline-flex items-center justify-center rounded-full bg-rg-purple px-5 py-2.5 text-sm font-semibold text-white transition-all duration-200 hover:bg-rg-darkPlum">
                                {{ __('Öne çıkan koleksiyonu gör') }}
                            </a>
                        @endif
                        <a href="#occasion-directory" class="rg-button-secondary">
                            {{ __('Tüm tarihleri incele') }}
                        </a>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3">
                        <div class="rg-occasion-stage-stat">
                            <span class="text-[11px] uppercase tracking-[0.24em] text-rg-copy-soft dark:text-white/48">{{ __('Aktif seçki') }}</span>
                            <strong class="mt-3 block text-base text-rg-copy-strong dark:text-white">{{ $occasions->count() }} {{ __('özel gün') }}</strong>
                        </div>
                        <div class="rg-occasion-stage-stat">
                            <span class="text-[11px] uppercase tracking-[0.24em] text-rg-copy-soft dark:text-white/48">{{ __('Yaklaşan tarih') }}</span>
                            <strong class="mt-3 block text-base text-rg-copy-strong dark:text-white">{{ $featuredOccasion ? $formatDate($featuredOccasion) : __('Yeni seçkiler yakında') }}</strong>
                        </div>
                        <div class="rg-occasion-stage-stat">
                            <span class="text-[11px] uppercase tracking-[0.24em] text-rg-copy-soft dark:text-white/48">{{ __('Teslim ritmi') }}</span>
                            <strong class="mt-3 block text-base text-rg-copy-strong dark:text-white">{{ __('Aynı gün teslim uyumlu seçimler') }}</strong>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-[minmax(0,1.08fr)_minmax(0,0.92fr)]">
                    <a href="{{ $featuredOccasion ? \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $featuredOccasion->slug]) : \App\Support\StorefrontLocale::route('special-occasions.index') }}"
                       class="rg-occasion-photo-card rg-occasion-photo-card--hero">
                        <img src="{{ $featuredOccasionVisual }}" alt="{{ $featuredOccasionTitle ?: __('Rose Garden özel gün seçkisi') }}" class="h-full w-full object-cover object-center">
                        <div class="rg-occasion-photo-card__content">
                            <span class="rg-occasion-photo-card__eyebrow">{{ __('Öne çıkan tarih') }}</span>
                            <h2 class="font-display text-[2rem] leading-tight text-white md:text-[2.3rem]">
                                {{ $featuredOccasionTitle ?: __('Yılın seçilmiş anları') }}
                            </h2>
                            <p class="max-w-sm text-sm leading-6 text-white/78">
                                {{ $featuredOccasion ? $occasionSummary($featuredOccasion) : __('Çiçek ve hediye birlikteliğiyle hazırlanmış seçimler burada yer alır.') }}
                            </p>
                        </div>
                    </a>

                    <div class="grid gap-4">
                        <div class="rg-surface-soft px-5 py-5">
                            <span class="rg-kicker">{{ __('Kutlama Editi') }}</span>
                            <h2 class="mt-3 font-display text-2xl text-rg-copy-strong dark:text-white">
                                {{ __('Her tarih kendi tonunu taşır') }}
                            </h2>
                            <p class="mt-3 text-sm leading-7 text-rg-copy-muted dark:text-white/86">
                                {{ __('Romantik, zarif ya da sıcak bir kutlama dili; ilgili ürün seçkileriyle birlikte kendi sayfasında daha net bir vitrine kavuşur.') }}
                            </p>
                        </div>
                        <div class="rg-surface-soft px-5 py-5">
                            <span class="rg-kicker">{{ __('Canlı Katalog') }}</span>
                            <p class="text-sm leading-7 text-rg-copy-muted dark:text-white/86">
                                {{ __('Özel gün akışları gerçek ürün görselleriyle beslenir; karar alanı dekoratif semboller yerine canlı katalogla kurulur.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($timelineOccasions->isNotEmpty())
                <div class="mt-7 grid gap-3 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">
                    <div class="rg-surface-soft px-5 py-5">
                        <span class="rg-kicker">{{ __('Takvimden Seçim') }}</span>
                        <h2 class="mt-3 font-display text-2xl text-rg-copy-strong dark:text-white">
                            {{ __('Yılın öne çıkan tarihleri tek bakışta') }}
                        </h2>
                        <p class="mt-3 max-w-lg text-sm leading-7 text-rg-copy-muted dark:text-white/86">
                            {{ __('Her tarih kendi çiçek ve hediye diliyle ilerler; böylece kutlanmak istenen ana en yakın seçimler daha rahat bulunur.') }}
                        </p>
                    </div>

                    <div class="grid gap-3 md:grid-cols-3 xl:grid-cols-5">
                        @foreach ($timelineOccasions as $occasion)
                            <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $occasion->slug]) }}"
                               class="rg-occasion-timeline-card rg-occasion-theme rg-occasion-theme--{{ $occasion->slug }}">
                                <span class="text-[10px] uppercase tracking-[0.22em] text-rg-copy-soft dark:text-white/46">{{ $formatDate($occasion) }}</span>
                                <h3 class="mt-2 text-sm font-semibold text-rg-copy-strong dark:text-white">{{ $occasion->getTranslation('name', app()->getLocale()) }}</h3>
                                <p class="mt-2 text-xs text-rg-grayText dark:text-white/80">{{ $daysLabel($occasion) }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>

        @if ($spotlightOccasions->isNotEmpty())
            <section class="space-y-5">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div class="space-y-2">
                        <span class="rg-kicker">{{ __('Öne Çıkan Günler') }}</span>
                        <h2 class="font-display text-2xl text-rg-copy-strong dark:text-white md:text-[2rem]">
                            {{ __('Her biri kendi atmosferiyle öne çıkan tarihler') }}
                        </h2>
                    </div>
                    <p class="max-w-2xl text-sm leading-7 text-rg-copy-muted dark:text-white/86">
                        {{ __('Yılın farklı anlarına göre hazırlanan seçimler; romantik, zarif ya da sıcak bir kutlama diliyle ayrı ayrı keşfedilebilir.') }}
                    </p>
                </div>

                <div class="grid gap-4 lg:grid-cols-3">
                    @foreach ($spotlightOccasions as $occasion)
                        <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $occasion->slug]) }}"
                           class="rg-occasion-splash-card rg-occasion-theme rg-occasion-theme--{{ $occasion->slug }}">
                            <img src="{{ $occasionVisual($occasion) }}" alt="{{ $occasion->getTranslation('name', app()->getLocale()) }}" class="h-full w-full object-cover object-center">
                            <div class="rg-occasion-splash-card__content">
                                <span class="rg-occasion-photo-card__eyebrow">{{ $formatDate($occasion) }}</span>
                                <h3 class="font-display text-[1.75rem] leading-tight text-white">
                                    {{ $occasion->getTranslation('name', app()->getLocale()) }}
                                </h3>
                                <p class="max-w-sm text-sm leading-6 text-white/78">
                                    {{ $occasionSummary($occasion) }}
                                </p>
                                <span class="inline-flex items-center gap-2 text-sm font-semibold text-white/92">
                                    {{ __('Koleksiyonu gör') }}
                                    <span aria-hidden="true">→</span>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="grid gap-4 xl:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)]">
            <div class="rg-surface px-5 py-6 sm:px-6">
                <span class="rg-kicker">{{ __('Kutlama Notları') }}</span>
                <h2 class="mt-3 font-display text-2xl text-rg-copy-strong dark:text-white md:text-[2rem]">
                    {{ __('Daha zarif bir seçim için küçük ama etkili dokunuşlar') }}
                </h2>

                <div class="mt-5 grid gap-3">
                    @foreach ($curationNotes as $item)
                        <article class="rg-occasion-note-card">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-copy-soft dark:text-white/48">{{ $item['label'] }}</p>
                            <h3 class="mt-3 text-base font-semibold text-rg-copy-strong dark:text-white">{{ $item['title'] }}</h3>
                            <p class="mt-3 text-sm leading-7 text-rg-copy-muted dark:text-white/86">{{ $item['copy'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="rg-surface px-5 py-6 sm:px-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div class="space-y-2">
                        <span class="rg-kicker">{{ __('Hazır Seçkiler') }}</span>
                        <h2 class="font-display text-2xl text-rg-copy-strong dark:text-white md:text-[2rem]">
                            {{ __('Özel günler için öne çıkan ürünler') }}
                        </h2>
                    </div>

                    @if ($featuredOccasion)
                        <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $featuredOccasion->slug]) }}" class="rg-inline-link">
                            {{ __('Tamamını gör') }}
                            <span aria-hidden="true">→</span>
                        </a>
                    @endif
                </div>

                @if ($featuredProducts->isNotEmpty())
                    <div class="mt-4">
                        <x-product-rail :products="$featuredProducts" :interactive="false" card-width="w-[78vw] min-[480px]:w-[15.75rem] md:w-[16.5rem] lg:w-[17.25rem]" />
                    </div>
                @else
                    <div class="mt-4 rounded-[1.5rem] border border-black/6 bg-rg-cream/72 px-5 py-5 dark:border-white/10 dark:bg-[#241f2c]">
                        <p class="text-sm leading-7 text-rg-copy-muted dark:text-white/86">{{ __('Bu özel gün için ürün atamaları güncellendiğinde seçili tasarımlar burada görünecek.') }}</p>
                    </div>
                @endif
            </div>
        </section>

        <section id="occasion-directory" class="space-y-5">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-2">
                    <span class="rg-kicker">{{ __('Tüm Tarihler') }}</span>
                    <h2 class="font-display text-2xl text-rg-copy-strong dark:text-white md:text-[2rem]">
                        {{ __('Takvimde yer alan tüm özel günler') }}
                    </h2>
                </div>
                <p class="max-w-2xl text-sm leading-7 text-rg-copy-muted dark:text-white/86">
                    {{ __('İster yaklaşan bir kutlama, ister sezon içinde planlanan bir jest olsun; her tarih kendi sayfasında ayrılmış seçimlerle sunulur.') }}
                </p>
            </div>

            @if ($occasions->isEmpty())
                <div class="rg-surface px-6 py-10 text-center">
                    <p class="text-sm text-rg-copy-muted dark:text-white/86">
                        {{ __('Şu an listelenecek özel gün bulunmuyor.') }}
                    </p>
                </div>
            @else
                <div class="grid gap-3">
                    @foreach ($occasions as $occasion)
                        @php
                            $occasionTitle = $occasion->getTranslation('name', app()->getLocale());
                        @endphp

                        <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $occasion->slug]) }}"
                           class="rg-occasion-line-card rg-occasion-theme rg-occasion-theme--{{ $occasion->slug }}">
                            <div class="overflow-hidden rounded-[1.3rem] border border-black/6 bg-white/55 dark:border-white/10 dark:bg-white/10">
                                <div class="aspect-[1.08/0.9] h-full min-h-[8rem]">
                                    <img src="{{ $occasionVisual($occasion) }}" alt="{{ $occasionTitle }}" class="h-full w-full object-cover object-center">
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rg-pill">{{ $formatDate($occasion) }}</span>
                                    <span class="text-xs uppercase tracking-[0.2em] text-rg-copy-soft dark:text-white/48">{{ $daysLabel($occasion) }}</span>
                                </div>

                                <div class="space-y-1">
                                    <h3 class="font-display text-[1.7rem] leading-tight text-rg-copy-strong dark:text-white">
                                        {{ $occasionTitle }}
                                    </h3>
                                    <p class="max-w-2xl text-sm leading-7 text-rg-copy-muted dark:text-white/86">
                                        {{ $occasionSummary($occasion) }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center justify-start xl:justify-end">
                                <span class="rg-inline-link">
                                    {{ __('Sayfayı aç') }}
                                    <span aria-hidden="true">→</span>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
