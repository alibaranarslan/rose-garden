@php
    $settings = $siteSettings ?? collect();
    $social = $settings->get('social', collect());
    $contact = $settings->get('contact', collect());
    $branding = \App\Support\SiteBranding::current();
    $siteName = $branding['site_name'] ?? 'Rose Garden';
    $siteTagline = $branding['site_tagline'] ?? null;
    $footerDescription = $branding['footer_description'] ?? $siteTagline;
    $socialProfiles = collect($branding['social_profiles'] ?? []);
    $contactPhone = $contact->get('contact_phone', '0552 271 70 67');
    $waPhone = \App\Support\ContactLinks::phoneForWhatsApp($contact);
    $contactEmail = $contact->get('contact_email') ?: 'info@rosegardencicekcilik.com.tr';
    $contactAddress = $contact->get('address', 'Yeni Sanayi Mah. 2819 Sk. No: 70/2B K.A.06 Adıyaman Merkez');
    $phoneRaw = \App\Support\ContactLinks::phoneForTel($contact);
    $footerPromoCards = collect($footerPromoVisuals ?? []);

    if ($footerPromoCards->isEmpty()) {
        $footerPromoCards = collect(\App\Support\StorefrontImage::footerPromoVisualCards(2));
    }

    $footerPromoCards = $footerPromoCards->take(2)->values();
    $localizeFooterHref = static function (?string $href): string {
        $fallback = \App\Support\StorefrontLocale::route('products.index');
        $href = trim((string) ($href ?: $fallback));

        if ($href === '' || preg_match('/^(#|mailto:|tel:|javascript:)/i', $href)) {
            return $href !== '' ? $href : $fallback;
        }

        $parts = parse_url($href);

        if ($parts === false) {
            return $fallback;
        }

        $isAbsolute = isset($parts['scheme']) || isset($parts['host']);
        $host = $parts['host'] ?? null;

        if ($isAbsolute && $host !== request()->getHost()) {
            return $href;
        }

        $query = [];
        parse_str($parts['query'] ?? '', $query);

        $root = $isAbsolute
            ? ($parts['scheme'] ?? request()->getScheme()).'://'.$host.(isset($parts['port']) ? ':'.$parts['port'] : '')
            : null;

        return \App\Support\StorefrontLocale::urlForPath(
            $parts['path'] ?? '/',
            null,
            $query,
            request()->route('locale') !== null,
            $root
        );
    };
@endphp

