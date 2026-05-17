<?php

namespace App\Support;

/**
 * product-import/incoming + ProductIncomingDefinitions ile eşleşen vitrin görselleri.
 * Önce incoming içe aktarımı (products:import-incoming) yapıldığında storage/products/{slug}.jpg oluşur.
 *
 * @see \App\Data\ProductIncomingDefinitions
 */
final class StorefrontIncomingAssets
{
    /**
     * @param  list<string>  $incomingFiles
     * @param  array<string, array<string, mixed>>  $catalog
     * @return array{
     *     matched:list<array{basename:string,source_path:string,catalog_key:string,definition:array<string,mixed>,match_source:string}>,
     *     ambiguous:list<array{basename:string,source_path:string,candidates:list<string>,match_key:string}>,
     *     unmatched:list<array{basename:string,source_path:string}>
     * }
     */
    public static function classifyIncomingFiles(array $incomingFiles, array $catalog): array
    {
        $exactCatalog = [];
        $normalizedCatalog = [];

        foreach ($catalog as $catalogKey => $definition) {
            if (! is_array($definition)) {
                continue;
            }

            $catalogKey = basename((string) $catalogKey);
            if ($catalogKey === '') {
                continue;
            }

            $exactCatalog[$catalogKey] = $definition;

            foreach (self::catalogAliases($catalogKey, $definition) as $aliasKey => $source) {
                $normalizedCatalog[$aliasKey][] = [
                    'catalog_key' => $catalogKey,
                    'definition' => $definition,
                    'match_source' => $source,
                ];
            }
        }

        $matched = [];
        $ambiguous = [];
        $unmatched = [];
        $claimed = [];

        foreach ($incomingFiles as $sourcePath) {
            $basename = basename((string) $sourcePath);
            if ($basename === '') {
                continue;
            }

            if (isset($exactCatalog[$basename])) {
                if (isset($claimed[$basename])) {
                    $ambiguous[] = [
                        'basename' => $basename,
                        'source_path' => $sourcePath,
                        'candidates' => [$basename],
                        'match_key' => self::normalizeIncomingKey(pathinfo($basename, PATHINFO_FILENAME)),
                    ];

                    continue;
                }

                $claimed[$basename] = true;
                $matched[] = [
                    'basename' => $basename,
                    'source_path' => $sourcePath,
                    'catalog_key' => $basename,
                    'definition' => $exactCatalog[$basename],
                    'match_source' => 'exact',
                ];

                continue;
            }

            $matchKey = self::normalizeIncomingKey(pathinfo($basename, PATHINFO_FILENAME));
            $candidates = $normalizedCatalog[$matchKey] ?? [];
            $candidates = array_values(array_reduce(
                $candidates,
                static function (array $carry, array $candidate): array {
                    $carry[$candidate['catalog_key']] = $candidate;

                    return $carry;
                },
                []
            ));

            if ($candidates === []) {
                $unmatched[] = [
                    'basename' => $basename,
                    'source_path' => $sourcePath,
                ];

                continue;
            }

            if (count($candidates) !== 1) {
                $ambiguous[] = [
                    'basename' => $basename,
                    'source_path' => $sourcePath,
                    'candidates' => array_values(array_unique(array_map(
                        static fn (array $candidate): string => $candidate['catalog_key'],
                        $candidates
                    ))),
                    'match_key' => $matchKey,
                ];

                continue;
            }

            $candidate = $candidates[0];
            if (isset($claimed[$candidate['catalog_key']])) {
                $ambiguous[] = [
                    'basename' => $basename,
                    'source_path' => $sourcePath,
                    'candidates' => [$candidate['catalog_key']],
                    'match_key' => $matchKey,
                ];

                continue;
            }

            $claimed[$candidate['catalog_key']] = true;
            $matched[] = [
                'basename' => $basename,
                'source_path' => $sourcePath,
                'catalog_key' => $candidate['catalog_key'],
                'definition' => $candidate['definition'],
                'match_source' => $candidate['match_source'],
            ];
        }

        return compact('matched', 'ambiguous', 'unmatched');
    }

