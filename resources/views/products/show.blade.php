@extends('layouts.app')

@push('schema')
    <x-schema-product :product="$product" />
    <x-schema-breadcrumb :items="[
        ['name' => __('Anasayfa'), 'url' => \App\Support\StorefrontLocale::route('home')],
        ['name' => __('Ürünler'), 'url' => \App\Support\StorefrontLocale::route('products.index')],
        ['name' => $product->name, 'url' => url()->current()],
    ]" />
@endpush

@php
    $galleryImages = $product->images->isNotEmpty()
        ? $product->images
            ->sortBy('sort_order')
            ->map(fn ($image) => \App\Support\StorefrontImage::publicImgSrc(
                \App\Support\StorefrontImage::resolveProduct(
                    $image->image_path,
                    $product->slug,
                    $product->name,
                    'images/product-placeholder.svg',
                )
            ))
            ->values()
        : collect([
            \App\Support\StorefrontImage::publicImgSrc(
                \App\Support\StorefrontImage::resolveProduct(
                    null,
                    $product->slug,
                    $product->name,
                    'images/product-placeholder.svg',
                )
            ),
        ]);

    $shortDescription = trim((string) ($product->short_description ?: ''));
    if ($shortDescription === '') {
        $shortDescription = \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 170);
    }

    $deliveryNote = trim((string) ($product->delivery_note ?: __('Aynı gün teslimat için siparişinizi erken saatlerde oluşturabilirsiniz.')));
    $highlightCards = collect($product->localizedHighlights())->take(3)->values();
    $tags = $product->tags
        ->map(fn ($tag) => $tag->getTranslation('name', app()->getLocale()) ?: $tag->slug)
        ->filter()
        ->take(2)
        ->values();
    $relatedFallbackLinks = [
        [
            'label' => __('Özel gün seçkilerini aç'),
            'href' => \App\Support\StorefrontLocale::route('special-occasions.index'),
        ],
        [
            'label' => __('Tüm ürünlere dön'),
            'href' => \App\Support\StorefrontLocale::route('products.index'),
        ],
        [
            'label' => __('Uygun alternatif için sor'),
            'href' => \App\Support\StorefrontLocale::route('contact'),
        ],
    ];
    $purchaseSupportNotes = [
        __('Kart mesajı, teslim notu ve adres detayını ödeme öncesi net biçimde ekleyebilirsiniz.'),
        __('Gerçek ürün fotoğrafına en yakın sunum hedeflenir; mevsimsel küçük değişimler butik hazırlık içinde yönetilir.'),
    ];
    $productStoryShareTitle = __('Bu ürünü paylaş');
@endphp

