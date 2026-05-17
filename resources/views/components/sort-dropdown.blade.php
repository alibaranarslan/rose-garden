@props([
    'sort' => 'recommended',
    'fullWidth' => false,
    'sortMode' => null,
])

@php
    $preserve = request()->except(['sort', 'page']);
    $labelsFull = [
        'recommended' => __('Önerilen'),
        'newest' => __('En Yeniler'),
        'best_sellers' => __('En Çok Satan'),
        'price_asc' => __('Fiyat: Düşükten Yükseğe'),
        'price_desc' => __('Fiyat: Yüksekten Düşüğe'),
    ];
    $labelsCatalog = [
        'recommended' => __('Önerilen'),
        'price_asc' => __('Artan Fiyat'),
        'price_desc' => __('Azalan Fiyat'),
        'newest' => __('En Yeniler'),
    ];
    $labels = $sortMode === 'catalog' ? $labelsCatalog : $labelsFull;
    $catalogKeys = array_keys($labelsCatalog);
    $highlightSort = ($sortMode === 'catalog' && ! in_array($sort, $catalogKeys, true)) ? 'recommended' : $sort;
    $currentLabel = $labels[$highlightSort] ?? ($sortMode === 'catalog' ? $labelsCatalog['recommended'] : $labelsFull['recommended']);
@endphp

<div class="{{ $fullWidth ? 'relative w-full' : 'relative w-full min-w-0 sm:w-auto sm:shrink-0' }}" x-data="{ open: false }" @keydown.escape.window="open = false">
    <button type="button"
            @click="open = !open"
            class="inline-flex {{ $fullWidth ? 'w-full' : 'w-full min-w-0 sm:min-w-[200px] sm:w-auto' }} items-center justify-between gap-2 rounded-xl border border-rg-lightLavender bg-white px-4 py-2.5 text-left text-sm font-medium text-rg-darkText shadow-sm transition-colors hover:border-rg-purple/40 focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple/40 dark:border-white/15 dark:bg-rg-deepPurple/50 dark:text-white"
            aria-haspopup="listbox"
            :aria-expanded="open">
        <span class="truncate">{{ $currentLabel }}</span>
        <svg class="h-4 w-4 shrink-0 text-rg-purple/70 dark:text-rg-lavender" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <form method="GET" class="{{ $fullWidth ? 'absolute inset-x-0' : 'absolute left-0 right-0 w-full sm:left-auto sm:right-0 sm:w-[min(100vw-2rem,260px)]' }} z-20 mt-1.5 rounded-xl border border-rg-lightLavender bg-white py-1 shadow-lg dark:border-white/10 dark:bg-rg-deepPurple"
          x-show="open"
          x-cloak
          x-transition:enter="transition ease-out duration-150"
          x-transition:enter-start="opacity-0 translate-y-1"
          x-transition:enter-end="opacity-100 translate-y-0"
          x-transition:leave="transition ease-in duration-100"
          x-transition:leave-start="opacity-100 translate-y-0"
          x-transition:leave-end="opacity-0 translate-y-1"
          @click.outside="open = false">
        @foreach ($preserve as $key => $value)
            @if (is_array($value))
                @foreach ($value as $v)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        <p class="sr-only">{{ __('Sıralama') }}</p>
        @foreach ($labels as $value => $label)
            <button type="submit" name="sort" value="{{ $value }}"
                    class="flex w-full items-center px-4 py-2.5 text-left text-sm transition-colors {{ $highlightSort === $value ? 'bg-rg-lightLavender/90 font-semibold text-rg-deepPurple dark:bg-white/10 dark:text-white' : 'text-rg-grayText hover:bg-rg-lightLavender/50 dark:text-white/92 dark:hover:bg-white/5' }}">
                {{ $label }}
            </button>
        @endforeach
    </form>
</div>
