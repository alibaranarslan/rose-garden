@php
    $contact = $siteSettings->get('contact', collect());
    $waPhone = preg_replace('/\D+/', '', (string) $contact->get('whatsapp_phone', '905522717067'));
    $branding = \App\Support\SiteBranding::current();
    $siteName = $branding['site_name'] ?? 'Rose Garden';
    $headerTheme = $activeHeaderTheme ?? null;
    $campaignVisuals = collect(data_get($headerTheme, 'campaign_visuals', []))->filter()->take(3)->values();
    $seasonalVisual = data_get($headerTheme, 'seasonal_visual') ?: $campaignVisuals->first();
    $seasonalMessage = data_get($headerTheme, 'seasonal_message') ?: data_get($headerTheme, 'message') ?: data_get($headerTheme, 'headline');
    $seasonalHeadline = data_get($headerTheme, 'headline') ?: $seasonalMessage;
    $seasonalSubline = data_get($headerTheme, 'subline');
    $seasonalCtaUrl = data_get($headerTheme, 'seasonal_cta.url') ?: data_get($headerTheme, 'seasonal_cta_url') ?: data_get($headerTheme, 'cta_url');
    $seasonalCtaLabel = data_get($headerTheme, 'seasonal_cta.label') ?: data_get($headerTheme, 'seasonal_cta_label') ?: data_get($headerTheme, 'cta_label');
    $seasonalIsPreview = (bool) data_get($headerTheme, 'is_preview');
