<?php

namespace Tests\Unit\Support;

use App\Support\CatalogTaxonomy;
use Tests\TestCase;

class CatalogTaxonomyTest extends TestCase
{
    public function test_it_derives_multiple_catalog_categories_for_bouquets(): void
    {
        $definition = [
            'category_slug' => 'cicek-buketleri',
            'name' => ['tr' => 'Pembe Zambak ve Gül Buketi'],
            'tags' => ['Romantik', 'Lüks', 'Anneler Günü', 'Güller'],
        ];

        $this->assertSame(
            ['cicek-buketleri', 'gul-buketleri', 'premium-buketler', 'zambakli-buketler'],
            CatalogTaxonomy::assignCategorySlugs($definition)
        );
        $this->assertContains('anneler-gunu', CatalogTaxonomy::assignOccasionSlugs($definition));
        $this->assertContains('sevgililer-gunu', CatalogTaxonomy::assignOccasionSlugs($definition));
    }

    public function test_it_derives_species_led_categories_for_potted_plants(): void
    {
        $definition = [
            'category_slug' => 'saksi-cicekleri',
            'name' => ['tr' => '2 Dal Beyaz Orkide'],
            'tags' => ['Özel Gün', 'Lüks'],
        ];

        $this->assertSame(
            ['saksi-cicekleri', 'orkideler'],
            CatalogTaxonomy::assignCategorySlugs($definition)
        );
        $this->assertContains('anneler-gunu', CatalogTaxonomy::assignOccasionSlugs($definition));
        $this->assertContains('yilbasi', CatalogTaxonomy::assignOccasionSlugs($definition));
    }
}
