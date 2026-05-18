@props([
    'breadcrumbItems' => [],
    'pageTitle' => '',
    'totalCount' => 0,
    'sort' => 'recommended',
    'allCategories' => collect(),
    'filterTags' => collect(),
    'category' => null,
    'availableSizes' => collect(),
    'catalogTotalCount' => 0,
])

@php
    $formAction = url()->current();
    $pageDescription = $category
        ? trim((string) $category->description) ?: __('Bu koleksiyondaki seçili ürünleri filtreleyerek daha rafine bir seçim yapın.')
        : __('Adıyaman için hazırladığımız buket ve saksı bitki seçkisini filtreleyerek keşfedin.');

    $heroVisual = $category
        ? \App\Support\StorefrontImage::publicImgSrc(
            \App\Support\StorefrontImage::resolveCategory(
                data_get($category, 'resolved_cover_path') ?: data_get($category, 'image'),
                data_get($category, 'slug'),
                data_get($category, 'name'),
            )
        )
        : \App\Support\StorefrontImage::publicImgSrc(
            \App\Support\StorefrontImage::productVisualStrip(1)[0] ?? \App\Support\StorefrontImage::productPlaceholderImgSrc()
        );

    $topCategories = $allCategories
        ->filter(fn ($item) => filled(data_get($item, 'slug')) && filled(data_get($item, 'name')))
        ->take(4)
        ->values();

    $heroLinks = $topCategories->map(function ($item) use ($category) {
        $isActive = (string) data_get($item, 'slug') === (string) data_get($category, 'slug');

        return [
            'name' => data_get($item, 'name'),
            'slug' => data_get($item, 'slug'),
            'count' => (int) data_get($item, 'products_count', 0),
            'active' => $isActive,
        ];
    });
    $allProductsPillCount = $category ? $catalogTotalCount : $totalCount;
@endphp

