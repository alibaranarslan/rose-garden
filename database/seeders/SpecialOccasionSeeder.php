<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SpecialOccasion;
use Illuminate\Database\Seeder;

class SpecialOccasionSeeder extends Seeder
{
    public function run(): void
    {
        $occasions = [
            ['slug' => 'sevgililer-gunu', 'name' => ['tr' => 'Sevgililer Günü', 'en' => 'Valentine\'s Day', 'ku' => 'Roja Evîndaran'], 'date_month' => 2, 'date_day' => 14, 'multiplier' => 2.0, 'category_slug' => 'cicek-buketleri'],
            ['slug' => 'kadinlar-gunu', 'name' => ['tr' => 'Dünya Kadınlar Günü', 'en' => 'International Women\'s Day', 'ku' => 'Roja Jinan a Cîhanê'], 'date_month' => 3, 'date_day' => 8, 'multiplier' => 1.5, 'category_slug' => 'cicek-buketleri'],
            ['slug' => 'tip-bayrami', 'name' => ['tr' => 'Tıp Bayramı', 'en' => 'Medical Day', 'ku' => 'Cejna Tibbê'], 'date_month' => 3, 'date_day' => 14, 'multiplier' => 1.5, 'category_slug' => 'cicek-buketleri'],
            ['slug' => 'anneler-gunu', 'name' => ['tr' => 'Anneler Günü', 'en' => 'Mother\'s Day', 'ku' => 'Roja Dêyan'], 'date_month' => 5, 'date_day' => 11, 'multiplier' => 1.5, 'category_slug' => 'cicek-buketleri'],
            ['slug' => 'babalar-gunu', 'name' => ['tr' => 'Babalar Günü', 'en' => 'Father\'s Day', 'ku' => 'Roja Bavan'], 'date_month' => 6, 'date_day' => 21, 'multiplier' => 1.5, 'category_slug' => 'saksi-cicekleri'],
            ['slug' => 'ogretmenler-gunu', 'name' => ['tr' => 'Öğretmenler Günü', 'en' => 'Teachers\' Day', 'ku' => 'Roja Mamosteyan'], 'date_month' => 11, 'date_day' => 24, 'multiplier' => 1.5, 'category_slug' => 'cicek-buketleri'],
            ['slug' => 'ramazan-bayrami', 'name' => ['tr' => 'Ramazan Bayramı', 'en' => 'Eid al-Fitr', 'ku' => 'Cejna Ramazanê'], 'date_month' => 3, 'date_day' => 20, 'multiplier' => 1.5, 'category_slug' => 'cikolata-tatli'],
            ['slug' => 'kurban-bayrami', 'name' => ['tr' => 'Kurban Bayramı', 'en' => 'Eid al-Adha', 'ku' => 'Cejna Qurbanê'], 'date_month' => 5, 'date_day' => 27, 'multiplier' => 1.5, 'category_slug' => 'cikolata-tatli'],
            ['slug' => 'yilbasi', 'name' => ['tr' => 'Yılbaşı', 'en' => 'New Year', 'ku' => 'Sersal'], 'date_month' => 12, 'date_day' => 31, 'multiplier' => 1.5, 'category_slug' => 'cicek-buketleri'],
            ['slug' => 'bahar-kampanyasi', 'name' => ['tr' => 'Bahar Koleksiyonu', 'en' => 'Spring Collection', 'ku' => 'Koleksiyona Biharê'], 'date_month' => 4, 'date_day' => 15, 'multiplier' => 1.2, 'category_slug' => 'cicek-buketleri'],
        ];

        foreach ($occasions as $occasion) {
            $categoryId = null;
            if (! empty($occasion['category_slug'])) {
                $categoryId = Category::where('slug', $occasion['category_slug'])->value('id');
            }

            SpecialOccasion::updateOrCreate(
                ['slug' => $occasion['slug']],
                [
                    'name' => $occasion['name'],
                    'date_month' => $occasion['date_month'],
                    'date_day' => $occasion['date_day'],
                    'category_id' => $categoryId,
                    'loyalty_multiplier' => $occasion['multiplier'],
                    'is_active' => true,
                ]
            );
        }
    }
}
