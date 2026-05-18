@props([
    /** @var \Illuminate\Support\Collection<int, \App\Models\Product>|iterable */
    'accentProducts' => [],
    /** @var list<string>|iterable Ürün atanamayan slotlar için foto URL’leri */
    'fallbackImageUrls' => [],
])

@php
    $trustAccentRow = collect($accentProducts)->values();
    $fallbackRow = collect($fallbackImageUrls)->values();

    if ($fallbackRow->isEmpty()) {
        $fallbackRow = collect(\App\Support\StorefrontImage::productVisualStrip(4))->values();
    }

    $cards = [
        [
            'title' => __('Aynı Gün Teslimat'),
            'body' => __('Saat 14:00’e kadar alınan siparişleri şehir içinde aynı gün ulaştırıyoruz.'),
        ],
        [
            'title' => __('Güvenli Ödeme'),
            'body' => __('SSL koruması, 3D Secure desteği ve PayTR altyapısıyla güvenli ödeme.'),
        ],
        [
            'title' => __('Taze Hazırlık'),
            'body' => __('Her ürün sipariş anında taze çiçeklerle hazırlanır; stoktan hazır paket çıkmaz.'),
        ],
        [
            'title' => __('İnsana Yakın Destek'),
            'body' => __('WhatsApp ve telefon hattımızla seçim, teslimat ve not kartı detaylarında hızlı destek veriyoruz.'),
        ],
    ];
@endphp

<section class="py-8 md:py-10">
    <div class="rg-surface px-5 py-5 md:px-6 md:py-6">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <span class="rg-kicker">{{ __('Rose Garden Güvencesi') }}</span>
                <h2 class="mt-3 text-balance font-display text-3xl text-rg-deepPurple dark:text-white md:text-4xl">
                    {{ __('Sipariş deneyimini güven veren küçük detaylar taşıyor.') }}
                </h2>
            </div>
            <p class="max-w-xl text-pretty text-sm leading-relaxed text-rg-grayText md:text-base dark:text-zinc-200">
                {{ __('Hazırlık kalitesi, teslimat hızı ve destek hattı; estetiği işlevle birlikte düşünmemizin sonucu.') }}
            </p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($cards as $idx => $card)
                @php
                    $p = $trustAccentRow->get($idx);
                    $fbUrl = $fallbackRow->get($idx);
                    $imgSrc = null;
                    $imgHref = null;
                    $imgAlt = '';
                    if ($p instanceof \App\Models\Product) {
                        $p->loadMissing(['images' => fn ($q) => $q->orderBy('sort_order')]);
                        $imgSrc = \App\Support\StorefrontImage::publicImgSrc(\App\Support\StorefrontImage::resolveProduct(
                            optional($p->images->first())->image_path,
                            $p->slug,
                            $p->name,
                        ));
                        $imgHref = \App\Support\StorefrontLocale::route('products.show', ['slug' => $p->slug]);
                        $imgAlt = $p->name;
                    } elseif (filled($fbUrl)) {
                        $imgSrc = \App\Support\StorefrontImage::publicImgSrc($fbUrl);
                        $imgHref = \App\Support\StorefrontLocale::route('products.index');
                        $imgAlt = $card['title'];
                    }
                @endphp
                <div class="flex min-h-0 flex-col overflow-hidden rounded-[1.35rem] border border-black/6 bg-rg-cream/80 dark:border-white/10 dark:bg-[#252030] dark:shadow-[inset_0_1px_0_rgba(255,255,255,0.06)]">
                    <div class="relative aspect-[16/11] w-full shrink-0 overflow-hidden bg-gradient-to-br from-rg-lightLavender/80 to-rg-cream dark:from-[#322a40] dark:to-[#1e1826]">
                        @if ($imgSrc && $imgHref)
                            <a href="{{ $imgHref }}" class="group relative block h-full w-full focus-visible:outline-none" aria-label="{{ $imgAlt }}">
                                <img
                                    src="{{ $imgSrc }}"
                                    alt="{{ $imgAlt }}"
                                    loading="lazy"
                                    decoding="async"
                                    class="h-full w-full object-cover transition duration-500 ease-out group-hover:scale-[1.04]"
                                >
                                <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(180deg,transparent_40%,rgba(26,14,22,0.25)_100%)]"></div>
                            </a>
                        @else
                            <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="flex h-full min-h-[8.5rem] items-center justify-center p-6 text-center text-sm font-semibold text-rg-midPurple dark:text-rg-lavender">
                                {{ __('Koleksiyonu Keşfet') }}
                            </a>
                        @endif
                    </div>
                    <div class="flex min-h-0 flex-1 flex-col p-4 md:p-5">
                        <h3 class="font-display text-lg font-semibold leading-snug text-rg-deepPurple dark:text-zinc-50">
                            {{ $card['title'] }}
                        </h3>
                        <p class="mt-2 text-pretty text-sm leading-relaxed text-rg-grayText dark:text-zinc-300">
                            {{ $card['body'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
