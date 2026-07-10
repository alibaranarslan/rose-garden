@php
    $guestLoyaltyPath = trim(\App\Support\StorefrontLocale::stripPrefix(request()->path()), '/');
    $guestLoyaltyBlockedPath = in_array($guestLoyaltyPath, ['giris', 'kayit', 'sifremi-unuttum', 'odeme'], true)
        || str_starts_with($guestLoyaltyPath, 'sifre-sifirla/')
        || str_starts_with($guestLoyaltyPath, 'odeme/');
    $guestLoyaltyCommercePath = in_array($guestLoyaltyPath, ['sepet', 'cart'], true)
        || str_starts_with($guestLoyaltyPath, 'urun/')
        || str_starts_with($guestLoyaltyPath, 'product/');
    $eligibleForPrompt = ! auth()->check()
        && ! request()->routeIs(['login', 'register', 'password.*', 'checkout', 'checkout.*', 'cart', 'products.show'])
        && ! $guestLoyaltyBlockedPath
        && ! $guestLoyaltyCommercePath;
    $registerUrl = \App\Support\StorefrontLocale::route('register');
    $featuredProduct = $eligibleForPrompt
        ? \App\Models\Product::query()
            ->storefrontReady()
            ->with(['images' => fn ($query) => $query->orderBy('sort_order')])
            ->orderByDesc('is_featured')
            ->orderByDesc('view_count')
            ->first()
        : null;
    $featuredImage = $featuredProduct
        ? \App\Support\StorefrontImage::resolveProduct(
            $featuredProduct->primaryImage,
            $featuredProduct->slug,
            $featuredProduct->name,
        )
        : \App\Support\StorefrontImage::productPlaceholderImgSrc();
@endphp

@if ($eligibleForPrompt)
    <div
        x-data="rgGuestLoyaltyPrompt()"
        x-init="init()"
        x-show="visible"
        x-cloak
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-[90] flex items-start justify-center overflow-y-auto px-4 py-6 sm:items-center"
    >
        <div class="absolute inset-0 bg-rg-deepPurple/30 backdrop-blur-[4px] dark:bg-black/55" @click="dismiss()"></div>

        <div class="relative max-h-[calc(100vh-3rem)] w-full max-w-[42rem] overflow-y-auto overflow-x-hidden rounded-[1.85rem] border border-white/80 bg-white shadow-[0_34px_90px_rgba(32,20,36,0.36)] ring-1 ring-rg-lightLavender/60 dark:border-white/12 dark:bg-[#1b1225] dark:shadow-[0_34px_90px_rgba(4,2,8,0.68)] dark:ring-white/10">
            <div class="grid gap-0 sm:grid-cols-[14rem_minmax(0,1fr)]">
                <div class="relative h-52 bg-rg-cream/60 dark:bg-white/6 sm:h-auto sm:min-h-[13rem]">
                    <img
                        src="{{ $featuredImage }}"
                        alt=""
                        class="h-full w-full object-cover"
                        loading="lazy"
                    >
                    <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(255,255,255,0.06),rgba(44,23,47,0.22))]"></div>
                    <div class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-white/88 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-rg-purple shadow-sm dark:bg-[#1d1228]/90 dark:text-rg-lavender">
                        <span class="h-1.5 w-1.5 rounded-full bg-rg-rosePink"></span>
                        {{ __('Paraçiçek') }}
                    </div>
                </div>

                <div class="relative bg-white p-5 text-rg-darkText dark:bg-[#1b1225] dark:text-white sm:p-6">
                    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(226,207,236,0.36),transparent_15rem)] dark:bg-[radial-gradient(circle_at_top_right,rgba(126,91,150,0.22),transparent_14rem)]"></div>
                    <div class="relative">
                    <div class="flex items-start gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-rg-lavender/24 text-rg-purple shadow-sm dark:bg-white/10 dark:text-rg-lavender">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m4-4H8m12 0a8 8 0 11-16 0 8 8 0 0116 0z"/>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/90">{{ __('Üyelik ve puan') }}</p>
                            <h2 class="mt-1 font-display text-xl font-semibold leading-tight text-rg-deepPurple dark:text-white">{{ __('Üye ol, puan biriktir') }}</h2>
                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/82">
                                {{ __('Siparişlerinden Paraçiçek Puan kazan. Hesabında biriktir, sonra kullan.') }}
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="dismiss()"
                            class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-black/5 bg-rg-cream/70 text-rg-grayText transition hover:bg-rg-lightLavender hover:text-rg-darkPlum dark:border-white/10 dark:bg-white/8 dark:text-white/72 dark:hover:bg-white/12 dark:hover:text-white"
                            aria-label="{{ __('Kapat') }}"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    @if ($featuredProduct)
                        <p class="mt-4 rounded-2xl bg-rg-cream/70 px-3.5 py-3 text-xs font-medium leading-relaxed text-rg-grayText dark:bg-white/10 dark:text-white/70">
                            {{ __('Öne çıkan ürünlerden ilham alan küçük bir üyelik hatırlatıcısı.') }}
                        </p>
                    @endif

                    <div class="mt-5 flex flex-col gap-2 sm:flex-row">
                        <a
                            href="{{ $registerUrl }}"
                            @click="dismiss()"
                            class="inline-flex flex-1 items-center justify-center rounded-xl bg-rg-purple px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rg-darkPlum hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple focus-visible:ring-offset-2 dark:focus-visible:ring-offset-rg-deepPurple"
                        >
                            {{ __('Üye ol') }}
                        </a>
                        <button
                            type="button"
                            @click="dismiss()"
                            class="inline-flex flex-1 items-center justify-center rounded-xl border border-rg-lightLavender/80 bg-white px-4 py-2.5 text-sm font-semibold text-rg-darkPlum transition hover:bg-rg-cream dark:border-white/12 dark:bg-white/8 dark:text-white dark:hover:bg-white/12"
                        >
                            {{ __('Daha sonra') }}
                        </button>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@once
    @push('scripts')
        <script>
            (function () {
                var storageKey = 'rg-guest-loyalty-prompt-dismissed-at';
                var cookieStorageKey = 'rg_site_cookie_consent';
                var cooldownMs = 1000 * 60 * 60 * 24 * 7;
                var promptDelayMs = 12000;

                window.rgGuestLoyaltyPrompt = function () {
                    return {
                        visible: false,

                        init: function () {
                            if (this.isDismissed()) {
                                return;
                            }

                            if (! this.hasCookieDecision()) {
                                window.addEventListener('cookie-consent-updated', () => {
                                    if (! this.isDismissed()) {
                                        this.schedule();
                                    }
                                }, { once: true });

                                return;
                            }

                            this.schedule();
                        },

                        schedule: function () {
                            window.setTimeout(() => {
                                if (! this.isDismissed()) {
                                    this.visible = true;
                                }
                            }, promptDelayMs);
                        },

                        isDismissed: function () {
                            try {
                                var dismissedAt = Number(localStorage.getItem(storageKey) || 0);

                                return dismissedAt && (Date.now() - dismissedAt) < cooldownMs;
                            } catch (error) {
                                return true;
                            }
                        },

                        hasCookieDecision: function () {
                            try {
                                return Boolean(localStorage.getItem(cookieStorageKey));
                            } catch (error) {
                                return true;
                            }
                        },

                        dismiss: function () {
                            try {
                                localStorage.setItem(storageKey, String(Date.now()));
                            } catch (error) {}

                            this.visible = false;
                        },
                    };
                };
            })();
        </script>
    @endpush
@endonce