@endphp
<header id="rg-site-header" class="relative z-10 overflow-visible {{ data_get($headerTheme, 'header_class') }}" x-data="{ mobileMenu: false }">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="relative overflow-visible rounded-b-[1.85rem] px-2 pb-4 pt-4 md:px-4 md:pb-[1.125rem] md:pt-[1.125rem]">
            <div class="pointer-events-none absolute inset-0 overflow-hidden rounded-b-[1.85rem]">
                <div class="absolute inset-x-10 top-0 h-full bg-[radial-gradient(circle_at_top,rgba(238,220,231,0.92),rgba(255,255,255,0))] dark:bg-[radial-gradient(circle_at_top,rgba(75,48,84,0.4),rgba(17,11,22,0))]"></div>
                <div class="absolute left-[16%] top-6 h-14 w-14 rounded-full bg-rg-rosePink/18 blur-3xl"></div>
                <div class="absolute right-[20%] top-8 h-12 w-12 rounded-full bg-rg-lavender/24 blur-3xl"></div>
                <div class="absolute inset-x-16 bottom-0 h-px bg-gradient-to-r from-transparent via-rg-lavender/55 to-transparent dark:via-white/12"></div>
            </div>

            <div class="relative flex items-center justify-center">
                <div class="rg-mobile-header-cart absolute left-0 top-1/2 -translate-y-1/2 md:hidden">
                    <livewire:cart-icon />
                </div>

                <a href="{{ \App\Support\StorefrontLocale::route('home') }}" class="block shrink-0" aria-label="{{ $siteName }}">
                    <x-site-logo
                        variant="adaptive"
                        type="wordmark"
                        placement="header"
                        loading="eager"
                        class="mx-auto h-14 max-w-[min(100%,16rem)] object-center sm:h-[3.8rem] sm:max-w-[18rem] md:h-[4.8rem] md:max-w-[21.5rem] lg:h-[5.4rem] lg:max-w-[24rem] 2xl:max-w-[25rem]"
                    />
                </a>
                <button type="button"
                    @click="mobileMenu = !mobileMenu"
                    class="rg-mobile-menu-button absolute right-0 top-1/2 inline-flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-black/8 bg-white/86 text-rg-darkText transition-colors hover:border-rg-purple/30 hover:text-rg-purple dark:border-white/10 dark:bg-white/14 dark:text-white dark:hover:text-rg-lavender md:hidden"
                    aria-label="{{ __('Menü') }}">
                    <svg x-show="!mobileMenu" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenu" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="relative mt-3 flex flex-wrap items-center justify-center gap-3 md:flex-nowrap md:items-center md:justify-between md:gap-4 md:px-1">
                <form action="{{ \App\Support\StorefrontLocale::route('search') }}" method="GET" class="rg-header-search-shell order-2 flex h-11 w-full overflow-hidden rounded-full border border-black/10 bg-white/94 shadow-[0_10px_24px_rgba(58,36,56,0.09)] transition-all duration-200 focus-within:border-rg-purple focus-within:bg-white focus-within:shadow-[0_14px_34px_rgba(78,55,95,0.16)] dark:border-white/12 dark:bg-[#1e1528]/92 dark:focus-within:border-rg-lavender dark:focus-within:bg-[#261935]/92 md:order-1 md:min-w-0 md:flex-1">
                    <span class="inline-flex shrink-0 items-center pl-3.5 text-rg-midPurple/72 dark:text-rg-lavender/78">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                    <input
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="{{ __('Buket, çikolata veya özel gün ara...') }}"
                        class="min-w-0 flex-1 bg-transparent px-3 py-0 text-[13px] outline-none placeholder:text-rg-grayText/80 dark:text-white dark:placeholder:text-white/42"
                        aria-label="{{ __('Ürün Ara') }}"
                    >
                    <button type="submit" class="rg-header-search-submit inline-flex shrink-0 items-center justify-center rounded-full bg-rg-purple px-4 text-sm font-semibold leading-none text-white transition-all duration-200 hover:bg-rg-darkPlum">
                        {{ __('Ara') }}
                    </button>
                </form>

                <div class="rg-header-control-cluster order-1 flex min-h-[2.75rem] w-full items-center justify-end gap-2.5 text-sm text-rg-darkText dark:text-white/85 md:order-2 md:w-auto md:justify-end">
                    <div class="rg-header-pill-row hidden gap-2 md:flex">
                        @guest
                            <a href="{{ \App\Support\StorefrontLocale::route('login') }}" class="inline-flex h-11 items-center justify-center rounded-full border border-black/8 bg-white/88 px-3.5 text-[13px] font-semibold text-rg-deepPurple transition-all duration-200 hover:border-rg-purple/30 hover:bg-white dark:border-white/12 dark:bg-white/14 dark:text-white dark:hover:bg-white/12">
                                {{ __('Giriş Yap') }}
                            </a>
                            <a href="{{ \App\Support\StorefrontLocale::route('register') }}" class="inline-flex h-11 items-center justify-center rounded-full border border-rg-purple/20 bg-rg-lightLavender/45 px-3.5 text-[13px] font-semibold text-rg-purple transition-all duration-200 hover:border-rg-purple/30 hover:bg-rg-lightLavender/65 dark:border-rg-lavender/30 dark:bg-white/10 dark:text-rg-lavender dark:hover:bg-white/14">
                                {{ __('Kayıt Ol') }}
                            </a>
                        @endguest

                        @auth
                            <a href="{{ \App\Support\StorefrontLocale::route('account.dashboard') }}" class="inline-flex h-11 items-center justify-center rounded-full border border-black/8 bg-white/88 px-3.5 text-[13px] font-semibold text-rg-deepPurple transition-all duration-200 hover:border-rg-purple/30 hover:bg-white dark:border-white/12 dark:bg-white/14 dark:text-white dark:hover:bg-white/12">
                                {{ __('Hesabım') }}
                            </a>
                            <form method="POST" action="{{ \App\Support\StorefrontLocale::route('logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex h-11 items-center justify-center rounded-full border border-black/8 bg-white/88 px-3.5 text-[13px] font-semibold text-rg-grayText transition-all duration-200 hover:border-rg-purple/30 hover:bg-white hover:text-rg-purple dark:border-white/12 dark:bg-white/14 dark:text-white/80 dark:hover:bg-white/12 dark:hover:text-rg-lavender">
                                    {{ __('Çıkış Yap') }}
                                </button>
                            </form>
                        @endauth
                    </div>

                    <div class="rg-header-pill-row hidden gap-2 lg:flex">
                        <a href="https://api.whatsapp.com/send?phone={{ $waPhone }}" target="_blank" rel="noopener" class="inline-flex h-11 items-center gap-2 rounded-full border border-black/8 bg-white/88 px-3.5 text-[13px] font-semibold text-rg-deepPurple transition-all duration-200 hover:border-rg-purple/30 hover:bg-white dark:border-white/12 dark:bg-white/14 dark:text-white dark:hover:bg-white/12">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            WhatsApp
                        </a>
                    </div>

                    <a href="{{ \App\Support\StorefrontLocale::route('account.favorites') }}" aria-label="{{ __('Favorilerim') }}" class="hidden h-11 items-center gap-2 rounded-full border border-black/8 bg-white/88 px-3.5 text-[13px] font-semibold text-rg-grayText transition-all duration-200 hover:border-rg-purple/30 hover:bg-white hover:text-rg-purple dark:border-white/12 dark:bg-white/14 dark:text-white/80 dark:hover:bg-white/12 dark:hover:text-rg-lavender md:inline-flex">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span class="hidden xl:inline">{{ __('Favorilerim') }}</span>
                    </a>

                    <a href="{{ \App\Support\StorefrontLocale::route('search') }}" class="hidden text-rg-grayText transition-colors hover:text-rg-purple dark:text-white/86 dark:hover:text-rg-lavender md:hidden" aria-label="{{ __('Arama') }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </a>

                    <div class="rg-header-right-utility-trio">
                        <div class="rg-header-square-control rg-header-theme-control rg-header-utility-control">
                            <x-theme-toggle />
                        </div>

                        <div class="rg-header-language-control rg-header-utility-control">
                            <x-language-switcher />
                        </div>

                        <div class="rg-header-square-control rg-header-cart-control rg-header-utility-control">
                            <livewire:cart-icon />
                        </div>
                    </div>

                    <button type="button"
                        @click="mobileMenu = !mobileMenu"
                        class="hidden"
                        aria-label="{{ __('Menü') }}">
                        <svg x-show="!mobileMenu" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenu" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        @if ($seasonalHeadline)
            <section id="rg-special-day-banner" class="special-day-campaign mb-2 mt-2" aria-label="{{ data_get($headerTheme, 'headline') }}">
                <div class="special-day-campaign-copy">
                    <div class="special-day-campaign-kicker">
                        <span>{{ $seasonalMessage }}</span>
                        @if ($seasonalIsPreview)
                            <span class="special-day-campaign-preview">{{ __('Önizleme') }}</span>
                        @endif
                    </div>
                    <h2>{{ $seasonalHeadline }}</h2>
                    @if ($seasonalSubline)
                        <p>{{ $seasonalSubline }}</p>
                    @endif
                    @if ($seasonalCtaUrl && $seasonalCtaLabel)
                        <a href="{{ $seasonalCtaUrl }}" class="special-day-campaign-cta">
                            <span>{{ $seasonalCtaLabel }}</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    @endif
                </div>

                @if ($campaignVisuals->isNotEmpty())
                    <div class="special-day-campaign-visuals" aria-hidden="true">
                        @foreach ($campaignVisuals as $visual)
                            <span class="special-day-campaign-photo">
                                <img src="{{ $visual }}" alt="" loading="lazy" decoding="async">
                            </span>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif
    </div>

    <div
        x-show="mobileMenu"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        x-cloak
        class="border-t border-black/5 bg-white/92 backdrop-blur-xl dark:border-white/10 dark:bg-[#17101f]/95 md:hidden"
    >
        <div class="mx-auto max-w-7xl space-y-4 px-4 py-4 sm:px-6">
            <div class="flex items-center justify-between rounded-[1.5rem] border border-black/5 bg-rg-cream/80 px-3.5 py-3 dark:border-white/10 dark:bg-white/10">
                <span class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/72">{{ __('Dil ve tema') }}</span>
                <div class="flex items-center gap-2">
                    <x-theme-toggle />
                    <x-language-switcher />
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-black/8 bg-white px-4 py-3 text-sm font-semibold text-rg-deepPurple shadow-sm dark:border-white/10 dark:bg-white/14 dark:text-white">
                    {{ __('Koleksiyon') }}
                </a>
                <a href="https://api.whatsapp.com/send?phone={{ $waPhone }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-2xl border border-emerald-500/25 bg-emerald-500/10 px-4 py-3 text-sm font-semibold text-emerald-700 dark:text-emerald-300">
                    WhatsApp
                </a>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                @guest
                    <a href="{{ \App\Support\StorefrontLocale::route('login') }}" class="inline-flex items-center justify-center rounded-2xl border border-black/8 bg-white px-4 py-3 text-sm font-semibold text-rg-deepPurple shadow-sm dark:border-white/10 dark:bg-white/14 dark:text-white">
                        {{ __('Giriş Yap') }}
                    </a>
                    <a href="{{ \App\Support\StorefrontLocale::route('register') }}" class="inline-flex items-center justify-center rounded-2xl border border-rg-purple/20 bg-rg-lightLavender/45 px-4 py-3 text-sm font-semibold text-rg-purple shadow-sm dark:border-rg-lavender/30 dark:bg-white/10 dark:text-rg-lavender">
                        {{ __('Kayıt Ol') }}
                    </a>
                @endguest

                @auth
                    <a href="{{ \App\Support\StorefrontLocale::route('account.dashboard') }}" class="inline-flex items-center justify-center rounded-2xl border border-black/8 bg-white px-4 py-3 text-sm font-semibold text-rg-deepPurple shadow-sm dark:border-white/10 dark:bg-white/14 dark:text-white">
                        {{ __('Hesabım') }}
                    </a>
                    <form method="POST" action="{{ \App\Support\StorefrontLocale::route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl border border-black/8 bg-white px-4 py-3 text-sm font-semibold text-rg-grayText shadow-sm dark:border-white/10 dark:bg-white/14 dark:text-white/85">
                            {{ __('Çıkış Yap') }}
                        </button>
                    </form>
                @endauth
            </div>

            <nav class="grid gap-2 rounded-[1.75rem] border border-black/5 bg-rg-cream/80 p-3 dark:border-white/10 dark:bg-white/10">
                <a href="{{ \App\Support\StorefrontLocale::route('account.dashboard') }}" class="flex items-center justify-between rounded-2xl px-3 py-3 text-sm font-medium text-rg-darkText transition-colors hover:bg-white dark:text-white/90 dark:hover:bg-white/8">
                    <span>{{ __('Hesabım') }}</span>
                    <svg class="h-4 w-4 text-rg-midPurple dark:text-rg-lavender/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ \App\Support\StorefrontLocale::route('account.favorites') }}" class="flex items-center justify-between rounded-2xl px-3 py-3 text-sm font-medium text-rg-darkText transition-colors hover:bg-white dark:text-white/90 dark:hover:bg-white/8">
                    <span>{{ __('Favorilerim') }}</span>
                    <svg class="h-4 w-4 text-rg-midPurple dark:text-rg-lavender/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ \App\Support\StorefrontLocale::route('cart') }}" class="flex items-center justify-between rounded-2xl px-3 py-3 text-sm font-medium text-rg-darkText transition-colors hover:bg-white dark:text-white/90 dark:hover:bg-white/8">
                    <span>{{ __('Sepetim') }}</span>
                    <svg class="h-4 w-4 text-rg-midPurple dark:text-rg-lavender/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ \App\Support\StorefrontLocale::route('contact') }}" class="flex items-center justify-between rounded-2xl px-3 py-3 text-sm font-medium text-rg-darkText transition-colors hover:bg-white dark:text-white/90 dark:hover:bg-white/8">
                    <span>{{ __('İletişim') }}</span>
                    <svg class="h-4 w-4 text-rg-midPurple dark:text-rg-lavender/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ \App\Support\StorefrontLocale::route('order.track') }}" class="flex items-center justify-between rounded-2xl px-3 py-3 text-sm font-medium text-rg-darkText transition-colors hover:bg-white dark:text-white/90 dark:hover:bg-white/8">
                    <span>{{ __('Sipariş Takip') }}</span>
                    <svg class="h-4 w-4 text-rg-midPurple dark:text-rg-lavender/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </nav>
        </div>
    </div>
</header>
