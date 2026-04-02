<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->input('q', ''));
        $results = collect();

        if (mb_strlen($query) >= 2) {
            $results = Product::active()
                ->with('images')
                ->where(function ($builder) use ($query) {
                    $builder->whereRaw(
                        "CONVERT(JSON_UNQUOTE(JSON_EXTRACT(name, '$.tr')) USING utf8mb4) LIKE ?",
                        ["%{$query}%"]
                    )->orWhereRaw(
                        "CONVERT(JSON_UNQUOTE(JSON_EXTRACT(short_description, '$.tr')) USING utf8mb4) LIKE ?",
                        ["%{$query}%"]
                    )->orWhereRaw(
                        "CONVERT(JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) USING utf8mb4) LIKE ?",
                        ["%{$query}%"]
                    );
                })
                ->latest()
                ->paginate(16)
                ->withQueryString();
        }

        return view('search.results', compact('query', 'results'))->with([
            'metaTitle' => $query ? '"' . $query . '" arama sonuçları' : 'Ürün Arama',
            'metaDescription' => 'Rose Garden ürün arama sonuçları.',
        ]);
    }
}
