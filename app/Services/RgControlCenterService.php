<?php

namespace App\Services;

use App\Filament\Pages\CacheManagement;
use App\Filament\Pages\LayoutStudio;
use App\Filament\Pages\LoyaltyManagement;
use App\Filament\Pages\PaymentSettings;
use App\Filament\Pages\ReportsAnalytics;
use App\Filament\Resources\AbandonedCartResource;
use App\Filament\Resources\AdminOperationAuditResource;
use App\Filament\Resources\NotificationLogResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\ProductResource;
use App\Models\AbandonedCart;
use App\Models\AdminOperationAudit;
use App\Models\Coupon;
use App\Models\CustomerEvent;
use App\Models\LayoutRevision;
use App\Models\LoyaltyPoint;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SpecialOccasion;
use App\Models\User;
use App\Support\AdminPrivileges;
use App\Support\OrderStatus;
use App\Support\ProductionReadiness;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RgControlCenterService
{
    public function __construct(
        private readonly LayoutConfigService $layoutConfigService,
    ) {
    }

    public function snapshot(array $filters, ?User $user): array
    {
        $normalizedFilters = $this->normalizeFilters($filters);
        $window = $this->resolveWindow($normalizedFilters['window']);
        $isOps = AdminPrivileges::canManageStorefrontOperations($user);
        $thresholds = config('control_center.attention', []);

        $urgentOrders = $this->urgentOrders($normalizedFilters['lens']);
        $awaitingPaymentCount = Order::query()->where('status', 'awaiting_payment')->count();
        $pendingOrdersCount = Order::query()->where('status', 'pending')->count();
        $failedNotifications = NotificationLog::query()
            ->where('status', 'failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();
        $eligibleCarts = AbandonedCart::query()->eligibleForReminder()->count();
        $featuredOutOfStock = Product::query()->active()->featured()->where('stock_status', 'out_of_stock')->count();
        $failedJobsCount = $this->failedJobsCount();
        $latestAudit = $this->latestOperationAudit();

        $attention = collect();

        if ($pendingOrdersCount > 0) {
            $attention->push([
                'tone' => $pendingOrdersCount >= (int) ($thresholds['pending_order_warning'] ?? 5) ? 'danger' : 'warning',
                'title' => 'Bekleyen siparişler var',
                'body' => "{$pendingOrdersCount} sipariş ilk işleme alınmayı bekliyor.",
                'meta' => 'Sipariş',
                'url' => OrderResource::getUrl(panel: 'admin'),
                'action_label' => 'Siparişleri Aç',
            ]);
        }

        if ($awaitingPaymentCount > 0) {
            $attention->push([
                'tone' => 'warning',
                'title' => 'Havale takibi gerekiyor',
                'body' => "{$awaitingPaymentCount} sipariş ödeme teyidi bekliyor.",
                'meta' => 'Ödeme',
                'url' => OrderResource::getUrl(panel: 'admin'),
                'action_label' => 'Ödeme Bekleyenler',
            ]);
        }

        if ($failedNotifications > 0) {
            $attention->push([
                'tone' => $failedNotifications >= (int) ($thresholds['failed_notification_warning'] ?? 1) ? 'danger' : 'warning',
                'title' => 'Bildirim hatası görüldü',
                'body' => "Son 24 saatte {$failedNotifications} iletişim denemesi başarısız oldu.",
                'meta' => 'Bildirim',
                'url' => NotificationLogResource::getUrl(panel: 'admin'),
                'action_label' => 'Logları Aç',
            ]);
        }

        if ($eligibleCarts >= (int) ($thresholds['abandoned_cart_warning'] ?? 2)) {
            $attention->push([
                'tone' => 'warning',
                'title' => 'Geri kazanılacak sepet var',
                'body' => "{$eligibleCarts} sepet hatırlatma için uygun durumda bekliyor.",
                'meta' => 'Geri kazanım',
                'url' => AbandonedCartResource::getUrl(panel: 'admin'),
                'action_label' => 'Sepetleri Aç',
            ]);
        }

        if ($featuredOutOfStock >= (int) ($thresholds['low_stock_featured_warning'] ?? 1)) {
            $attention->push([
                'tone' => 'danger',
                'title' => 'Öne çıkan ürün stok dışı',
                'body' => "{$featuredOutOfStock} vitrinde güçlü ürün stok dışı durumda.",
                'meta' => 'Vitrin',
                'url' => ProductResource::getUrl(panel: 'admin'),
                'action_label' => 'Ürünleri Aç',
            ]);
        }

        if ($failedJobsCount > 0) {
            $attention->push([
                'tone' => 'danger',
                'title' => 'Kuyrukta başarısız iş var',
                'body' => "{$failedJobsCount} failed job kaydı operasyon müdahalesi bekliyor.",
                'meta' => 'Operasyon',
                'url' => $isOps ? CacheManagement::getUrl(panel: 'admin') : NotificationLogResource::getUrl(panel: 'admin'),
                'action_label' => $isOps ? 'Operasyon' : 'Loglar',
            ]);
        }

        $fulfillmentRisk = $this->buildFulfillmentRisk();
        $paymentExceptions = $this->buildPaymentExceptions();
        $storefrontStatus = $this->buildStorefrontStatus();
        $recoveryStatus = $this->buildRecoveryStatus();
        $dailySummary = $this->buildDailySummary($window['from'], $window['to']);

        return [
            'header' => [
                'title' => 'Operasyon Masası',
                'summary' => 'Sipariş, ödeme, vitrin ve geri kazanımı tek yerde yönetin.',
                'window_label' => $window['label'],
                'lens_label' => $normalizedFilters['lens_label'],
                'last_refreshed_at' => now(),
                'primary_action' => [
                    'label' => 'Siparişleri Aç',
                    'url' => OrderResource::getUrl(panel: 'admin'),
                ],
            ],
            'filters' => $normalizedFilters,
            'is_ops' => $isOps,
            'snapshot' => [
                [
                    'label' => 'Bugünkü Ciro',
                    'value' => 'TL '.number_format((float) $dailySummary['today_revenue'], 2, ',', '.'),
                    'meta' => 'İptal ve iade hariç gelir',
                    'tone' => 'success',
                ],
                [
                    'label' => 'Sipariş Adedi',
                    'value' => number_format($dailySummary['today_orders']),
                    'meta' => 'Bugün oluşan sipariş',
                    'tone' => 'neutral',
                ],
                [
                    'label' => 'Ort. Sipariş',
                    'value' => 'TL '.number_format((float) $dailySummary['average_order_value'], 2, ',', '.'),
                    'meta' => 'Bugünkü ortalama sepet',
                    'tone' => 'neutral',
                ],
                [
                    'label' => 'Açık Müdahale',
                    'value' => number_format(count($attention)),
                    'meta' => 'Öncelikli işaret sayısı',
                    'tone' => count($attention) > 0 ? 'warning' : 'success',
                ],
            ],
            'attention' => $attention
                ->take((int) ($thresholds['max_items'] ?? 5))
                ->values()
                ->all(),
            'urgent_orders' => [
                'title' => 'Acil İşlem Bekleyen Siparişler',
                'summary' => 'Ödeme, hazırlama ve bugün teslim akışını tek tabloda toplayın.',
                'rows' => $urgentOrders,
            ],
            'fulfillment_risk' => $fulfillmentRisk,
            'payment_exceptions' => $paymentExceptions,
            'storefront_status' => $storefrontStatus,
            'recovery_status' => $recoveryStatus,
            'production_readiness' => app(ProductionReadiness::class)->snapshot(),
            'daily_summary' => $dailySummary,
            'quick_actions' => $this->quickActions($isOps),
            'ops_health' => $isOps ? [
                [
                    'label' => 'Queue',
                    'value' => strtoupper((string) config('queue.default', 'sync')),
                    'meta' => 'Arka plan işlerinin sürücüsü',
                    'tone' => 'neutral',
                ],
                [
                    'label' => 'Failed Jobs',
                    'value' => number_format($failedJobsCount),
                    'meta' => 'Tekrar denenecek kayıt',
                    'tone' => $failedJobsCount > 0 ? 'danger' : 'success',
                ],
                [
                    'label' => 'Cache',
                    'value' => strtoupper((string) config('cache.default', 'file')),
                    'meta' => 'Admin ve storefront cache katmanı',
                    'tone' => 'neutral',
                ],
                [
                    'label' => 'Yedekleme',
                    'value' => class_exists(\Spatie\Backup\BackupServiceProvider::class) ? 'Uygulama içi' : 'Hosting katmanı',
                    'meta' => 'Canlı backup zinciri ayrıca doğrulanmalı',
                    'tone' => 'neutral',
                ],
                [
                    'label' => 'Son kritik işlem',
                    'value' => $latestAudit['value'],
                    'meta' => $latestAudit['meta'],
                    'tone' => $latestAudit['tone'],
                    'url' => AdminOperationAuditResource::getUrl(panel: 'admin'),
                ],
            ] : [],
        ];
    }

    private function normalizeFilters(array $filters): array
    {
        $window = in_array(($filters['window'] ?? null), ['today', '7d', '30d'], true)
            ? $filters['window']
            : '7d';
        $lens = in_array(($filters['lens'] ?? null), ['all', 'payments', 'delivery'], true)
            ? $filters['lens']
            : 'all';

        return [
            'window' => $window,
            'window_label' => match ($window) {
                'today' => 'Bugün',
                '30d' => 'Son 30 gün',
                default => 'Son 7 gün',
            },
            'lens' => $lens,
            'lens_label' => match ($lens) {
                'payments' => 'Ödeme odaklı',
                'delivery' => 'Teslimat odaklı',
                default => 'Tüm siparişler',
            },
        ];
    }

    private function resolveWindow(string $window): array
    {
        return match ($window) {
            'today' => [
                'from' => now()->startOfDay(),
                'to' => now()->endOfDay(),
                'label' => 'Bugün',
            ],
            '30d' => [
                'from' => now()->subDays(30)->startOfDay(),
                'to' => now()->endOfDay(),
                'label' => 'Son 30 gün',
            ],
            default => [
                'from' => now()->subDays(7)->startOfDay(),
                'to' => now()->endOfDay(),
                'label' => 'Son 7 gün',
            ],
        };
    }

    private function urgentOrders(string $lens): array
    {
        $query = Order::query()
            ->with(['deliveryTimeSlot', 'payment'])
            ->where(function ($builder): void {
                $builder
                    ->whereIn('status', ['pending', 'awaiting_payment', 'paid', 'preparing'])
                    ->orWhere(function ($deliveryQuery): void {
                        $deliveryQuery
                            ->whereDate('delivery_date', today())
                            ->whereNotIn('status', ['delivered', 'cancelled', 'refunded']);
                    });
            });

        if ($lens === 'payments') {
            $query->whereIn('status', ['pending', 'awaiting_payment']);
        }

        if ($lens === 'delivery') {
            $query->whereDate('delivery_date', today());
        }

        return $query
            ->orderByRaw("
                CASE
                    WHEN status = 'awaiting_payment' THEN 1
                    WHEN status = 'pending' THEN 2
                    WHEN delivery_date = CURRENT_DATE THEN 3
                    WHEN status = 'preparing' THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('delivery_date')
            ->orderByDesc('created_at')
            ->limit((int) config('control_center.orders.urgent_limit', 8))
            ->get()
            ->map(function (Order $order): array {
                return [
                    'order_number' => $order->order_number,
                    'customer' => $order->sender_name,
                    'status' => OrderStatus::label($order->status),
                    'status_tone' => $this->orderTone($order->status),
                    'payment' => $order->payment_method === 'bank_transfer' ? 'Havale/EFT' : 'Kart',
                    'delivery' => $order->delivery_date?->format('d.m.Y').' / '.($order->deliveryTimeSlot?->label ?? 'Slot yok'),
                    'total' => 'TL '.number_format((float) $order->total, 2, ',', '.'),
                    'url' => OrderResource::getUrl('edit', ['record' => $order], panel: 'admin'),
                ];
            })
            ->all();
    }

    private function buildFulfillmentRisk(): array
    {
        $stockRisk = Product::query()
            ->active()
            ->featured()
            ->where('stock_status', 'out_of_stock')
            ->limit(4)
            ->get()
            ->map(fn (Product $product): array => [
                'title' => $product->getTranslation('name', 'tr'),
                'meta' => 'Öne çıkan ürün stok dışı',
                'url' => ProductResource::getUrl('edit', ['record' => $product], panel: 'admin'),
            ])
            ->all();

        $todayDeliveries = Order::query()
            ->with('deliveryTimeSlot')
            ->whereDate('delivery_date', today())
            ->whereNotIn('status', ['delivered', 'cancelled', 'refunded'])
            ->orderBy('delivery_time_slot_id')
            ->limit(5)
            ->get()
            ->map(fn (Order $order): array => [
                'title' => $order->order_number.' / '.$order->recipient_name,
                'meta' => OrderStatus::label($order->status).' - '.($order->deliveryTimeSlot?->label ?? 'Slot yok'),
                'url' => OrderResource::getUrl('edit', ['record' => $order], panel: 'admin'),
            ])
            ->all();

        return [
            'title' => 'Teslimat ve fulfillment riski',
            'summary' => 'Stok sorunu ve bugün teslim edilecek siparişleri erken ayırt edin.',
            'stock_risk' => $stockRisk,
            'today_deliveries' => $todayDeliveries,
        ];
    }

    private function buildPaymentExceptions(): array
    {
        $pendingPayments = Payment::query()
            ->with('order')
            ->where('status', 'pending')
            ->latest('id')
            ->limit(4)
            ->get()
            ->map(function (Payment $payment): array {
                return [
                    'title' => optional($payment->order)->order_number ?? 'Sipariş bağlantısı yok',
                    'meta' => ($payment->payment_method === 'bank_transfer' ? 'Havale' : 'Kart')." / TL ".number_format((float) $payment->amount, 2, ',', '.'),
                    'url' => OrderResource::getUrl('edit', ['record' => $payment->order_id], panel: 'admin'),
                ];
            })
            ->all();

        $failedNotifications = NotificationLog::query()
            ->where('status', 'failed')
            ->latest('created_at')
            ->limit(4)
            ->get()
            ->map(function (NotificationLog $log): array {
                return [
                    'title' => strtoupper((string) $log->channel).' / '.$log->recipient,
                    'meta' => $log->subject ?: 'Bildirim logu',
                    'url' => NotificationLogResource::getUrl(panel: 'admin'),
                ];
            })
            ->all();

        return [
            'title' => 'Ödeme ve bildirim istisnaları',
            'summary' => 'Ödeme akışı ve iletişim zincirindeki bekleyen ya da hatalı kayıtlar.',
            'pending_payments' => $pendingPayments,
            'failed_notifications' => $failedNotifications,
        ];
    }

    private function buildStorefrontStatus(): array
    {
        $draftRevision = $this->layoutConfigService->getDraftRevision();
        $publishedRevision = $this->layoutConfigService->getPublishedRevision();
        $draftState = $draftRevision->payload ?? [];
        $publishedState = $publishedRevision?->payload ?? [];
        $hasPendingChanges = $publishedRevision === null || md5(json_encode($draftState)) !== md5(json_encode($publishedState));
        $activeCoupons = Coupon::query()->active()->count();
        $upcomingOccasions = SpecialOccasion::query()->active()->get()->filter(fn (SpecialOccasion $occasion): bool => $occasion->isUpcoming(30));
        $nearestOccasion = $upcomingOccasions->sortBy(fn (SpecialOccasion $occasion): int => $occasion->daysUntil())->first();
        $latestArchived = LayoutRevision::query()
            ->where('area', LayoutConfigService::AREA_HOME)
            ->where('status', LayoutRevision::STATUS_ARCHIVED)
            ->latest('updated_at')
            ->first();

        return [
            'title' => 'Vitrin ve kampanya durumu',
            'summary' => 'Yayın, kampanya ve özel gün akışlarını aynı yerde okuyun.',
            'state' => $hasPendingChanges ? 'Taslak farklı' : 'Canlı ile senkron',
            'tone' => $hasPendingChanges ? 'warning' : 'success',
            'published_at' => $publishedRevision?->published_at,
            'draft_updated_at' => $draftRevision?->updated_at,
            'active_coupons' => $activeCoupons,
            'upcoming_occasions' => $upcomingOccasions->count(),
            'nearest_occasion' => $nearestOccasion?->getTranslation('name', 'tr'),
            'archived_revision_at' => $latestArchived?->updated_at,
            'url' => LayoutStudio::getUrl(panel: 'admin'),
        ];
    }

    private function buildRecoveryStatus(): array
    {
        $eligibleCarts = AbandonedCart::query()->eligibleForReminder()->get();
        $dueEvents = CustomerEvent::query()->active()->get()->filter(fn (CustomerEvent $event): bool => $event->isDueForReminder());
        $loyaltyAccounts = LoyaltyPoint::query()->where('balance', '>', 0)->count();

        return [
            'title' => 'Geri kazanım ve sadakat',
            'summary' => 'Sepet geri kazanımı, özel gün hatırlatması ve puan baskısını takip edin.',
            'eligible_cart_count' => $eligibleCarts->count(),
            'eligible_cart_value' => (float) $eligibleCarts->sum('total_value'),
            'due_event_count' => $dueEvents->count(),
            'loyalty_accounts' => $loyaltyAccounts,
            'abandoned_url' => AbandonedCartResource::getUrl(panel: 'admin'),
            'loyalty_url' => LoyaltyManagement::getUrl(panel: 'admin'),
        ];
    }

    private function buildDailySummary($from, $to): array
    {
        $todayOrders = Order::query()
            ->whereDate('created_at', today())
            ->count();
        $todayRevenue = Order::query()
            ->whereDate('created_at', today())
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->sum('total');
        $averageOrderValue = $todayOrders > 0 ? round($todayRevenue / $todayOrders, 2) : 0.0;

        return [
            'title' => 'Günlük ticari özet',
            'summary' => 'Filtre aralığına bakarken bugünün ticari nabzını da sabit gösterir.',
            'today_revenue' => $todayRevenue,
            'today_orders' => $todayOrders,
            'average_order_value' => $averageOrderValue,
            'window_orders' => Order::query()->whereBetween('created_at', [$from, $to])->count(),
            'window_revenue' => Order::query()
                ->whereBetween('created_at', [$from, $to])
                ->whereNotIn('status', ['cancelled', 'refunded'])
                ->sum('total'),
            'reports_url' => ReportsAnalytics::getUrl(panel: 'admin'),
        ];
    }

    private function quickActions(bool $isOps): array
    {
        $actions = [
            [
                'label' => 'Siparişler',
                'description' => 'Ödeme, hazırlama ve teslimat akışına müdahale edin.',
                'url' => OrderResource::getUrl(panel: 'admin'),
                'tone' => 'danger',
            ],
            [
                'label' => 'Ürünler',
                'description' => 'Stok, görsel ve vitrin kalitesini hızla düzenleyin.',
                'url' => ProductResource::getUrl(panel: 'admin'),
                'tone' => 'neutral',
            ],
            [
                'label' => 'Geri kazanım',
                'description' => 'Terk edilen sepetleri ve reminder akışlarını kontrol edin.',
                'url' => AbandonedCartResource::getUrl(panel: 'admin'),
                'tone' => 'warning',
            ],
            [
                'label' => 'Raporlar',
                'description' => 'Ciro ve sipariş desenlerini derin inceleyin.',
                'url' => ReportsAnalytics::getUrl(panel: 'admin'),
                'tone' => 'success',
            ],
        ];

        if ($isOps) {
            $actions[] = [
                'label' => 'Yerleşim Stüdyosu',
                'description' => 'Storefront yayın kararını ve blok sırasını yönetin.',
                'url' => LayoutStudio::getUrl(panel: 'admin'),
                'tone' => 'warning',
            ];
            $actions[] = [
                'label' => 'Kritik ayarlar',
                'description' => 'Ödeme ve sistem akışlarını son kez kontrol edin.',
                'url' => PaymentSettings::getUrl(panel: 'admin'),
                'secondary_url' => CacheManagement::getUrl(panel: 'admin'),
                'secondary_label' => 'Operasyon',
                'tone' => 'neutral',
            ];
        }

        return $actions;
    }

    private function failedJobsCount(): int
    {
        if (! Schema::hasTable('failed_jobs')) {
            return 0;
        }

        return (int) DB::table('failed_jobs')->count();
    }

    private function latestOperationAudit(): array
    {
        if (! Schema::hasTable('admin_operation_audits')) {
            return [
                'value' => 'Kurulum bekliyor',
                'meta' => 'Audit migration henüz çalışmamış',
                'tone' => 'warning',
            ];
        }

        $audit = AdminOperationAudit::query()->latest('created_at')->first();

        if (! $audit) {
            return [
                'value' => 'Kayıt yok',
                'meta' => 'Riskli aksiyon çalışınca burada görünür',
                'tone' => 'neutral',
            ];
        }

        return [
            'value' => $audit->action,
            'meta' => trim(($audit->user?->email ?? 'Sistem').' / '.$audit->created_at?->diffForHumans()),
            'tone' => $audit->status === 'failed' ? 'danger' : ($audit->status === 'blocked' ? 'warning' : 'success'),
        ];
    }

    private function orderTone(string $status): string
    {
        return match ($status) {
            'awaiting_payment', 'pending' => 'danger',
            'preparing', 'paid' => 'warning',
            default => 'neutral',
        };
    }
}
