@php
    $localeLabels = \App\Support\StorefrontLocale::labels();
    $currentLocale = app()->getLocale();
    $currentLabel = $localeLabels[$currentLocale] ?? strtoupper($currentLocale);
@endphp

<div
    x-data="{ open: false }"
    class="rg-header-language-root relative z-[70] flex h-full items-center self-center"
    @keydown.escape.window="open = false"
>
    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open.toString()"
        class="inline-flex h-11 min-h-[2.75rem] items-center gap-2 rounded-full border border-rg-lightLavender/80 bg-white/96 px-3.5 py-0 text-xs font-semibold leading-none text-rg-deepPurple shadow-[0_10px_24px_rgba(34,24,40,0.08)] transition hover:border-rg-purple/35 hover:bg-rg-lightLavender/30 dark:border-white/14 dark:bg-[#21162c] dark:text-white/95 dark:shadow-[0_12px_28px_rgba(7,4,11,0.24)] dark:hover:bg-white/10"
        aria-label="{{ strtoupper($currentLocale) }} {{ __('Dil seçenekleri') }}"
    >
        <span class="rounded-full bg-rg-lightLavender/75 px-2 py-0.5 text-[11px] uppercase tracking-[0.18em] text-rg-deepPurple dark:bg-white/10 dark:text-white/82">{{ $currentLocale }}</span>
        <span class="hidden min-w-0 text-left text-sm sm:block">{{ $currentLabel }}</span>
        <svg class="h-3.5 w-3.5 transition" :class="{ 'rotate-180': open }" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.17l3.71-3.94a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
        </svg>
    </button>

    <div
        x-show="open"
        x-cloak
        x-transition.origin.top.right
        @click.outside="open = false"
        class="absolute right-0 top-full z-[80] mt-2 w-[min(18rem,calc(100vw-1.5rem))] overflow-hidden rounded-2xl border border-rg-lightLavender/80 bg-white p-2 shadow-[0_20px_44px_rgba(34,24,40,0.18)] dark:border-white/14 dark:bg-[#1b1226] dark:shadow-[0_26px_56px_rgba(6,4,10,0.62)]"
    >
        <div class="px-2 pb-2 pt-1">
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/88">{{ __('Dil seçenekleri') }}</p>
            <p class="mt-1 text-xs leading-relaxed text-rg-grayText dark:text-white/78">{{ __('Storefront kabuğunu ve içeriği seçtiğiniz dile geçirir.') }}</p>
        </div>

        @foreach ($localeLabels as $lang => $label)
            <a
                href="{{ \App\Support\StorefrontLocale::currentRequestUrl($lang, true) }}"
                class="flex items-center justify-between gap-3 rounded-xl px-3 py-3 text-sm transition hover:bg-rg-lightLavender/50 dark:hover:bg-white/10 {{ $currentLocale === $lang ? 'bg-rg-lightLavender/55 font-semibold text-rg-purple dark:bg-white/12 dark:text-rg-lavender' : 'text-rg-darkText dark:text-white/92' }}"
            >
                <span class="min-w-0">
                    <span class="block">{{ $label }}</span>
                    <span class="mt-0.5 block text-[11px] uppercase tracking-[0.18em] text-rg-grayText dark:text-white/62">{{ $lang }}</span>
                </span>

                @if ($currentLocale === $lang)
                    <span class="rounded-full bg-white px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-rg-purple shadow-sm dark:bg-white/12 dark:text-rg-lavender">{{ __('Aktif') }}</span>
                @endif
            </a>
        @endforeach
    </div>
</div>
