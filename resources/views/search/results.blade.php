@extends('layouts.app')

@section('content')
    @php
        $searchVisuals = array_map(
            fn (string $path) => \App\Support\StorefrontImage::publicImgSrc($path),
            \App\Support\StorefrontImage::productVisualStrip(3)
        );
        $searchVisualFallback = $searchVisuals[0] ?? \App\Support\StorefrontImage::productPlaceholderImgSrc();
        $searchVisuals = array_pad($searchVisuals, 3, $searchVisualFallback);
    @endphp

    <section class="space-y-7">
        <x-page-hero
            compact
            :eyebrow="__('Arama')"
            :title="__('Ürün arama sonuçları')"
            :description="$query ? __('“:query” için çıkan sonuçları aşağıda görebilirsiniz. Sonuç yoksa önerilen anahtar kelimelerle vitrinde daha hızlı ilerleyebilirsiniz.', ['query' => $query]) : __('Anahtar kelime ile buket, saksı bitkisi veya özel gün seçkileri arasında hızlıca gezinebilirsiniz.')"
        >
            <x-slot:stats>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Bulunan ürün') }}</p>
                    <p class="mt-2 text-2xl font-semibold text-rg-deepPurple dark:text-white">
                        {{ $results instanceof \Illuminate\Pagination\LengthAwarePaginator ? number_format($results->total(), 0, ',', '.') : '0' }}
                    </p>
                </div>
                <div class="rg-page-stat">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('İpucu') }}</p>
                    <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/84">{{ __('Kategori adı, çiçek türü veya özel gün ismiyle arama yapmak daha sağlıklı sonuç verir.') }}</p>
                </div>
            </x-slot:stats>

            <x-slot:aside>
                @php
                    $searchHeroVisual = \App\Support\StorefrontImage::publicImgSrc(
                        \App\Support\StorefrontImage::productVisualStrip(1)[0] ?? \App\Support\StorefrontImage::productPlaceholderImgSrc()
                    );
                @endphp
                <div class="space-y-3">
                    <div class="rg-photo-card rg-photo-card--tall">
                        <img src="{{ $searchHeroVisual }}" alt="{{ __('Rose Garden arama') }}">
                        <div class="rg-photo-card__content">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-white/72">{{ __('Keşif') }}</p>
                            <h2 class="mt-2 font-display text-[1.85rem] leading-tight text-white">{{ __('Katalog içinde daha hızlı gezinme') }}</h2>
                        </div>
                    </div>
                    <form action="{{ \App\Support\StorefrontLocale::route('search') }}" method="GET" class="rg-mini-note">
                        <label for="search-query" class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Yeniden ara') }}</label>
                        <div class="mt-3 flex items-center gap-2">
                            <input
                                id="search-query"
                                type="text"
                                name="q"
                                value="{{ $query }}"
                                placeholder="{{ __('Örn. orkide, buket, anneler günü') }}"
                                class="w-full rounded-full border border-rg-lightLavender bg-white/90 px-4 py-2.5 text-sm text-rg-darkText outline-none transition-all placeholder:text-rg-grayText focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/25 dark:border-white/12 dark:bg-white/14 dark:text-white dark:placeholder:text-white/62"
                            >
                            <button type="submit" aria-label="{{ __('Aramayı gönder') }}" class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-rg-purple text-white transition-colors hover:bg-rg-darkPlum">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </x-slot:aside>
        </x-page-hero>

        @if ($results instanceof \Illuminate\Pagination\LengthAwarePaginator && $results->count() > 0)
            <x-product-grid>
                @foreach ($results as $product)
                    <x-product-card :product="$product" :interactive="false" :image-alternate-products="$results" />
                @endforeach
            </x-product-grid>
            {{ $results->links('vendor.pagination.rg-catalog') }}

        @elseif (mb_strlen((string) $query) < 2)
            <div class="rg-surface py-16 text-center">
                <svg class="mx-auto mb-4 h-16 w-16 text-rg-lightLavender dark:text-white/22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <p class="rg-copy-muted">{{ __('Aramak için en az 2 karakter girin.') }}</p>
            </div>

        @else
            <div class="rg-surface py-16 text-center">
                <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-full bg-rg-lightLavender/55 dark:bg-white/14">
                    <svg class="h-10 w-10 text-rg-midPurple dark:text-rg-lavender" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="mb-2 font-display text-xl font-semibold text-rg-darkText dark:text-white">{{ __('Sonuç bulunamadı') }}</h2>
                <p class="rg-copy-muted mx-auto mb-6 max-w-xs text-sm">
                    "{{ $query }}" {{ __('için bir ürün bulunamadı. Farklı bir anahtar kelime deneyin.') }}
                </p>
                <div class="mb-8 flex flex-wrap justify-center gap-2">
                    @foreach (['gül', 'orkide', 'buket', 'anneler günü', 'saksı'] as $suggestion)
                        <a href="{{ \App\Support\StorefrontLocale::route('search', ['q' => $suggestion]) }}"
                           class="inline-flex items-center rounded-full border border-black/5 bg-rg-lightLavender/45 px-4 py-1.5 text-sm text-rg-darkPlum transition-colors duration-200 hover:bg-rg-lightLavender dark:border-white/10 dark:bg-white/12 dark:text-white dark:hover:bg-white/10">
                            {{ $suggestion }}
                        </a>
                    @endforeach
                </div>
                <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}"
                   class="inline-flex items-center gap-2 rounded-full bg-rg-purple px-6 py-3 text-sm font-semibold text-white transition-colors duration-200 hover:bg-rg-darkPlum">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    {{ __('Tüm ürünleri görüntüle') }}
                </a>
            </div>
        @endif
    </section>
@endsection
