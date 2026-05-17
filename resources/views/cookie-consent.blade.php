@php
    $cookieConsentPath = \App\Support\StorefrontLocale::stripPrefix(request()->path());
    $cookieConsentNormalizedPath = trim($cookieConsentPath, '/');
    $cookieConsentCheckoutSafe = request()->routeIs('checkout', 'checkout.*')
        || $cookieConsentNormalizedPath === 'odeme'
        || str_starts_with($cookieConsentNormalizedPath, 'odeme/');
    $cookieConsentAuthSafe = request()->routeIs('login', 'register', 'password.*')
        || in_array($cookieConsentNormalizedPath, ['giris', 'kayit', 'sifremi-unuttum'], true)
        || str_starts_with($cookieConsentNormalizedPath, 'sifre-sifirla/');
    $cookieConsentCommerceSafe = request()->routeIs('cart', 'products.show')
        || in_array($cookieConsentNormalizedPath, ['sepet', 'cart'], true)
        || str_starts_with($cookieConsentNormalizedPath, 'urun/')
        || str_starts_with($cookieConsentNormalizedPath, 'product/');
    $cookieConsentContentSafe = request()->routeIs('blog.*', 'contact', 'faq', 'delivery.info', 'order.track', 'page.show')
        || in_array($cookieConsentNormalizedPath, ['blog', 'iletisim', 'sss', 'teslimat-bilgileri', 'siparis-takip'], true)
        || str_starts_with($cookieConsentNormalizedPath, 'blog/')
        || str_starts_with($cookieConsentNormalizedPath, 'sayfa/');
@endphp

<div
    x-data="{
        storageKey: 'rg_site_cookie_consent',
        openSettings: false,
        consent: { required: true, analytics: false, marketing: false, functional: false },
        visible: false,
        init() {
            const raw = localStorage.getItem(this.storageKey);
            if (!raw) {
                this.visible = true;
                return;
            }

            try {
                const parsed = JSON.parse(raw);
                const isExpired = !parsed.expires_at || Date.now() > parsed.expires_at;

                if (isExpired) {
                    localStorage.removeItem(this.storageKey);
                    this.visible = true;
                    return;
                }

                this.consent.analytics = !!parsed.analytics;
                this.consent.marketing = !!parsed.marketing;
                this.consent.functional = !!parsed.functional;
                window.dispatchEvent(new CustomEvent('cookie-consent-updated', { detail: parsed }));
            } catch (_) {
                this.visible = true;
            }
        },
        persist(values) {
            const payload = {
                required: true,
                analytics: !!values.analytics,
                marketing: !!values.marketing,
                functional: !!values.functional,
                updated_at: Date.now(),
                expires_at: Date.now() + 365 * 24 * 60 * 60 * 1000,
            };

            localStorage.setItem(this.storageKey, JSON.stringify(payload));
            this.persistServer(payload);
            window.dispatchEvent(new CustomEvent('cookie-consent-updated', { detail: payload }));
            this.visible = false;
            this.openSettings = false;
        },
        persistServer(payload) {
            const categories = ['necessary'];
            if (payload.analytics) categories.push('analytics');
            if (payload.marketing) categories.push('marketing');

            fetch('{{ \App\Support\StorefrontLocale::route('cookie-consent.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.content ?? '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ categories }),
            }).catch(() => {});
        },
        acceptAll() {
            this.persist({ analytics: true, marketing: true, functional: true });
        },
        rejectOptional() {
            this.persist({ analytics: false, marketing: false, functional: false });
        },
        saveCustom() {
            this.persist(this.consent);
        }
    }"
    x-show="visible"
    x-transition.opacity
    @class([
        'rg-cookie-consent fixed bottom-3 right-3 z-40 w-[min(16.75rem,calc(100vw-1.5rem))] md:bottom-3 md:right-4 md:w-[17.75rem] lg:w-[18.5rem]',
        'rg-cookie-consent--cart-safe' => $cookieConsentCommerceSafe,
        'rg-cookie-consent--checkout-safe' => $cookieConsentCheckoutSafe,
        'rg-cookie-consent--auth-safe' => $cookieConsentAuthSafe,
        'rg-cookie-consent--content-safe' => $cookieConsentContentSafe,
    ])
    style="display: none;"
