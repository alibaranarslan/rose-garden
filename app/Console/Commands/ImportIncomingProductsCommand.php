<?php

namespace App\Console\Commands;

use App\Data\IncomingProductCatalogLoader;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SpecialOccasion;
use App\Models\Tag;
use App\Support\CatalogTaxonomy;
use App\Support\ProductHighlightPreset;
use App\Support\StorefrontIncomingAssets;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ImportIncomingProductsCommand extends Command
{
    protected $signature = 'products:import-incoming
                            {--dry-run : Sadece rapor; veritabanı ve dosya kopyası yok}
                            {--force : Onay sorusunu atla}
                            {--incoming-dir= : Görsel klasörü (varsayılan: storage/app/product-import/incoming)}
                            {--catalog= : JSON katalog dosyası; verilmezse varsa catalog.json, yoksa PHP tanımları}';

    protected $description = 'storage/app/product-import/incoming görsellerinden yerel lansman kataloğunu senkronize eder';

    public function handle(): int
    {
        $incomingDir = $this->option('incoming-dir') ?: storage_path('app/product-import/incoming');

        try {
            $catalogOption = $this->option('catalog');
            $catalog = IncomingProductCatalogLoader::load(
                is_string($catalogOption) && $catalogOption !== '' ? $catalogOption : null
            );
        } catch (\InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $files = glob($incomingDir.DIRECTORY_SEPARATOR.'*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [];
        $files = array_values(array_filter($files, fn (string $path) => is_file($path)));
        sort($files);

        if ($files === []) {
            $this->warn('incoming klasöründe görsel yok: '.$incomingDir);

            return self::SUCCESS;
        }

        $classification = StorefrontIncomingAssets::classifyIncomingFiles($files, $catalog);
        $matched = $classification['matched'];
        $ambiguous = $classification['ambiguous'];
        $unmatched = $classification['unmatched'];
        $matchedBasenames = array_map(static fn (array $item): string => $item['basename'], $matched);
        $missingFiles = array_values(array_diff(array_keys($catalog), $matchedBasenames));

        $rows = [];
        foreach ($matched as $item) {
            $definition = $item['definition'];
            $extension = strtolower(pathinfo($item['basename'], PATHINFO_EXTENSION));
            $destination = 'storage/products/'.$definition['slug'].'.'.$extension;
            $rows[] = [
                $item['basename'],
                $definition['slug'],
                $item['match_source'],
                $definition['category_slug'],
                $definition['price'],
                $destination,
            ];
        }

        $catalogSlugs = array_map(
            static fn (array $row): string => (string) $row[1],
            $rows
        );

        $this->table(['Dosya', 'Slug', 'Eşleşme', 'Kategori', 'Fiyat (TRY)', 'Hedef yol'], $rows);

        if ($ambiguous !== []) {
            $this->warn('Belirsiz eşleşmeler otomatik işlenmedi:');
            $this->table(
                ['Dosya', 'Adaylar', 'Match key'],
                array_map(
                    static fn (array $item): array => [
                        $item['basename'],
                        implode(', ', $item['candidates']),
                        $item['match_key'],
                    ],
                    $ambiguous
                )
            );
        }

        if ($unmatched !== []) {
            $this->warn('Eşleşmeyen incoming dosyalar bırakıldı:');
            $this->line(implode("\n", array_map(
                static fn (array $item): string => $item['basename'],
                $unmatched
            )));
        }

        if ($missingFiles !== []) {
            $this->warn('Katalogda var ama klasörde bulunmayan dosyalar:');
            $this->line(implode("\n", $missingFiles));
        }

        if ($matched === []) {
            $this->warn('Güvenli eşleşme bulunamadı; veri yazılmadı.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->info('Dry-run: '.count($matched).' güvenli ürün işlenecek.');

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm(count($matched).' güvenli ürün içe aktarılsın mı? Katalog dışı ürünler inactive yapılacak.', false)) {
            $this->warn('İptal.');

            return self::FAILURE;
        }

        $categoryIds = Category::query()->pluck('id', 'slug')->all();
        $occasionIds = SpecialOccasion::query()->pluck('id', 'slug')->all();
        $requiredCategorySlugs = collect($catalog)
            ->flatMap(fn (array $definition) => CatalogTaxonomy::assignCategorySlugs($definition))
            ->unique()
            ->values();

        foreach ($requiredCategorySlugs as $slug) {
            if (! isset($categoryIds[$slug])) {
                $this->error("Kategori bulunamadı: {$slug} — önce kategori seed'i veya panel kaydı gerekli.");

                return self::FAILURE;
            }
        }

        $publicProductsDirectory = storage_path('app/public/products');
        File::ensureDirectoryExists($publicProductsDirectory);

        DB::transaction(function () use ($matched, $catalogSlugs, $publicProductsDirectory, $categoryIds, $occasionIds): void {
            Product::query()
                ->whereNotIn('slug', $catalogSlugs)
                ->update(['status' => 'inactive']);

            $order = 0;

            foreach ($matched as $item) {
                $basename = $item['basename'];
                $definition = $item['definition'];
                $order++;

                $sourcePath = $item['source_path'];
                $extension = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
                $destinationPath = $publicProductsDirectory.DIRECTORY_SEPARATOR.$definition['slug'].'.'.$extension;

                File::copy($sourcePath, $destinationPath);

                $existing = Product::withTrashed()->where('slug', $definition['slug'])->first();
                $generatedSku = 'RG-'.strtoupper(substr(preg_replace('/[^a-z0-9]/', '', $definition['slug']), 0, 10)).'-'.substr(sha1($definition['slug']), 0, 4);
                $highlights = $existing?->getTranslations('product_highlights') ?? [];

                if ($highlights === [] || collect($highlights)->flatten(1)->isEmpty()) {
                    $highlights = ProductHighlightPreset::forCategory($definition['category_slug']);
                }

                $payload = [
                    'name' => $definition['name'],
                    'slug' => $definition['slug'],
                    'short_description' => $definition['short_description'],
                    'description' => $definition['description'],
                    'sku' => $existing?->sku ?: $generatedSku,
                    'price' => $definition['price'],
                    'sale_price' => null,
                    'stock_status' => 'in_stock',
                    'status' => 'active',
                    'is_featured' => (bool) ($definition['is_featured'] ?? false),
                    'is_new' => true,
                    'delivery_note' => $definition['delivery_note'],
                    'product_highlights' => $highlights,
                    'meta_title' => $definition['meta_title'],
                    'meta_description' => $definition['meta_description'],
                    'sort_order' => $order,
                ];

                if ($existing) {
                    $existing->fill($payload);

                    if ($existing->trashed()) {
                        $existing->restore();
                    }

                    $existing->save();
                    $product = $existing;
                } else {
                    $product = Product::query()->create($payload);
                }

                $product->images()->delete();
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'image_path' => 'storage/products/'.$definition['slug'].'.'.$extension,
                    'alt_text' => $definition['name']['tr'],
                    'is_primary' => true,
                    'sort_order' => 1,
                ]);

                $productCategoryIds = collect(CatalogTaxonomy::assignCategorySlugs($definition))
                    ->map(fn (string $slug) => $categoryIds[$slug] ?? null)
                    ->filter()
                    ->values()
                    ->all();

                if ($productCategoryIds === []) {
                    $productCategoryIds = [$categoryIds[$definition['category_slug']]];
                }

                $product->categories()->sync($productCategoryIds);

                $tagIds = [];
                foreach ($definition['tags'] as $tagLabelTr) {
                    $tagSlug = Str::slug($tagLabelTr, '-', 'tr');
                    $tag = Tag::query()->firstOrCreate(
                        ['slug' => $tagSlug],
                        ['name' => ['tr' => $tagLabelTr, 'en' => $tagLabelTr, 'ku' => $tagLabelTr]]
                    );
                    $tagIds[] = $tag->id;
                }

                $product->tags()->sync($tagIds);
                $product->specialOccasions()->sync(
                    collect(CatalogTaxonomy::assignOccasionSlugs($definition))
                        ->map(fn (string $slug) => $occasionIds[$slug] ?? null)
                        ->filter()
                        ->values()
                        ->all()
                );
                $product->ensurePrimaryImage();
            }
        });

        $this->info('Tamamlandı: '.count($matched).' ürün yerel katalogla senkronize edildi.');

        return self::SUCCESS;
    }
}
