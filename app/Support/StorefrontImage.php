<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Product;
use Illuminate\Support\Str;

final class StorefrontImage
{
    public static function resolveProduct(
        ?string $path,
        ?string $slug = null,
        ?string $name = null,
        string $fallback = 'images/product-placeholder.svg'
    ): string {
        $clean = self::stripBlockedRemote($path);

        if (($clean === null || $clean === '') && $slug) {
            $incoming = StorefrontIncomingAssets::firstExistingStorageProductsPath([$slug]);
            if ($incoming !== null) {
                return self::normalizeLocalPath($incoming, $fallback);
            }
        }

        if ($clean === null || $clean === '') {
            return self::webPublicUrl($fallback);
        }

        if (Str::startsWith($clean, ['http://', 'https://'])) {
            return self::isAllowedProductAbsoluteUrl($clean)
                ? self::publicImgSrc($clean)
                : self::webPublicUrl($fallback);
        }

        if (self::isProductStorageImagePath($clean) && self::storagePublicFileMissing($clean) && $slug) {
            $incoming = StorefrontIncomingAssets::firstExistingStorageProductsPath([$slug]);
            if ($incoming !== null) {
                return self::normalizeLocalPath($incoming, $fallback);
            }
        }

        if (self::storageBackedLocalPath($clean) !== null && self::storagePublicFileMissing($clean)) {
            return self::webPublicUrl($fallback);
        }

        if ($slug && self::publicImagesFileMissing($clean)) {
            $incoming = StorefrontIncomingAssets::firstExistingStorageProductsPath([$slug]);
            if ($incoming !== null) {
                return self::normalizeLocalPath($incoming, $fallback);
            }

            return self::webPublicUrl($fallback);
        }

        return self::normalizeLocalPath($clean, $fallback);
    }

    public static function resolveProductWithAlternates(
        ?string $path,
        ?string $slug,
        ?string $name,
        iterable $alternates,
        string $fallback = 'images/product-placeholder.svg'
    ): string {
        $primary = self::resolveProduct($path, $slug, $name, $fallback);

        if (! self::isResolvedProductPlaceholder($primary)) {
            return $primary;
        }

        foreach ($alternates as $product) {
            if (! $product instanceof Product) {
                continue;
            }

            $product->loadMissing(['images' => fn ($query) => $query->orderBy('sort_order')]);
            $alt = self::resolveProduct(
                $product->primaryImage,
                $product->slug,
                $product->name,
                $fallback,
            );

            if (! self::isResolvedProductPlaceholder($alt)) {
                return $alt;
            }
        }

        return $primary;
    }

    public static function resolveCategory(
        ?string $path,
        ?string $slug = null,
        ?string $name = null,
        string $fallback = 'images/placeholder.svg'
    ): string {
        $clean = self::stripBlockedRemote($path);

        if ($clean !== null && $clean !== '') {
            if (Str::startsWith($clean, ['http://', 'https://'])) {
                return self::isAllowedProductAbsoluteUrl($clean)
                    ? self::publicImgSrc($clean)
                    : self::categoryFallback($slug, $fallback);
            }

            if (self::storageBackedLocalPath($clean) !== null && self::storagePublicFileMissing($clean)) {
                return self::categoryFallback($slug, $fallback);
            }

            if (self::publicImagesFileMissing($clean)) {
                return self::categoryFallback($slug, $fallback);
            }

            return self::normalizeLocalPath($clean, $fallback);
        }

        return self::categoryFallback($slug, $fallback);
    }

