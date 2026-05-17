<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tag;
use App\Support\StorefrontCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProductController extends Controller
{
    public function index(Request $request, ?string $locale = null, ?string $slug = null)
    {
        $query = Product::storefrontReady()->with([
            'images' => fn ($imageQuery) => $imageQuery->orderBy('sort_order'),
            'categories' => fn ($categoryQuery) => $categoryQuery->with('parent')->orderBy('sort_order'),
            'variants' => fn ($variantQuery) => $variantQuery->where('is_active', true)->orderBy('sort_order'),
        ]);

        $category = null;
        $availableSizes = $this->getAvailableSizes();
        $allCategories = StorefrontCatalog::activeCategoriesWithStorefrontCounts()
            ->filter(fn (Category $item) => (int) data_get($item, 'products_count', 0) > 0)
            ->values();
        $catalogTotalCount = Product::storefrontReady()->count();

        if ($slug) {
            $category = Category::active()
                ->where('slug', $slug)
                ->firstOrFail();

            $categoryIds = StorefrontCatalog::categorySubtreeIds($category, $allCategories);

            $categoryProductExists = Product::storefrontReady()
                ->whereHas('categories', fn ($builder) => $builder->whereIn('categories.id', $categoryIds))
                ->exists();

            abort_unless($categoryProductExists, 404);

            $query->whereHas('categories', fn ($builder) => $builder->whereIn('categories.id', $categoryIds));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        if ($request->boolean('stock')) {
            $query->where('stock_status', 'in_stock');
        }

        if ($request->filled('size')) {
            $size = trim((string) $request->input('size'));

            $query->whereHas('variants', function ($variantQuery) use ($size) {
                $variantQuery->where('is_active', true)
                    ->where(function ($localizedQuery) use ($size) {
                        $localizedQuery
                            ->whereJsonContains('name->tr', $size)
                            ->orWhereJsonContains('name->en', $size)
                            ->orWhereJsonContains('name->ku', $size);
                    });
            });
        }

        $tagSlugs = array_values(array_filter((array) $request->input('tags', [])));
        foreach ($tagSlugs as $tagSlug) {
            $query->whereHas('tags', fn ($tagQuery) => $tagQuery->where('slug', $tagSlug));
        }

        $sort = $request->input('sort', 'recommended');
        $query = match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->latest('updated_at'),
            'best_sellers' => $query->orderByDesc('view_count'),
            default => $query->orderBy('sort_order'),
        };

        $products = $query->paginate(16)->withQueryString();
        StorefrontCatalog::decorateProductsWithDisplayCategory($products->getCollection());

        $filterTags = Tag::query()
            ->whereHas('products', fn ($productQuery) => $productQuery->storefrontReady())
            ->get()
            ->sortBy(fn (Tag $tag) => mb_strtolower($tag->getTranslation('name', app()->getLocale()) ?? $tag->slug))
            ->values();

        return view('products.index', compact('products', 'category', 'allCategories', 'sort', 'availableSizes', 'filterTags', 'catalogTotalCount'))->with([
            'metaTitle' => $category?->name ? $category->name.' Ürünleri' : 'Rose Garden Ürünleri',
            'metaDescription' => 'Rose Garden yerel ürün kataloğu ve filtrelenmiş ürün listesi.',
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::storefrontReady()
            ->where('slug', $slug)
            ->with([
                'images' => fn ($query) => $query->orderBy('sort_order'),
                'variants' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
                'categories' => fn ($query) => $query->with('parent')->orderBy('sort_order'),
                'tags',
            ])
            ->firstOrFail();

        $product->increment('view_count');
        StorefrontCatalog::decorateProductsWithDisplayCategory([$product]);

        $relatedBaseQuery = Product::storefrontReady()
            ->where('id', '!=', $product->id)
            ->with([
                'images' => fn ($query) => $query->orderBy('sort_order'),
                'categories' => fn ($query) => $query->with('parent')->orderBy('sort_order'),
                'variants' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
            ])
            ->orderByDesc('is_featured')
            ->orderByDesc('view_count');

        $preferredCategoryIds = StorefrontCatalog::preferredProductCategoryIds($product);
        $fallbackCategoryIds = $product->categories
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $related = collect();

        if ($preferredCategoryIds !== []) {
            $related = (clone $relatedBaseQuery)
                ->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $preferredCategoryIds))
                ->take(4)
                ->get();
        }

        if ($related->count() < 4 && $fallbackCategoryIds !== []) {
            $related = $related->concat(
                (clone $relatedBaseQuery)
                    ->whereNotIn('products.id', $related->pluck('id')->all())
                    ->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $fallbackCategoryIds))
                    ->take(4 - $related->count())
                    ->get()
            )->values();
        }

        StorefrontCatalog::decorateProductsWithDisplayCategory($related);

        $metaTitle = $this->localizedProductText($product, 'meta_title')
            ?: $this->localizedProductText($product, 'name')
            ?: $product->slug;
        $metaDescription = $this->localizedProductText($product, 'meta_description', 30)
            ?: $this->localizedProductText($product, 'short_description', 30)
            ?: \Illuminate\Support\Str::limit(strip_tags((string) $this->localizedProductText($product, 'description', 30)), 160)
            ?: 'Rose Garden ürün detay sayfası; taze çiçek, butik hazırlık ve güvenli sipariş akışını bir arada sunar.';

        return view('products.show', compact('product', 'related'))->with([
            'metaTitle' => $metaTitle,
            'metaDescription' => $metaDescription,
            'ogImage' => $product->primaryImage,
            'ogType' => 'product',
        ]);
    }

    private function localizedProductText(Product $product, string $field, int $minLength = 1): string
    {
        foreach ([app()->getLocale(), 'tr', 'en', 'ku'] as $locale) {
            $value = trim((string) $product->getTranslation($field, $locale, false));

            if (mb_strlen(strip_tags($value)) >= $minLength) {
                return $value;
            }
        }

        return '';
    }

    private function getAvailableSizes(): Collection
    {
        return ProductVariant::query()
            ->where('is_active', true)
            ->whereHas('product', fn ($query) => $query->storefrontReady())
            ->get()
            ->map(function (ProductVariant $variant): ?string {
                $name = $variant->getTranslation('name', app()->getLocale(), false)
                    ?: $variant->getTranslation('name', 'tr', false)
                    ?: $variant->getTranslation('name', 'en', false)
                    ?: $variant->getTranslation('name', 'ku', false);

                return is_string($name) ? trim($name) : null;
            })
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

}
