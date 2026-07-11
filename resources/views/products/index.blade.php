@extends('layouts.app')

@section('content')
    @php
        $categoryDisplayName = $category
            ? (trim((string) $category->name) !== '' ? $category->name : \Illuminate\Support\Str::title(str_replace('-', ' ', $category->slug)))
            : null;
        $pageTitle = $categoryDisplayName ?? __('Tüm Ürünler');
        $breadcrumbItems = [['label' => __('Anasayfa'), 'url' => \App\Support\StorefrontLocale::route('home')]];
        if ($category) {
            $breadcrumbItems[] = ['label' => __('Tüm Ürünler'), 'url' => \App\Support\StorefrontLocale::route('products.index')];
            $breadcrumbItems[] = ['label' => $categoryDisplayName, 'url' => null];
        } else {
            $breadcrumbItems[] = ['label' => __('Tüm Ürünler'), 'url' => null];
        }
        $clearFiltersUrl = $category
            ? \App\Support\StorefrontLocale::route('products.category', ['slug' => $category->slug])
            : \App\Support\StorefrontLocale::route('products.index');
        $catalogSupportLinks = [
            [
                'label' => __('Özel gün hediyeleri'),
                'href' => \App\Support\StorefrontLocale::route('special-occasions.index'),
            ],
            [
                'label' => __('Teslimat bilgileri'),
                'href' => \App\Support\StorefrontLocale::route('delivery.info'),
            ],
            [
                'label' => __('WhatsApp desteği'),
                'href' => \App\Support\StorefrontLocale::route('contact'),
            ],
        ];
        $catalogSupportNotes = [
            __('Kart mesajı ve teslim notunu ödeme öncesinde ekleyebilirsiniz.'),
            __('Kararsız kalırsanız benzer tonda alternatifler için mağazadan destek alabilirsiniz.'),
        ];
    @endphp

    <x-product-list-layout
        :breadcrumb-items="$breadcrumbItems"
        :page-title="$pageTitle"
        :total-count="$products->total()"
        :sort="$sort"
        :all-categories="$allCategories"
        :filter-tags="$filterTags"
        :category="$category"
        :available-sizes="$availableSizes ?? collect()"
        :catalog-total-count="$catalogTotalCount ?? $products->total()"
    >
        @if ($products->isEmpty())
            <x-plp-empty-state :clear-url="$clearFiltersUrl" />
        @else
            <x-product-grid variant="catalog">
                @foreach ($products as $product)
                    <x-product-card
                        :product="$product"
                        :image-alternate-products="$products"
                        :eager-image="$loop->index < 4"
                    />
                @endforeach
            </x-product-grid>
            {{ $products->links('vendor.pagination.rg-catalog') }}

            <section class="mt-6 border-t border-black/6 pt-5 dark:border-white/10">
                <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                    <div class="max-w-2xl">
                        <h2 class="font-display text-2xl text-rg-deepPurple dark:text-white">{{ __('Siparişe karar vermek kolay olsun') }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/82">
                            {{ __('Teslimat, özel gün ve hediye seçimiyle ilgili kısa destek kapıları burada elinizin altında.') }}
                        </p>
                    </div>

                    <div class="grid gap-2.5 sm:grid-cols-3 xl:w-[42rem]">
                        @foreach ($catalogSupportLinks as $link)
                            <a href="{{ $link['href'] }}" class="flex items-center justify-between rounded-[1.15rem] border border-black/6 bg-rg-cream/72 px-3.5 py-3 text-sm font-semibold text-rg-deepPurple transition-colors hover:border-rg-purple/35 hover:bg-rg-cream dark:border-white/10 dark:bg-white/8 dark:text-white">
                                <span>{{ $link['label'] }}</span>
                                <span aria-hidden="true">→</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="mt-3 grid gap-2.5 md:grid-cols-2">
                    @foreach ($catalogSupportNotes as $note)
                        <div class="rounded-[1.15rem] border border-black/6 bg-white/76 px-3.5 py-3.5 text-sm leading-relaxed text-rg-grayText dark:border-white/10 dark:bg-white/8 dark:text-white/82">
                            {{ $note }}
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </x-product-list-layout>
@endsection