    public static function resolveBlog(
        ?string $path,
        ?string $slug = null,
        ?string $title = null,
        ?string $category = null,
        string $fallback = 'images/blog/flower-care.svg'
    ): string {
        $clean = self::stripBlockedRemote($path);

        if ($clean !== null && $clean !== '') {
            if (Str::startsWith($clean, ['http://', 'https://'])) {
                return self::isAllowedProductAbsoluteUrl($clean)
                    ? self::publicImgSrc($clean)
                    : self::blogFallback($slug, $fallback);
            }

            if (self::storageBackedLocalPath($clean) !== null && self::storagePublicFileMissing($clean)) {
                return self::blogFallback($slug, $fallback);
            }

            if (self::publicImagesFileMissing($clean)) {
                return self::blogFallback($slug, $fallback);
            }

            return self::normalizeLocalPath($clean, $fallback);
        }

        $catalogPath = StorefrontIncomingAssets::firstExistingStorageProductsPath(
            StorefrontIncomingAssets::blogCoverProductSlugs($slug)
        );

        if ($catalogPath !== null) {
            return self::normalizeLocalPath($catalogPath, $fallback);
        }

        return self::blogFallback($slug, $fallback);
    }

    public static function isBlogDecorativeCover(string $url): bool
    {
        $path = Str::lower(rawurldecode((string) (parse_url($url, PHP_URL_PATH) ?? $url)));

        return Str::endsWith($path, '.svg');
    }

    public static function resolveBlogPostCoverUrl(BlogPost $post): string
    {
        $title = $post->getTranslation('title', app()->getLocale()) ?? '';
        $primary = self::resolveBlog(
            $post->featured_image,
            $post->slug,
            $title,
            $post->category?->name,
        );

        if (! self::isBlogDecorativeCover($primary)) {
            return $primary;
        }

        $post->loadMissing([
            'products' => function ($query): void {
                $query->storefrontReady()
                    ->with(['images' => fn ($imageQuery) => $imageQuery->orderBy('sort_order')]);
            },
        ]);

        foreach ($post->products as $product) {
            $resolved = self::resolveProduct(
                $product->primaryImage,
                $product->slug,
                $product->name,
            );

            if (! self::isResolvedProductPlaceholder($resolved)) {
                return $resolved;
            }
        }

        $fallbackStrip = self::productVisualStrip(1, []);

        return $fallbackStrip[0] ?? $primary;
    }

    /**
     * @return list<string>
     */
    public static function specialOccasionGallery(
        ?string $slug = null,
        ?string $name = null,
        ?string $category = null,
        ?string $categorySlug = null
    ): array {
        $urls = [];
        $seen = [];
        $append = function (string $url) use (&$urls, &$seen): void {
            if ($url === '') {
                return;
            }

            $key = Str::lower(rawurldecode((string) (parse_url($url, PHP_URL_PATH) ?? $url)));
            if ($key === '' || isset($seen[$key])) {
                return;
            }

            $seen[$key] = true;
            $urls[] = $url;
        };

        if ($slug) {
            $occasionProducts = Product::query()
                ->storefrontReady()
                ->whereHas('specialOccasions', fn ($query) => $query->where('slug', $slug))
                ->with(['images' => fn ($query) => $query->orderBy('sort_order')])
                ->orderByDesc('is_featured')
                ->orderByDesc('view_count')
                ->take(6)
                ->get();

            foreach ($occasionProducts as $product) {
                $resolved = self::resolveProduct(
                    $product->primaryImage,
                    $product->slug,
                    $product->name,
                );

                if (! self::isResolvedProductPlaceholder($resolved)) {
                    $append($resolved);
                }

                if (count($urls) >= 3) {
                    return array_slice($urls, 0, 3);
                }
            }
        }

        $slugCandidates = array_values(array_unique(array_merge(
            StorefrontIncomingAssets::specialOccasionProductSlugs($slug),
            StorefrontIncomingAssets::categoryCoverProductSlugs($categorySlug),
        )));

        foreach ($slugCandidates as $productSlug) {
            $stored = StorefrontIncomingAssets::firstExistingStorageProductsPath([$productSlug]);
            if ($stored !== null) {
                $append(self::normalizeLocalPath($stored, 'images/product-placeholder.svg'));
            }

            if (count($urls) >= 3) {
                return array_slice($urls, 0, 3);
            }
        }

        if ($slugCandidates !== []) {
            $dbProducts = Product::query()
                ->storefrontReady()
                ->whereIn('slug', $slugCandidates)
                ->with(['images' => fn ($query) => $query->orderBy('sort_order')])
                ->get()
                ->sortBy(fn (Product $product) => array_search($product->slug, $slugCandidates, true) ?? 999)
                ->values();

            foreach ($dbProducts as $product) {
                $resolved = self::resolveProduct(
                    $product->primaryImage,
                    $product->slug,
                    $product->name,
                );

                if (! self::isResolvedProductPlaceholder($resolved)) {
                    $append($resolved);
                }

                if (count($urls) >= 3) {
                    return array_slice($urls, 0, 3);
                }
            }
        }

        if (count($urls) < 3) {
            foreach (self::productVisualStrip(3) as $fallbackUrl) {
                if (count($urls) >= 3) {
                    break;
                }

                $append($fallbackUrl);
            }
        }

        return array_slice($urls, 0, 3);
    }

