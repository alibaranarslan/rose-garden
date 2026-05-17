<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\LoyaltyManagement;
use App\Filament\Resources\AbandonedCartResource\Pages\ListAbandonedCarts;
use App\Filament\Resources\CustomerEventResource\Pages\CreateCustomerEvent;
use App\Filament\Resources\DataRequestResource\Pages\EditDataRequest;
use App\Filament\Resources\NotificationTemplateResource\Pages\CreateNotificationTemplate;
use App\Filament\Resources\NotificationTemplateResource\Pages\EditNotificationTemplate;
use App\Livewire\CheckoutWizard;
use App\Models\AbandonedCart;
use App\Models\CartItem;
use App\Models\CustomerEvent;
use App\Models\DataRequest;
use App\Models\DeliveryTimeSlot;
use App\Models\DeliveryZone;
use App\Models\LoyaltyPoint;
use App\Models\LoyaltyTransaction;
use App\Models\NotificationTemplate;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class AdminCustomerComplianceAutomationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_data_request_completion_sets_completed_timestamp(): void
    {
        $admin = $this->adminUser();
        $customer = User::factory()->create(['is_admin' => false, 'is_active' => true]);
        $request = DataRequest::query()->create([
            'user_id' => $customer->id,
            'type' => 'export',
            'status' => 'pending',
            'reason' => 'KVKK export',
        ]);

        Livewire::actingAs($admin)
            ->test(EditDataRequest::class, ['record' => $request->getRouteKey()])
            ->set('data.status', 'completed')
            ->set('data.admin_notes', 'Export delivered')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('completed', $request->fresh()->status);
        $this->assertNotNull($request->fresh()->completed_at);

        Livewire::actingAs($admin)
            ->test(EditDataRequest::class, ['record' => $request->getRouteKey()])
            ->set('data.status', 'processing')
            ->call('save')
            ->assertHasErrors(['data.status']);

        $this->assertSame('completed', $request->fresh()->status);
        $this->assertNotNull($request->fresh()->completed_at);
    }

    public function test_admin_customer_event_rejects_invalid_calendar_and_reminder_values(): void
    {
        $admin = $this->adminUser();
        $customer = User::factory()->create(['is_admin' => false, 'is_active' => true]);

        Livewire::actingAs($admin)
            ->test(CreateCustomerEvent::class)
            ->set('data.user_id', $customer->id)
            ->set('data.event_type', 'birthday')
            ->set('data.event_label', '  Hatalı tarih  ')
            ->set('data.recipient_name', '  Ayşe  ')
            ->set('data.event_month', '2')
            ->set('data.event_day', '31')
            ->set('data.detected_from', 'manual')
            ->set('data.reminder_days_before', 120)
            ->set('data.is_active', true)
            ->call('create')
            ->assertHasErrors(['data.event_day', 'data.reminder_days_before']);

        $this->assertDatabaseMissing('customer_events', [
            'user_id' => $customer->id,
            'event_label' => 'Hatalı tarih',
        ]);
    }

    public function test_customer_event_due_check_ignores_invalid_legacy_dates(): void
    {
        $event = CustomerEvent::query()->create([
            'user_id' => User::factory()->create(['is_admin' => false, 'is_active' => true])->id,
            'event_type' => 'birthday',
            'event_month' => 2,
            'event_day' => 31,
            'detected_from' => 'manual',
            'reminder_days_before' => 5,
            'is_active' => true,
        ]);

        $this->assertFalse($event->isDueForReminder());
    }

    public function test_admin_loyalty_rules_and_manual_points_reflect_in_checkout(): void
    {
        Notification::fake();

        $admin = $this->adminUser();
        $customer = User::factory()->create(['is_admin' => false, 'is_active' => true]);
        $this->actingAs($customer);

        Livewire::actingAs($admin)
            ->test(LoyaltyManagement::class)
            ->assertSee('Manuel puan işlemini uygula')
            ->set('data.earn_rate', '8')
            ->set('data.min_use_amount', '300')
            ->set('data.expiry_months', '6')
            ->call('saveRules')
            ->assertHasNoErrors();

        $this->assertSame('0.08', Setting::get('loyalty', 'earn_rate'));
        $this->assertSame('300', Setting::get('loyalty', 'min_use_amount'));

        Livewire::actingAs($admin)
            ->test(LoyaltyManagement::class)
            ->set('manualData.user_id', $customer->id)
            ->set('manualData.points', '120')
            ->set('manualData.operation', 'add')
            ->set('manualData.reason', 'Phase5 manual bonus')
            ->call('processManualPoints')
            ->assertHasNoErrors();

        $this->assertSame('120.00', LoyaltyPoint::query()->where('user_id', $customer->id)->value('balance'));
        $this->assertDatabaseHas('loyalty_transactions', [
            'user_id' => $customer->id,
            'type' => 'earned',
            'amount' => 120,
            'description' => '[Admin] Phase5 manual bonus',
        ]);

        $zone = DeliveryZone::query()->create([
            'name' => 'Phase5 Zone',
            'fee' => 0,
            'min_free_amount' => null,
            'cutoff_time' => '22:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $slot = DeliveryTimeSlot::query()->create([
            'label' => '18:00 - 20:00',
            'start_time' => '18:00',
            'end_time' => '20:00',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $product = Product::query()->create([
            'name' => ['tr' => 'Phase5 Product'],
            'slug' => 'phase5-product',
            'price' => 200,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);
        CartItem::query()->create([
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        Livewire::actingAs($customer)
            ->test(CheckoutWizard::class)
            ->set('step', 3)
            ->set('senderName', 'Customer')
            ->set('senderPhone', '05000000000')
            ->set('senderEmail', $customer->email)
            ->set('recipientName', 'Recipient')
            ->set('recipientPhone', '05000000001')
            ->set('recipientAddress', 'Ataturk Bulvari No: 1')
            ->set('recipientDistrict', 'Merkez')
            ->set('deliveryDate', now()->addDay()->toDateString())
            ->set('deliveryTimeSlotId', $slot->id)
            ->set('deliveryZoneId', $zone->id)
            ->set('paymentMethod', 'bank_transfer')
            ->set('useLoyaltyPoints', true)
            ->set('loyaltyPointsToUse', 20)
            ->set('distanceSalesAgreement', true)
            ->set('kvkkAcknowledgement', true)
            ->set('explicitConsent', true)
            ->call('createOrder')
            ->assertHasErrors(['loyaltyPointsToUse']);
    }

    public function test_admin_loyalty_rules_reject_extreme_financial_values(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(LoyaltyManagement::class)
            ->set('data.earn_rate', '150')
            ->set('data.min_use_amount', '-1')
            ->set('data.expiry_months', '999')
            ->call('saveRules')
            ->assertHasErrors([
                'data.earn_rate',
                'data.min_use_amount',
                'data.expiry_months',
            ]);

        $this->assertNull(Setting::get('loyalty', 'earn_rate'));
        $this->assertNull(Setting::get('loyalty', 'min_use_amount'));
        $this->assertNull(Setting::get('loyalty', 'expiry_months'));
    }

    public function test_admin_notification_template_create_and_edit_render_variables(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateNotificationTemplate::class)
            ->set('data.key', 'phase5_template')
            ->set('data.name', 'Phase5 Template')
            ->set('data.channel', 'both')
            ->set('data.is_active', true)
            ->set('data.sms_body', 'Merhaba {musteri_adi}, siparis {siparis_no}')
            ->set('data.email_subject', 'Siparis {siparis_no}')
            ->set('data.email_body', '<p>Sayin {musteri_adi}</p>')
            ->set('data.variables', ['musteri_adi', 'siparis_no'])
            ->call('create')
            ->assertHasNoErrors();

        $template = NotificationTemplate::query()->where('key', 'phase5_template')->firstOrFail();

        $this->assertSame('Siparis RG-001', $template->renderEmailSubject([
            'siparis_no' => 'RG-001',
            'musteri_adi' => 'Ali',
        ], 'tr'));
        $this->assertSame('Merhaba Ali, siparis RG-001', $template->renderSms([
            'siparis_no' => 'RG-001',
            'musteri_adi' => 'Ali',
        ], 'tr'));

        Livewire::actingAs($admin)
            ->test(EditNotificationTemplate::class, ['record' => $template->getRouteKey()])
            ->set('data.is_active', false)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertNull(NotificationTemplate::findByKey('phase5_template'));
    }

    public function test_admin_notification_template_rejects_missing_channel_content(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateNotificationTemplate::class)
            ->set('data.key', 'missing_sms_content')
            ->set('data.name', 'Eksik SMS')
            ->set('data.channel', 'sms')
            ->set('data.is_active', true)
            ->set('data.sms_body', '')
            ->call('create')
            ->assertHasErrors(['data.sms_body']);

        $this->assertDatabaseMissing('notification_templates', [
            'key' => 'missing_sms_content',
        ]);
    }

    public function test_admin_notification_template_rejects_undeclared_variables(): void
    {
        $admin = $this->adminUser();

        Livewire::actingAs($admin)
            ->test(CreateNotificationTemplate::class)
            ->set('data.key', 'undeclared_variable_template')
            ->set('data.name', 'Eksik Değişken')
            ->set('data.channel', 'sms')
            ->set('data.is_active', true)
            ->set('data.sms_body', 'Merhaba {musteri_adi}, sipariş {siparis_no}')
            ->set('data.variables', ['musteri_adi'])
            ->call('create')
            ->assertHasErrors(['data.variables']);

        $this->assertDatabaseMissing('notification_templates', [
            'key' => 'undeclared_variable_template',
        ]);
    }

    public function test_admin_abandoned_cart_reminder_action_marks_cart_and_dispatches_notification(): void
    {
        Notification::fake();

        $admin = $this->adminUser();
        $cart = AbandonedCart::query()->create([
            'session_id' => 'phase5-cart-session',
            'email' => 'guest@example.com',
            'phone' => '05000000000',
            'cart_data' => [
                ['product_id' => 10, 'quantity' => 2],
            ],
            'total_value' => 450,
            'reminder_count' => 0,
            'recovered' => false,
            'abandoned_at' => now()->subHours(5),
        ]);

        Livewire::actingAs($admin)
            ->test(ListAbandonedCarts::class)
            ->callTableAction('send_reminder', $cart)
            ->assertHasNoTableActionErrors();

        $cart->refresh();

        $this->assertSame(1, $cart->reminder_count);
        $this->assertSame('queued', $cart->last_reminder_status);
        $this->assertSame('email', $cart->last_reminder_channel);
        $this->assertNotNull($cart->last_reminded_at);
        Notification::assertSentOnDemand(\App\Notifications\AbandonedCartNotification::class);
    }

    private function adminUser(): User
    {
        return User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);
    }
}
