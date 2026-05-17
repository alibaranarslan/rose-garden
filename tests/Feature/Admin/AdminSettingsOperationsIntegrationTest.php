<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\EmailSettings;
use App\Filament\Pages\ReportsAnalytics;
use App\Filament\Pages\SmsSettings;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSettingsOperationsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_settings_apply_runtime_mail_configuration_after_save(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        config([
            'mail.default' => 'log',
            'mail.mailers.smtp.host' => 'env-host',
            'mail.from.address' => 'env@example.com',
            'mail.from.name' => 'Env Sender',
        ]);

        Livewire::actingAs($admin)
            ->test(EmailSettings::class)
            ->set('data.smtp_host', 'smtp.admin.test')
            ->set('data.smtp_port', '465')
            ->set('data.smtp_username', 'admin-user')
            ->set('data.smtp_password', 'admin-pass')
            ->set('data.smtp_encryption', 'ssl')
            ->set('data.from_name', 'Rose Admin Sender')
            ->set('data.from_email', 'admin-mail@rose.test')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('smtp.admin.test', config('mail.mailers.smtp.host'));
        $this->assertSame(465, config('mail.mailers.smtp.port'));
        $this->assertSame('admin-mail@rose.test', config('mail.from.address'));
        $this->assertSame('Rose Admin Sender', config('mail.from.name'));
    }

    public function test_email_sender_settings_do_not_force_smtp_when_smtp_is_not_configured(): void
    {
        config([
            'mail.default' => 'log',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.username' => null,
            'mail.mailers.smtp.password' => null,
            'mail.from.address' => 'env@example.com',
            'mail.from.name' => 'Env Sender',
        ]);

        Setting::set('email', 'from_email', 'info@adiyamancicekcisi.com.tr');
        Setting::set('email', 'from_name', 'Rose Garden Çiçek Çikolata');

        \App\Support\DynamicMailConfig::apply();

        $this->assertSame('log', config('mail.default'));
        $this->assertSame('info@adiyamancicekcisi.com.tr', config('mail.from.address'));
        $this->assertSame('Rose Garden Çiçek Çikolata', config('mail.from.name'));
    }

    public function test_email_settings_reject_partial_smtp_configuration(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(EmailSettings::class)
            ->set('data.smtp_host', 'smtp.partial.test')
            ->set('data.smtp_username', '')
            ->set('data.smtp_password', '')
            ->set('data.from_name', 'Rose Garden')
            ->set('data.from_email', 'info@adiyamancicekcisi.com.tr')
            ->call('save')
            ->assertHasErrors(['data.smtp_username', 'data.smtp_password']);

        $this->assertNull(Setting::get('email', 'smtp_host'));
        $this->assertNull(Setting::get('email', 'smtp_username'));
        $this->assertNull(Setting::get('email', 'smtp_password'));
    }

    public function test_email_settings_reject_invalid_sender_and_smtp_port(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(EmailSettings::class)
            ->set('data.smtp_host', 'smtp.example.test')
            ->set('data.smtp_port', '999999')
            ->set('data.smtp_username', 'user')
            ->set('data.smtp_password', 'pass')
            ->set('data.from_email', 'bad-email')
            ->call('save')
            ->assertHasErrors(['data.smtp_port', 'data.from_email']);

        $this->assertNull(Setting::get('email', 'smtp_host'));
        $this->assertNull(Setting::get('email', 'from_email'));
    }

    public function test_email_test_action_does_not_send_when_smtp_is_not_ready(): void
    {
        Mail::fake();

        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
            'email' => 'admin@example.test',
        ]);

        config([
            'mail.default' => 'log',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.username' => null,
            'mail.mailers.smtp.password' => null,
        ]);

        Livewire::actingAs($admin)
            ->test(EmailSettings::class)
            ->call('sendTestEmail')
            ->assertHasNoErrors();

        Mail::assertNothingSent();
    }

    public function test_sms_settings_drop_stale_service_instance_after_save(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        config()->set('services.sms.enabled', false);
        config()->set('services.sms.api_url', '');

        Setting::set('sms', 'enabled', '0');
        app()->instance(SmsService::class, app(SmsService::class));

        Livewire::actingAs($admin)
            ->test(SmsSettings::class)
            ->set('data.sms_api_url', 'https://sms.example.test')
            ->set('data.sms_username', 'panel-user')
            ->set('data.sms_password', 'panel-pass')
            ->set('data.sms_subscriber_no', '12345')
            ->set('data.sms_sender_title', 'ROSEGARDEN')
            ->set('data.sms_enabled', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('https://sms.example.test', Setting::get('sms', 'api_url'));
        $this->assertSame('ROSEGARDEN', Setting::get('sms', 'sender_title'));
        $this->assertTrue(app(SmsService::class)->canSend());
    }

    public function test_sms_settings_reject_enabled_sms_with_missing_provider_fields(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(SmsSettings::class)
            ->set('data.sms_api_url', 'https://sms.example.test')
            ->set('data.sms_username', '')
            ->set('data.sms_password', '')
            ->set('data.sms_subscriber_no', '')
            ->set('data.sms_sender_title', 'ROSE GARDEN')
            ->set('data.sms_enabled', true)
            ->call('save')
            ->assertHasErrors([
                'data.sms_username',
                'data.sms_password',
                'data.sms_subscriber_no',
                'data.sms_sender_title',
            ]);

        $this->assertNull(Setting::get('sms', 'api_url'));
        $this->assertNull(Setting::get('sms', 'enabled'));
    }

    public function test_sms_settings_reject_invalid_api_url(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(SmsSettings::class)
            ->set('data.sms_api_url', 'ftp://sms.example.test')
            ->set('data.sms_username', 'panel-user')
            ->set('data.sms_password', 'panel-pass')
            ->set('data.sms_subscriber_no', '12345')
            ->set('data.sms_sender_title', 'ROSEGARDEN')
            ->set('data.sms_enabled', true)
            ->call('save')
            ->assertHasErrors(['data.sms_api_url']);

        $this->assertNull(Setting::get('sms', 'api_url'));
    }

    public function test_sms_settings_can_save_disabled_empty_provider_state(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(SmsSettings::class)
            ->set('data.sms_api_url', '')
            ->set('data.sms_username', '')
            ->set('data.sms_password', '')
            ->set('data.sms_subscriber_no', '')
            ->set('data.sms_sender_title', '')
            ->set('data.sms_enabled', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('0', Setting::get('sms', 'enabled'));
        $this->assertSame('', Setting::get('sms', 'api_url'));
    }

    public function test_reports_keep_revenue_metrics_on_non_cancelled_orders(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $paidProduct = Product::create([
            'name' => ['tr' => 'Gelir Urunu'],
            'slug' => 'gelir-urunu',
            'price' => 100,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        $cancelledProduct = Product::create([
            'name' => ['tr' => 'Iptal Urunu'],
            'slug' => 'iptal-urunu',
            'price' => 900,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        $paidOrder = $this->createOrder('paid', 100);
        $cancelledOrder = $this->createOrder('cancelled', 900);

        OrderItem::create([
            'order_id' => $paidOrder->id,
            'product_id' => $paidProduct->id,
            'product_name' => 'Gelir Urunu',
            'quantity' => 1,
            'unit_price' => 100,
            'total_price' => 100,
        ]);

        OrderItem::create([
            'order_id' => $cancelledOrder->id,
            'product_id' => $cancelledProduct->id,
            'product_name' => 'Iptal Urunu',
            'quantity' => 1,
            'unit_price' => 900,
            'total_price' => 900,
        ]);

        $data = Livewire::actingAs($admin)
            ->test(ReportsAnalytics::class)
            ->set('dateFrom', now()->subDay()->format('Y-m-d'))
            ->set('dateTo', now()->addDay()->format('Y-m-d'))
            ->instance()
            ->getViewData();

        $this->assertSame(100.0, (float) $data['totalRevenue']);
        $this->assertSame(2, $data['totalOrders']);
        $this->assertSame(100.0, (float) $data['avgOrderValue']);
        $this->assertSame('Gelir Urunu', $data['topProducts']->first()->getTranslation('name', 'tr'));
        $this->assertSame(100.0, (float) $data['dailyRevenue']->first()->revenue);
    }

    private function createOrder(string $status, int $total): Order
    {
        return Order::create([
            'status' => $status,
            'subtotal' => $total,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'loyalty_points_used' => 0,
            'total' => $total,
            'payment_method' => 'credit_card',
            'sender_name' => 'Admin Test',
            'sender_phone' => '05551234567',
            'sender_email' => 'admin@example.test',
            'recipient_name' => 'Recipient',
            'recipient_phone' => '05551234567',
            'recipient_address' => 'Test address',
            'delivery_date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
