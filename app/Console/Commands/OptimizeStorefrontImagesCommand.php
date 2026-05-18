<?php

namespace App\Console\Commands;

use App\Support\StorefrontImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class OptimizeStorefrontImagesCommand extends Command
{
    protected $signature = 'storefront:optimize-images
                            {--widths=320,480,640,960,1280 : Comma-separated target widths}
                            {--quality=78 : WebP quality, 1-100}
                            {--force : Regenerate existing optimized files}';

    protected $description = 'Generate lightweight WebP variants for storefront storage images.';

    public function handle(): int
    {
        if (! extension_loaded('gd') || ! function_exists('imagewebp')) {
            $this->error('PHP GD with WebP support is required.');

            return self::FAILURE;
        }

        $widths = collect(explode(',', (string) $this->option('widths')))
            ->map(fn (string $width): int => (int) trim($width))
            ->filter(fn (int $width): bool => $width >= 80 && $width <= 2560)
            ->unique()
            ->sort()
            ->values();

        if ($widths->isEmpty()) {
            $this->error('No valid widths were provided.');

            return self::FAILURE;
        }

        $quality = max(1, min(100, (int) $this->option('quality')));
        $force = (bool) $this->option('force');
        $base = storage_path('app/public');
        $patterns = [
            $base.DIRECTORY_SEPARATOR.'products'.DIRECTORY_SEPARATOR.'*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}',
            $base.DIRECTORY_SEPARATOR.'categories'.DIRECTORY_SEPARATOR.'*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}',
            $base.DIRECTORY_SEPARATOR.'blog'.DIRECTORY_SEPARATOR.'*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}',
        ];

        $files = [];
        foreach ($patterns as $pattern) {
            foreach (glob($pattern, GLOB_BRACE) ?: [] as $file) {
                if (is_file($file)) {
                    $files[] = $file;
                }
            }
        }

        $files = array_values(array_unique($files));
        sort($files);

        $generated = 0;
        $skipped = 0;
        $failed = 0;
        $unreadable = 0;

        foreach ($files as $file) {
            $relative = Str::of($file)
                ->replace('\\', '/')
                ->after(str_replace('\\', '/', $base).'/')
                ->toString();

            $source = $this->loadImage($file);
            if (! $source instanceof \GdImage) {
                $unreadable++;
                $this->warn('Cannot read image: '.$relative);

                continue;
            }

            $sourceWidth = imagesx($source);
            $sourceHeight = imagesy($source);
            if ($sourceWidth < 1 || $sourceHeight < 1) {
                imagedestroy($source);
                $unreadable++;

                continue;
            }

            foreach ($widths as $width) {
                $targetWidth = min($width, $sourceWidth);
                $targetRelative = StorefrontImage::optimizedStorageRelativePath($relative, $targetWidth);
                $targetPath = $base.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $targetRelative);

                if (! $force && is_file($targetPath)) {
                    $skipped++;

                    continue;
                }

                File::ensureDirectoryExists(dirname($targetPath));
                $targetHeight = max(1, (int) round($sourceHeight * ($targetWidth / $sourceWidth)));
                $target = imagecreatetruecolor($targetWidth, $targetHeight);
                imagealphablending($target, false);
                imagesavealpha($target, true);
                imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

                if (! imagewebp($target, $targetPath, $quality)) {
                    $failed++;
                    imagedestroy($target);

                    continue;
                }

                imagedestroy($target);
                $generated++;
            }

            imagedestroy($source);
        }

        $this->info("Optimized image variants generated={$generated}, skipped={$skipped}, unreadable={$unreadable}, failed={$failed}");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function loadImage(string $path): ?\GdImage
    {
        $data = @file_get_contents($path);
        if ($data === false) {
            return null;
        }

        $image = @imagecreatefromstring($data);
        if (! $image instanceof \GdImage) {
            return null;
        }

        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        return $image;
    }
}