@section('content')
    <x-breadcrumb :items="[
        ['label' => __('Anasayfa'), 'url' => \App\Support\StorefrontLocale::route('home')],
        ['label' => __('Ürünler'), 'url' => \App\Support\StorefrontLocale::route('products.index')],
        ['label' => $product->name],
    ]" />

    <section class="rg-pdp-shell grid gap-8 lg:grid-cols-[minmax(0,1.04fr)_minmax(0,0.96fr)] lg:items-start">
        <div class="contents lg:block lg:space-y-6">
            <div
                x-data="{
                    images: @js($galleryImages->values()),
                    activeImage: @js($galleryImages->first()),
                    lightbox: false,
                    lightboxImage: '',
                    openLightbox(src) { this.lightboxImage = src; this.lightbox = true; },
                }"
                class="rg-pdp-gallery order-1 space-y-4 lg:order-none"
            >
                <div class="rg-surface p-2.5 md:p-3">
                    <div class="relative aspect-[4/3] overflow-hidden rounded-[1.8rem] bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.96),rgba(239,228,235,0.72)_58%,rgba(227,215,224,0.88))] dark:bg-[radial-gradient(circle_at_top,rgba(62,43,72,0.9),rgba(31,23,37,0.96)_60%,rgba(22,15,28,0.98))] sm:aspect-[4/5]">
                        <button type="button" class="absolute inset-0 z-10 cursor-zoom-in" @click="openLightbox(activeImage)" aria-label="{{ __('Görseli büyüt') }}"></button>
                        <img
                            :src="activeImage"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-contain object-center p-4 transition-transform duration-700 hover:scale-[1.02] sm:p-6"
                            loading="lazy"
                        >
                        <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(180deg,rgba(255,255,255,0)_62%,rgba(22,14,28,0.18)_100%)]"></div>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-3 sm:grid-cols-5">
                    @foreach ($galleryImages as $image)
                        <button
                            type="button"
                            @click="activeImage = '{{ $image }}'"
                            class="overflow-hidden rounded-[1.15rem] border-2 bg-rg-lightLavender/24 transition-all duration-200 dark:bg-white/6"
                            :class="activeImage === '{{ $image }}' ? 'border-rg-purple shadow-[0_12px_26px_rgba(90,62,108,0.18)]' : 'border-transparent hover:border-rg-lavender'"
                            aria-label="{{ __('Görsel seç') }}"
                        >
                            <img src="{{ $image }}" alt="{{ $product->name }}" class="h-16 w-full object-cover object-center sm:h-20" loading="lazy">
                        </button>
                    @endforeach
                </div>

                <div
                    x-show="lightbox"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 p-4"
                    @click.self="lightbox = false"
                    @keydown.escape.window="lightbox = false"
                    x-cloak
                >
                    <button
                        type="button"
                        @click="lightbox = false"
                        aria-label="{{ __('Işık kutusunu kapat') }}"
                        class="absolute right-4 top-4 z-50 text-3xl text-white transition-colors hover:text-white/70"
                    >&times;</button>
                    <img :src="lightboxImage || activeImage" alt="{{ $product->name }}" class="max-h-[90vh] max-w-full rounded-[1.5rem] object-contain">
                </div>
            </div>

            <div class="rg-pdp-story order-3 rg-surface p-5 lg:order-none md:p-6">
                <div class="max-w-3xl">
                    <span class="rg-kicker">{{ __('Ürün Hikâyesi') }}</span>
                    <h2 class="mt-3 font-display text-[2rem] leading-tight text-rg-deepPurple dark:text-white md:text-[2.15rem]">{{ __('Hazırlık ve sunum dili') }}</h2>
                    <p class="mt-4 text-sm leading-[1.85] text-rg-grayText dark:text-white/84 md:text-[15px]">
                        {{ __('Her ürün yerel atölyede siparişe özel hazırlanır; fotoğraftaki tona yakın, taze ve özenli bir sunum hedeflenir.') }}
                    </p>
                </div>

                <div class="prose prose-sm mt-6 max-w-none text-rg-grayText dark:prose-invert dark:text-white/88">
                    {!! $product->description !!}
                </div>

                <div class="mt-6 border-t border-black/5 pt-5 dark:border-white/10">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ $productStoryShareTitle }}</p>
                    <div class="mt-3">
                        <x-share-buttons :title="$product->name" />
                    </div>
                </div>
            </div>
        </div>

        <div class="order-2 lg:order-none lg:sticky lg:top-32">
            <div class="rg-product-buybox rg-surface p-5 md:p-6">
                <div class="flex flex-wrap gap-2">
                    @foreach ($product->categories->take(2) as $category)
                        <span class="rounded-full border border-black/5 bg-rg-lightLavender/85 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-deepPurple dark:border-white/10 dark:bg-white/14 dark:text-rg-lavender">{{ $category->name }}</span>
                    @endforeach
                    @if ($product->is_new)
                        <span class="rounded-full bg-rg-leafGreen px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-white">{{ __('Yeni') }}</span>
                    @endif
                    @if ($product->stock_status !== 'in_stock')
                        <span class="rounded-full bg-zinc-700 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-white">{{ __('Tükendi') }}</span>
                    @endif
                </div>

                <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                    <h1 class="font-display text-[2rem] leading-tight text-rg-deepPurple dark:text-white sm:text-4xl md:text-[3.1rem]">{{ $product->name }}</h1>
                    <div class="shrink-0 self-start">
                        <livewire:favorite-toggle :product-id="$product->id" :key="'favorite-detail-'.$product->id" />
                    </div>
                </div>

                @if ($shortDescription !== '')
                    <p class="mt-4 text-base leading-[1.8] text-rg-grayText dark:text-white/84">{{ $shortDescription }}</p>
                @endif

                @if ($tags->isNotEmpty())
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($tags as $tag)
                            <span class="rounded-full border border-black/6 bg-rg-cream/75 px-3 py-1 text-xs font-medium text-rg-deepPurple dark:border-white/10 dark:bg-white/10 dark:text-white/86">{{ $tag }}</span>
                        @endforeach
                    </div>
                @endif

                <div class="mt-6">
                    <livewire:add-to-cart :product-id="$product->id" layout="detail" />
                </div>

                <div class="rg-pdp-whatsapp-action mt-4">
                    <a href="https://api.whatsapp.com/send?phone={{ data_get($siteSettings, 'contact.whatsapp_phone', '905522717067') }}&text={{ urlencode(__('Merhaba, bu ürünü sipariş vermek istiyorum: ') . $product->name . ' - ' . url()->current()) }}"
                       target="_blank" rel="noopener"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-emerald-500 px-5 py-3.5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-emerald-600">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        {{ __('WhatsApp ile Sipariş') }}
                    </a>
                </div>

                <div class="rg-pdp-support-grid mt-6 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-[1.15rem] border border-black/6 bg-rg-cream/72 px-4 py-3.5 dark:border-white/10 dark:bg-[#17131f]">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Teslimat Notu') }}</p>
                        <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/84">{{ $deliveryNote }}</p>
                    </div>
                    <div class="rounded-[1.15rem] border border-black/6 bg-rg-cream/72 px-4 py-3.5 dark:border-white/10 dark:bg-[#17131f]">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Hazırlık Dili') }}</p>
                        <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/84">{{ __('Gerçek ürün fotoğrafı, butik düzenleme ve sipariş ritmi birlikte korunur.') }}</p>
                    </div>
                </div>

                <div class="rg-pdp-trust-chips mt-4 flex flex-wrap gap-2">
                    <span class="rounded-full border border-black/6 bg-white/76 px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-rg-deepPurple dark:border-white/10 dark:bg-white/10 dark:text-white/84">{{ __('Gerçek ürün fotoğrafı') }}</span>
                    <span class="rounded-full border border-black/6 bg-white/76 px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-rg-deepPurple dark:border-white/10 dark:bg-white/10 dark:text-white/84">{{ __('Butik sunum') }}</span>
                </div>

                <div class="rg-pdp-whatsapp-action rg-pdp-whatsapp-action--duplicate mt-4">
                    <a href="https://api.whatsapp.com/send?phone={{ data_get($siteSettings, 'contact.whatsapp_phone', '905522717067') }}&text={{ urlencode(__('Merhaba, bu ürünü sipariş vermek istiyorum: ') . $product->name . ' - ' . url()->current()) }}"
                       target="_blank" rel="noopener"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-emerald-500 px-5 py-3.5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-emerald-600">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        {{ __('WhatsApp ile Sipariş') }}
                    </a>
                </div>

                @if ($highlightCards->isNotEmpty())
                    <div class="rg-pdp-highlights mt-6 border-t border-black/5 pt-6 dark:border-white/10">
                        <div class="mb-4">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Neden Seçiliyor') }}</p>
                            <h2 class="mt-2 font-display text-2xl text-rg-deepPurple dark:text-white">{{ __('Kararı hızlandıran detaylar') }}</h2>
                        </div>

                        <div class="grid gap-3 md:grid-cols-2">
                            @foreach ($highlightCards as $highlight)
                                <article class="rounded-[1.15rem] border border-black/5 bg-white/74 p-4 dark:border-white/10 dark:bg-white/8">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rg-lightLavender/75 text-rg-deepPurple dark:bg-white/10 dark:text-rg-lavender">
                                            @switch($highlight['icon'])
                                                @case('truck')
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17H6.5A1.5 1.5 0 015 15.5V6.5A1.5 1.5 0 016.5 5h8A1.5 1.5 0 0116 6.5V8m0 9h1m-8 0h4m4 0a2 2 0 104 0m-4 0a2 2 0 114 0m0 0h.5a1.5 1.5 0 001.5-1.5V13l-3-3h-3"/></svg>
                                                    @break
                                                @case('gift')
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12v7a1 1 0 01-1 1H5a1 1 0 01-1-1v-7m16 0H4m16 0V9a1 1 0 00-1-1h-3.38a1 1 0 01-.7-.29L12 5l-2.92 2.71a1 1 0 01-.7.29H5a1 1 0 00-1 1v3m8-7v15"/></svg>
                                                    @break
                                                @case('sun')
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.5m0 13V21m9-9h-2.5M5.5 12H3m15.364 6.364-1.768-1.768M7.404 7.404 5.636 5.636m12.728 0-1.768 1.768M7.404 16.596l-1.768 1.768M15.5 12a3.5 3.5 0 11-7 0 3.5 3.5 0 017 0z"/></svg>
                                                    @break
                                                @case('chat-bubble-left-right')
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h7m-9 8 2.5-2H18a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h1"/></svg>
                                                    @break
                                                @case('shield-check')
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 3v5c0 4.5-2.5 7.5-7 10-4.5-2.5-7-5.5-7-10V6l7-3zm-2 9 1.5 1.5L15 10"/></svg>
                                                    @break
                                                @default
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l1.9 5.3 5.6.2-4.5 3.4 1.6 5.2L12 14.2 7.4 17l1.6-5.2-4.5-3.4 5.6-.2L12 3z"/></svg>
                                            @endswitch
                                        </div>
                                        <div>
                                            <h2 class="font-display text-lg text-rg-deepPurple dark:text-white">{{ $highlight['title'] }}</h2>
                                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ $highlight['body'] }}</p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="rg-pdp-related mt-14 border-t border-black/5 pt-10 dark:border-white/10">
        <div class="mb-5 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <span class="rg-kicker">{{ __('İlgili Ürünler') }}</span>
                <h2 class="mt-3 font-display text-3xl text-rg-deepPurple dark:text-white md:text-4xl">{{ __('Benzer atmosferde seçimler') }}</h2>
            </div>
            <p class="rg-pdp-related-copy max-w-md text-sm leading-relaxed text-rg-grayText dark:text-white/84">{{ __('Aynı kategori ve sunum tonunda kalan gerçek alternatifler burada öne çıkar.') }}</p>
        </div>

        @if ($related->isNotEmpty())
            <x-product-rail :products="$related" :interactive="false" />
        @else
            <div class="grid gap-3 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
                <div class="rounded-[1.25rem] border border-black/6 bg-white/86 px-5 py-4 shadow-[0_12px_30px_rgba(34,24,40,0.05)] dark:border-white/10 dark:bg-[#201824]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Alternatif Yönler') }}</p>
                    <p class="mt-3 text-sm leading-relaxed text-rg-grayText dark:text-white/82">
                        {{ __('Bu ürüne yakın alternatifleri özel gün koleksiyonlarında, tüm ürünlerde veya WhatsApp desteğiyle inceleyebilirsiniz.') }}
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3">
                    @foreach ($relatedFallbackLinks as $link)
                        <a href="{{ $link['href'] }}" class="flex items-center justify-between rounded-[1.2rem] border border-black/6 bg-rg-cream/72 px-4 py-3 text-sm font-semibold text-rg-deepPurple transition-colors hover:border-rg-purple/35 hover:bg-rg-cream dark:border-white/10 dark:bg-white/8 dark:text-white">
                            <span>{{ $link['label'] }}</span>
                            <span aria-hidden="true">→</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

    </section>
@endsection
