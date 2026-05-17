<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;

final class StorefrontCatalog
{
    public static function activeCategoriesWithStorefrontCounts(): Collection
    {
        $categories = Category::active()
            ->with('parent')
            ->orderBy('sort_order')
            ->get();

        if ($categories->isEmpty()) {
            return $categories;
        }

        $childrenByParent = [];
        foreach ($categories as $category) {
            if ($category->parent_id !== null) {
                $childrenByParent[(int) $category->parent_id][] = (int) $category->id;
            }
        }

        $productIdsByCategory = [];
        $pairs = Product::query()
            ->storefrontReady()
            ->join('product_category', 'products.id', '=', 'product_category.product_id')
            ->select('products.id as product_id', 'product_category.category_id')
            ->get();

        foreach ($pairs as $pair) {
            $categoryId = (int) $pair->category_id;
            $productId = (int) $pair->product_id;
            $productIdsByCategory[$categoryId][$productId] = true;
        }

        $subtreeProductSets = [];
        $collectSubtreeProductSet = function (int $categoryId) use (&$collectSubtreeProductSet, &$subtreeProductSets, $childrenByParent, $productIdsByCategory): array {
            if (array_key_exists($categoryId, $subtreeProductSets)) {
                return $subtreeProductSets[$categoryId];
            }

            $productSet = $productIdsByCategory[$categoryId] ?? [];

            foreach ($childrenByParent[$categoryId] ?? [] as $childId) {
                $productSet += $collectSubtreeProductSet($childId);
            }

            return $subtreeProductSets[$categoryId] = $productSet;
        };

        return $categories
            ->map(function (Category $category) use ($collectSubtreeProductSet) {
                $category->setAttribute('products_count', count($collectSubtreeProductSet((int) $category->id)));

                return $category;
            })
            ->values();
    }

    /**
     * @param  Collection<int, Category>|null  $categories
     * @return list<int>
     */
    public static function categorySubtreeIds(Category|int $rootCategory, ?Collection $categories = null): array
    {
        $rootId = $rootCategory instanceof Category ? (int) $rootCategory->id : (int) $rootCategory;
        $categories ??= Category::active()->get(['id', 'parent_id']);

        $childrenByParent = [];
        foreach ($categories as $category) {
            if ($category->parent_id !== null) {
                $childrenByParent[(int) $category->parent_id][] = (int) $category->id;
            }
        }

        $ids = [$rootId];
        $pending = [$rootId];

        while ($pending !== []) {
            $current = array_shift($pending);

            foreach ($childrenByParent[$current] ?? [] as $childId) {
                if (! in_array($childId, $ids, true)) {
                    $ids[] = $childId;
                    $pending[] = $childId;
                }
            }
        }

        return $ids;
    }

    /**
     * @param  iterable<Product>  $products
     */
    public static function decorateProductsWithDisplayCategory(iterable $products): void
    {
        foreach ($products as $product) {
            if (! $product instanceof Product) {
                continue;
            }

            $orderedCategories = self::orderedProductCategories($product);
            $displayCategory = $orderedCategories->first();

            $product->setRelation('categories', $orderedCategories);
            $product->setAttribute('display_category', $displayCategory);
            $product->setAttribute('display_category_name', $displayCategory?->name);
        }
    }

    /**
     * @return list<int>
     */
    public static function preferredProductCategoryIds(Product $product): array
    {
        $orderedCategories = self::orderedProductCategories($product);
        if ($orderedCategories->isEmpty()) {
            return [];
        }

        $maxDepth = self::categoryDepth($orderedCategories->first());

        return $orderedCategories
            ->filter(fn (Category $category) => self::categoryDepth($category) === $maxDepth)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, Category>
     */
    private static function orderedProductCategories(Product $product): Collection
    {
        $categories = $product->relationLoaded('categories')
            ? $product->categories
            : $product->categories()->with('parent')->get();

        return $categories
            ->filter(fn ($category) => $category instanceof Category)
            ->sort(function (Category $left, Category $right): int {
                $depthComparison = self::categoryDepth($right) <=> self::categoryDepth($left);
                if ($depthComparison !== 0) {
                    return $depthComparison;
                }

                $sortComparison = ((int) $left->sort_order) <=> ((int) $right->sort_order);
                if ($sortComparison !== 0) {
                    return $sortComparison;
                }

                return strcmp((string) $left->slug, (string) $right->slug);
            })
            ->values();
    }

    private static function categoryDepth(Category $category): int
    {
        $depth = 0;
        $current = $category;
        $visited = [];

        while ($current->parent_id !== null && ! isset($visited[$current->id])) {
            $visited[$current->id] = true;
            $depth++;

            if (! $current->relationLoaded('parent') || ! $current->parent) {
                break;
            }

            $current = $current->parent;
        }

        return $depth;
    }
}
