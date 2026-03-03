<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeliveryTimeSlotSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [
            ['label' => '09:00 - 12:00', 'start_time' => '09:00:00', 'end_time' => '12:00:00', 'sort_order' => 1],
            ['label' => '12:00 - 15:00', 'start_time' => '12:00:00', 'end_time' => '15:00:00', 'sort_order' => 2],
            ['label' => '15:00 - 18:00', 'start_time' => '15:00:00', 'end_time' => '18:00:00', 'sort_order' => 3],
            ['label' => '18:00 - 21:00', 'start_time' => '18:00:00', 'end_time' => '21:00:00', 'sort_order' => 4],
        ];

        foreach ($slots as $slot) {
            DB::table('delivery_time_slots')->updateOrInsert(
                ['label' => $slot['label']],
                [
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'is_active' => true,
                    'sort_order' => $slot['sort_order'],
                ]
            );
        }
    }
}
