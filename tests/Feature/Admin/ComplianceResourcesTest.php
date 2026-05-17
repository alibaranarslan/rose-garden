<?php

namespace Tests\Feature\Admin;

use App\Models\DataRequest;
use App\Models\SpecialOccasion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplianceResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_data_request_resource_surfaces(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $customer = User::factory()->create([
            'is_admin' => false,
            'is_active' => true,
        ]);

        $request = DataRequest::create([
            'user_id' => $customer->id,
            'type' => 'export',
            'status' => 'pending',
            'reason' => 'KVKK export testi',
        ]);

        $this->actingAs($admin)
            ->get('/admin/data-requests')
            ->assertOk()
            ->assertSee('KVKK Talepleri')
            ->assertSee($customer->name);

        $this->actingAs($admin)
            ->get("/admin/data-requests/{$request->id}/edit")
            ->assertOk()
            ->assertSee('Admin Notu');
    }

    public function test_admin_can_open_special_occasion_resource_surfaces(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $occasion = SpecialOccasion::create([
            'name' => ['tr' => 'Anneler Gunu'],
            'slug' => 'anneler-gunu',
            'date_month' => 5,
            'date_day' => 12,
            'loyalty_multiplier' => 2.0,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/special-occasions')
            ->assertOk()
            ->assertSee('Özel Günler', false)
            ->assertSee('Anneler Gunu');

        $this->actingAs($admin)
            ->get("/admin/special-occasions/{$occasion->id}/edit")
            ->assertOk()
            ->assertSee('Anneler Gunu');
    }
}