<div class="w-full" x-data="{ plpDrawer: false }" @keydown.escape.window="plpDrawer = false">
    <x-breadcrumb :items="$breadcrumbItems" class="mb-3 text-xs text-rg-grayText dark:text-white/72 md:mb-4" />

    <x-page-hero class="rg-plp-hero mb-3 md:mb-4" :eyebrow="$category ? __('Kategori Koleksiyonu') : __('Rose Garden Katalog')" :title="$pageTitle" :description="$pageDescription" compact>
        <x-slot:actions>
            @if ($heroLinks->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    <a href="{{ \App\Support\StorefrontLocale::route('products.index') }}"
                       @class([
                           'inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold transition-colors',
                           'border-rg-purple bg-rg-purple text-white shadow-sm' => request()->routeIs('products.index'),
                           'border-black/8 bg-white/76 text-rg-deepPurple hover:border-rg-purple/35 hover:bg-white dark:border-white/10 dark:bg-white/10 dark:text-white dark:hover:bg-white/14' => ! request()->routeIs('products.index'),
                       ])>
                        <span>{{ __('Tüm Ürünler') }}</span>
                        <span class="rounded-full bg-black/8 px-2 py-0.5 text-[11px] tabular-nums text-current/80 dark:bg-white/12">{{ number_format($allProductsPillCount, 0, ',', '.') }}</span>
                    </a>
                    @foreach ($heroLinks as $link)
                        <a href="{{ \App\Support\StorefrontLocale::route('products.category', ['slug' => $link['slug']]) }}"
                           @class([
                               'inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold transition-colors',
                               'border-rg-purple bg-rg-purple text-white shadow-sm' => $link['active'],
                               'border-black/8 bg-white/76 text-rg-deepPurple hover:border-rg-purple/35 hover:bg-white dark:border-white/10 dark:bg-white/10 dark:text-white dark:hover:bg-white/14' => ! $link['active'],
                           ])>
                            <span>{{ $link['name'] }}</span>
                            @if ($link['count'] > 0)
                                <span class="rounded-full bg-black/8 px-2 py-0.5 text-[11px] tabular-nums text-current/80 dark:bg-white/12">{{ number_format($link['count'], 0, ',', '.') }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </x-slot:actions>

        <x-slot:stats>
            <div class="rg-page-stat max-w-sm">
                <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Katalog yoğunluğu') }}</p>
                <p class="mt-2 text-2xl font-semibold tabular-nums text-rg-deepPurple dark:text-white">{{ number_format($totalCount, 0, ',', '.') }}</p>
                <p class="mt-1 text-sm text-rg-copy-muted dark:text-white/82">{{ __('ürün görünür durumda') }}</p>
            </div>
            <p class="max-w-sm text-xs leading-6 text-rg-copy-muted dark:text-white/72">{{ __('Filtre ve sıralama ile aradığınız buket ya da hediyeye daha kolay ulaşın.') }}</p>
        </x-slot:stats>

        <x-slot:aside>
            <a href="{{ $category ? \App\Support\StorefrontLocale::route('products.category', ['slug' => $category->slug]) : \App\Support\StorefrontLocale::route('products.index') }}"
               class="group relative hidden aspect-[4/3] overflow-hidden rounded-[1.45rem] border border-black/5 bg-rg-lightLavender/24 shadow-[0_10px_24px_rgba(34,24,40,0.06)] dark:border-white/10 dark:bg-white/8 sm:block">
                <img src="{{ $heroVisual }}" alt="{{ $pageTitle }}" class="h-full w-full object-cover object-center transition-transform duration-700 group-hover:scale-[1.02]">
                <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(180deg,rgba(16,10,20,0.04),rgba(16,10,20,0.52))]"></div>
                <div class="absolute inset-x-0 bottom-0 p-3.5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-white/75">{{ __('Seçili Koleksiyon') }}</p>
                    <h2 class="mt-2 max-w-xs text-balance font-display text-[1.45rem] leading-tight text-white">{{ $pageTitle }}</h2>
                </div>
            </a>
        </x-slot:aside>
    </x-page-hero>

    <div class="sticky z-30 -mx-4 mb-3 border-y border-rg-lightLavender/70 bg-rg-cream/92 px-4 py-3 backdrop-blur-xl dark:border-white/10 dark:bg-[#1a0f22]/92 md:hidden sm:-mx-6 sm:px-6">
        <button type="button"
                @click="plpDrawer = true"
                class="flex w-full items-center justify-center gap-2 rounded-full border border-rg-midPurple/25 bg-white px-4 py-3 text-sm font-semibold text-rg-deepPurple shadow-sm transition-colors hover:border-rg-purple hover:bg-rg-lightLavender/50 dark:border-white/12 dark:bg-white/14 dark:text-white dark:hover:bg-white/10">
            <svg class="h-5 w-5 text-rg-purple dark:text-rg-lavender" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
            </svg>
            {{ __('Filtrele ve Sırala') }}
        </button>
    </div>

    <div class="flex flex-col gap-5 md:flex-row md:items-start md:gap-6 lg:gap-7">
        <aside class="hidden w-full shrink-0 md:block md:w-[22%] lg:w-[21%]">
            <div class="sticky top-28 rounded-[1.6rem] border border-black/5 bg-white/86 p-4 shadow-[0_16px_44px_rgba(34,24,40,0.08)] backdrop-blur-md dark:border-white/10 dark:bg-white/12">
                <h2 class="mb-4 text-sm font-bold uppercase tracking-wider text-rg-midPurple dark:text-rg-lavender">{{ __('Filtreler') }}</h2>
                <form method="GET" action="{{ $formAction }}">
                    <x-plp-filter-fields
                        :all-categories="$allCategories"
                        :filter-tags="$filterTags"
                        :category="$category"
                        :available-sizes="$availableSizes"
                    />
                </form>
            </div>
        </aside>

        <div class="min-w-0 flex-1">
            <x-product-list-toolbar :total="$totalCount" :sort="$sort" sort-mode="catalog" />
            {{ $slot }}
        </div>
    </div>

    <div class="md:hidden" x-show="plpDrawer" x-cloak aria-modal="true" role="dialog">
        <div class="fixed inset-0 z-[60] flex flex-col justify-end bg-black/50 backdrop-blur-[2px]" @click.self="plpDrawer = false">
            <div class="max-h-[min(92vh,720px)] overflow-hidden rounded-t-[2rem] border border-b-0 border-rg-lightLavender bg-white shadow-2xl dark:border-white/10 dark:bg-[#23182c]"
                 @click.stop>
                <div class="flex items-center justify-between border-b border-rg-lightLavender px-4 py-3 dark:border-white/10">
                    <span class="text-base font-semibold text-rg-darkText dark:text-white">{{ __('Filtreler') }}</span>
                    <button type="button" class="rounded-full p-2 text-rg-grayText hover:bg-rg-lightLavender dark:text-white/86 dark:hover:bg-white/10" @click="plpDrawer = false" aria-label="{{ __('Kapat') }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="max-h-[calc(min(92vh,720px)-56px)] overflow-y-auto px-4 pb-6 pt-2">
                    <form method="GET" action="{{ $formAction }}" @submit="plpDrawer = false">
                        <x-plp-filter-fields
                            :all-categories="$allCategories"
                            :filter-tags="$filterTags"
                            :category="$category"
                            :available-sizes="$availableSizes"
                        />
                    </form>
                    <div class="mt-6 border-t border-rg-lightLavender pt-4 dark:border-white/10">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender">{{ __('Sıralama') }}</p>
                        <x-sort-dropdown :sort="$sort" full-width sort-mode="catalog" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
