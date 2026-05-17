<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNavigationAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_all_primary_admin_navigation_surfaces(): void
    {
        $admin = $this->makeAdmin();

        foreach ($this->primaryAdminPaths() as $path) {
            $response = $this->actingAs($admin)->get($path);

            $this->assertSame(200, $response->getStatusCode(), "{$path} should be open to admin.");
        }
    }

    public function test_admin_can_open_create_surfaces_for_creatable_resources(): void
    {
        $admin = $this->makeAdmin('admin-create@example.com');

        foreach ($this->createPaths() as $path) {
            $response = $this->actingAs($admin)->get($path);

            $this->assertSame(200, $response->getStatusCode(), "{$path} should be open to admin.");
        }
    }

    public function test_customer_cannot_open_primary_admin_navigation_surfaces(): void
    {
        $customer = User::factory()->create([
            'is_admin' => false,
            'is_active' => true,
        ]);

        foreach ($this->primaryAdminPaths() as $path) {
            $response = $this->actingAs($customer)->get($path);

            $this->assertContains($response->getStatusCode(), [302, 403], "{$path} should not be open to customers.");
        }
    }

    public function test_inactive_admin_cannot_open_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $this->assertContains($response->getStatusCode(), [302, 403]);
    }

    public function test_admin_without_super_admin_role_cannot_open_sensitive_user_and_role_surfaces(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        foreach (['/admin/users', '/admin/shield/roles'] as $path) {
            $response = $this->actingAs($admin)->get($path);

            $this->assertContains($response->getStatusCode(), [302, 403], "{$path} should require super_admin.");
        }
    }

    public function test_admin_without_super_admin_role_can_still_open_regular_operational_surfaces(): void
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        foreach (['/admin', '/admin/products', '/admin/orders'] as $path) {
            $response = $this->actingAs($admin)->get($path);

            $this->assertSame(200, $response->getStatusCode(), "{$path} should remain open to regular admins.");
        }
    }

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get('/admin')
            ->assertRedirect('/admin/login');
    }

    /**
     * @return array<int, string>
     */
    private function primaryAdminPaths(): array
    {
        return [
            '/admin',
            '/admin/products',
            '/admin/categories',
            '/admin/special-occasions',
            '/admin/coupons',
            '/admin/orders',
            '/admin/payments',
            '/admin/users',
            '/admin/data-requests',
            '/admin/blog-categories',
            '/admin/blog-posts',
            '/admin/pages',
            '/admin/delivery-zones',
            '/admin/delivery-time-slots',
            '/admin/notification-templates',
            '/admin/notification-logs',
            '/admin/abandoned-carts',
            '/admin/customer-events',
            '/admin/keyword-dictionaries',
            '/admin/header-themes',
            '/admin/general-settings',
            '/admin/seo-settings',
            '/admin/payment-settings',
            '/admin/sms-settings',
            '/admin/email-settings',
            '/admin/loyalty-management',
            '/admin/media-library',
            '/admin/layout-studio',
            '/admin/reports-analytics',
            '/admin/cache-management',
            '/admin/shield/roles',
        ];
    }

    /**
     * @return array<int, string>
     */
    private function createPaths(): array
    {
        return [
            '/admin/products/create',
            '/admin/categories/create',
            '/admin/special-occasions/create',
            '/admin/coupons/create',
            '/admin/blog-categories/create',
            '/admin/blog-posts/create',
            '/admin/pages/create',
            '/admin/delivery-zones/create',
            '/admin/delivery-time-slots/create',
            '/admin/notification-templates/create',
            '/admin/customer-events/create',
            '/admin/keyword-dictionaries/create',
            '/admin/header-themes/create',
            '/admin/shield/roles/create',
        ];
    }

    private function makeAdmin(string $email = 'admin-navigation@example.com'): User
    {
        $this->seed(RoleSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => $email,
            'is_admin' => true,
            'is_active' => true,
        ]);

        $admin->assignRole('super_admin');

        return $admin;
    }
}
