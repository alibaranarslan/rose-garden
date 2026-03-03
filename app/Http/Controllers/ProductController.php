<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, ?string $slug = null)
    {
        $query = Product::active()->with(['images', 'categories', 'variants']);
        $category = null;

        if ($slug) {
            $category = Category::active()->where('slug', $slug)->firstOrFail();
            $query->whereHas('categories', fn ($builder) => $builder->where('categories.id', $category->id));
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

        $sort = $request->input('sort', 'recommended');
        $query = match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->latest(),
            default => $query->orderBy('sort_order'),
        };

        $products = $query->paginate(16)->withQueryString();
        $allCategories = Category::active()->withCount('products')->orderBy('sort_order')->get();

        return view('products.index', compact('products', 'category', 'allCategories', 'sort'))->with([
            'metaTitle' => $category?->name ? $category->name . ' Urunleri' : 'Rose Garden Urunleri',
            'metaDescription' => 'Rose Garden urun katalogu ve filtrelenmis urun listesi.',
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with(['images', 'variants', 'categories', 'tags'])
            ->firstOrFail();

        $product->increment('view_count');

        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $product->categories->pluck('id')))
            ->with('images')
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'related'))->with([
            'metaTitle' => $product->meta_title ?: $product->name,
            'metaDescription' => $product->meta_description ?: $product->short_description,
            'ogImage' => $product->images->first()?->image_path,
            'ogType' => 'product',
        ]);
    }
}
