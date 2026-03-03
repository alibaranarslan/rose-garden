<header class="bg-white border-b border-rg-lightLavender sticky top-0 z-30">
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
            {{-- Mobile search icon --}}
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
        </div>
    </div>
</header>
