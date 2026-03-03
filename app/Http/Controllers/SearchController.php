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
                        "JSON_UNQUOTE(JSON_EXTRACT(name, '$.tr')) COLLATE utf8mb4_unicode_ci LIKE ?",
                        ["%{$query}%"]
                    )->orWhereRaw(
                        "JSON_UNQUOTE(JSON_EXTRACT(short_description, '$.tr')) COLLATE utf8mb4_unicode_ci LIKE ?",
                        ["%{$query}%"]
                    )->orWhereRaw(
                        "JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) COLLATE utf8mb4_unicode_ci LIKE ?",
                        ["%{$query}%"]
                    );
                })
                ->latest()
                ->paginate(16)
                ->withQueryString();
        }

        return view('search.results', compact('query', 'results'))->with([
            'metaTitle' => $query ? '"' . $query . '" arama sonuclari' : 'Urun arama',
            'metaDescription' => 'Rose Garden urun arama sonuclari.',
        ]);
    }
}
