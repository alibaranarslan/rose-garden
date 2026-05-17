@php
    $linkBase = 'flex items-center gap-3 rounded-r-xl border-l-4 px-3.5 py-2.5 text-sm font-medium transition-colors';
    $linkIdle = 'border-transparent text-rg-grayText hover:border-rg-lightLavender hover:bg-rg-cream/80 dark:text-white/86 dark:hover:border-white/10 dark:hover:bg-white/5';
    $linkActive = 'border-rg-purple bg-rg-lightLavender/70 text-rg-purple dark:border-rg-lavender dark:bg-white/10 dark:text-rg-lavender';

    $isActive = static function (array $patterns): bool {
        foreach ($patterns as $p) {
            if (request()->routeIs($p)) {
                return true;
            }
        }

        return false;
    };
@endphp

<aside class="w-full shrink-0 lg:sticky lg:top-32 lg:w-52 xl:w-60">
    <div class="rounded-[1.6rem] border border-rg-lightLavender bg-white p-2.5 shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/40 dark:shadow-black/20">
        <p class="mb-2 px-3 pt-1 text-xs font-bold uppercase tracking-wider text-rg-midPurple dark:text-rg-lavender">{{ __('Hesabım') }}</p>
        <nav class="flex flex-col gap-0.5" aria-label="{{ __('Hesap menüsü') }}">
            <a href="{{ \App\Support\StorefrontLocale::route('account.dashboard') }}"
               class="{{ $linkBase }} {{ $isActive(['account.dashboard']) ? $linkActive : $linkIdle }}">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                {{ __('Özet') }}
            </a>
            <a href="{{ \App\Support\StorefrontLocale::route('account.profile') }}"
               class="{{ $linkBase }} {{ $isActive(['account.profile']) ? $linkActive : $linkIdle }}">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ __('Profilim') }}
            </a>
            <a href="{{ \App\Support\StorefrontLocale::route('account.orders') }}"
               class="{{ $linkBase }} {{ $isActive(['account.orders', 'account.order.show']) ? $linkActive : $linkIdle }}">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                {{ __('Siparişlerim') }}
            </a>
            <a href="{{ \App\Support\StorefrontLocale::route('account.addresses') }}"
               class="{{ $linkBase }} {{ $isActive(['account.addresses']) ? $linkActive : $linkIdle }}">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ __('Adreslerim') }}
            </a>
            <a href="{{ \App\Support\StorefrontLocale::route('account.favorites') }}"
               class="{{ $linkBase }} {{ $isActive(['account.favorites']) ? $linkActive : $linkIdle }}">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                {{ __('Favorilerim') }}
            </a>
            <a href="{{ \App\Support\StorefrontLocale::route('account.loyalty') }}"
               class="{{ $linkBase }} {{ $isActive(['account.loyalty']) ? $linkActive : $linkIdle }}">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ __('Puanlarım') }}
            </a>
            <a href="{{ \App\Support\StorefrontLocale::route('account.kvkk') }}"
               class="{{ $linkBase }} {{ $isActive(['account.kvkk']) ? $linkActive : $linkIdle }}">
                <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                {{ __('KVKK ve gizlilik') }}
            </a>
        </nav>

        <div class="mt-2 border-t border-rg-lightLavender/70 px-2 pt-3 dark:border-white/10">
            <p class="px-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-rg-grayText dark:text-white/55">{{ __('Hızlı işlemler') }}</p>
            <div class="mt-2 grid gap-2">
                <a href="{{ \App\Support\StorefrontLocale::route('password.request') }}" class="inline-flex items-center justify-between rounded-2xl border border-rg-lightLavender/80 bg-white px-3 py-2.5 text-sm font-medium text-rg-deepPurple transition hover:border-rg-purple/30 hover:bg-rg-lightLavender/30 dark:border-white/10 dark:bg-white/8 dark:text-white dark:hover:bg-white/12">
                    <span>{{ __('Şifre sıfırla') }}</span>
                    <svg class="h-4 w-4 opacity-65" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <form method="POST" action="{{ \App\Support\StorefrontLocale::route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex w-full items-center justify-between rounded-2xl border border-rg-lightLavender/80 bg-white px-3 py-2.5 text-sm font-medium text-rg-grayText transition hover:border-rg-purple/30 hover:bg-rg-lightLavender/30 hover:text-rg-purple dark:border-white/10 dark:bg-white/8 dark:text-white/86 dark:hover:bg-white/12 dark:hover:text-rg-lavender">
                        <span>{{ __('Çıkış yap') }}</span>
                        <svg class="h-4 w-4 opacity-65" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
