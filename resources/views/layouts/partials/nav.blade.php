<nav class="bg-rg-warmWhite border-b border-rg-lightLavender" x-data="{ mobileNavOpen: false }">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between py-2 md:hidden">
            <span class="text-sm font-medium text-rg-grayText">{{ __('Kategoriler') }}</span>
            <button @click="mobileNavOpen = !mobileNavOpen"
                    class="flex items-center gap-1 text-sm font-medium text-rg-darkPlum"
                    aria-label="{{ __('Menüyü Aç/Kapat') }}">
                <span x-text="mobileNavOpen ? '{{ __('Kapat') }}' : '{{ __('Tümü') }}'"></span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="mobileNavOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
        <ul class="hidden md:flex items-center gap-0 min-w-max text-sm font-medium overflow-x-auto">
            <li>
                <a href="{{ route('products.index') }}"
                   class="block px-3 py-3 border-b-2 transition-colors duration-200
                          {{ request()->routeIs('products.index') && !request()->route('slug')
                             ? 'border-rg-purple text-rg-purple'
                             : 'border-transparent text-rg-darkText hover:text-rg-purple hover:border-rg-midPurple' }}">
                    {{ __('Tüm Ürünler') }}
                </a>
            </li>
            @foreach ($navCategories ?? collect() as $category)
            <li>
                <a href="{{ route('products.category', ['slug' => $category->slug]) }}"
                   class="block px-3 py-3 border-b-2 transition-colors duration-200
                          {{ request()->route('slug') === $category->slug
                             ? 'border-rg-purple text-rg-purple'
                             : 'border-transparent text-rg-darkText hover:text-rg-purple hover:border-rg-midPurple' }}">
                    {{ $category->name }}
                </a>
            </li>
            @endforeach
        </ul>
        {{-- Mobile dropdown --}}
        <div x-show="mobileNavOpen"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden pb-2">
            <ul class="grid grid-cols-2 gap-1">
                <li>
                    <a href="{{ route('products.index') }}"
                       class="block px-3 py-2 rounded-btn text-sm transition-colors duration-200
                              {{ request()->routeIs('products.index') && !request()->route('slug')
                                 ? 'bg-rg-lightLavender text-rg-purple font-semibold'
                                 : 'text-rg-darkText hover:bg-rg-lightLavender/50 hover:text-rg-purple' }}">
                        {{ __('Tüm Ürünler') }}
                    </a>
                </li>
                @foreach ($navCategories ?? collect() as $category)
                <li>
                    <a href="{{ route('products.category', ['slug' => $category->slug]) }}"
                       class="block px-3 py-2 rounded-btn text-sm transition-colors duration-200
                              {{ request()->route('slug') === $category->slug
                                 ? 'bg-rg-lightLavender text-rg-purple font-semibold'
                                 : 'text-rg-darkText hover:bg-rg-lightLavender/50 hover:text-rg-purple' }}">
                        {{ $category->name }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</nav>
