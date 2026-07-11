@php
    $locale = app()->getLocale();
    $title = data_get($settings, "title_override.$locale")
        ?: (filled(data_get($homeContent, 'home_intro_heading')) ? data_get($homeContent, 'home_intro_heading') : __('Kategoriler'));
    $subtitle = data_get($settings, "subtitle_override.$locale")
        ?: (filled(data_get($homeContent, 'home_intro_body')) ? data_get($homeContent, 'home_intro_body') : __('Buket, saksı bitkisi, orkide ve çikolata seçeneklerine hızlıca geçin.'));
    $categories = collect($categories ?? [])->take((int) data_get($settings, 'content_limit', 6))->values();
@endphp

@if ($categories->isNotEmpty())
    <section class="rg-section rg-home-categories">
        <div class="mb-4 flex items-end justify-between gap-4">
            <div class="min-w-0">
                <h2 class="font-display text-2xl text-rg-deepPurple dark:text-white md:text-3xl">{{ $title }}</h2>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-rg-grayText dark:text-white/80">{{ $subtitle }}</p>
            </div>
            <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="rg-inline-link shrink-0">
                {{ __('Tüm Kategoriler') }}
            </a>
        </div>

        <div class="rg-home-category-strip">
            @foreach ($categories as $category)
                @php
                    $coverPath = data_get($category, 'resolved_cover_path');
                    $cover = \App\Support\StorefrontImage::publicImgSrc(\App\Support\StorefrontImage::resolveProduct(
                        $coverPath,
                        $category->slug,
                        $category->name,
                    ));
                    $coverSrc = \App\Support\StorefrontImage::optimizedImgSrc($cover, 420);
                @endphp

                <a href="{{ \App\Support\StorefrontLocale::route('products.category', ['slug' => $category->slug]) }}" class="rg-home-category-pill">
                    <span class="rg-home-category-pill-image">
                        <img
                            src="{{ $coverSrc }}"
                            alt="{{ $category->name }}"
                            loading="{{ $loop->index < 4 ? 'eager' : 'lazy' }}"
                            fetchpriority="{{ $loop->index < 4 ? 'high' : 'auto' }}"
                            decoding="async"
                        >
                    </span>
                    <span class="rg-home-category-pill-label">{{ $category->name }}</span>
                    <span class="rg-home-category-pill-arrow" aria-hidden="true">→</span>
                </a>
            @endforeach
        </div>
    </section>
@endif
