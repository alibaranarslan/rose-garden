<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class StagingPrerequisiteSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DeliveryZoneSeeder::class,
            DeliveryTimeSlotSeeder::class,
            NotificationTemplateSeeder::class,
        ]);

        Setting::set('payment', 'transfer_timeout_hours', Setting::get('payment', 'transfer_timeout_hours', '72') ?: '72');
        Setting::set('sms', 'enabled', Setting::get('sms', 'enabled', '0') ?: '0');
    }
}
