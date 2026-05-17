<?php

namespace Tests\Feature\Seeders;

use App\Models\NotificationTemplate;
use Database\Seeders\StagingPrerequisiteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StagingPrerequisiteSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_minimum_operational_prerequisites_idempotently(): void
    {
        $this->seed(StagingPrerequisiteSeeder::class);
        $this->seed(StagingPrerequisiteSeeder::class);

        $this->assertSame(4, DB::table('delivery_zones')->where('is_active', true)->count());
        $this->assertSame(4, DB::table('delivery_time_slots')->where('is_active', true)->count());
        $this->assertSame(12, NotificationTemplate::query()->where('is_active', true)->count());

        $this->assertDatabaseHas('delivery_zones', [
            'name' => 'Adıyaman Merkez',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $template = NotificationTemplate::findByKey('bank_transfer_warning');

        $this->assertNotNull($template);
        $this->assertSame('Havale süresi dolmak üzere - RG-1', $template->renderEmailSubject([
            'siparis_no' => 'RG-1',
        ], 'tr'));
    }
}
