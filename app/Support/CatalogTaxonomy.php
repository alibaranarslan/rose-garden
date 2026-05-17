<?php

namespace App\Support;

use Illuminate\Support\Str;

final class CatalogTaxonomy
{
    /**
     * @return list<array{
     *     slug:string,
     *     parent_slug:?string,
     *     sort_order:int,
     *     name:array{tr:string,en:string,ku:string},
     *     description:array{tr:string,en:string,ku:string},
     *     cover_product_slugs:list<string>
     * }>
     */
    public static function categoryBlueprints(): array
    {
        return [
            self::category(
                'cicek-buketleri',
                null,
                10,
                ['tr' => 'Çiçek Buketleri', 'en' => 'Flower Bouquets', 'ku' => 'Destegulên Kulilkan'],
                [
                    'tr' => 'Gül, mevsim ve premium çizgide hazırlanan canlı buket seçkileri.',
                    'en' => 'A live bouquet selection spanning roses, seasonal stems and premium designs.',
                    'ku' => 'Hilbijartinek buketên taze yên ji gul, demsal û sêwiranên premium pêk tê.',
                ],
                ['rustik-kirmizi-gul-pamuk-hediye-buket', 'altin-zarafet-karisik-buket']
            ),
            self::category(
                'gul-buketleri',
                'cicek-buketleri',
                11,
                ['tr' => 'Gül Buketleri', 'en' => 'Rose Bouquets', 'ku' => 'Destegulên Gulan'],
                [
                    'tr' => 'Romantik, gösterişli ve yıldönümüne uygun gül tasarımları.',
                    'en' => 'Romantic and statement-ready bouquet designs centered on roses.',
                    'ku' => 'Sêwiranên romantîk û balkêş ên ku li ser gulan disekinin.',
                ],
                ['premium-kirmizi-gul-cipso-kubbe', 'asil-ask-siyah-kagit-gul-buket']
            ),
            self::category(
                'karisik-buketler',
                'cicek-buketleri',
                12,
                ['tr' => 'Karışık Buketler', 'en' => 'Mixed Bouquets', 'ku' => 'Destegulên Tevlihev'],
                [
                    'tr' => 'Gerbera, krizantem, lale ve mevsim çiçekleriyle hazırlanan renkli buketler.',
                    'en' => 'Colorful bouquets built with gerberas, chrysanthemums, tulips and seasonal stems.',
                    'ku' => 'Buketên rengîn ên ji gerbera, krizantem, lale û kulilkên demsalê pêk tên.',
                ],
                ['mor-ruya-karisik-buket', 'pastel-gerbera-krizantem-buket']
            ),
            self::category(
                'premium-buketler',
                'cicek-buketleri',
                13,
                ['tr' => 'Premium Buketler', 'en' => 'Premium Bouquets', 'ku' => 'Destegulên Premium'],
                [
                    'tr' => 'Hacimli, katmanlı ve hediye etkisi yüksek özel buket kompozisyonları.',
                    'en' => 'Layered, high-volume bouquet compositions with a stronger gift impact.',
                    'ku' => 'Kompozîsyonên taybet ên buketê yên qelew û bandora diyariyê zêdetir.',
                ],
                ['jumbo-kirmizi-gul-buket-beyaz-kagit', 'kral-kirmizi-gul-cipso-siyah-altin']
            ),
            self::category(
                'zambakli-buketler',
                'cicek-buketleri',
                14,
                ['tr' => 'Zambaklı Buketler', 'en' => 'Lily Bouquets', 'ku' => 'Destegulên Zembaqan'],
                [
                    'tr' => 'Zambak ve lilya karakteriyle daha zarif, tören uyumlu buketler.',
                    'en' => 'Elegant bouquets with lily character, ideal for ceremonies and refined gestures.',
                    'ku' => 'Buketên nazik ên bi karaktera zembaqê, ji bo merasim û jestên rafine guncaw.',
                ],
                ['pembe-zambak-gul-buket', 'beyaz-zambak-turuncu-gunes-buket']
            ),
            self::category(
                'saksi-cicekleri',
                null,
                20,
                ['tr' => 'Saksı Bitkileri', 'en' => 'Potted Plants', 'ku' => 'Nebatên Saksiyê'],
                [
                    'tr' => 'Ev, ofis ve kalıcı jestler için hazırlanan yaşayan bitki koleksiyonları.',
                    'en' => 'Living plant collections prepared for homes, offices and lasting gestures.',
                    'ku' => 'Koleksiyonên nebatên zindî ji bo mal, ofîs û jestên mayîndar.',
                ],
                ['2li-beyaz-orkide', 'orgulu-benjamin-ficus-premium']
            ),
            self::category(
                'orkideler',
                'saksi-cicekleri',
                21,
                ['tr' => 'Orkideler', 'en' => 'Orchids', 'ku' => 'Orkîde'],
                [
                    'tr' => 'Tekli, çift dallı ve renk varyasyonlu orkide seçkileri.',
                    'en' => 'Single and double stem orchid designs in multiple color expressions.',
                    'ku' => 'Hilbijartinên orkîdeyê yên yekdalî û dudalî bi rengên cihêreng.',
                ],
                ['2li-beyaz-orkide', '2li-mor-orkide-a']
            ),
            self::category(
                'dracaena-yukka',
                'saksi-cicekleri',
                22,
                ['tr' => 'Dracaena & Yukka', 'en' => 'Dracaena & Yucca', 'ku' => 'Dracaena û Yucca'],
                [
                    'tr' => 'Dikey formu güçlü, ofis ve giriş alanına uygun yeşil bitkiler.',
                    'en' => 'Vertical green plants that work well in offices and entry areas.',
                    'ku' => 'Nebatên kesk ên formên xurt ku ji bo ofîs û dergehê guncaw in.',
                ],
                ['2li-dracaena-marginata-a', '2li-yukka']
            ),
            self::category(
                'tropik-yesil-bitkiler',
                'saksi-cicekleri',
                23,
                ['tr' => 'Tropik Yeşil Bitkiler', 'en' => 'Tropical Green Plants', 'ku' => 'Nebatên Kesk ên Tropîk'],
                [
                    'tr' => 'Areka, benjamin ve büyük yeşil türlerle hacimli iç mekân bitkileri.',
                    'en' => 'Lush indoor plants built around areca, ficus and larger tropical greens.',
                    'ku' => 'Nebatên navmalê yên qelew bi areka, ficus û cureyên tropîk ên mezin.',
                ],
                ['orgulu-benjamin-ficus-premium', 'buyuk-tropik-bitki']
            ),
            self::category(
                'cicekli-saksilar',
                'saksi-cicekleri',
                24,
                ['tr' => 'Çiçekli Saksılar', 'en' => 'Blooming Potted Plants', 'ku' => 'Saksiyên Bi Kulilk'],
                [
                    'tr' => 'Antoryum, guzmanya ve siklamen gibi çiçekli saksı alternatifleri.',
                    'en' => 'Blooming potted choices such as anthurium, guzmania and cyclamen.',
                    'ku' => 'Alternatîfên saksiyê yên bi kulilk wek anthurium, guzmania û cyclamen.',
                ],
                ['guzmanya-saksi', 'ithal-antoryum-saksi']
            ),
            self::category(
                'sarmasik-bitkileri',
                'saksi-cicekleri',
                25,
                ['tr' => 'Sarmaşık Bitkileri', 'en' => 'Trailing Plants', 'ku' => 'Nebatên Pêçayî'],
                [
                    'tr' => 'Patos ve örgülü sarmaşık karakteri taşıyan kolay bakım seçenekleri.',
                    'en' => 'Easy-care trailing plant options centered on pothos varieties.',
                    'ku' => 'Hilbijartinên hêsan ên lênihêrînê ku li ser cûreyên pothos disekinin.',
                ],
                ['patos-sarmaşık-saksi', 'orgulu-patos']
            ),
        ];
    }

