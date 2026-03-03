<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpecialOccasionSeeder extends Seeder
{
    public function run(): void
    {
        $occasions = [
            ['slug' => 'sevgililer-gunu', 'name' => 'Sevgililer Gunu', 'date_month' => 2, 'date_day' => 14, 'multiplier' => 2.0],
            ['slug' => 'anneler-gunu', 'name' => 'Anneler Gunu', 'date_month' => 5, 'date_day' => 11, 'multiplier' => 1.5],
            ['slug' => 'babalar-gunu', 'name' => 'Babalar Gunu', 'date_month' => 6, 'date_day' => 21, 'multiplier' => 1.5],
            ['slug' => 'ogretmenler-gunu', 'name' => 'Ogretmenler Gunu', 'date_month' => 11, 'date_day' => 24, 'multiplier' => 1.5],
            ['slug' => 'yilbasi', 'name' => 'Yilbasi', 'date_month' => 12, 'date_day' => 31, 'multiplier' => 1.5],
        ];

        foreach ($occasions as $occasion) {
            DB::table('special_occasions')->updateOrInsert(
                ['slug' => $occasion['slug']],
                [
                    'name' => json_encode(['tr' => $occasion['name']], JSON_UNESCAPED_UNICODE),
                    'date_month' => $occasion['date_month'],
                    'date_day' => $occasion['date_day'],
                    'category_id' => null,
                    'loyalty_multiplier' => $occasion['multiplier'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
