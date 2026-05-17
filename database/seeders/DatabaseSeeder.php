<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            DeliveryZoneSeeder::class,
            DeliveryTimeSlotSeeder::class,
            HeaderThemeSeeder::class,
            KeywordDictionarySeeder::class,
            NotificationTemplateSeeder::class,
            SettingsSeeder::class,
            DemoContentSeeder::class,
            SpecialOccasionSeeder::class,
            CustomerContentSeeder::class,
        ]);
    }
}
