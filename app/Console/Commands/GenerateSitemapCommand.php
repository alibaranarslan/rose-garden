<?php

namespace App\Console\Commands;

use App\Support\SitemapXml;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'XML sitemap olustur ve public dizinine kaydet';

    public function handle(): int
    {
        $xml = SitemapXml::render();

        file_put_contents(public_path('sitemap.xml'), $xml);

        $this->info('Sitemap olusturuldu: '.SitemapXml::urls()->count().' URL.');

        return self::SUCCESS;
    }
}
