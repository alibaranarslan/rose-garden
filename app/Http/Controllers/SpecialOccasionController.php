<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SpecialOccasion;
use Illuminate\Database\Eloquent\Builder;

class SpecialOccasionController extends Controller
{
    public function index()
    {
        $occasions = SpecialOccasion::active()
            ->with('category')
            ->get()
            ->sortBy(fn (SpecialOccasion $occasion) => $occasion->daysUntil())
            ->values();

        $featuredOccasion = $occasions->first();
        $upcomingOccasions = $occasions->slice(1, 3)->values();
        $featuredProducts = $featuredOccasion
            ? (clone $this->occasionProductsQuery($featuredOccasion))->take(8)->get()
            : collect();

        return view('special-occasions.index', compact(
            'occasions',
            'featuredOccasion',
            'upcomingOccasions',
            'featuredProducts',
        ))->with([
            'metaTitle' => __('Özel Günler'),
            'metaDescription' => __('Yıl içindeki özel günler için çiçek, çikolata ve hediye önerilerini keşfedin.'),
        ]);
    }

    public function show(string $slug)
    {
        $occasion = SpecialOccasion::active()
            ->with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        $productsQuery = $this->occasionProductsQuery($occasion);
        $featuredProducts = (clone $productsQuery)
            ->take(8)
            ->get();

        $products = $productsQuery
            ->paginate(16)
            ->withQueryString();

        $relatedOccasions = SpecialOccasion::active()
            ->whereKeyNot($occasion->id)
            ->get()
            ->sortBy(fn (SpecialOccasion $item) => $item->daysUntil())
            ->take(4)
            ->values();

        $occasionName = $occasion->getTranslation('name', app()->getLocale());

        return view('special-occasions.show', compact(
            'occasion',
            'products',
            'featuredProducts',
            'relatedOccasions',
        ))->with([
            'metaTitle' => $occasionName,
            'metaDescription' => __(':name için seçilmiş ürünler.', ['name' => $occasionName]),
        ]);
    }

    private function occasionProductsQuery(SpecialOccasion $occasion): Builder
    {
        return Product::storefrontReady()
            ->with(['images' => fn ($query) => $query->orderBy('sort_order')])
            ->where(function ($q) use ($occasion) {
                $q->whereHas(
                    'specialOccasions',
                    fn ($b) => $b->where('special_occasions.id', $occasion->id)
                );

                if ($occasion->category_id) {
                    $q->orWhereHas(
                        'categories',
                        fn ($b) => $b->where('categories.id', $occasion->category_id)
                    );
                }
            })
            ->orderByDesc('is_featured')
            ->orderByDesc('is_new')
            ->orderByDesc('view_count')
            ->orderBy('sort_order');
    }
}
