<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\ReportsAnalytics;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportsAndOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();

        parent::tearDown();
    }

    public function test_admin_can_open_reports_and_operations_surfaces(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Operasyon Masası')
            ->assertSee('Bugün Müdahale Gerektirenler')
            ->assertSee('Ödeme ve Bildirim İstisnaları')
            ->assertSee('Geri Kazanım ve Sadakat');

        $this->actingAs($admin)
            ->get('/admin/reports-analytics')
            ->assertOk()
            ->assertSee('Raporlar ve Analitik');

        $this->actingAs($admin)
            ->get('/admin/cache-management')
            ->assertOk()
            ->assertSee('Yönetimi');
    }

    public function test_reports_export_action_downloads_csv(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ReportsAnalytics::class)
            ->call('exportCsv')
            ->assertFileDownloaded('rose-garden-analytics.csv');
    }

    public function test_reports_normalizes_unsafe_date_ranges_and_unknown_periods(): void
    {
        CarbonImmutable::setTestNow('2026-05-11 12:00:00');

        $page = app(ReportsAnalytics::class);
        $page->dateFrom = '2020-01-01';
        $page->dateTo = '2030-01-01';

        $data = $page->getViewData();

        $this->assertSame('2025-05-11', $page->dateFrom);
        $this->assertSame('2026-05-11', $page->dateTo);
        $this->assertSame('11.05.2025 - 11.05.2026', $data['periodLabel']);

        $page->setPeriod('all-time');

        $this->assertSame('30days', $page->period);
        $this->assertSame('2026-04-11', $page->dateFrom);
        $this->assertSame('2026-05-11', $page->dateTo);
    }

    public function test_reports_top_products_excludes_zero_revenue_and_refunded_orders(): void
    {
        CarbonImmutable::setTestNow('2026-05-11 12:00:00');

        $sold = Product::query()->create([
            'name' => ['tr' => 'Rapor Satılan Ürün'],
            'slug' => 'rapor-satilan-urun',
            'price' => 300,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);
        Product::query()->create([
            'name' => ['tr' => 'Rapor Sıfır Ürün'],
            'slug' => 'rapor-sifir-urun',
            'price' => 100,
            'stock_status' => 'in_stock',
            'status' => 'active',
        ]);

        $paidOrder = $this->order('delivered', 300);
        $paidOrder->items()->create([
            'product_id' => $sold->id,
            'product_name' => 'Rapor Satılan Ürün',
            'quantity' => 1,
            'unit_price' => 300,
            'total_price' => 300,
        ]);

        $refundedOrder = $this->order('refunded', 900);
        $refundedOrder->items()->create([
            'product_id' => $sold->id,
            'product_name' => 'Rapor Satılan Ürün',
            'quantity' => 1,
            'unit_price' => 900,
            'total_price' => 900,
        ]);

        $page = app(ReportsAnalytics::class);
        $page->mount();
        $data = $page->getViewData();

        $this->assertSame(300.0, (float) $data['totalRevenue']);
        $this->assertSame(['rapor-satilan-urun'], $data['topProducts']->pluck('slug')->all());
        $this->assertSame(300.0, (float) $data['topProducts']->first()->revenue);
    }

    private function order(string $status, float $total): Order
    {
        return Order::query()->create([
            'status' => $status,
            'subtotal' => $total,
            'delivery_fee' => 0,
            'discount_amount' => 0,
            'loyalty_points_used' => 0,
            'total' => $total,
            'payment_method' => 'bank_transfer',
            'sender_name' => 'Ali Test',
            'sender_phone' => '05000000000',
            'sender_email' => 'ali@example.com',
            'recipient_name' => 'Ayse Test',
            'recipient_phone' => '05000000001',
            'recipient_address' => 'Ataturk Bulvari No: 1',
            'recipient_district' => 'Merkez',
            'delivery_date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
