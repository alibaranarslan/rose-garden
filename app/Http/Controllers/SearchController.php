<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\StorefrontCatalog;
use App\Support\StorefrontLocale;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->input('q', ''));
        $results = collect();

        if (mb_strlen($query) >= 2) {
            $driver = Product::query()->getConnection()->getDriverName();
            $normalizedQuery = $this->normalizeSearchValue($query);
            $searchTerms = $this->extractSearchTerms($normalizedQuery);

            $results = Product::storefrontReady()
                ->with([
                    'images' => fn ($query) => $query->orderBy('sort_order'),
                    'categories' => fn ($query) => $query->with('parent')->orderBy('sort_order'),
                    'variants' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order'),
                ])
                ->where(function (Builder $builder) use ($driver, $normalizedQuery, $searchTerms) {
                    $this->applyLocaleAwareSearch($builder, $driver, '%'.$normalizedQuery.'%');

                    if (count($searchTerms) > 1) {
                        $builder->orWhere(function (Builder $tokenBuilder) use ($driver, $searchTerms) {
                            foreach ($searchTerms as $term) {
                                $tokenBuilder->where(function (Builder $termMatchBuilder) use ($driver, $term) {
                                    $this->applyLocaleAwareSearch($termMatchBuilder, $driver, '%'.$term.'%');
                                });
                            }
                        });
                    }
                })
                ->latest()
                ->paginate(16)
                ->withQueryString();

            StorefrontCatalog::decorateProductsWithDisplayCategory($results->getCollection());
        }

        return view('search.results', compact('query', 'results'))->with([
            'metaTitle' => $query ? __('":query" arama sonuçları', ['query' => $query]) : __('Ürün Arama'),
            'metaDescription' => __('Rose Garden ürün arama sonuçları.'),
        ]);
    }

    /**
     * @return list<string>
     */
    private function searchableJsonExpressions(string $driver): array
    {
        $expressions = [];

        foreach (['name', 'short_description'] as $column) {
            foreach (StorefrontLocale::codes() as $locale) {
                $expressions[] = $this->jsonLikeExpression($driver, $column, $locale);
            }
        }

        return $expressions;
    }

    private function applyLocaleAwareSearch(Builder $builder, string $driver, string $likeQuery): void
    {
        foreach ($this->searchableJsonExpressions($driver) as $expression) {
            $builder->orWhereRaw($expression, [$likeQuery]);
        }
    }

    /**
     * @return list<string>
     */
    private function extractSearchTerms(string $query): array
    {
        return collect(preg_split('/[\s\p{P}]+/u', $query) ?: [])
            ->map(fn ($term) => trim((string) $term))
            ->filter(fn ($term) => mb_strlen($term) >= 2)
            ->unique()
            ->take(4)
            ->values()
            ->all();
    }

    private function jsonLikeExpression(string $driver, string $column, string $locale): string
    {
        $baseExpression = match ($driver) {
            'sqlite' => "coalesce(json_extract({$column}, '$.{$locale}'), '')",
            'pgsql' => "coalesce({$column}->>'{$locale}', '')",
            default => "coalesce(JSON_UNQUOTE(JSON_EXTRACT({$column}, '$.{$locale}')), '')",
        };

        return $this->normalizeSqlExpression($baseExpression).' LIKE ?';
    }

    private function normalizeSqlExpression(string $expression): string
    {
        $normalized = "lower({$expression})";

        foreach ([
            'ç' => 'c',
            'ğ' => 'g',
            'ı' => 'i',
            'ö' => 'o',
            'ş' => 's',
            'ü' => 'u',
            'â' => 'a',
            'ê' => 'e',
            'î' => 'i',
            'ô' => 'o',
            'û' => 'u',
        ] as $from => $to) {
            $normalized = "replace({$normalized}, '{$from}', '{$to}')";
        }

        return $normalized;
    }

    private function normalizeSearchValue(string $value): string
    {
        return strtr(mb_strtolower($value), [
            'ç' => 'c',
            'ğ' => 'g',
            'ı' => 'i',
            'ö' => 'o',
            'ş' => 's',
            'ü' => 'u',
            'â' => 'a',
            'ê' => 'e',
            'î' => 'i',
            'ô' => 'o',
            'û' => 'u',
        ]);
    }
}
