<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLanguageQualityTest extends TestCase
{
    use RefreshDatabase;

    public function test_representative_admin_pages_do_not_render_mojibake_sequences(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'quality-admin@example.com',
            'password' => 'secret-password',
            'is_admin' => true,
            'is_active' => true,
        ]);

        foreach ([
            '/admin',
            '/admin/reports-analytics',
            '/admin/media-library',
            '/admin/payment-settings',
            '/admin/general-settings',
            '/admin/products',
            '/admin/categories',
            '/admin/special-occasions',
            '/admin/blog-posts',
            '/admin/blog-categories',
            '/admin/pages',
            '/admin/orders',
            '/admin/coupons',
            '/admin/loyalty-management',
            '/admin/notification-templates',
            '/admin/notification-logs',
            '/admin/delivery-zones',
            '/admin/delivery-time-slots',
        ] as $path) {
            $response = $this->actingAs($admin)->get($path)->assertOk();

            foreach (['Ã', 'Ä', 'Å', 'â€™', 'Â'] as $brokenFragment) {
                $response->assertDontSee($brokenFragment);
            }
        }
    }

    public function test_representative_admin_pages_do_not_render_ascii_turkish_fallback_labels(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'quality-admin-ascii@example.com',
            'password' => 'secret-password',
            'is_admin' => true,
            'is_active' => true,
        ]);

        foreach (['/admin/cache-management', '/admin/notification-templates', '/admin/abandoned-carts'] as $path) {
            $response = $this->actingAs($admin)->get($path)->assertOk();

            foreach (['Islemleri', 'Konfigurasyon', 'sablonlarini', 'Tum Cache', 'Test Gonder', 'Ikisi', 'Musteri / E-posta', 'Hatirlatma Gonder'] as $brokenFragment) {
                $response->assertDontSee($brokenFragment);
            }
        }
    }
}
