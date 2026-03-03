<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\SpecialOccasion;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::active()
            ->featured()
            ->with('images')
            ->take(8)
            ->get();

        $newProducts = Product::active()
            ->where('is_new', true)
            ->with('images')
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::active()
            ->roots()
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $activeOccasion = SpecialOccasion::active()
            ->where('date_month', now()->month)
            ->where('date_day', '>=', now()->day)
            ->orderBy('date_day')
            ->first();

        $occasionProducts = $activeOccasion
            ? Product::active()
                ->whereHas('categories', fn ($query) => $query->where('categories.id', $activeOccasion->category_id))
                ->with('images')
                ->take(4)
                ->get()
            : collect();

        $blogPosts = BlogPost::published()
            ->with('category')
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('home.index', compact(
            'featuredProducts',
            'newProducts',
            'categories',
            'activeOccasion',
            'occasionProducts',
            'blogPosts'
        ))->with([
            'metaTitle' => 'Rose Garden Cicek ve Cikolata',
            'metaDescription' => 'Butik cicek ve cikolata tasarimlari ile ayni gun teslimat.',
            'ogImage' => $featuredProducts->first()?->images->first()?->image_path,
        ]);
    }
}
