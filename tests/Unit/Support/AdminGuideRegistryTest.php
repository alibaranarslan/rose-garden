<?php

namespace Tests\Unit\Support;

use App\Models\User;
use App\Support\AdminGuides\AdminGuideRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminGuideRegistryTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_sees_super_admin_guides(): void
    {
        Role::query()->create(['name' => 'super_admin', 'guard_name' => 'web']);

        $admin = User::query()->create([
            'name' => 'Super Admin',
            'email' => 'super-admin@example.com',
            'password' => 'secret-password',
            'is_admin' => true,
            'is_active' => true,
        ]);

        $admin->assignRole('super_admin');

        $catalog = app(AdminGuideRegistry::class)->catalogForUser($admin);
        $keys = collect($catalog)->pluck('guide_key')->all();

        $this->assertContains('dashboard-overview', $keys);
        $this->assertContains('layout-publishing', $keys);
        $this->assertContains('general-settings', $keys);
        $this->assertContains('users', $keys);
    }

    public function test_admin_catalog_hides_super_admin_guides(): void
    {
        Role::query()->create(['name' => 'super_admin', 'guard_name' => 'web']);

        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'secret-password',
            'is_admin' => true,
            'is_active' => true,
        ]);

        $catalog = app(AdminGuideRegistry::class)->catalogForUser($admin);
        $keys = collect($catalog)->pluck('guide_key')->all();

        $this->assertContains('dashboard-overview', $keys);
        $this->assertContains('layout-studio', $keys);
        $this->assertNotContains('layout-publishing', $keys);
        $this->assertNotContains('general-settings', $keys);
        $this->assertNotContains('users', $keys);
    }
}
