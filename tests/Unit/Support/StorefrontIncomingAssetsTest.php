<?php

namespace Tests\Unit\Support;

use App\Support\StorefrontIncomingAssets;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StorefrontIncomingAssetsTest extends TestCase
{
    #[Test]
    public function it_classifies_exact_and_normalized_matches_without_touching_unmatched_files(): void
    {
        $catalog = [
            'fixture-import.png' => [
                'slug' => 'fixture-import',
                'category_slug' => 'cicek-buketleri',
                'price' => 199.5,
                'tags' => [],
                'name' => ['tr' => 'Fixture Import', 'en' => 'Fixture Import', 'ku' => 'Fixture Import'],
                'short_description' => ['tr' => 'Kisa', 'en' => 'Short', 'ku' => 'Short'],
                'description' => ['tr' => '<p>Aciklama</p>', 'en' => '<p>Aciklama</p>', 'ku' => '<p>Aciklama</p>'],
                'delivery_note' => ['tr' => 'Not', 'en' => 'Note', 'ku' => 'Note'],
                'meta_title' => ['tr' => 'Meta', 'en' => 'Meta', 'ku' => 'Meta'],
                'meta_description' => ['tr' => 'Desc', 'en' => 'Desc', 'ku' => 'Desc'],
            ],
            'areka-en-uygun-çiçekçi-adıyaman-merkez.png' => [
                'slug' => 'areka-palm-merkez',
                'category_slug' => 'saksi-cicekleri',
                'price' => 2790,
                'tags' => [],
                'name' => ['tr' => 'Areka Palm (Merkez)', 'en' => 'Areca palm centerpiece', 'ku' => 'Areka'],
                'short_description' => ['tr' => 'Kisa', 'en' => 'Short', 'ku' => 'Short'],
                'description' => ['tr' => '<p>Aciklama</p>', 'en' => '<p>Aciklama</p>', 'ku' => '<p>Aciklama</p>'],
                'delivery_note' => ['tr' => 'Not', 'en' => 'Note', 'ku' => 'Note'],
                'meta_title' => ['tr' => 'Meta', 'en' => 'Meta', 'ku' => 'Meta'],
                'meta_description' => ['tr' => 'Desc', 'en' => 'Desc', 'ku' => 'Desc'],
            ],
        ];

        $report = StorefrontIncomingAssets::classifyIncomingFiles([
            '/tmp/fixture-import.png',
            '/tmp/areka en uygun cicekci adiyaman merkez.png',
            '/tmp/uuid-orphan.jpg',
        ], $catalog);

        $this->assertCount(2, $report['matched']);
        $this->assertCount(1, $report['unmatched']);
        $this->assertSame(['fixture-import.png', 'areka en uygun cicekci adiyaman merkez.png'], array_map(
            static fn (array $item): string => $item['basename'],
            $report['matched']
        ));
    }
}