>
    <div class="max-h-[calc(100vh-1.5rem)] overflow-y-auto overflow-x-hidden rounded-[1.25rem] border border-black/8 bg-white/88 text-rg-deepPurple shadow-[0_14px_34px_rgba(40,24,38,0.12)] backdrop-blur-lg dark:border-white/10 dark:bg-[#1d161f]/88 dark:text-white">
        <div class="bg-[linear-gradient(135deg,rgba(244,229,237,0.86),rgba(236,229,243,0.8))] px-3.5 py-3 dark:bg-[linear-gradient(135deg,rgba(62,40,61,0.84),rgba(45,31,52,0.82))]">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/75">{{ __('Çerez Tercihleri') }}</p>
                    <h3 class="mt-1.5 font-display text-[1.08rem] leading-tight text-rg-deepPurple dark:text-white md:text-[1.12rem]">{{ __('Deneyimi sizin ritminize göre ayarlıyoruz.') }}</h3>
                </div>

                <button type="button" class="rounded-full border border-black/8 px-2.5 py-1 text-[11px] font-semibold text-rg-deepPurple transition-colors hover:bg-white/70 dark:border-white/10 dark:text-white dark:hover:bg-white/10" @click="rejectOptional">
                    {{ __('Reddet') }}
                </button>
            </div>
        </div>

        <div class="space-y-3 px-3.5 py-3">
            <p class="text-[11px] leading-relaxed text-rg-grayText dark:text-white/72 md:text-[12px]">
                {{ __('Sitenin temel işleyişi için zorunlu çerezleri kullanıyoruz. Analitik, işlevsel ve pazarlama tercihlerinizi ise tamamen siz belirleyebilirsiniz.') }}
            </p>

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" class="inline-flex items-center justify-center rounded-full bg-rg-deepPurple px-3.5 py-2 text-sm font-semibold text-white transition-colors duration-200 hover:bg-rg-purple" @click="acceptAll">
                    {{ __('Tümünü Kabul Et') }}
                </button>
                <button type="button" class="inline-flex items-center justify-center rounded-full border border-black/8 bg-white px-3.5 py-2 text-sm font-semibold text-rg-deepPurple transition-colors duration-200 hover:bg-rg-cream/70 dark:border-white/10 dark:bg-white/12 dark:text-white dark:hover:bg-white/10" @click="openSettings = !openSettings">
                    {{ __('Tercihleri Yönet') }}
                </button>
            </div>

            <p class="hidden text-[11px] leading-relaxed text-rg-grayText/85 dark:text-white/72 sm:block">
                {{ __('Detaylı bilgi için') }}
                <a href="{{ \App\Support\StorefrontLocale::route('page.show', ['slug' => 'cerez-politikasi']) }}" class="font-semibold underline decoration-rg-midPurple/50 underline-offset-4 transition-colors hover:text-rg-purple dark:hover:text-rg-lavender">{{ __('Çerez Politikası') }}</a>
                {{ __('sayfasını inceleyebilirsiniz.') }}
            </p>
        </div>

        <div x-show="openSettings" x-transition class="border-t border-black/6 bg-black/[0.02] px-3.5 py-3 dark:border-white/10 dark:bg-white/[0.03]" style="display: none;">
            <div class="space-y-3 text-sm">
                <label class="flex items-center justify-between gap-4 rounded-[1.05rem] border border-black/6 bg-white/72 px-3 py-2.5 dark:border-white/10 dark:bg-white/10">
                    <span class="font-medium">{{ __('Zorunlu Çerezler') }} <span class="text-xs text-rg-grayText dark:text-white/70">{{ __('Her zaman aktif') }}</span></span>
                    <input type="checkbox" checked disabled class="h-4 w-4">
                </label>
                <label class="flex items-center justify-between gap-4 rounded-[1.05rem] border border-black/6 bg-white/72 px-3 py-2.5 dark:border-white/10 dark:bg-white/10">
                    <span class="font-medium">{{ __('Analitik Çerezler') }}</span>
                    <input type="checkbox" x-model="consent.analytics" class="h-4 w-4">
                </label>
                <label class="flex items-center justify-between gap-4 rounded-[1.05rem] border border-black/6 bg-white/72 px-3 py-2.5 dark:border-white/10 dark:bg-white/10">
                    <span class="font-medium">{{ __('Fonksiyonel Çerezler') }}</span>
                    <input type="checkbox" x-model="consent.functional" class="h-4 w-4">
                </label>
                <label class="flex items-center justify-between gap-4 rounded-[1.05rem] border border-black/6 bg-white/72 px-3 py-2.5 dark:border-white/10 dark:bg-white/10">
                    <span class="font-medium">{{ __('Pazarlama Çerezleri') }}</span>
                    <input type="checkbox" x-model="consent.marketing" class="h-4 w-4">
                </label>
            </div>

            <button type="button" class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-rg-deepPurple px-4 py-2 text-sm font-semibold text-white transition-colors duration-200 hover:bg-rg-purple" @click="saveCustom">
                {{ __('Tercihleri Kaydet') }}
            </button>
        </div>
    </div>
</div>