    /**
     * @return list<string>
     */
    public static function navigationCategorySlugs(): array
    {
        return [
            'cicek-buketleri',
            'gul-buketleri',
            'karisik-buketler',
            'premium-buketler',
            'zambakli-buketler',
            'saksi-cicekleri',
            'orkideler',
            'dracaena-yukka',
            'tropik-yesil-bitkiler',
            'cicekli-saksilar',
            'sarmasik-bitkileri',
        ];
    }

    /**
     * @return list<string>
     */
    public static function homeSpotlightCategorySlugs(): array
    {
        return [
            'gul-buketleri',
            'karisik-buketler',
            'premium-buketler',
            'orkideler',
            'tropik-yesil-bitkiler',
            'cicekli-saksilar',
        ];
    }

    /**
     * @return list<string>
     */
    public static function coverProductSlugs(?string $categorySlug): array
    {
        foreach (self::categoryBlueprints() as $blueprint) {
            if ($blueprint['slug'] === $categorySlug) {
                return $blueprint['cover_product_slugs'];
            }
        }

        return [];
    }

    /**
     * @param  array<string, mixed>  $definition
     * @return list<string>
     */
    public static function assignCategorySlugs(array $definition): array
    {
        $categorySlug = (string) ($definition['category_slug'] ?? '');
        $name = self::normalizedText((string) data_get($definition, 'name.tr', ''));
        $tags = collect((array) ($definition['tags'] ?? []))
            ->map(fn ($tag) => self::normalizedText((string) $tag))
            ->filter()
            ->values()
            ->all();

        $slugs = [];

        if ($categorySlug !== '') {
            $slugs[] = $categorySlug;
        }

        if ($categorySlug === 'cicek-buketleri') {
            if (in_array('guller', $tags, true) || Str::contains($name, 'gul')) {
                $slugs[] = 'gul-buketleri';
            }

            if (
                in_array('mevsimlik', $tags, true)
                || Str::contains($name, ['karisik', 'gerbera', 'krizantem', 'nergis', 'aycicegi', 'lale'])
            ) {
                $slugs[] = 'karisik-buketler';
            }

            if (in_array('luks', $tags, true) || Str::contains($name, ['premium', 'jumbo', 'mega', 'kral'])) {
                $slugs[] = 'premium-buketler';
            }

            if (Str::contains($name, ['zambak', 'lilya'])) {
                $slugs[] = 'zambakli-buketler';
            }
        }

        if ($categorySlug === 'saksi-cicekleri') {
            if (Str::contains($name, 'orkide')) {
                $slugs[] = 'orkideler';
            }

            if (Str::contains($name, ['dracaena', 'yukka', 'seflera'])) {
                $slugs[] = 'dracaena-yukka';
            }

            if (Str::contains($name, ['benjamin', 'ficus', 'areca', 'palm', 'tropik'])) {
                $slugs[] = 'tropik-yesil-bitkiler';
            }

            if (Str::contains($name, ['guzmania', 'antoryum', 'siklamen'])) {
                $slugs[] = 'cicekli-saksilar';
            }

            if (Str::contains($name, ['patos', 'sarmasik'])) {
                $slugs[] = 'sarmasik-bitkileri';
            }
        }

        $known = collect(self::categoryBlueprints())->pluck('slug')->all();

        return collect($slugs)
            ->filter(fn ($slug) => in_array($slug, $known, true))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $definition
     * @return list<string>
     */
    public static function assignOccasionSlugs(array $definition): array
    {
        $tags = collect((array) ($definition['tags'] ?? []))
            ->map(fn ($tag) => self::normalizedText((string) $tag))
            ->filter()
            ->values()
            ->all();
        $name = self::normalizedText((string) data_get($definition, 'name.tr', ''));
        $categorySlug = (string) ($definition['category_slug'] ?? '');

        $slugs = [];

        if (in_array('sevgililer gunu', $tags, true) || (in_array('romantik', $tags, true) && (in_array('guller', $tags, true) || Str::contains($name, 'gul')))) {
            $slugs[] = 'sevgililer-gunu';
        }

        if (in_array('anneler gunu', $tags, true) || Str::contains($name, ['zambak', 'orkide'])) {
            $slugs[] = 'anneler-gunu';
        }

        if (
            in_array('dogum gunu', $tags, true)
            || in_array('bebek', $tags, true)
            || in_array('ekonomik', $tags, true)
        ) {
            $slugs[] = 'ogretmenler-gunu';
        }

        if ($categorySlug === 'saksi-cicekleri' || Str::contains($name, ['yukka', 'dracaena', 'ficus', 'palm', 'tropik'])) {
            $slugs[] = 'babalar-gunu';
        }

        if (
            in_array('yeni', $tags, true)
            || in_array('ozel gun', $tags, true)
            || in_array('luks', $tags, true)
        ) {
            $slugs[] = 'yilbasi';
        }

        if (in_array('mevsimlik', $tags, true) || in_array('yeni', $tags, true)) {
            $slugs[] = 'bahar-kampanyasi';
        }

        return collect($slugs)->unique()->values()->all();
    }

    /**
     * @return list<string>
     */
    public static function specialOccasionFallbackProductSlugs(?string $occasionSlug): array
    {
        return match ($occasionSlug) {
            'sevgililer-gunu' => [
                'asil-ask-siyah-kagit-gul-buket',
                'premium-kirmizi-gul-cipso-kubbe',
                'kalbin-ortasi-pembe-gul-buket',
            ],
            'anneler-gunu' => [
                'pembe-zambak-gul-buket',
                '2li-pembe-orkide-dogum-gunu',
                'lila-krizantem-zerafet-buket',
            ],
            'babalar-gunu' => [
                'orgulu-benjamin-ficus-premium',
                'buyuk-tropik-bitki',
                '2li-yukka',
            ],
            'ogretmenler-gunu' => [
                'pastel-gerbera-krizantem-buket',
                'mor-ruya-karisik-buket',
                'siklamen-saksi',
            ],
            'yilbasi' => [
                'altin-zarafet-karisik-buket',
                'kis-masali-ayi-hediye-buket',
                'jumbo-kirmizi-gul-buket-beyaz-kagit',
            ],
            'bahar-kampanyasi' => [
                'bahar-nergis-cipso-buket',
                'aycicegi-lale-bahar-buket',
                'lavanta-bahar-karisik-buket',
            ],
            default => [],
        };
    }

    /**
     * @return array{
     *     slug:string,
     *     parent_slug:?string,
     *     sort_order:int,
     *     name:array{tr:string,en:string,ku:string},
     *     description:array{tr:string,en:string,ku:string},
     *     cover_product_slugs:list<string>
     * }
     */
    private static function category(
        string $slug,
        ?string $parentSlug,
        int $sortOrder,
        array $name,
        array $description,
        array $coverProductSlugs
    ): array {
        return [
            'slug' => $slug,
            'parent_slug' => $parentSlug,
            'sort_order' => $sortOrder,
            'name' => $name,
            'description' => $description,
            'cover_product_slugs' => $coverProductSlugs,
        ];
    }

    private static function normalizedText(string $value): string
    {
        $value = Str::of($value)
            ->ascii()
            ->lower()
            ->replace(['’', "'", '—', '–'], ' ')
            ->replace(['&', '/'], ' ')
            ->squish()
            ->value();

        return trim($value);
    }
}
