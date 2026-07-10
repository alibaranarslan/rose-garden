@php
    $locale = app()->getLocale();
    $title = data_get($settings, "title_override.$locale")
        ?: (filled(data_get($homeContent, 'home_intro_heading')) ? data_get($homeContent, 'home_intro_heading') : __('Kategorilerden hızlıca seçin.'));
    $subtitle = data_get($settings, "subtitle_override.$locale")
        ?: (filled(data_get($homeContent, 'home_intro_body')) ? data_get($homeContent, 'home_intro_body') : __('Buket, saksı bitkisi ve özel gün seçeneklerine tek dokunuşla geçin.'));
    $categories = collect($categories ?? [])->take((int) data_get($settings, 'content_limit', 6))->values();
    $discoveryProducts = collect($discoveryProducts ?? [])->take(2)->values();
@endphp

@if ($categories->isNotEmpty())
    <section class="rg-section rg-home-categories">
        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-2xl">
                <span class="rg-kicker">{{ __('Hızlı Seçim') }}</span>
                <h2 class="mt-2 text-balance font-display text-2xl text-rg-deepPurple dark:text-white md:text-3xl">{{ $title }}</h2>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-rg-grayText dark:text-white/80">{{ $subtitle }}</p>
            </div>
            <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="rg-inline-link self-start sm:self-auto">
                {{ __('Tüm ürünler') }}
            </a>
        </div>

        <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_minmax(16rem,0.34fr)] lg:items-start">
            <div class="rg-home-category-grid grid grid-cols-2 gap-2.5 sm:grid-cols-3 lg:gap-3">
                @foreach ($categories as $category)
                    @php
                        $coverPath = data_get($category, 'resolved_cover_path');
                        $cover = \App\Support\StorefrontImage::publicImgSrc(\App\Support\StorefrontImage::resolveProduct(
                            $coverPath,
                            $category->slug,
                            $category->name,
                        ));
                        $coverSrc = \App\Support\StorefrontImage::optimizedImgSrc($cover, 480);
                    @endphp

                    <a href="{{ \App\Support\StorefrontLocale::route('products.category', ['slug' => $category->slug]) }}"
                        class="group relative isolate min-h-[8.5rem] overflow-hidden rounded-[1.2rem] border border-black/6 bg-rg-lightLavender/35 shadow-[0_10px_26px_rgba(34,24,40,0.055)] transition hover:-translate-y-0.5 hover:shadow-[0_16px_34px_rgba(34,24,40,0.08)] dark:border-white/10 dark:bg-white/8">
                        <img src="{{ $coverSrc }}" alt="{{ $category->name }}" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover object-center transition duration-500 group-hover:scale-[1.04]">
                        <span class="absolute inset-0 bg-gradient-to-t from-[#1b1020]/78 via-[#1b1020]/25 to-transparent"></span>
                        <span class="absolute inset-x-3 bottom-3 flex items-end justify-between gap-2 text-white">
                            <span class="min-w-0">
                                <span class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-white/74">{{ __('Kategori') }}</span>
                                <span class="mt-1 block truncate text-sm font-semibold">{{ $category->name }}</span>
                            </span>
                            <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/16 text-sm backdrop-blur" aria-hidden="true">&rarr;</span>
                        </span>
                    </a>
                @endforeach
            </div>

            <aside class="rounded-[1.25rem] border border-black/6 bg-white/82 p-4 shadow-[0_12px_30px_rgba(34,24,40,0.045)] dark:border-white/10 dark:bg-white/8">
                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Teslimat') }}</p>
                <h3 class="mt-2 text-base font-semibold text-rg-deepPurple dark:text-white">{{ __('Adıyaman içi aynı gün destek') }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/78">{{ __('Kararsız kalırsanız WhatsApp üzerinden uygun ürün ve teslimat saati için destek alın.') }}</p>
                <div class="mt-4 grid gap-2">
                    @foreach ($discoveryProducts as $product)
                        <x-product-card-mini :product="$product" />
                    @endforeach
                </div>
            </aside>
        </div>
    </section>
@endif
