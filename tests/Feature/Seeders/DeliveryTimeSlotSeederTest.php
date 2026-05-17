<?php

namespace Tests\Feature\Seeders;

use Database\Seeders\DeliveryTimeSlotSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeliveryTimeSlotSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_time_slot_seeder_uses_18_00_to_20_00_for_the_last_slot(): void
    {
        $this->seed(DeliveryTimeSlotSeeder::class);

        $this->assertDatabaseHas('delivery_time_slots', [
            'sort_order' => 4,
            'label' => '18:00 - 20:00',
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
        ]);

        $this->assertSame(
            0,
            DB::table('delivery_time_slots')
                ->where('label', '18:00 - 21:00')
                ->count()
        );
    }
}
