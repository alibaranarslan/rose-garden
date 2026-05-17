<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminGuideModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_help_trigger_and_intro_card(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => 'secret-password',
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Yardım')
            ->assertSee('Yönetim Panelini Tanı')
            ->assertSee('data-tour-anchor="dashboard.attention"', false)
            ->assertSee('dashboard-overview');
    }

    public function test_guide_progress_is_persisted_for_visible_guide(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin-progress@example.com',
            'password' => 'secret-password',
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.guides.progress.store'), [
                'guide_key' => 'dashboard-overview',
                'status' => 'completed',
                'last_step_index' => 2,
                'meta' => ['path' => '/admin'],
            ])
            ->assertOk()
            ->assertJsonPath('progress.status', 'completed');

        $this->assertDatabaseHas('admin_guide_progress', [
            'user_id' => $admin->id,
            'guide_key' => 'dashboard-overview',
            'status' => 'completed',
            'last_step_index' => 2,
        ]);
    }

    public function test_admin_cannot_store_progress_for_super_admin_only_guide(): void
    {
        Role::query()->create(['name' => 'super_admin', 'guard_name' => 'web']);

        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin-forbidden@example.com',
            'password' => 'secret-password',
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->postJson(route('admin.guides.progress.store'), [
                'guide_key' => 'general-settings',
                'status' => 'in_progress',
                'last_step_index' => 1,
            ])
            ->assertForbidden();
    }

    public function test_layout_and_reports_pages_render_tour_anchors(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin-anchors@example.com',
            'password' => 'secret-password',
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin/layout-studio')
            ->assertOk()
            ->assertSee('data-tour-anchor="layout.hero"', false)
            ->assertSee('data-tour-anchor="layout.publish"', false);

        $this->actingAs($admin)
            ->get('/admin/reports-analytics')
            ->assertOk()
            ->assertSee('data-tour-anchor="reports.hero"', false)
            ->assertSee('data-tour-anchor="reports.devices"', false);
    }
}
