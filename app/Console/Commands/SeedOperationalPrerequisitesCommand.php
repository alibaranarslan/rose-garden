<?php

namespace App\Console\Commands;

use Database\Seeders\StagingPrerequisiteSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SeedOperationalPrerequisitesCommand extends Command
{
    protected $signature = 'rg:seed-operational-prerequisites';

    protected $description = 'Seed the minimum delivery and notification data needed for a safe staging checkout smoke.';

    public function handle(): int
    {
        Artisan::call('db:seed', [
            '--class' => StagingPrerequisiteSeeder::class,
            '--force' => true,
        ]);

        $checks = [
            'Active delivery zones' => DB::table('delivery_zones')->where('is_active', true)->count(),
            'Active delivery slots' => DB::table('delivery_time_slots')->where('is_active', true)->count(),
            'Active notification templates' => DB::table('notification_templates')->where('is_active', true)->count(),
        ];

        foreach ($checks as $label => $count) {
            $this->line("{$label}: {$count}");

            if ($count < 1) {
                $this->error("{$label} is missing.");

                return Command::FAILURE;
            }
        }

        $this->info('Operational prerequisites are ready for staging/local checkout smoke.');

        return Command::SUCCESS;
    }
}