<footer class="rg-footer mt-12 border-t border-black/5 bg-[#faf6f1] text-rg-deepPurple dark:border-white/8 dark:bg-[#140f18] dark:text-white">
    <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6">
        <div class="rg-footer-feature overflow-hidden rounded-[1.65rem] border border-black/6 bg-[linear-gradient(135deg,rgba(255,255,255,0.95),rgba(246,236,231,0.98))] px-5 py-4 shadow-[0_14px_38px_rgba(32,22,36,0.06)] dark:border-white/10 dark:bg-[linear-gradient(135deg,rgba(38,24,42,0.94),rgba(28,18,33,0.98))] dark:shadow-[0_20px_46px_rgba(6,4,8,0.28)] sm:px-6 md:px-7 md:py-5">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)] lg:items-center lg:gap-10">
                <div class="min-w-0 space-y-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-rg-midPurple/80 dark:text-white/86">{{ $siteName }}</p>
                    <div class="max-w-2xl space-y-3">
                        <h2 class="text-balance font-display text-3xl leading-[1.12] text-rg-deepPurple dark:text-white md:text-[2.4rem]">
                            {{ $siteTagline ?: __('Adıyaman için taze çiçek ve hediyelik bitkiler.') }}
                        </h2>
                        <p class="max-w-2xl text-pretty text-sm leading-[1.7] text-rg-grayText md:text-base dark:text-white/84">
                            {{ $footerDescription ?: __('Rose Garden; Adıyaman içinde özenle hazırlanan çiçek, saksı bitkisi ve butik hediye seçenekleri sunar. Sipariş ve destek için her zaman bize ulaşabilirsiniz.') }}
                        </p>
                    </div>
                    <div class="rg-footer-cta-row flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="rg-btn-primary inline-flex items-center justify-center rounded-full px-5 py-3 text-sm font-semibold dark:bg-white dark:text-rg-deepPurple dark:hover:bg-white/90">
                            {{ __('Koleksiyonu Keşfet') }}
                        </a>
                        <a href="{{ \App\Support\StorefrontLocale::route('contact') }}" class="rg-button-secondary px-5 py-3">
                            {{ __('Mağaza ile İletişim') }}
                        </a>
                    </div>
                </div>

                @if ($footerPromoCards->isNotEmpty())
                    <div class="rg-footer-promo-grid grid gap-4 sm:grid-cols-2">
                        @foreach ($footerPromoCards as $card)
                            @php
                                $visualSrc = \App\Support\StorefrontImage::publicImgSrc($card['src'] ?? '');
                                $visualHref = $localizeFooterHref($card['href'] ?? null);
                                $displayTitle = trim((string) ($card['label'] ?? '')) ?: __('Yerel Seçki');
                            @endphp

                            <a href="{{ $visualHref }}" class="group relative block overflow-hidden rounded-[1.7rem] border border-black/6 bg-white/80 shadow-[0_14px_34px_rgba(34,24,40,0.08)] transition-[transform,box-shadow] duration-200 hover:-translate-y-0.5 hover:shadow-[0_20px_44px_rgba(34,24,40,0.12)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rg-purple dark:border-white/10 dark:bg-white/10 dark:shadow-[0_16px_40px_rgba(0,0,0,0.35)] dark:hover:shadow-[0_22px_50px_rgba(0,0,0,0.45)]">
                                <div class="relative aspect-[1.02/1.08] w-full overflow-hidden bg-rg-lightLavender/20 dark:bg-white/6">
                                    <img src="{{ $visualSrc }}" alt="{{ $displayTitle }}" loading="lazy" decoding="async" class="h-full w-full object-cover object-center transition-transform duration-500 group-hover:scale-[1.04]">
                                    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(180deg,rgba(0,0,0,0.04),rgba(17,10,22,0.7))]"></div>
                                    <div class="absolute inset-x-0 bottom-0 p-4">
                                        <p class="text-sm font-semibold leading-snug text-white drop-shadow-[0_2px_12px_rgba(0,0,0,0.55)]">{{ $displayTitle }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="rg-footer-main mx-auto grid max-w-7xl grid-cols-1 gap-7 px-4 pb-9 pt-3 sm:px-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,0.8fr)_minmax(0,1fr)] lg:gap-7">
        <div class="rg-footer-brand space-y-4 lg:pr-6">
            <x-site-logo variant="adaptive" type="lockup" placement="footer" loading="lazy" decoding="async" />
            @if ($siteTagline)
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-rg-midPurple/75 dark:text-white/78">{{ $siteTagline }}</p>
            @endif
            <p class="max-w-md text-base leading-relaxed text-rg-grayText dark:text-white/86">
                {{ $footerDescription ?: __('Buket, saksı bitkisi ve özel gün hediyelerini gerçek ürün fotoğraflarıyla inceleyin; sipariş için bize kolayca ulaşın.') }}
            </p>
            <div class="flex flex-wrap gap-2">
                <span class="rounded-full border border-black/6 bg-white/70 px-3 py-1.5 text-xs font-semibold text-rg-deepPurple shadow-sm dark:border-white/10 dark:bg-white/12 dark:text-white/88">{{ __('Aynı Gün Teslimat') }}</span>
                <span class="rounded-full border border-black/6 bg-white/70 px-3 py-1.5 text-xs font-semibold text-rg-deepPurple shadow-sm dark:border-white/10 dark:bg-white/12 dark:text-white/88">{{ __('Butik Tasarım') }}</span>
                <span class="rounded-full border border-black/6 bg-white/70 px-3 py-1.5 text-xs font-semibold text-rg-deepPurple shadow-sm dark:border-white/10 dark:bg-white/12 dark:text-white/88">{{ __('Gerçek Ürün Görselleri') }}</span>
            </div>
            <div class="flex items-center gap-3">
                @foreach ($socialProfiles as $profile)
                    <a href="{{ $profile['url'] }}" target="_blank" rel="noopener" aria-label="{{ $profile['label'] }}"
                       class="flex h-10 w-10 items-center justify-center rounded-full border border-black/8 bg-white/78 text-rg-deepPurple transition-colors duration-200 hover:bg-white dark:border-white/10 dark:bg-white/12 dark:text-white/90 dark:hover:bg-white/12 dark:hover:text-white">
                        @switch($profile['platform'])
                            @case('facebook')
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                @break
                            @case('twitter')
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.742l7.736-8.843L2.139 2.25H8.48l4.265 5.634 5.499-5.634zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                @break
                            @case('youtube')
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                @break
                            @default
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        @endswitch
                    </a>
                @endforeach
                <a href="https://api.whatsapp.com/send?phone={{ $waPhone }}" target="_blank" rel="noopener" aria-label="WhatsApp"
                   class="flex h-10 w-10 items-center justify-center rounded-full border border-black/8 bg-white/78 text-rg-deepPurple transition-colors duration-200 hover:bg-emerald-50 dark:border-white/10 dark:bg-white/12 dark:text-white/90 dark:hover:bg-emerald-500/20 dark:hover:text-white">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
            </div>
        </div>

        <div class="rg-footer-links grid grid-cols-1 gap-6 sm:grid-cols-2">
            <div class="min-w-0">
                <p class="mb-4 text-[11px] font-semibold uppercase tracking-[0.26em] text-rg-midPurple/75 dark:text-white/86">{{ __('Keşfet') }}</p>
                <ul class="space-y-3 text-sm text-rg-grayText dark:text-white/86">
                    <li><a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="transition-colors duration-200 hover:text-rg-deepPurple dark:hover:text-white">{{ __('Tüm Ürünler') }}</a></li>
                    <li><a href="{{ \App\Support\StorefrontLocale::route('special-occasions.index') }}" class="transition-colors duration-200 hover:text-rg-deepPurple dark:hover:text-white">{{ __('Özel Günler') }}</a></li>
                    <li><a href="{{ \App\Support\StorefrontLocale::route('blog.index') }}" class="transition-colors duration-200 hover:text-rg-deepPurple dark:hover:text-white">{{ __('Blog') }}</a></li>
                    <li><a href="{{ \App\Support\StorefrontLocale::route('faq') }}" class="transition-colors duration-200 hover:text-rg-deepPurple dark:hover:text-white">{{ __('Sık Sorulan Sorular') }}</a></li>
                    <li><a href="{{ \App\Support\StorefrontLocale::route('delivery.info') }}" class="transition-colors duration-200 hover:text-rg-deepPurple dark:hover:text-white">{{ __('Teslimat Bilgileri') }}</a></li>
                    <li><a href="{{ \App\Support\StorefrontLocale::route('contact') }}" class="transition-colors duration-200 hover:text-rg-deepPurple dark:hover:text-white">{{ __('İletişim') }}</a></li>
                </ul>
            </div>

            <div class="min-w-0">
                <p class="mb-4 text-[11px] font-semibold uppercase tracking-[0.26em] text-rg-midPurple/75 dark:text-white/86">{{ __('Yasal') }}</p>
                <ul class="space-y-3 text-sm text-rg-grayText dark:text-white/86">
                    <li><a href="{{ \App\Support\StorefrontLocale::route('page.show', ['slug' => 'gizlilik-politikasi']) }}" class="transition-colors duration-200 hover:text-rg-deepPurple dark:hover:text-white">{{ __('Gizlilik Politikası') }}</a></li>
                    <li><a href="{{ \App\Support\StorefrontLocale::route('page.show', ['slug' => 'cerez-politikasi']) }}" class="transition-colors duration-200 hover:text-rg-deepPurple dark:hover:text-white">{{ __('Çerez Politikası') }}</a></li>
                    <li><a href="{{ \App\Support\StorefrontLocale::route('page.show', ['slug' => 'iade-iptal']) }}" class="transition-colors duration-200 hover:text-rg-deepPurple dark:hover:text-white">{{ __('İade ve İptal Koşulları') }}</a></li>
                    <li><a href="{{ \App\Support\StorefrontLocale::route('page.show', ['slug' => 'kvkk-aydinlatma']) }}" class="transition-colors duration-200 hover:text-rg-deepPurple dark:hover:text-white">{{ __('KVKK Aydınlatma') }}</a></li>
                    <li><a href="{{ \App\Support\StorefrontLocale::route('page.show', ['slug' => 'mesafeli-satis-sozlesmesi']) }}" class="transition-colors duration-200 hover:text-rg-deepPurple dark:hover:text-white">{{ __('Mesafeli Satış Sözleşmesi') }}</a></li>
                </ul>
            </div>
        </div>

        <div class="min-w-0">
            <p class="mb-4 text-[11px] font-semibold uppercase tracking-[0.26em] text-rg-midPurple/75 dark:text-white/86">{{ __('İletişim ve Ödeme') }}</p>
            <div class="rg-footer-contact-card min-w-0 rounded-[1.45rem] border border-black/6 bg-white/72 p-4 shadow-sm dark:border-white/10 dark:bg-white/10">
                <ul class="space-y-3 text-sm text-rg-grayText dark:text-white/86">
                    <li class="flex min-w-0 items-start gap-3">
                        <span class="mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center text-rg-purple dark:text-rg-lavender" aria-hidden="true">
                            <svg class="block h-5 w-5 max-h-5 max-w-5" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        <span class="min-w-0">{{ $contactAddress }}</span>
                    </li>
                    <li class="flex min-w-0 items-center gap-3">
                        <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center text-rg-purple dark:text-rg-lavender" aria-hidden="true">
                            <svg class="block h-5 w-5 max-h-5 max-w-5" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </span>
                        <a href="tel:+{{ $phoneRaw }}" class="min-w-0 transition-colors duration-200 hover:text-rg-deepPurple dark:hover:text-white">{{ $contactPhone }}</a>
                    </li>
                    <li class="flex min-w-0 items-center gap-3">
                        <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center text-rg-purple dark:text-rg-lavender" aria-hidden="true">
                            <svg class="block h-5 w-5 max-h-5 max-w-5" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <a href="mailto:{{ $contactEmail }}" class="min-w-0 break-all transition-colors duration-200 hover:text-rg-deepPurple dark:hover:text-white">{{ $contactEmail }}</a>
                    </li>
                </ul>
                <div class="mt-5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple/75 dark:text-white/86">{{ __('Ödeme Yöntemleri') }}</p>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <div class="flex h-8 items-center justify-center rounded-full bg-white px-3 text-xs font-bold text-[#1A1F71] shadow-sm">VISA</div>
                        <div class="flex h-8 items-center justify-center gap-0.5 rounded-full bg-white px-3 shadow-sm">
                            <div class="h-4 w-4 rounded-full bg-red-500 opacity-90"></div>
                            <div class="-ml-2 h-4 w-4 rounded-full bg-yellow-400 opacity-90"></div>
                        </div>
                        <div class="flex h-8 items-center justify-center rounded-full bg-white px-3 text-xs font-bold text-[#0066A1] shadow-sm">TROY</div>
                        <div class="flex h-8 items-center justify-center rounded-full bg-white px-3 text-xs font-bold text-gray-700 shadow-sm">PayTR</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-black/6 dark:border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-4 text-sm text-rg-grayText/80 sm:px-6 md:flex-row dark:text-white/86">
            <p>&copy; {{ date('Y') }} {{ $siteName }}. {{ __('Tüm hakları saklıdır.') }}</p>
            <p>{{ __('Adıyaman’da tasarlandı ve geliştirildi.') }}</p>
        </div>
    </div>
    <div class="rg-footer-signature">
        <div class="mx-auto max-w-7xl px-4 py-4 text-center">
            <p class="m-0 leading-none">
                <span class="rg-footer-signature-made">made by</span>
                <span class="rg-footer-signature-name">Ali Baran Arslan</span>
            </p>
            <a href="mailto:alibaranarslann@outlook.com" class="rg-footer-signature-mail">
                alibaranarslann@outlook.com
            </a>
        </div>
    </div>
</footer>
