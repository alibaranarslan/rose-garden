<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            DeliveryZoneSeeder::class,
            DeliveryTimeSlotSeeder::class,
            SpecialOccasionSeeder::class,
            KeywordDictionarySeeder::class,
            NotificationTemplateSeeder::class,
            SettingsSeeder::class,
            DemoContentSeeder::class,
        ]);
    }
}
