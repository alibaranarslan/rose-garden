<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Support\AdminGuides\AdminGuideRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelDeliveryReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_guides_and_permission_labels_are_customer_friendly_turkish(): void
    {
        app()->setLocale('tr');

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $catalog = app(AdminGuideRegistry::class)->catalogForUser($admin);
        $rendered = json_encode($catalog, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $this->assertSame('Yetki Yönetimi', __('filament-shield::filament-shield.nav.group'));
        $this->assertStringContainsString('Yerleşim Stüdyosu', $rendered);
        $this->assertStringContainsString('Bildirim Şablonları', $rendered);
        $this->assertStringContainsString('Müşteri Davranış Kayıtları', $rendered);

        foreach (['Filament Shield', 'Yerlesim', 'Studyosu', 'Musteri', 'Sablonlari', 'Ã', 'â€'] as $fragment) {
            $this->assertStringNotContainsString($fragment, $rendered);
        }
    }

    public function test_risky_admin_actions_have_confirmation_and_audit_hooks(): void
    {
        $expectations = [
            app_path('Filament/Resources/ProductResource.php') => [
                'product.duplicate',
                'product.bulk_activate',
                'Seçili ürünleri aktifleştir',
                'Seçili ürünleri sil',
            ],
            app_path('Filament/Resources/OrderResource.php') => [
                'order.approve_bank_transfer',
                'Havale ödemesini onayla',
            ],
            app_path('Filament/Resources/NotificationTemplateResource.php') => [
                'notification_template.test_send',
                'Test bildirimi gönder',
            ],
            app_path('Filament/Pages/SmsSettings.php') => [
                'settings.sms.test_sent',
                'Test SMS gönder',
            ],
            app_path('Filament/Pages/EmailSettings.php') => [
                'settings.email.test_sent',
                'Test e-postası gönder',
            ],
            app_path('Filament/Pages/MediaLibrary.php') => [
                'media.delete_blocked_attached',
                'media.delete_orphaned',
            ],
            app_path('Filament/Pages/CacheManagement.php') => [
                'cache.clear_all',
                'cache.command',
            ],
            app_path('Filament/Pages/LoyaltyManagement.php') => [
                'loyalty.manual_points',
                'loyalty.rules_save',
            ],
            app_path('Filament/Pages/LayoutStudio.php') => [
                'layout.save_draft',
                'layout.publish_draft',
                'layout.restore_revision',
            ],
        ];

        foreach ($expectations as $file => $needles) {
            $source = file_get_contents($file);

            foreach ($needles as $needle) {
                $this->assertStringContainsString($needle, $source, "{$needle} missing from {$file}");
            }
        }
    }
}