    /**
     * @return list<string>
     */
    public static function blogCoverProductSlugs(?string $blogSlug): array
    {
        return match ($blogSlug) {
            'kesme-ciceklerin-omrunu-uzatmanin-7-yolu' => [
                'lavanta-bahar-karisik-buket',
                'bahar-nergis-cipso-buket',
                'mor-ruya-karisik-buket',
                'pastel-gerbera-krizantem-buket',
                'karma-mevsim-buketi',
            ],
            'anneler-gunu-icin-en-iyi-cicek-secenekleri' => [
                'pembe-zambak-gul-buket',
                'lila-krizantem-zerafet-buket',
                'lavanta-pembe-sprey-gul-buket',
                '2li-pembe-orkide-dogum-gunu',
                '2li-beyaz-orkide',
            ],
            'cicek-diliyle-duygularinizi-anlatin' => [
                'asil-ask-siyah-kagit-gul-buket',
                'premium-kirmizi-gul-cipso-kubbe',
                'kirmizi-gul-tek-beyaz-vurgu-buket',
                'gece-yarisi-kirmizi-gul-altin-cizgi',
            ],
            'saksi-cicegi-bakim-rehberi-orkide' => [
                '2li-beyaz-orkide',
                '2li-mor-orkide-a',
                'dalmacyali-orkide-2dal',
                '2li-mavi-orkide-tasarim',
                '2li-pembe-orkide-dogum-gunu',
            ],
            default => [],
        };
    }

    /**
     * @return list<string>
     */
    public static function categoryCoverProductSlugs(?string $categorySlug): array
    {
        return CatalogTaxonomy::coverProductSlugs($categorySlug);
    }

    /**
     * @return list<string>
     */
    public static function specialOccasionProductSlugs(?string $occasionSlug): array
    {
        return CatalogTaxonomy::specialOccasionFallbackProductSlugs($occasionSlug);
    }

    /**
     * @param  list<string>  $slugs
     */
    public static function firstExistingStorageProductsPath(array $slugs): ?string
    {
        foreach ($slugs as $slug) {
            $slug = trim((string) $slug, '/');
            if ($slug === '') {
                continue;
            }
            foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                $relative = 'products/'.$slug.'.'.$ext;
                if (is_file(storage_path('app/public/'.$relative))) {
                    return 'storage/'.$relative;
                }
            }
        }

        return null;
    }

    /**
     * Blog seed / varsayılan featured_image satırı (dosya henüz yoksa bile tutarlı yol).
     *
     * @param  list<string>  $slugs
     */
    public static function preferredStorageProductsPath(array $slugs): ?string
    {
        $existing = self::firstExistingStorageProductsPath($slugs);
        if ($existing !== null) {
            return $existing;
        }

        foreach ($slugs as $slug) {
            $slug = trim((string) $slug, '/');
            if ($slug !== '') {
                return 'storage/products/'.$slug.'.jpg';
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $definition
     * @return array<string, string>
     */
    private static function catalogAliases(string $catalogKey, array $definition): array
    {
        $aliases = [
            self::normalizeIncomingKey(pathinfo($catalogKey, PATHINFO_FILENAME)) => 'catalog_filename',
            self::normalizeIncomingKey((string) data_get($definition, 'slug', '')) => 'slug',
            self::normalizeIncomingKey((string) data_get($definition, 'name.tr', '')) => 'name_tr',
            self::normalizeIncomingKey((string) data_get($definition, 'name.en', '')) => 'name_en',
        ];

        return array_filter($aliases, static fn (string $key): bool => $key !== '');
    }

    private static function normalizeIncomingKey(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $value = \Illuminate\Support\Str::of($value)
            ->ascii()
            ->lower()
            ->replace(['_', '-', '.', ',', "'", '"', '(', ')', '[', ']', '{', '}', '/', '\\', '+', '&'], ' ')
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->value();

        return $value;
    }
}
