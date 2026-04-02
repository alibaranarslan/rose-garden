<header class="bg-white border-b border-rg-lightLavender sticky top-0 z-30" x-data="{ mobileMenu: false }">
    <div class="max-w-7xl mx-auto px-4 py-4 grid grid-cols-2 md:grid-cols-3 gap-3 items-center">
        <a href="{{ route('home') }}" class="block">
            <img src="{{ asset('images/branding/rg-logo-light.svg') }}"
                 alt="Rose Garden Çiçek Çikolata"
                 class="h-12 w-auto"
                 loading="eager">
        </a>
        <form action="{{ route('search') }}" method="GET" class="hidden md:flex items-center border border-rg-lightLavender focus-within:border-rg-purple rounded-btn overflow-hidden transition-colors">
            <input name="q" value="{{ request('q') }}"
                   placeholder="{{ __('Ürün ara...') }}"
                   class="flex-1 px-3 py-2 text-sm outline-none bg-transparent"
                   aria-label="{{ __('Ürün Ara') }}">
            <button type="submit" class="px-3 py-2 text-rg-grayText hover:text-rg-purple transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </form>
        <div class="justify-self-end flex items-center gap-2 md:gap-3 text-sm">
            <x-language-switcher />
            <a href="{{ route('search') }}" class="md:hidden text-rg-grayText hover:text-rg-purple" aria-label="{{ __('Arama') }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </a>
            <a href="{{ route('account.dashboard') }}" class="hidden sm:inline hover:text-rg-purple transition-colors">{{ __('Hesabım') }}</a>
            <a href="{{ route('account.favorites') }}" class="hidden sm:flex items-center gap-1 hover:text-rg-purple transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </a>
            <livewire:cart-icon />
            {{-- Mobile hamburger --}}
            <button type="button"
                    @click="mobileMenu = !mobileMenu"
                    class="sm:hidden text-rg-grayText hover:text-rg-purple transition-colors"
                    aria-label="{{ __('Menü') }}">
                <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu panel --}}
    <div x-show="mobileMenu"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         x-cloak
         class="sm:hidden border-t border-rg-lightLavender bg-white">
        <nav class="max-w-7xl mx-auto px-4 py-4 space-y-3">
            <form action="{{ route('search') }}" method="GET" class="flex items-center border border-rg-lightLavender rounded-btn overflow-hidden">
                <input name="q" value="{{ request('q') }}"
                       placeholder="{{ __('Ürün ara...') }}"
                       class="flex-1 px-3 py-2 text-sm outline-none bg-transparent">
                <button type="submit" class="px-3 py-2 text-rg-grayText">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </form>
            <a href="{{ route('account.dashboard') }}" class="flex items-center gap-2 py-2 text-sm hover:text-rg-purple transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ __('Hesabım') }}
            </a>
            <a href="{{ route('account.favorites') }}" class="flex items-center gap-2 py-2 text-sm hover:text-rg-purple transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                {{ __('Favorilerim') }}
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-2 py-2 text-sm hover:text-rg-purple transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                {{ __('Tüm Ürünler') }}
            </a>
            <a href="{{ route('order.track') }}" class="flex items-center gap-2 py-2 text-sm hover:text-rg-purple transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ __('Sipariş Takip') }}
            </a>
        </nav>
    </div>
</header>