    public static function resolveSpecialOccasion(
        ?string $slug = null,
        ?string $name = null,
        ?string $category = null,
        ?string $categorySlug = null
    ): string {
        return self::specialOccasionGallery($slug, $name, $category, $categorySlug)[0]
            ?? self::productPlaceholderImgSrc();
    }

    /**
     * @return list<string>
     */
    public static function decorativeCategoryStrip(): array
    {
        return [
            self::webPublicUrl('images/categories/cicek-buketleri.svg'),
            self::webPublicUrl('images/categories/kutuda-cicekler.svg'),
            self::webPublicUrl('images/categories/hediye-setleri.svg'),
            self::webPublicUrl('images/categories/saksi-cicekleri.svg'),
            self::webPublicUrl('images/categories/cikolata-tatli.svg'),
        ];
    }

    /**
     * @param  array<int>  $excludeProductIds
     * @return list<string>
     */
    public static function productVisualStrip(int $targetCount, array $excludeProductIds = []): array
    {
        if ($targetCount < 1) {
            return [];
        }

        $exclude = array_fill_keys($excludeProductIds, true);
        $products = Product::query()
            ->storefrontReady()
            ->with(['images' => fn ($query) => $query->orderBy('sort_order')])
            ->orderByDesc('is_featured')
            ->orderByDesc('view_count')
            ->orderByDesc('updated_at')
            ->take(160)
            ->get();

        $out = [];
        $seen = [];

        foreach ($products as $product) {
            if (isset($exclude[$product->id])) {
                continue;
            }

            $rawPath = $product->primaryImage;
            $url = self::resolveProduct($rawPath, $product->slug, $product->name);

            if (self::isResolvedProductPlaceholder($url)) {
                continue;
            }

            $signature = strtolower(trim((string) $rawPath));
            if ($signature === '') {
                $signature = strtolower(rawurldecode((string) (parse_url($url, PHP_URL_PATH) ?? $url)));
            }

            if ($signature === '' || isset($seen[$signature])) {
                continue;
            }

            $seen[$signature] = true;
            $out[] = $url;

            if (count($out) >= $targetCount) {
                break;
            }
        }

        return $out;
    }

    /**
     * @param  array<int>  $excludeProductIds
     * @return list<string>
     */
    public static function decorativeOrProductStrip(int $targetCount, array $excludeProductIds = []): array
    {
        $productUrls = self::productVisualStrip($targetCount, $excludeProductIds);

        if (count($productUrls) >= $targetCount) {
            return array_slice($productUrls, 0, $targetCount);
        }

        foreach (self::decorativeCategoryStrip() as $decorativeUrl) {
            if (count($productUrls) >= $targetCount) {
                break;
            }

            $productUrls[] = $decorativeUrl;
        }

        return $productUrls;
    }

