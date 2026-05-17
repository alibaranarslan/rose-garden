<?php

namespace App\Console\Commands;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\HeaderTheme;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SpecialOccasion;
use App\Models\Tag;
use App\Support\StorefrontLocale;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Spatie\Translatable\HasTranslations;

class StorefrontLocaleAuditCommand extends Command
{
    protected $signature = 'storefront:locale-audit
        {--json : Print machine-readable JSON}
        {--fail-on-missing : Return a non-zero exit code when missing translations are detected}';

    protected $description = 'Audit storefront EN/KU translation keys and translatable content coverage.';

    /**
     * @var array<int, class-string<Model>>
     */
    private array $translatableModels = [
        Product::class,
        ProductVariant::class,
        Category::class,
        Tag::class,
        SpecialOccasion::class,
        BlogPost::class,
        BlogCategory::class,
        Page::class,
        HeaderTheme::class,
    ];

    public function handle(): int
    {
        $report = [
            'locales' => StorefrontLocale::codes(),
            'translation_keys' => $this->auditTranslationKeys(),
            'content_records' => $this->auditContentRecords(),
        ];

        $report['summary'] = [
            'missing_key_count' => count($report['translation_keys']['missing']),
            'missing_content_field_count' => count($report['content_records']['missing']),
        ];

        if ($this->option('json')) {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Missing storefront translation keys', $report['summary']['missing_key_count']],
                    ['Missing EN/KU content fields', $report['summary']['missing_content_field_count']],
                ]
            );

            if ($report['summary']['missing_key_count'] > 0) {
                $this->warn('Missing translation keys:');
                foreach (array_slice($report['translation_keys']['missing'], 0, 30) as $missing) {
                    $this->line(sprintf(
                        '- [%s] %s (%s)',
                        $missing['locale'],
                        $missing['key'],
                        implode(', ', $missing['files'])
                    ));
                }
            }

            if ($report['summary']['missing_content_field_count'] > 0) {
                $this->warn('Missing translatable content fields:');
                foreach (array_slice($report['content_records']['missing'], 0, 30) as $missing) {
                    $this->line(sprintf(
                        '- [%s] %s#%s.%s (%s)',
                        $missing['locale'],
                        $missing['model'],
                        $missing['id'],
                        $missing['field'],
                        $missing['label']
                    ));
                }
            }
        }

        return $this->option('fail-on-missing')
            && ($report['summary']['missing_key_count'] > 0 || $report['summary']['missing_content_field_count'] > 0)
                ? self::FAILURE
                : self::SUCCESS;
    }

    /**
     * @return array{scanned:int, missing:list<array{locale:string,key:string,files:list<string>}>}
     */
    private function auditTranslationKeys(): array
    {
        $keys = [];

        foreach ($this->sourceFiles() as $file) {
            $relative = str_replace('\\', '/', $file->getPathname());
            $relative = str_replace(str_replace('\\', '/', base_path()).'/', '', $relative);
            $contents = File::get($file->getPathname());

            preg_match_all('/__\(\s*([\'"])((?:\\\\.|(?!\1).)*?)\1/ms', $contents, $matches);

            foreach ($matches[2] as $rawKey) {
                $key = stripcslashes($rawKey);
                $keys[$key] ??= [];
                $keys[$key][$relative] = true;
            }
        }

        $missing = [];

        foreach ($keys as $key => $files) {
            foreach (['en', 'ku'] as $locale) {
                if ($this->translationExists($key, $locale)) {
                    continue;
                }

                $missing[] = [
                    'locale' => $locale,
                    'key' => $key,
                    'files' => array_keys($files),
                ];
            }
        }

        return [
            'scanned' => count($keys),
            'missing' => $missing,
        ];
    }

    /**
     * @return list<SplFileInfo>
     */
    private function sourceFiles(): array
    {
        $files = [];
        $roots = [
            resource_path('views'),
            app_path('Livewire'),
            app_path('Http/Controllers'),
        ];

        foreach ($roots as $root) {
            if (! is_dir($root)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

            /** @var SplFileInfo $file */
            foreach ($iterator as $file) {
                if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.php')) {
                    continue;
                }

                $normalized = str_replace('\\', '/', $file->getPathname());

                if (str_contains($normalized, '/resources/views/admin/')
                    || str_contains($normalized, '/resources/views/filament/')
                    || str_contains($normalized, '/resources/views/emails/')) {
                    continue;
                }

                $files[] = $file;
            }
        }

        return $files;
    }

    private function translationExists(string $key, string $locale): bool
    {
        if (Lang::hasForLocale($key, $locale)) {
            return true;
        }

        $jsonPath = lang_path($locale.'.json');
        $json = File::exists($jsonPath)
            ? json_decode(File::get($jsonPath), true)
            : [];

        return is_array($json) && array_key_exists($key, $json);
    }

    /**
     * @return array{missing:list<array{locale:string,model:string,id:int|string,field:string,label:string}>}
     */
    private function auditContentRecords(): array
    {
        $missing = [];

        foreach ($this->translatableModels as $modelClass) {
            if (! class_exists($modelClass)) {
                continue;
            }

            /** @var Model&HasTranslations $prototype */
            $prototype = new $modelClass;

            if (! property_exists($prototype, 'translatable')) {
                continue;
            }

            try {
                $modelClass::query()
                    ->select(['id', ...$prototype->translatable])
                    ->chunkById(100, function ($records) use (&$missing, $modelClass, $prototype): void {
                        foreach ($records as $record) {
                            foreach ($prototype->translatable as $field) {
                                foreach (['en', 'ku'] as $locale) {
                                    $value = $record->getTranslation($field, $locale, false);

                                    if ($this->hasLocalizedValue($value)) {
                                        continue;
                                    }

                                    $missing[] = [
                                        'locale' => $locale,
                                        'model' => class_basename($modelClass),
                                        'id' => $record->getKey(),
                                        'field' => $field,
                                        'label' => $this->recordLabel($record),
                                    ];
                                }
                            }
                        }
                    });
            } catch (\Throwable $exception) {
                $missing[] = [
                    'locale' => 'audit',
                    'model' => class_basename($modelClass),
                    'id' => '-',
                    'field' => 'query',
                    'label' => $exception->getMessage(),
                ];
            }
        }

        return ['missing' => $missing];
    }

    private function hasLocalizedValue(mixed $value): bool
    {
        if (is_array($value)) {
            return collect($value)->filter(fn ($item) => filled($item))->isNotEmpty();
        }

        return filled($value);
    }

    private function recordLabel(Model $record): string
    {
        foreach (['slug', 'name', 'title'] as $attribute) {
            $value = $record->getAttribute($attribute);

            if (is_array($value)) {
                $value = $value['tr'] ?? $value['en'] ?? reset($value);
            }

            if (filled($value)) {
                return (string) $value;
            }
        }

        return class_basename($record).'#'.$record->getKey();
    }
}
