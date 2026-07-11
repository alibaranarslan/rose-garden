<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SpecialOccasion;
use App\Support\CatalogTaxonomy;
use App\Support\LocalizedSettings;
use App\Support\StorefrontCatalog;
use App\Support\StorefrontImage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class HomeModuleDataService
{
    public function collect(array $layoutState): array
    {
        $moduleMap = collect($layoutState['modules'] ?? [])->keyBy('key');

        $productCardWith = [
            'images' => fn ($query) => $query->orderBy('sort_order'),
            'categories' => fn ($query) => $query->with('parent')->orderBy('sort_order'),
            'variants' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
        ];

        $featuredProducts = Product::storefrontReady()
            ->featured()
            ->with($productCardWith)
            ->orderBy('sort_order')
            ->take(max(8, (int) data_get($moduleMap, 'featured_showcase.settings.content_limit', 1)))
            ->get();

        $newProducts = Product::storefrontReady()
            ->where('is_new', true)
            ->with($productCardWith)
            ->latest('updated_at')
            ->take((int) data_get($moduleMap, 'new_arrivals.settings.content_limit', 8))
            ->get();

        $bestSellers = Product::storefrontReady()
            ->with($productCardWith)
            ->orderByDesc('view_count')
            ->orderBy('sort_order')
            ->take((int) data_get($moduleMap, 'best_sellers.settings.content_limit', 8))
            ->get();

        $categorySlugs = CatalogTaxonomy::homeSpotlightCategorySlugs();
        $categories = StorefrontCatalog::activeCategoriesWithStorefrontCounts()
            ->whereIn('slug', $categorySlugs)
            ->filter(fn (Category $category) => (int) data_get($category, 'products_count', 0) > 0)
            ->sortBy(fn (Category $category) => array_search($category->slug, $categorySlugs, true))
            ->take((int) data_get($moduleMap, 'category_showcase.settings.content_limit', 6))
            ->values();

        $activeOccasion = SpecialOccasion::nearestActiveUpcoming(with: ['category']);

        $occasionProducts = $activeOccasion
            ? Product::storefrontReady()
                ->with($productCardWith)
                ->whereHas('categories', fn ($query) => $query->where('categories.id', $activeOccasion->category_id))
                ->orderByDesc('is_featured')
                ->orderByDesc('view_count')
                ->take((int) data_get($moduleMap, 'occasion_spotlight.settings.content_limit', 4))
                ->get()
            : collect();

        $blogPosts = BlogPost::published()
            ->with([
                'category',
                'products' => fn ($query) => $query->storefrontReady()->with(['images' => fn ($imageQuery) => $imageQuery->orderBy('sort_order')]),
            ])
            ->latest('published_at')
            ->take((int) data_get($moduleMap, 'blog_preview.settings.content_limit', 3))
            ->get();

        $heroSpotlight = $this->resolveHeroSpotlight($featuredProducts, $newProducts, $bestSellers);
        $heroProduct = $heroSpotlight['product'];
        $featuredShowcase = $this->takeDistinctProduct(
            $featuredProducts->concat($newProducts)->concat($bestSellers),
            array_filter([$heroProduct?->id]),
        );

        $usedProductIds = collect([$heroProduct?->id, $featuredShowcase?->id])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        $this->attachCategoryCoverPaths($categories, $usedProductIds);

        $bestSellers = $this->takeDistinctProducts(
            $bestSellers,
            $usedProductIds,
            (int) data_get($moduleMap, 'best_sellers.settings.content_limit', 8)
        );
        $newProducts = $this->takeDistinctProducts(
            $newProducts,
            array_merge($usedProductIds, $bestSellers->pluck('id')->all()),
            (int) data_get($moduleMap, 'new_arrivals.settings.content_limit', 8)
        );
        $occasionProducts = $this->takeDistinctProducts(
            $occasionProducts,
            array_merge($usedProductIds, $newProducts->pluck('id')->all(), $bestSellers->pluck('id')->all()),
            (int) data_get($moduleMap, 'occasion_spotlight.settings.content_limit', 4)
        );

        $homeContent = $this->loadHomeContent();
        $blogCards = $this->buildBlogCards($blogPosts);
        $trustAccentProducts = $featuredProducts
            ->concat($newProducts)
            ->concat($bestSellers)
            ->unique('id')
            ->take(4)
            ->values();

        foreach ([$featuredProducts, $newProducts, $bestSellers, $occasionProducts, $trustAccentProducts] as $catalogProducts) {
            StorefrontCatalog::decorateProductsWithDisplayCategory($catalogProducts);
        }

        if ($featuredShowcase instanceof Product) {
            StorefrontCatalog::decorateProductsWithDisplayCategory([$featuredShowcase]);
        }

        if ($heroProduct instanceof Product) {
            StorefrontCatalog::decorateProductsWithDisplayCategory([$heroProduct]);
        }

        $socialLinks = json_decode(Setting::get('social', 'links', '[]'), true) ?? [];
        $instagramUrl = collect($socialLinks)
            ->firstWhere('platform', 'instagram')['url'] ?? null;

        return compact(
            'featuredProducts',
            'newProducts',
            'bestSellers',
            'categories',
            'activeOccasion',
            'occasionProducts',
            'blogPosts',
            'blogCards',
            'homeContent',
            'heroSpotlight',
            'heroProduct',
            'featuredShowcase',
            'trustAccentProducts',
            'instagramUrl'
        );
    }

    public function buildSections(array $layoutState, array $data): array
    {
        return collect($layoutState['modules'] ?? [])
            ->filter(fn (array $module): bool => (bool) ($module['is_active'] ?? true))
            ->sortBy('sort_order')
            ->map(function (array $module) use ($data): array {
                return [
                    'key' => $module['key'],
                    'name' => $module['name'],
                    'settings' => $module['settings'] ?? [],
                    'has_content' => $this->moduleHasContent($module['key'], $data, $module['settings'] ?? []),
                ];
            })
            ->filter(fn (array $module): bool => $module['has_content'])
            ->values()
            ->all();
    }

    private function moduleHasContent(string $key, array $data, array $settings = []): bool
    {
        return match ($key) {
            'announcement_bar' => true,
            'hero' => filled($data['heroProduct'] ?? null)
                || $this->settingsHaveLocalizedText($settings, ['title_override', 'subtitle_override', 'cta_label'])
                || filled(data_get($data, 'homeContent.hero_heading'))
                || filled(data_get($data, 'homeContent.hero_subheading'))
                || collect(data_get($data, 'homeContent.hero_highlights', []))->isNotEmpty(),
            'category_showcase' => ($data['categories'] ?? collect())->isNotEmpty(),
            'featured_showcase' => filled($data['featuredShowcase'] ?? null),
            'occasion_spotlight' => filled($data['activeOccasion'] ?? null),
            'new_arrivals' => ($data['newProducts'] ?? collect())->isNotEmpty(),
            'best_sellers' => ($data['bestSellers'] ?? collect())->isNotEmpty(),
            'trust_badges' => ($data['trustAccentProducts'] ?? collect())->isNotEmpty(),
            'instagram_preview' => filled($data['instagramUrl'] ?? null),
            'blog_preview' => ($data['blogCards'] ?? collect())->isNotEmpty(),
            default => true,
        };
    }

    private function settingsHaveLocalizedText(array $settings, array $keys): bool
    {
        foreach ($keys as $key) {
            if (collect(data_get($settings, $key, []))->contains(fn ($value): bool => filled($value))) {
                return true;
            }
        }

        return false;
    }

    private function resolveHeroSpotlight(Collection $featuredProducts, Collection $newProducts, Collection $bestSellers): array
    {
        $mode = Setting::get('storefront', 'hero_spotlight_mode', 'best_seller');
        $manualProductId = (int) Setting::get('storefront', 'hero_spotlight_product_id', 0);

        if ($mode === 'manual' && $manualProductId > 0) {
            $manualProduct = Product::storefrontReady()
                ->with([
                    'images' => fn ($query) => $query->orderBy('sort_order'),
                    'variants' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
                ])
                ->find($manualProductId);

            if ($manualProduct) {
                return [
                    'product' => $manualProduct,
                    'eyebrow' => __('Öne çıkan ürün'),
                    'summary' => __('Müşterilerin hızlıca inceleyip siparişe geçebileceği seçili ürün.'),
                ];
            }
        }

        $sources = [
            'featured' => [
                'products' => $featuredProducts,
                'eyebrow' => __('Öne çıkan ürün'),
                'summary' => __('Popüler seçeneklerden biri; detayını inceleyip siparişe geçebilirsiniz.'),
            ],
            'newest' => [
                'products' => $newProducts,
                'eyebrow' => __('Yeni Gelen'),
                'summary' => __('Yeni eklenen ürünlerden hızlıca siparişe uygun bir seçenek.'),
            ],
            'best_seller' => [
                'products' => $bestSellers,
                'eyebrow' => __('Çok Satanlardan'),
                'summary' => __('En çok incelenen seçeneklerden biri; fiyatı ve görseliyle karar vermeyi kolaylaştırır.'),
            ],
        ];

        $selectedMode = array_key_exists($mode, $sources) ? $mode : 'best_seller';
        $selectedSource = $sources[$selectedMode];
        $product = $selectedSource['products']->first()
            ?? $featuredProducts->first()
            ?? $newProducts->first()
            ?? $bestSellers->first();

        return [
            'product' => $product,
            'eyebrow' => $selectedSource['eyebrow'],
            'summary' => $selectedSource['summary'],
        ];
    }

    private function loadHomeContent(): array
    {
        return [
            'hero_heading' => LocalizedSettings::resolveText(Setting::get('storefront', 'hero_heading', '')),
            'hero_subheading' => LocalizedSettings::resolveText(Setting::get('storefront', 'hero_subheading', '')),
            'hero_highlights' => LocalizedSettings::resolveRepeater(Setting::get('storefront', 'hero_highlights', '[]'), ['label', 'value']),
            'home_intro_heading' => LocalizedSettings::resolveText(Setting::get('storefront', 'home_intro_heading', '')),
            'home_intro_body' => LocalizedSettings::resolveText(Setting::get('storefront', 'home_intro_body', '')),
            'home_intro_points' => LocalizedSettings::resolveRepeater(Setting::get('storefront', 'home_intro_points', '[]'), ['title', 'text']),
            'showcase_heading' => LocalizedSettings::resolveText(Setting::get('storefront', 'showcase_heading', '')),
            'showcase_body' => LocalizedSettings::resolveText(Setting::get('storefront', 'showcase_body', '')),
            'showcase_points' => LocalizedSettings::resolveRepeater(Setting::get('storefront', 'showcase_points', '[]'), ['title', 'text']),
            'best_sellers_heading' => LocalizedSettings::resolveText(Setting::get('storefront', 'best_sellers_heading', '')),
            'best_sellers_body' => LocalizedSettings::resolveText(Setting::get('storefront', 'best_sellers_body', '')),
        ];
    }

    private function buildBlogCards(Collection $blogPosts): Collection
    {
        return $blogPosts->map(function ($post) {
            $coverImage = StorefrontImage::publicImgSrc(StorefrontImage::resolveBlogPostCoverUrl($post));

            return [
                'slug' => $post->slug,
                'title' => $post->title,
                'excerpt' => Str::limit((string) $post->excerpt, 160),
                'cover_image' => $coverImage,
                'cover_illustration' => StorefrontImage::isBlogDecorativeCover($coverImage),
                'published_label' => $post->published_at
                    ? $post->published_at->locale(app()->getLocale())->translatedFormat('j F Y')
                    : '',
                'published_at' => $post->published_at?->toDateString(),
            ];
        });
    }

    private function attachCategoryCoverPaths(Collection $categories, array $excludedProductIds = []): void
    {
        foreach ($categories as $category) {
            $categoryIds = StorefrontCatalog::categorySubtreeIds($category);
            $productQuery = Product::query()
                ->storefrontReady()
                ->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $categoryIds))
                ->with(['images' => fn ($query) => $query->orderBy('sort_order')])
                ->orderByDesc('is_featured')
                ->orderBy('sort_order');

            if ($excludedProductIds !== []) {
                $productQuery->whereNotIn('id', $excludedProductIds);
            }

            $product = $productQuery->first();

            if (! $product && $excludedProductIds !== []) {
                $product = Product::query()
                    ->storefrontReady()
                    ->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $categoryIds))
                    ->with(['images' => fn ($query) => $query->orderBy('sort_order')])
                    ->orderByDesc('is_featured')
                    ->orderBy('sort_order')
                    ->first();
            }

            $category->setAttribute('resolved_cover_path', $product?->primaryImage);
        }
    }

    private function takeDistinctProduct(Collection $products, array $usedProductIds): ?Product
    {
        return $products->first(
            fn ($product) => $product instanceof Product && ! in_array((int) $product->id, $usedProductIds, true)
        );
    }

    private function takeDistinctProducts(Collection $products, array $usedProductIds, int $limit): Collection
    {
        return $products
            ->filter(fn ($product) => $product instanceof Product)
            ->reject(fn (Product $product) => in_array((int) $product->id, $usedProductIds, true))
            ->unique('id')
            ->take($limit)
            ->values();
    }
}
