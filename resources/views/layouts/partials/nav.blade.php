@php
    $navCats = $navCategories ?? collect();
    $navOccasions = $navSpecialOccasions ?? collect();
    $currentSlug = request()->route('slug');
    $featuredNavOccasion = $navOccasions->first();
    $isTurkish = app()->getLocale() === 'tr';
    $featuredNavOccasionTitle = $featuredNavOccasion?->getTranslation('name', app()->getLocale());
    $featuredNavOccasionVisual = $featuredNavOccasion
        ? \App\Support\StorefrontImage::publicImgSrc(
            \App\Support\StorefrontImage::resolveSpecialOccasion(
                $featuredNavOccasion->slug,
                $featuredNavOccasionTitle,
                $featuredNavOccasion->category?->getTranslation('name', app()->getLocale()),
                $featuredNavOccasion->category?->slug,
            )
        )
        : null;

    $occasionDateLabel = fn ($occasion) => $occasion->nextOccurrence()
        ->locale(app()->getLocale())
        ->translatedFormat('d F');

    $occasionStatusLabel = function ($occasion) {
        if ($occasion->isToday()) {
            return __('Bugün');
        }

        return __(':count gün kaldı', ['count' => $occasion->daysUntil()]);
    };
@endphp

<nav class="relative z-20 border-t border-black/6 bg-rg-warmWhite/86 backdrop-blur-lg dark:border-white/10 dark:bg-[#17101f]/90" x-data="{ mobileNavOpen: false }">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="rg-mobile-nav-scroll -mx-4 flex gap-2 overflow-x-auto px-4 py-2.5 md:hidden" aria-label="{{ __('Mobil hızlı gezinme') }}">
            <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}"
                class="rg-mobile-nav-pill {{ request()->routeIs('products.index') && ! $currentSlug ? 'rg-mobile-nav-pill-active' : '' }}">
                {{ $isTurkish ? 'Tüm Koleksiyon' : __('Tüm Koleksiyon') }}
            </a>
            <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}"
                class="rg-mobile-nav-pill {{ request()->routeIs('products.category') ? 'rg-mobile-nav-pill-active' : '' }}">
                {{ __('Kategoriler') }}
            </a>
            <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.index') }}"
                class="rg-mobile-nav-pill {{ request()->routeIs('special-occasions.*') ? 'rg-mobile-nav-pill-active' : '' }}">
                {{ __('Özel Günler') }}
            </a>
            <a href="{{ \App\Support\StorefrontLocale::route('blog.index') }}"
                class="rg-mobile-nav-pill {{ request()->routeIs('blog.*') ? 'rg-mobile-nav-pill-active' : '' }}">
                Blog
            </a>
        </div>

        <div class="hidden">
            <span class="text-sm font-medium text-rg-grayText dark:text-white/80">{{ __('Keşfet') }}</span>
            <button type="button"
                @click="mobileNavOpen = !mobileNavOpen"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-black/8 bg-white/84 px-3.5 py-2 text-sm font-medium text-rg-deepPurple shadow-sm dark:border-white/10 dark:bg-white/10 dark:text-rg-lavender"
                :aria-label="mobileNavOpen ? '{{ __('Kapat') }}' : '{{ __('Menü') }}'"
                :aria-expanded="mobileNavOpen.toString()"
                aria-controls="mobile-nav-panel">
                <span x-text="mobileNavOpen ? '{{ __('Kapat') }}' : '{{ __('Menü') }}'"></span>
                <svg class="h-4 w-4 transition-transform duration-200" :class="mobileNavOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <div class="relative hidden items-center gap-2.5 py-3 md:flex">
            <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}"
                class="rg-pill rg-header-nav-pill relative z-10 whitespace-nowrap {{ request()->routeIs('products.index') && ! $currentSlug ? 'border-rg-purple/30 bg-rg-lightLavender text-rg-purple dark:border-rg-lavender/40 dark:bg-white/12 dark:text-rg-lavender' : 'hover:border-rg-purple/20 hover:text-rg-purple dark:bg-white/10 dark:text-zinc-100 dark:hover:text-rg-lavender' }}">
                {{ $isTurkish ? 'Tüm Koleksiyon' : __('Tüm Koleksiyon') }}
            </a>

            @if ($navCats->isNotEmpty())
                <div class="group relative">
                    <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}"
                        class="rg-pill rg-header-nav-pill relative z-10 whitespace-nowrap {{ request()->routeIs('products.category') ? 'border-rg-purple/30 bg-rg-lightLavender text-rg-purple dark:border-rg-lavender/40 dark:bg-white/12 dark:text-rg-lavender' : 'hover:border-rg-purple/20 hover:text-rg-purple dark:bg-white/10 dark:text-zinc-100 dark:hover:text-rg-lavender' }}">
                        {{ __('Kategoriler') }}
                        <svg class="h-4 w-4 shrink-0 transition-transform duration-200 group-hover:rotate-180 group-focus-within:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>

                    <div class="rg-nav-flyout pointer-events-none absolute left-0 top-full z-50 w-[min(100vw-2rem,40rem)] opacity-0 transition-all duration-150 group-hover:pointer-events-auto group-hover:opacity-100 group-focus-within:pointer-events-auto group-focus-within:opacity-100">
                        <div class="grid gap-4 rounded-[1.7rem] border border-[#eadde7] bg-[#fffdfa] p-4 shadow-[0_28px_70px_rgba(43,28,44,0.16)] ring-1 ring-black/4 dark:border-white/12 dark:bg-[#1f1824] dark:ring-white/6 lg:grid-cols-[minmax(0,0.72fr)_minmax(0,1.28fr)]">
                            <div class="rounded-[1.35rem] border border-[#eadbe4] bg-[#f8f1f5] p-4 dark:border-white/10 dark:bg-[#2a1f31]">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ $isTurkish ? 'Rose Garden Atölye' : __('Rose Garden Atölye') }}</p>
                                <h3 class="mt-2 font-display text-xl text-rg-deepPurple dark:text-white">{{ $isTurkish ? 'Teslime hazır canlı katalog' : __('Teslime hazır canlı katalog') }}</h3>
                                <p class="mt-3 text-sm leading-relaxed text-rg-grayText dark:text-white/86">
                                    {{ $isTurkish ? 'Buket, saksı bitkisi ve özel seçkiler aynı akışta daha sakin bir keşif deneyimi sunar.' : __('Buket, saksı bitkisi ve özel seçkiler aynı akışta daha sakin bir keşif deneyimi sunar.') }}
                                </p>
                                <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}"
                                   class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-rg-deepPurple transition-colors duration-200 hover:text-rg-purple dark:text-white dark:hover:text-rg-lavender">
                                    {{ $isTurkish ? 'Tüm koleksiyonu aç' : __('Tüm koleksiyonu aç') }}
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>

                            <div class="space-y-3">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/76">{{ $isTurkish ? 'Kategori Seçkisi' : __('Kategori Seçkisi') }}</p>
                                <ul class="grid gap-2 sm:grid-cols-2">
                                    @foreach ($navCats as $category)
                                        <li>
                                            <a href="{{ \App\Support\StorefrontLocale::route('products.category', ['slug' => $category->slug]) }}"
                                                class="flex items-center justify-between rounded-[1.1rem] border border-[#ece3ea] bg-white px-4 py-3 text-sm font-medium text-rg-deepPurple shadow-sm transition-all duration-200 hover:border-rg-purple/20 hover:bg-[#fcf8fb] dark:border-white/10 dark:bg-[#241c2a] dark:text-white dark:hover:bg-[#2a2131] {{ $currentSlug === $category->slug ? 'border-rg-purple/25 bg-rg-lightLavender text-rg-purple dark:border-rg-lavender/40 dark:bg-[#32263d] dark:text-rg-lavender' : '' }}">
                                                <span>
                                                    @if ($category->parent)
                                                        <span class="block text-[10px] uppercase tracking-[0.2em] text-rg-grayText dark:text-white/46">{{ $category->parent->name }}</span>
                                                    @endif
                                                    <span>{{ $category->name }}</span>
                                                </span>
                                                <svg class="h-4 w-4 shrink-0 opacity-65" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($navOccasions->isNotEmpty())
                <div class="group relative">
                    <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.index') }}"
                        class="rg-pill rg-header-nav-pill relative z-10 whitespace-nowrap {{ request()->routeIs('special-occasions.*') ? 'border-rg-purple/30 bg-rg-lightLavender text-rg-purple dark:border-rg-lavender/40 dark:bg-white/12 dark:text-rg-lavender' : 'hover:border-rg-purple/20 hover:text-rg-purple dark:bg-white/10 dark:text-zinc-100 dark:hover:text-rg-lavender' }}">
                        {{ __('Özel Günler') }}
                        <svg class="h-4 w-4 shrink-0 transition-transform duration-200 group-hover:rotate-180 group-focus-within:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>

                    <div class="rg-nav-flyout pointer-events-none absolute left-0 top-full z-50 w-[min(100vw-2rem,42rem)] opacity-0 transition-all duration-150 group-hover:pointer-events-auto group-hover:opacity-100 group-focus-within:pointer-events-auto group-focus-within:opacity-100">
                        <div class="grid gap-4 rounded-[1.75rem] border border-[#eadde7] bg-[#fffdfa] p-4 shadow-[0_30px_80px_rgba(43,28,44,0.18)] ring-1 ring-black/4 dark:border-white/12 dark:bg-[#1f1824] dark:ring-white/6 lg:grid-cols-[minmax(0,0.78fr)_minmax(0,1.22fr)]">
                            <div class="rounded-[1.4rem] border border-[#eadbe4] bg-[#f8f1f5] p-4 dark:border-white/10 dark:bg-[#271d2f]">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/82">{{ __('Yaklaşan Özel Gün') }}</p>

                                @if ($featuredNavOccasion)
                                    <div class="mt-3 space-y-3">
                                        <div class="overflow-hidden rounded-[1.1rem] border border-black/6 bg-white/50 dark:border-white/10 dark:bg-white/10">
                                            <div class="aspect-[1.18/1]">
                                                <img src="{{ $featuredNavOccasionVisual }}" alt="{{ $featuredNavOccasionTitle }}" loading="lazy" decoding="async" class="h-full w-full object-cover object-center">
                                            </div>
                                        </div>

                                        <div class="flex flex-wrap gap-2">
                                            <span class="rg-pill">{{ $occasionDateLabel($featuredNavOccasion) }}</span>
                                            <span class="rg-pill">{{ $occasionStatusLabel($featuredNavOccasion) }}</span>
                                        </div>

                                        <div>
                                            <h3 class="font-display text-[1.7rem] leading-tight text-rg-deepPurple dark:text-white">
                                                {{ $featuredNavOccasionTitle }}
                                            </h3>
                                            <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/86">
                                                {{ $isTurkish ? 'Bu tarihe atanmış seçkiler, çiçek ve hediye birlikteliğiyle ayrı bir vitrinde sunulur.' : __('Bu tarihe atanmış seçkiler, çiçek ve hediye birlikteliğiyle ayrı bir vitrinde sunulur.') }}
                                            </p>
                                        </div>

                                        <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $featuredNavOccasion->slug]) }}"
                                           class="inline-flex items-center gap-2 text-sm font-semibold text-rg-deepPurple transition-colors duration-200 hover:text-rg-purple dark:text-white dark:hover:text-rg-lavender">
                                            {{ __('Koleksiyona git') }}
                                            <span aria-hidden="true">&rarr;</span>
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/76">{{ $isTurkish ? 'Takvim Seçkisi' : __('Takvim Seçkisi') }}</p>
                                    <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.index') }}" class="text-xs font-semibold uppercase tracking-[0.18em] text-rg-grayText transition-colors hover:text-rg-purple dark:text-white/72 dark:hover:text-rg-lavender">
                                        {{ __('Tümünü Gör') }}
                                    </a>
                                </div>

                                <ul class="grid gap-2">
                                    @foreach ($navOccasions as $occasion)
                                        @php
                                            $occasionTitle = $occasion->getTranslation('name', app()->getLocale());
                                            $occasionVisual = \App\Support\StorefrontImage::publicImgSrc(
                                                \App\Support\StorefrontImage::resolveSpecialOccasion(
                                                    $occasion->slug,
                                                    $occasionTitle,
                                                    $occasion->category?->getTranslation('name', app()->getLocale()),
                                                    $occasion->category?->slug,
                                                )
                                            );
                                        @endphp
                                        <li>
                                            <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $occasion->slug]) }}"
                                               class="rg-nav-occasion-link {{ request()->routeIs('special-occasions.show') && $currentSlug === $occasion->slug ? 'border-rg-purple/25 bg-rg-lightLavender dark:border-rg-lavender/35 dark:bg-[#302539]' : '' }}">
                                                <div class="flex items-start gap-3">
                                                    <div class="h-12 w-12 overflow-hidden rounded-[0.95rem] border border-black/6 bg-white/55 dark:border-white/10 dark:bg-white/12">
                                                        <img src="{{ $occasionVisual }}" alt="{{ $occasionTitle }}" loading="lazy" decoding="async" class="h-full w-full object-cover object-center">
                                                    </div>
                                                    <div class="space-y-1">
                                                        <p class="text-[10px] uppercase tracking-[0.22em] text-rg-copy-soft dark:text-white/46">{{ $occasionDateLabel($occasion) }}</p>
                                                        <h3 class="text-sm font-semibold text-rg-copy-strong dark:text-white">{{ $occasionTitle }}</h3>
                                                        <p class="text-xs text-rg-grayText dark:text-white/80">{{ $occasionStatusLabel($occasion) }}</p>
                                                    </div>
                                                </div>
                                                <svg class="h-4 w-4 shrink-0 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.index') }}"
                    class="rg-pill rg-header-nav-pill relative z-10 whitespace-nowrap {{ request()->routeIs('special-occasions.*') ? 'border-rg-purple/30 bg-rg-lightLavender text-rg-purple dark:border-rg-lavender/40 dark:bg-white/12 dark:text-rg-lavender' : 'hover:border-rg-purple/20 hover:text-rg-purple dark:bg-white/10 dark:text-zinc-100 dark:hover:text-rg-lavender' }}">
                    {{ __('Özel Günler') }}
                </a>
            @endif

            <a href="{{ \App\Support\StorefrontLocale::route('blog.index') }}"
                class="rg-pill rg-header-nav-pill relative z-10 whitespace-nowrap {{ request()->routeIs('blog.*') ? 'border-rg-purple/30 bg-rg-lightLavender text-rg-purple dark:border-rg-lavender/40 dark:bg-white/12 dark:text-rg-lavender' : 'hover:border-rg-purple/20 hover:text-rg-purple dark:bg-white/10 dark:text-zinc-100 dark:hover:text-rg-lavender' }}">
                Blog
            </a>

            <span class="ml-auto hidden text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/70 2xl:inline">
                {{ $isTurkish ? 'Alışverişe hazır vitrin' : __('Alışverişe hazır vitrin') }}
            </span>
        </div>

        <div id="mobile-nav-panel"
            x-show="mobileNavOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            x-cloak
            class="pb-4 md:hidden">
            <div class="grid gap-3 rounded-[1.75rem] border border-black/5 bg-white/82 p-3 shadow-sm dark:border-white/10 dark:bg-white/10">
                <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}"
                    class="flex items-center justify-between rounded-2xl px-3 py-3 text-sm font-semibold {{ request()->routeIs('products.index') && ! $currentSlug ? 'bg-rg-lightLavender text-rg-purple dark:bg-white/10 dark:text-rg-lavender' : 'text-rg-deepPurple hover:bg-rg-lightLavender/45 dark:text-white/86 dark:hover:bg-white/14' }}">
                    {{ $isTurkish ? 'Tüm Koleksiyon' : __('Tüm Koleksiyon') }}
                    <svg class="h-4 w-4 opacity-65" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                @if ($navCats->isNotEmpty())
                    <div class="rounded-[1.35rem] border border-black/5 bg-rg-cream/70 px-3 py-3 dark:border-white/10 dark:bg-white/8">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-rg-copy-soft dark:text-white/48">{{ __('Kategoriler') }}</p>
                        <div class="mt-3 grid gap-2">
                            @foreach ($navCats as $category)
                                <a href="{{ \App\Support\StorefrontLocale::route('products.category', ['slug' => $category->slug]) }}"
                                    class="flex items-center justify-between rounded-2xl px-3 py-3 text-sm font-medium {{ $currentSlug === $category->slug ? 'bg-rg-lightLavender text-rg-purple dark:bg-white/10 dark:text-rg-lavender' : 'text-rg-darkText hover:bg-rg-lightLavender/45 dark:text-white/84 dark:hover:bg-white/14' }}">
                                    <span>{{ $category->name }}</span>
                                    <svg class="h-4 w-4 opacity-65" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.index') }}"
                    class="flex items-center justify-between rounded-2xl px-3 py-3 text-sm font-medium {{ request()->routeIs('special-occasions.*') ? 'bg-rg-lightLavender text-rg-purple dark:bg-white/10 dark:text-rg-lavender' : 'text-rg-darkText hover:bg-rg-lightLavender/45 dark:text-white/84 dark:hover:bg-white/14' }}">
                    {{ __('Özel Günler') }}
                    <svg class="h-4 w-4 opacity-65" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                @if ($navOccasions->isNotEmpty())
                    <div class="grid gap-2 px-1 pb-1">
                        @foreach ($navOccasions->take(4) as $occasion)
                            <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.show', ['slug' => $occasion->slug]) }}"
                               class="rounded-[1.25rem] border border-black/5 bg-white/84 px-3 py-3 text-sm dark:border-white/10 dark:bg-white/12">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="space-y-1">
                                        <p class="text-[10px] uppercase tracking-[0.2em] text-rg-copy-soft dark:text-white/48">{{ $occasionDateLabel($occasion) }}</p>
                                        <h3 class="font-semibold text-rg-copy-strong dark:text-white">{{ $occasion->getTranslation('name', app()->getLocale()) }}</h3>
                                        <p class="text-xs text-rg-grayText dark:text-white/80">{{ $occasionStatusLabel($occasion) }}</p>
                                    </div>
                                    <span class="mt-1 h-2.5 w-2.5 rounded-full bg-rg-purple/70 dark:bg-rg-lavender/80"></span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                <a href="{{ \App\Support\StorefrontLocale::route('blog.index') }}"
                    class="flex items-center justify-between rounded-2xl px-3 py-3 text-sm font-medium {{ request()->routeIs('blog.*') ? 'bg-rg-lightLavender text-rg-purple dark:bg-white/10 dark:text-rg-lavender' : 'text-rg-darkText hover:bg-rg-lightLavender/45 dark:text-white/84 dark:hover:bg-white/14' }}">
                    Blog
                    <svg class="h-4 w-4 opacity-65" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</nav>
