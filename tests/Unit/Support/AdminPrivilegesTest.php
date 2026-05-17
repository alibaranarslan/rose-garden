<?php

namespace Tests\Unit\Support;

use App\Models\User;
use App\Support\AdminPrivileges;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPrivilegesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_without_super_admin_role_cannot_publish_when_roles_are_configured(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->assertTrue(AdminPrivileges::canAccessAdminPanel($admin));
        $this->assertFalse(AdminPrivileges::canPublishConfiguration($admin));
    }

    public function test_inactive_admin_cannot_access_panel_or_publish_configuration(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => false,
        ]);
        $admin->syncRoles(['super_admin']);

        $this->assertFalse(AdminPrivileges::canAccessAdminPanel($admin));
        $this->assertFalse(AdminPrivileges::canPublishConfiguration($admin));
    }

    public function test_super_admin_role_can_publish_configuration(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);
        $admin->syncRoles(['super_admin']);

        $this->assertTrue(AdminPrivileges::canPublishConfiguration($admin));
    }
}