    /**
     * @return list<array{src:string,href:?string,label:string}>
     */
    public static function footerPromoVisualCards(int $count = 3): array
    {
        if ($count < 1) {
            return [];
        }

        $products = Product::query()
            ->storefrontReady()
            ->with(['images' => fn ($query) => $query->orderBy('sort_order')])
            ->orderByDesc('is_featured')
            ->orderByDesc('view_count')
            ->orderByDesc('updated_at')
            ->take(72)
            ->get();

        $cards = [];
        $seen = [];

        foreach ($products as $product) {
            $resolved = self::resolveProduct(
                $product->primaryImage,
                $product->slug,
                $product->name,
            );

            if (self::isResolvedProductPlaceholder($resolved)) {
                continue;
            }

            $pathKey = strtolower(trim((string) ($product->primaryImage ?? '')));
            if ($pathKey !== '' && isset($seen[$pathKey])) {
                continue;
            }

            if ($pathKey !== '') {
                $seen[$pathKey] = true;
            }

            $cards[] = [
                'src' => $resolved,
                'href' => url('/urun/'.$product->slug),
                'label' => (string) $product->name,
            ];

            if (count($cards) >= $count) {
                break;
            }
        }

        return $cards;
    }

    public static function resolvePath(?string $path, string $fallback = 'images/placeholder.svg'): string
    {
        return self::normalizeLocalPath($path, $fallback);
    }

    public static function productPlaceholderImgSrc(): string
    {
        return self::publicImgSrc(self::webPublicUrl('images/product-placeholder.svg'));
    }

