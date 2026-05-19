<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Models\SpecialOccasion;
use Illuminate\Support\Collection;

class SitemapXml
{
    public static function render(): string
    {
        return self::buildXml(self::urls());
    }

    public static function urls(): Collection
    {
        $baseUrl = self::resolveBaseUrl();

        $urls = collect([
            self::entry($baseUrl, '1.0', 'daily'),
            self::entry(self::url($baseUrl, '/urunler'), '0.9', 'daily'),
            self::entry(self::url($baseUrl, '/ozel-gunler'), '0.7', 'weekly'),
            self::entry(self::url($baseUrl, '/blog'), '0.6', 'weekly'),
            self::entry(self::url($baseUrl, '/iletisim'), '0.5', 'monthly'),
            self::entry(self::url($baseUrl, '/sss'), '0.4', 'monthly'),
            self::entry(self::url($baseUrl, '/teslimat-bilgileri'), '0.4', 'monthly'),
            self::entry(self::url($baseUrl, '/siparis-takip'), '0.3', 'monthly'),
        ]);

        Product::storefrontReady()
            ->select(['slug', 'updated_at'])
            ->orderBy('id')
            ->chunk(500, function ($products) use ($baseUrl, $urls): void {
                foreach ($products as $product) {
                    $urls->push(self::entry(
                        self::url($baseUrl, '/urun/'.$product->slug),
                        '0.8',
                        'weekly',
                        $product->updated_at?->toAtomString(),
                    ));
                }
            });

        Category::active()
            ->whereHas('products', fn ($query) => $query->storefrontReady())
            ->select(['slug'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->each(fn ($category) => $urls->push(self::entry(
                self::url($baseUrl, '/kategori/'.$category->slug),
                '0.7',
                'daily',
            )));

        SpecialOccasion::active()
            ->select(['slug', 'updated_at'])
            ->orderBy('date_month')
            ->orderBy('date_day')
            ->get()
            ->each(fn ($occasion) => $urls->push(self::entry(
                self::url($baseUrl, '/ozel-gunler/'.$occasion->slug),
                '0.6',
                'weekly',
                $occasion->updated_at?->toAtomString(),
            )));

        BlogPost::published()
            ->select(['slug', 'published_at', 'updated_at'])
            ->orderByDesc('published_at')
            ->chunk(200, function ($posts) use ($baseUrl, $urls): void {
                foreach ($posts as $post) {
                    $urls->push(self::entry(
                        self::url($baseUrl, '/blog/'.$post->slug),
                        '0.5',
                        'monthly',
                        ($post->updated_at ?? $post->published_at)?->toAtomString(),
                    ));
                }
            });

        Page::published()
            ->select(['slug', 'updated_at'])
            ->orderBy('slug')
            ->get()
            ->each(fn ($page) => $urls->push(self::entry(
                self::url($baseUrl, '/sayfa/'.$page->slug),
                '0.5',
                'monthly',
                $page->updated_at?->toAtomString(),
            )));

        return $urls->unique('loc')->values();
    }

    public static function resolveBaseUrl(): string
    {
        $configured = trim((string) Setting::get('seo', 'canonical_domain', config('app.url')));

        if ($configured === '') {
            $configured = (string) config('app.url');
        }

        if (! str_starts_with($configured, 'http://') && ! str_starts_with($configured, 'https://')) {
            $configured = 'https://'.$configured;
        }

        $parts = parse_url($configured);

        if (! is_array($parts) || empty($parts['host'])) {
            return rtrim((string) config('app.url'), '/');
        }

        $scheme = $parts['scheme'] ?? 'https';
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';

        return rtrim("{$scheme}://{$parts['host']}{$port}", '/');
    }

    private static function entry(string $loc, string $priority, string $changefreq, ?string $lastmod = null): array
    {
        return array_filter([
            'loc' => $loc,
            'lastmod' => $lastmod,
            'priority' => $priority,
            'changefreq' => $changefreq,
        ], fn ($value): bool => $value !== null && $value !== '');
    }

    private static function url(string $baseUrl, string $path): string
    {
        return rtrim($baseUrl, '/').'/'.ltrim($path, '/');
    }

    private static function buildXml(Collection $urls): string
    {
        $lines = ['<?xml version="1.0" encoding="UTF-8"?>'];
        $lines[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $lines[] = '  <url>';
            $lines[] = '    <loc>'.htmlspecialchars((string) $url['loc'], ENT_XML1).'</loc>';

            foreach (['lastmod', 'changefreq', 'priority'] as $field) {
                if (! empty($url[$field])) {
                    $lines[] = '    <'.$field.'>'.htmlspecialchars((string) $url[$field], ENT_XML1).'</'.$field.'>';
                }
            }

            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines)."\n";
    }
}
