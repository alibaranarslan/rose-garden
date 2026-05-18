<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SpecialOccasion;
use App\Support\CatalogTaxonomy;
use App\Support\LocalizedSettings;
use App\Support\StorefrontImage;
use Illuminate\Support\Collection;

/**
 * Legacy homepage reference path.
 *
 * This controller is intentionally not bound to the live storefront homepage in routes/web.php.
 * Active homepage ownership lives in StorefrontHomeController@index -> home.layout-studio.
 */
class HomeController extends Controller
{
    public function index()
    {
        // Returns the legacy home.index view only; do not treat this method as the active homepage owner.
        $productCardWith = [
            'images' => fn ($query) => $query->orderBy('sort_order'),
            'categories',
            'variants' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
        ];

        $featuredProducts = Product::storefrontReady()
            ->featured()
            ->with($productCardWith)
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $newProducts = Product::storefrontReady()
            ->where('is_new', true)
            ->with($productCardWith)
            ->latest('updated_at')
            ->take(8)
            ->get();

        $bestSellers = Product::storefrontReady()
            ->with($productCardWith)
            ->orderByDesc('view_count')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $categorySlugs = CatalogTaxonomy::homeSpotlightCategorySlugs();
        $categories = Category::active()
            ->whereIn('slug', $categorySlugs)
            ->whereHas('products', fn ($query) => $query->storefrontReady())
            ->with('parent')
            ->withCount(['products as products_count' => fn ($query) => $query->storefrontReady()])
            ->get()
            ->sortBy(fn (Category $category) => array_search($category->slug, $categorySlugs, true))
            ->values();

        $this->attachCategoryCoverPaths($categories);

        $activeOccasion = SpecialOccasion::nearestActive(with: ['category']);

        $occasionProducts = $activeOccasion
            ? Product::storefrontReady()
                ->with($productCardWith)
                ->whereHas('categories', fn ($query) => $query->where('categories.id', $activeOccasion->category_id))
                ->orderByDesc('is_featured')
                ->orderByDesc('view_count')
                ->take(4)
                ->get()
            : collect();

        $blogPosts = BlogPost::published()
            ->with([
                'category',
                'products' => fn ($query) => $query->storefrontReady()->with(['images' => fn ($imageQuery) => $imageQuery->orderBy('sort_order')]),
            ])
            ->latest('published_at')
            ->take(3)
            ->get();

        $heroSpotlight = $this->resolveHeroSpotlight($featuredProducts, $newProducts, $bestSellers);
        $heroProduct = $heroSpotlight['product'];
        $featuredShowcase = $this->takeDistinctProduct(
            $featuredProducts->concat($newProducts)->concat($bestSellers),
            array_filter([$heroProduct?->id]),
        );

        $usedProductIds = collect([
            $heroProduct?->id,
            $featuredShowcase?->id,
        ])->filter()->map(fn ($id) => (int) $id)->all();

        $newProducts = $this->takeDistinctProducts($newProducts, $usedProductIds, 8);
        $bestSellers = $this->takeDistinctProducts(
            $bestSellers,
            array_merge($usedProductIds, $newProducts->pluck('id')->all()),
            8,
        );
        $occasionProducts = $this->takeDistinctProducts(
            $occasionProducts,
            array_merge($usedProductIds, $newProducts->pluck('id')->all(), $bestSellers->pluck('id')->all()),
            4,
        );
        $homeContent = $this->loadHomeContent();
        $ogImage = $heroProduct
            ? StorefrontImage::resolveProduct(
                $heroProduct->primaryImage,
                $heroProduct->slug,
                $heroProduct->name,
            )
            : StorefrontImage::productPlaceholderImgSrc();

        return view('home.index', [
            'featuredProducts' => $featuredProducts,
            'newProducts' => $newProducts,
            'bestSellers' => $bestSellers,
            'categories' => $categories,
            'activeOccasion' => $activeOccasion,
            'occasionProducts' => $occasionProducts,
            'blogPosts' => $blogPosts,
            'homeContent' => $homeContent,
            'heroSpotlightProduct' => $heroSpotlight['product'],
            'heroSpotlightEyebrow' => $heroSpotlight['eyebrow'],
            'heroSpotlightSummary' => $heroSpotlight['summary'],
            'featuredShowcase' => $featuredShowcase,
            'metaTitle' => null,
            'metaDescription' => __('Adıyaman’ın butik çiçek ve saksı bitki seçkisi. Yerel ürünler, rafine sunum ve aynı gün teslimat odağı.'),
            'ogImage' => $ogImage,
        ]);
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
                    'eyebrow' => __('Seçili Vitrin'),
                    'summary' => __('Atölyenin bu dönem öne çıkarmak istediği ürün.'),
                ];
            }
        }

        $sources = [
            'featured' => [
                'products' => $featuredProducts,
                'eyebrow' => __('Editör Seçimi'),
                'summary' => __('Floral dili ve sunum gücüyle vitrinin merkezine taşınan ürün.'),
            ],
            'newest' => [
                'products' => $newProducts,
                'eyebrow' => __('Yeni Gelen'),
                'summary' => __('Koleksiyona son eklenen yerel ürünlerden öne çıkan seçim.'),
            ],
            'best_seller' => [
                'products' => $bestSellers,
                'eyebrow' => __('Çok Satanlardan'),
                'summary' => __('Müşterilerin en çok ilgi gösterdiği ürünler arasından seçilir.'),
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

    /**
     * @param  \Illuminate\Support\Collection<int, Category>  $categories
     */
    private function attachCategoryCoverPaths(Collection $categories): void
    {
        foreach ($categories as $category) {
            $treeIds = $this->categorySubtreeIds($category->id);
            $product = Product::query()
                ->storefrontReady()
                ->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $treeIds))
                ->whereHas('images')
                ->with(['images' => fn ($query) => $query->orderBy('sort_order')])
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->first();

            $category->setAttribute('resolved_cover_path', $product?->primaryImage);
        }
    }

    /**
     * @return list<int>
     */
    private function categorySubtreeIds(int $rootId): array
    {
        $ids = [$rootId];
        $pending = [$rootId];

        while ($pending !== []) {
            $batch = array_splice($pending, 0, 50);
            $children = Category::query()
                ->whereIn('parent_id', $batch)
                ->pluck('id')
                ->all();

            foreach ($children as $childId) {
                if (! in_array($childId, $ids, true)) {
                    $ids[] = $childId;
                    $pending[] = $childId;
                }
            }
        }

        return $ids;
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
