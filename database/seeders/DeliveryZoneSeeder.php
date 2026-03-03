<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'Merkez', 'fee' => 0.00, 'sort_order' => 1],
            ['name' => 'Besni', 'fee' => 25.00, 'sort_order' => 2],
            ['name' => 'Kahta', 'fee' => 30.00, 'sort_order' => 3],
            ['name' => 'Golbasi', 'fee' => 35.00, 'sort_order' => 4],
        ];

        foreach ($zones as $zone) {
            DB::table('delivery_zones')->updateOrInsert(
                ['name' => $zone['name']],
                [
                    'fee' => $zone['fee'],
                    'min_free_amount' => null,
                    'cutoff_time' => null,
                    'is_active' => true,
                    'sort_order' => $zone['sort_order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
