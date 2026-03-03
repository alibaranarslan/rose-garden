<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'XML sitemap oluştur ve public dizinine kaydet';

    public function handle(): int
    {
        $baseUrl = config('app.url');
        $urls    = [];

        // Homepage
        $urls[] = ['loc' => $baseUrl, 'priority' => '1.0', 'changefreq' => 'daily'];

        // Active products
        Product::active()
            ->select(['slug', 'updated_at'])
            ->chunk(500, function ($products) use ($baseUrl, &$urls) {
                foreach ($products as $product) {
                    $urls[] = [
                        'loc'        => "{$baseUrl}/urun/{$product->slug}",
                        'lastmod'    => $product->updated_at?->toAtomString(),
                        'priority'   => '0.8',
                        'changefreq' => 'weekly',
                    ];
                }
            });

        // Categories
        Category::active()
            ->select(['slug'])
            ->get()
            ->each(function ($cat) use ($baseUrl, &$urls) {
                $urls[] = [
                    'loc'        => "{$baseUrl}/kategori/{$cat->slug}",
                    'priority'   => '0.6',
                    'changefreq' => 'daily',
                ];
            });

        // Published blog posts
        BlogPost::where('status', 'published')
            ->select(['slug', 'published_at', 'updated_at'])
            ->chunk(200, function ($posts) use ($baseUrl, &$urls) {
                foreach ($posts as $post) {
                    $urls[] = [
                        'loc'        => "{$baseUrl}/blog/{$post->slug}",
                        'lastmod'    => ($post->updated_at ?? $post->published_at)?->toAtomString(),
                        'priority'   => '0.5',
                        'changefreq' => 'monthly',
                    ];
                }
            });

        // Published pages
        Page::published()->select(['slug'])->get()->each(function ($page) use ($baseUrl, &$urls) {
            $urls[] = [
                'loc'        => "{$baseUrl}/sayfa/{$page->slug}",
                'priority'   => '0.5',
                'changefreq' => 'monthly',
            ];
        });

        $xml = $this->buildXml($urls);
        file_put_contents(public_path('sitemap.xml'), $xml);

        $this->info('Sitemap oluşturuldu: ' . count($urls) . ' URL.');

        return self::SUCCESS;
    }

    private function buildXml(array $urls): string
    {
        $lines = ['<?xml version="1.0" encoding="UTF-8"?>'];
        $lines[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($urls as $url) {
            $lines[] = '  <url>';
            $lines[] = '    <loc>' . htmlspecialchars($url['loc']) . '</loc>';
            if (!empty($url['lastmod']))    $lines[] = '    <lastmod>' . $url['lastmod'] . '</lastmod>';
            if (!empty($url['changefreq'])) $lines[] = '    <changefreq>' . $url['changefreq'] . '</changefreq>';
            if (!empty($url['priority']))   $lines[] = '    <priority>' . $url['priority'] . '</priority>';
            $lines[] = '  </url>';
        }

        $lines[] = '</urlset>';

        return implode("\n", $lines);
    }
}
