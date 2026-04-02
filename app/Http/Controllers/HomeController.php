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

        $bestSellers = Product::active()
            ->orderByDesc('view_count')
            ->with('images')
            ->take(8)
            ->get();

        $today = now();
        $futureDate = $today->copy()->addDays(14);
        $activeOccasion = SpecialOccasion::active()
            ->where(function ($q) use ($today, $futureDate) {
                $q->where(function ($inner) use ($today) {
                    $inner->where('date_month', $today->month);
                })->orWhere(function ($inner) use ($futureDate) {
                    $inner->where('date_month', $futureDate->month)
                          ->where('date_day', '<=', $futureDate->day);
                });
            })
            ->orderByRaw('date_month ASC, date_day ASC')
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
            'bestSellers',
            'categories',
            'activeOccasion',
            'occasionProducts',
            'blogPosts'
        ))->with([
            'metaTitle' => null,
            'metaDescription' => 'Adıyaman\'ın en özel butik çiçek ve çikolata mağazası. El yapımı tasarımlar, aynı gün teslimat.',
            'ogImage' => $featuredProducts->first()?->images->first()?->image_path,
        ]);
    }
}
