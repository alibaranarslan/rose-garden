@if ($activeOccasion)
    @php
        $occasionProducts = collect($occasionProducts ?? []);
        $leadProduct = $occasionProducts->first();
        $remainingProducts = $occasionProducts->slice(1)->take(2)->values();
        $isTurkish = app()->getLocale() === 'tr';
        $occasionCategory = $activeOccasion->category;
        $occasionCategoryName = $occasionCategory?->getTranslation('name', app()->getLocale());
        $occasionTitle = $activeOccasion->getTranslation('name', app()->getLocale());
        $occasionVisual = \App\Support\StorefrontImage::publicImgSrc(
            \App\Support\StorefrontImage::resolveSpecialOccasion(
                $activeOccasion->slug,
                $occasionTitle,
                $activeOccasion->category?->getTranslation('name', app()->getLocale()),
                $activeOccasion->category?->slug,
            )
        );
        $occasionVisualSrc = \App\Support\StorefrontImage::optimizedImgSrc($occasionVisual, 960);
        $occasionVisualSrcset = \App\Support\StorefrontImage::optimizedImgSrcset($occasionVisual, [480, 640, 960]);
        $occasionDateLabel = $activeOccasion->nextOccurrence()
            ->locale(app()->getLocale())
            ->translatedFormat('d F');
        $occasionStatusLabel = $activeOccasion->isToday()
            ? __('Bugün')
            : __(':count gün kaldı', ['count' => $activeOccasion->daysUntil()]);
        $leadProductImage = $leadProduct
            ? \App\Support\StorefrontImage::publicImgSrc(\App\Support\StorefrontImage::resolveProduct(
                data_get($leadProduct, 'image') ?? data_get($leadProduct, 'images.0.image_path'),
                data_get($leadProduct, 'slug'),
                data_get($leadProduct, 'name'),
            ))
            : null;
        $leadProductImageSrc = $leadProductImage ? \App\Support\StorefrontImage::optimizedImgSrc($leadProductImage, 960) : null;
        $leadProductImageSrcset = $leadProductImage ? \App\Support\StorefrontImage::optimizedImgSrcset($leadProductImage, [480, 640, 960]) : '';
        $leadProductPrice = $leadProduct?->cardPriceDisplay();
        $occasionProductCount = $occasionProducts->count();
    @endphp

    <section class="rg-section">
        <div class="grid gap-4 xl:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)] xl:items-start">
            <article class="overflow-hidden rounded-[1.85rem] border border-black/6 bg-white/90 shadow-[0_16px_40px_rgba(34,24,40,0.06)] dark:border-white/10 dark:bg-[#211927]">
                <div class="grid gap-0 md:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">
                    <div class="relative min-h-[18rem] overflow-hidden bg-rg-lightLavender/40 dark:bg-white/8">
                        <img
                            src="{{ $occasionVisualSrc }}"
                            alt="{{ $occasionTitle }}"
                            loading="lazy"
                            decoding="async"
                            @if ($occasionVisualSrcset !== '') srcset="{{ $occasionVisualSrcset }}" sizes="(min-width: 768px) 34vw, 100vw" @endif
                            class="h-full w-full object-cover object-center"
                        >
                        <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(255,255,255,0.04),rgba(22,12,24,0.42))]"></div>
                        <div class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full border border-white/24 bg-white/82 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-deepPurple shadow-md backdrop-blur dark:border-white/16 dark:bg-[#1b1420]/86 dark:text-white">
                            <span class="h-2 w-2 rounded-full bg-rg-rosePink"></span>
                            {{ __('Yaklaşan Özel Gün') }}
                        </div>
                    </div>

                    <div class="px-5 py-5 md:px-6 md:py-6">
                        <span class="rg-kicker">{{ $isTurkish ? 'Yaklaşan Kutlama' : __('Yaklaşan Kutlama') }}</span>
                        <h2 class="mt-3 text-balance font-display text-3xl text-rg-deepPurple dark:text-white md:text-4xl">{{ $occasionTitle }}</h2>
                        <p class="mt-3 text-sm leading-relaxed text-rg-grayText dark:text-white/82">
                            {{ data_get($settings, 'subtitle_override.'.app()->getLocale()) ?: ($isTurkish ? 'Yaklaşan güne uygun çiçek ve hediye seçeneklerini tek bakışta inceleyin; dilerseniz aynı gün teslimat için destek alın.' : __('Yaklaşan güne uygun çiçek ve hediye seçeneklerini tek bakışta inceleyin; dilerseniz aynı gün teslimat için destek alın.')) }}
                        </p>

                        <div class="mt-5 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-[1.2rem] border border-black/6 bg-rg-cream/75 px-4 py-3 dark:border-white/10 dark:bg-white/8">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-rg-grayText dark:text-white/58">{{ __('Tarih') }}</p>
                                <p class="mt-2 text-sm font-semibold text-rg-deepPurple dark:text-white">{{ $occasionDateLabel }}</p>
                            </div>
                            <div class="rounded-[1.2rem] border border-black/6 bg-rg-cream/75 px-4 py-3 dark:border-white/10 dark:bg-white/8">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-rg-grayText dark:text-white/58">{{ __('Durum') }}</p>
                                <p class="mt-2 text-sm font-semibold text-rg-deepPurple dark:text-white">{{ $occasionStatusLabel }}</p>
                            </div>
                            <div class="rounded-[1.2rem] border border-black/6 bg-rg-cream/75 px-4 py-3 dark:border-white/10 dark:bg-white/8">
                                @if ($occasionProductCount > 0)
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-rg-grayText dark:text-white/58">{{ $isTurkish ? 'Ürün Seçkisi' : __('Ürün Seçkisi') }}</p>
                                    <p class="mt-2 text-sm font-semibold text-rg-deepPurple dark:text-white">{{ trans_choice(':count ürün', $occasionProductCount, ['count' => $occasionProductCount]) }}</p>
                                @else
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-rg-grayText dark:text-white/58">{{ $isTurkish ? 'Hazırlık Yönü' : __('Hazırlık Yönü') }}</p>
                                    <p class="mt-2 text-sm font-semibold text-rg-deepPurple dark:text-white">{{ $occasionCategoryName ?: __('Seçki güncelleniyor') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap gap-3">
                            <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $activeOccasion->slug]) }}" class="inline-flex items-center justify-center rounded-full bg-rg-purple px-5 py-3 text-sm font-semibold text-white transition-colors duration-200 hover:bg-rg-darkPlum">
                                {{ __('Seçkiyi aç') }}
                            </a>
                            <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="rg-button-secondary">
                                {{ $isTurkish ? 'Ürünlere dön' : __('Ürünlere dön') }}
                            </a>
                        </div>
                    </div>
                </div>
            </article>

            <div class="grid gap-3">
                @if ($leadProduct)
                    <article class="overflow-hidden rounded-[1.55rem] border border-black/6 bg-white/88 shadow-[0_16px_36px_rgba(34,24,40,0.08)] dark:border-white/10 dark:bg-[#1a1420]">
                        <a href="{{ \App\Support\StorefrontLocale::route('products.show', ['slug' => $leadProduct->slug]) }}" class="relative block aspect-[16/9] overflow-hidden">
                            <img
                                src="{{ $leadProductImageSrc }}"
                                alt="{{ $leadProduct->name }}"
                                loading="lazy"
                                decoding="async"
                                @if ($leadProductImageSrcset !== '') srcset="{{ $leadProductImageSrcset }}" sizes="(min-width: 1280px) 50vw, 100vw" @endif
                                class="h-full w-full object-cover object-center transition-transform duration-700 hover:scale-[1.02]"
                            >
                            <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(22,12,24,0.06),rgba(22,12,24,0.72))]"></div>
                            <div class="absolute inset-x-4 bottom-4 flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-white/72">{{ $isTurkish ? 'Özel Gün Önerisi' : __('Özel Gün Önerisi') }}</p>
                                    <h3 class="mt-1 text-lg font-semibold text-white">{{ $leadProduct->name }}</h3>
                                </div>
                                @if ($leadProductPrice)
                                    <span class="rounded-full border border-white/20 bg-white/12 px-3 py-2 text-sm font-semibold text-white backdrop-blur">
                                        &#8378; {{ number_format($leadProductPrice['current'], 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    </article>
                @endif

                <div class="grid gap-3 md:grid-cols-2">
                    @foreach (range(0, 1) as $slot)
                        @php
                            $supportProduct = $remainingProducts->get($slot);
                        @endphp

                        @if ($supportProduct)
                            <x-product-card-mini :product="$supportProduct" />
                        @elseif ($slot === 0)
                            <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $activeOccasion->slug]) }}" class="flex h-full flex-col justify-between rounded-[1.35rem] border border-black/6 bg-white/84 px-4 py-4 shadow-[0_12px_26px_rgba(34,24,40,0.05)] transition-colors duration-200 hover:border-rg-purple/20 dark:border-white/10 dark:bg-white/8">
                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-rg-grayText dark:text-white/56">{{ $isTurkish ? 'Kutlama Seçkisi' : __('Kutlama Seçkisi') }}</p>
                                    <h3 class="mt-2 text-base font-semibold text-rg-deepPurple dark:text-white">{{ $occasionTitle }}</h3>
                                    <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/80">
                                        {{ $occasionProductCount > 0
                                            ? ($isTurkish ? 'Mevcut ürünlerle özel gün seçkisine devam edin.' : __('Mevcut ürünlerle özel gün seçkisine devam edin.'))
                                            : ($isTurkish ? 'Bu güne uygun seçenekleri inceleyin veya WhatsApp üzerinden destek alın.' : __('Bu güne uygun seçenekleri inceleyin veya WhatsApp üzerinden destek alın.')) }}
                                    </p>
                                </div>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <span class="rg-pill">{{ $occasionStatusLabel }}</span>
                                    <span class="rg-pill">{{ $occasionDateLabel }}</span>
                                </div>
                            </a>
                        @elseif ($occasionCategory)
                            <a href="{{ \App\Support\StorefrontLocale::route('products.category', ['slug' => $occasionCategory->slug]) }}" class="flex h-full flex-col justify-between rounded-[1.35rem] border border-black/6 bg-white/84 px-4 py-4 shadow-[0_12px_26px_rgba(34,24,40,0.05)] transition-colors duration-200 hover:border-rg-purple/20 dark:border-white/10 dark:bg-white/8">
                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-rg-grayText dark:text-white/56">{{ $isTurkish ? 'İlgili Kategori' : __('İlgili Kategori') }}</p>
                                    <h3 class="mt-2 text-base font-semibold text-rg-deepPurple dark:text-white">{{ $occasionCategoryName }}</h3>
                                    <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/80">
                                        {{ $isTurkish ? 'Bu özel gün için ilişkili kategoriye geçip daha geniş ürün seçkisini görün.' : __('Bu özel gün için ilişkili kategoriye geçip daha geniş ürün seçkisini görün.') }}
                                    </p>
                                </div>
                                <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-rg-deepPurple dark:text-white">
                                    {{ __('Koleksiyona git') }}
                                    <span aria-hidden="true">&rarr;</span>
                                </span>
                            </a>
                        @else
                            <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="flex h-full flex-col justify-between rounded-[1.35rem] border border-black/6 bg-white/84 px-4 py-4 shadow-[0_12px_26px_rgba(34,24,40,0.05)] transition-colors duration-200 hover:border-rg-purple/20 dark:border-white/10 dark:bg-white/8">
                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-rg-grayText dark:text-white/56">{{ __('Teslimat Ritmi') }}</p>
                                    <p class="mt-2 text-base font-semibold text-rg-deepPurple dark:text-white">{{ $occasionDateLabel }}</p>
                                    <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/80">{{ $occasionStatusLabel }}</p>
                                </div>
                                <span class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-rg-deepPurple dark:text-white">
                                    {{ $isTurkish ? 'Ürünlere dön' : __('Ürünlere dön') }}
                                    <span aria-hidden="true">&rarr;</span>
                                </span>
                            </a>
                        @endif
                    @endforeach
                </div>

                @if (! $leadProduct)
                    <div class="grid gap-3 xl:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
                        <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $activeOccasion->slug]) }}" class="flex h-full flex-col justify-between rounded-[1.4rem] border border-black/6 bg-white/84 px-5 py-5 shadow-[0_14px_30px_rgba(34,24,40,0.06)] transition-colors duration-200 hover:border-rg-purple/20 dark:border-white/10 dark:bg-white/8">
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-rg-grayText dark:text-white/56">{{ $isTurkish ? 'Seçim Desteği' : __('Seçim Desteği') }}</p>
                                <h3 class="mt-2 text-lg font-semibold text-rg-deepPurple dark:text-white">{{ $occasionTitle }}</h3>
                                <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/80">
                                    {{ $isTurkish ? 'Bu özel gün için çiçek ve hediye seçeneklerini birlikte inceleyebilirsiniz.' : __('Bu özel gün için çiçek ve hediye seçeneklerini birlikte inceleyebilirsiniz.') }}
                                </p>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="rg-pill">{{ $occasionDateLabel }}</span>
                                <span class="rg-pill">{{ $occasionStatusLabel }}</span>
                            </div>
                        </a>
                        <div class="rounded-[1.4rem] border border-black/6 bg-white/84 px-5 py-5 shadow-[0_14px_30px_rgba(34,24,40,0.06)] dark:border-white/10 dark:bg-white/8">
                            <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-rg-grayText dark:text-white/56">{{ __('Teslimat Ritmi') }}</p>
                            <p class="mt-2 text-lg font-semibold text-rg-deepPurple dark:text-white">{{ __('Aynı gün hazırlık desteği') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/80">
                                {{ $isTurkish ? 'Uygun ürün sayısı sınırlıysa aynı gün teslimat için destek alabilirsiniz.' : __('Uygun ürün sayısı sınırlıysa aynı gün teslimat için destek alabilirsiniz.') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