    public static function publicImgSrc(string $url): string
    {
        if ($url === '' || Str::startsWith($url, 'data:')) {
            return $url;
        }

        if (! Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        if (! app()->runningInConsole()) {
            try {
                if (app()->bound('request') && request()) {
                    $requestHost = Str::lower((string) request()->getHost());
                    $urlHost = Str::lower((string) parse_url($url, PHP_URL_HOST));
                    $appHost = Str::lower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
                    $path = parse_url($url, PHP_URL_PATH);

                    if ($appHost !== '' && $urlHost === $appHost && $requestHost !== $urlHost && is_string($path) && $path !== '') {
                        $query = parse_url($url, PHP_URL_QUERY);
                        $fragment = parse_url($url, PHP_URL_FRAGMENT);
                        $out = $path;

                        if (is_string($query) && $query !== '') {
                            $out .= '?'.$query;
                        }

                        if (is_string($fragment) && $fragment !== '') {
                            $out .= '#'.$fragment;
                        }

                        return $out;
                    }
                }
            } catch (\Throwable) {
                // ignore
            }
        }

        if (! self::shouldUseRootRelativeImgSrc($url)) {
            return $url;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            return $url;
        }

        $query = parse_url($url, PHP_URL_QUERY);
        $fragment = parse_url($url, PHP_URL_FRAGMENT);
        $out = $path;

        if (is_string($query) && $query !== '') {
            $out .= '?'.$query;
        }

        if (is_string($fragment) && $fragment !== '') {
            $out .= '#'.$fragment;
        }

        return $out;
    }

    public static function optimizedImgSrc(string $url, int $width = 960): string
    {
        $optimized = self::optimizedPublicPath($url, $width);

        return $optimized !== null ? self::webPublicUrl($optimized) : self::publicImgSrc($url);
    }

    public static function optimizedImgSrcset(string $url, array $widths = [320, 640, 960]): string
    {
        $items = [];

        foreach ($widths as $width) {
            $width = (int) $width;
            if ($width < 80) {
                continue;
            }

            $optimized = self::optimizedPublicPath($url, $width);
            if ($optimized !== null) {
                $items[] = self::webPublicUrl($optimized).' '.$width.'w';
            }
        }

        return implode(', ', array_values(array_unique($items)));
    }

    public static function isResolvedProductPlaceholder(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?? $url;
        $path = Str::lower(rawurldecode($path));

        return Str::endsWith($path, 'product-placeholder.svg');
    }

    private static function categoryFallback(?string $slug, string $fallback): string
    {
        $incoming = StorefrontIncomingAssets::firstExistingStorageProductsPath(
            StorefrontIncomingAssets::categoryCoverProductSlugs($slug)
        );

        if ($incoming !== null) {
            return self::normalizeLocalPath($incoming, $fallback);
        }

        if ($slug && file_exists(public_path('images/categories/'.$slug.'.svg'))) {
            return self::webPublicUrl('images/categories/'.$slug.'.svg');
        }

        return self::webPublicUrl($fallback);
    }

    private static function blogFallback(?string $slug, string $fallback): string
    {
        $svg = self::blogSvgForSlug($slug);

        return self::webPublicUrl($svg ?? $fallback);
    }

    private static function shouldUseRootRelativeImgSrc(string $url): bool
    {
        $urlHost = Str::lower((string) parse_url($url, PHP_URL_HOST));
        if ($urlHost === '') {
            return false;
        }

        $appHost = Str::lower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
        if ($urlHost === $appHost) {
            return true;
        }

        if (self::isLoopbackHost($urlHost) && self::isLoopbackHost($appHost)) {
            return true;
        }

        if (! app()->runningInConsole()) {
            try {
                $requestHost = Str::lower((string) request()->getHost());
                if ($urlHost === $requestHost) {
                    return true;
                }

                if (self::isLoopbackHost($urlHost) && self::isLoopbackHost($requestHost)) {
                    return true;
                }
            } catch (\Throwable) {
                // ignore
            }
        }

        return false;
    }

    private static function isAllowedProductAbsoluteUrl(string $url): bool
    {
        if (! Str::startsWith($url, ['http://', 'https://'])) {
            return true;
        }

        return self::shouldUseRootRelativeImgSrc($url);
    }

    private static function isLoopbackHost(string $host): bool
    {
        return in_array($host, ['localhost', '127.0.0.1', '[::1]', '::1'], true);
    }

    private static function occasionPrimaryAsset(?string $categorySlug): string
    {
        if ($categorySlug && file_exists(public_path('images/categories/'.$categorySlug.'.svg'))) {
            return self::webPublicUrl('images/categories/'.$categorySlug.'.svg');
        }

        return self::webPublicUrl('images/categories/cicek-buketleri.svg');
    }

    private static function webPublicUrl(string $relativePath): string
    {
        $relative = ltrim(str_replace('\\', '/', $relativePath), '/');
        if ($relative === '') {
            return self::webPublicUrl('images/product-placeholder.svg');
        }

        try {
            if (! app()->runningInConsole() && app()->bound('request') && request()) {
                $host = request()->getSchemeAndHttpHost();
                if (is_string($host) && $host !== '') {
                    return url('/'.$relative);
                }
            }
        } catch (\Throwable) {
            // ignore
        }

        return asset($relative);
    }

    private static function optimizedPublicPath(string $url, int $width): ?string
    {
        $source = self::storageBackedPublicImagePath($url);
        if ($source === null || Str::endsWith(Str::lower($source), '.svg')) {
            return null;
        }

        $optimized = self::optimizedStorageRelativePath($source, $width);

        return is_file(storage_path('app/public/'.$optimized))
            ? 'storage/'.$optimized
            : null;
    }

    public static function optimizedStorageRelativePath(string $storageRelativePath, int $width): string
    {
        $storageRelativePath = ltrim(str_replace('\\', '/', $storageRelativePath), '/');
        if (Str::startsWith($storageRelativePath, 'storage/')) {
            $storageRelativePath = substr($storageRelativePath, strlen('storage/'));
        }

        $width = max(80, min(2560, $width));
        $dir = trim((string) pathinfo($storageRelativePath, PATHINFO_DIRNAME), '.');
        $name = Str::slug((string) pathinfo($storageRelativePath, PATHINFO_FILENAME));
        if ($name === '') {
            $name = md5($storageRelativePath);
        }

        $prefix = 'optimized';
        if ($dir !== '') {
            $prefix .= '/'.$dir;
        }

        return $prefix.'/'.$name.'-'.$width.'.webp';
    }

    public static function storageBackedPublicImagePath(string $url): ?string
    {
        if ($url === '' || Str::startsWith($url, 'data:')) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            $path = $url;
        }

        $path = ltrim(rawurldecode(str_replace('\\', '/', $path)), '/');
        if (Str::startsWith($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if (! Str::startsWith(Str::lower($path), ['products/', 'categories/', 'blog/'])) {
            return null;
        }

        return is_file(storage_path('app/public/'.$path)) ? $path : null;
    }

    private static function normalizeLocalPath(?string $path, string $fallback): string
    {
        if (! $path) {
            return self::webPublicUrl($fallback);
        }

        if (self::isBlockedRemoteUrl($path)) {
            return self::webPublicUrl($fallback);
        }

        if (Str::startsWith($path, ['http://', 'https://', 'data:'])) {
            return self::isAllowedProductAbsoluteUrl($path)
                ? self::publicImgSrc($path)
                : self::webPublicUrl($fallback);
        }

        $normalized = ltrim($path, '/');

        if (Str::startsWith($normalized, ['images/', 'storage/', 'build/'])) {
            if (Str::startsWith($normalized, 'storage/') && self::storagePublicFileMissing($normalized)) {
                return self::webPublicUrl($fallback);
            }

            return self::webPublicUrl($normalized);
        }

        $storageKey = 'storage/'.$normalized;
        if (self::storagePublicFileMissing($storageKey)) {
            return self::webPublicUrl($fallback);
        }

        return self::webPublicUrl($storageKey);
    }

    private static function stripBlockedRemote(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return self::isBlockedRemoteUrl($path) ? null : $path;
    }

    private static function isBlockedRemoteUrl(string $path): bool
    {
        if (! Str::startsWith($path, ['http://', 'https://'])) {
            return false;
        }

        $host = Str::lower((string) parse_url($path, PHP_URL_HOST));

        return Str::contains($host, [
            'unsplash.com',
            'pexels.com',
            'picsum.photos',
        ]);
    }

    private static function isProductStorageImagePath(string $path): bool
    {
        $normalized = Str::lower(ltrim($path, '/'));

        return Str::contains($normalized, 'storage/products/')
            || Str::startsWith($normalized, 'products/');
    }

    private static function storageBackedLocalPath(?string $path): ?string
    {
        if ($path === null || $path === '' || Str::startsWith($path, ['http://', 'https://', 'data:'])) {
            return null;
        }

        $normalized = ltrim($path, '/');
        $lower = Str::lower($normalized);

        if (Str::startsWith($lower, ['images/', 'build/'])) {
            return null;
        }

        if (Str::startsWith($lower, 'storage/')) {
            return substr($normalized, strlen('storage/'));
        }

        return $normalized;
    }

    private static function publicImagesFileMissing(string $path): bool
    {
        $normalized = ltrim($path, '/');
        if (! Str::startsWith($normalized, 'images/')) {
            return false;
        }

        return ! is_file(public_path($normalized));
    }

    private static function storagePublicFileMissing(string $path): bool
    {
        $relative = self::storageBackedLocalPath($path);
        if ($relative === null) {
            return false;
        }

        $appFile = storage_path('app/public/'.$relative);
        if (! is_file($appFile)) {
            return true;
        }

        return ! is_file(public_path('storage/'.$relative));
    }

    private static function blogSvgForSlug(?string $slug): ?string
    {
        return match ($slug) {
            'kesme-ciceklerin-omrunu-uzatmanin-7-yolu' => 'images/blog/flower-care.svg',
            'anneler-gunu-icin-en-iyi-cicek-secenekleri' => 'images/blog/mothers-day.svg',
            'cicek-diliyle-duygularinizi-anlatin' => 'images/blog/flower-language.svg',
            'saksi-cicegi-bakim-rehberi-orkide' => 'images/blog/orchid-care.svg',
            default => null,
        };
    }
}
