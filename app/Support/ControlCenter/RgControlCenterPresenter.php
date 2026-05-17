<?php

namespace App\Support\ControlCenter;

use App\Filament\Resources\PaymentResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class RgControlCenterPresenter
{
    public function present(array $data): array
    {
        $attentionItems = collect($data['attention'] ?? [])->take(4)->values();
        $queueRows = collect($data['urgent_orders']['rows'] ?? [])->take(6)->values();
        $pendingPayments = collect($data['payment_exceptions']['pending_payments'] ?? [])->take(4)->values();
        $failedNotifications = collect($data['payment_exceptions']['failed_notifications'] ?? [])->take(4)->values();
        $stockRisk = collect($data['fulfillment_risk']['stock_risk'] ?? [])->take(3)->values();
        $todayDeliveries = collect($data['fulfillment_risk']['today_deliveries'] ?? [])->take(3)->values();
        $todayRevenue = (float) ($data['daily_summary']['today_revenue'] ?? 0);
        $todayOrders = (int) ($data['daily_summary']['today_orders'] ?? 0);
        $averageOrder = (float) ($data['daily_summary']['average_order_value'] ?? 0);
        $recoveryCount = (int) ($data['recovery_status']['eligible_cart_count'] ?? 0);
        $dueEvents = (int) ($data['recovery_status']['due_event_count'] ?? 0);
        $loyaltyAccounts = (int) ($data['recovery_status']['loyalty_accounts'] ?? 0);
        $windowRevenue = (float) ($data['daily_summary']['window_revenue'] ?? 0);
        $windowOrders = (int) ($data['daily_summary']['window_orders'] ?? 0);
        $publishedAt = $this->formatDateTime($data['storefront_status']['published_at'] ?? null);
        $draftUpdatedAt = $this->formatDateTime($data['storefront_status']['draft_updated_at'] ?? null);
        $paymentResourceUrl = PaymentResource::getUrl(panel: 'admin');

        return [
            'hero' => [
                'title' => 'Operasyon Masası',
                'summary' => 'Sipariş, ödeme, fulfillment ve geri kazanımı tek operasyon yüzeyinde yönetin.',
                'window_label' => Arr::get($data, 'filters.window_label', 'Son 7 gün'),
                'lens_label' => Arr::get($data, 'filters.lens_label', 'Tüm siparişler'),
                'last_refreshed_at' => $data['header']['last_refreshed_at'] ?? now(),
                'primary_action' => [
                    'label' => 'Siparişleri Aç',
                    'url' => Arr::get($data, 'header.primary_action.url', '#'),
                ],
                'secondary_action' => [
                    'label' => 'Ödemeleri Aç',
                    'url' => $paymentResourceUrl,
                ],
                'guide_label' => 'Yönetim Panelini Tanı',
            ],
            'signals' => [
                [
                    'label' => 'Bugünkü ciro',
                    'value' => 'TL '.number_format($todayRevenue, 2, ',', '.'),
                    'meta' => 'İptal ve iade hariç net gelir',
                    'tone' => $todayRevenue > 0 ? 'success' : 'neutral',
                    'bars' => $this->miniBars([$todayRevenue / 160, $todayRevenue / 190, $windowRevenue / 420, $averageOrder / 18]),
                ],
                [
                    'label' => 'Bekleyen ödeme',
                    'value' => number_format($pendingPayments->count()),
                    'meta' => $pendingPayments->isNotEmpty() ? 'Listede ilk 4 kayıt gösteriliyor' : 'Kritik ödeme baskısı yok',
                    'tone' => $pendingPayments->isNotEmpty() ? 'warning' : 'neutral',
                    'bars' => $this->miniBars([$queueRows->count() * 12, $pendingPayments->count() * 18, 34, 26]),
                ],
                [
                    'label' => 'Bugün teslimat',
                    'value' => number_format($todayDeliveries->count()),
                    'meta' => $todayDeliveries->isNotEmpty() ? 'Teslimat akışı açık' : 'Bugün teslimat baskısı yok',
                    'tone' => $todayDeliveries->isNotEmpty() ? 'warning' : 'success',
                    'bars' => $this->miniBars([$todayDeliveries->count() * 18, $stockRisk->count() * 20, 28, 16]),
                ],
                [
                    'label' => 'Geri kazanım adayı',
                    'value' => number_format($recoveryCount),
                    'meta' => $dueEvents > 0 ? number_format($dueEvents).' hatırlatma zamanı geldi' : 'Hatırlatma kuyruğu sakin',
                    'tone' => $recoveryCount > 0 ? 'warning' : 'neutral',
                    'bars' => $this->miniBars([$recoveryCount * 18, $dueEvents * 22, $loyaltyAccounts / 2, 20]),
                ],
            ],
            'attention' => [
                'title' => 'Bugün Müdahale Gerektirenler',
                'summary' => 'Önce çözülecek sipariş, ödeme ve bildirim sinyalleri.',
                'items' => $attentionItems->map(fn (array $item): array => [
                    'title' => $item['title'] ?? 'Operasyon sinyali',
                    'meta' => $item['meta'] ?? 'Durum',
                    'body' => $item['body'] ?? 'Detay kaydı bu alanda görüntülenir.',
                    'action_label' => $item['action_label'] ?? 'Aç',
                    'url' => $item['url'] ?? '#',
                    'tone' => $item['tone'] ?? 'neutral',
                ])->all(),
            ],
            'primary_queue' => [
                'title' => 'Acil Sipariş Kuyruğu',
                'summary' => 'Bugün işleme alınacak siparişleri tek tabloda ayırın.',
                'rows' => $queueRows->map(fn (array $row): array => [
                    'order_number' => $row['order_number'] ?? 'Sipariş',
                    'customer' => $row['customer'] ?? 'Müşteri yok',
                    'status' => $row['status'] ?? 'Bekliyor',
                    'status_tone' => $row['status_tone'] ?? 'neutral',
                    'delivery' => $row['delivery'] ?? 'Planlanmadı',
                    'total' => $row['total'] ?? 'TL 0,00',
                    'url' => $row['url'] ?? '#',
                ])->all(),
            ],
            'payment_exceptions' => [
                'title' => 'Ödeme ve Bildirim İstisnaları',
                'summary' => 'Bekleyen ödeme kayıtları ve son başarısız iletişim denemeleri.',
                'pending_payments' => $pendingPayments->all(),
                'failed_notifications' => $failedNotifications->all(),
                'payments_url' => $paymentResourceUrl,
            ],
            'fulfillment' => [
                'title' => 'Teslimat ve Fulfillment Riski',
                'summary' => 'Stok baskısı ile bugün teslim edilecek siparişleri aynı yerde görün.',
                'stock_risk' => $stockRisk->all(),
                'today_deliveries' => $todayDeliveries->all(),
            ],
            'storefront' => [
                'title' => 'Vitrin ve Kampanya Durumu',
                'summary' => 'Vitrin yayını ile kampanya sinyallerini kısa okuyun.',
                'state' => $data['storefront_status']['state'] ?? 'Kayıt yok',
                'tone' => $data['storefront_status']['tone'] ?? 'neutral',
                'active_coupons' => (int) ($data['storefront_status']['active_coupons'] ?? 0),
                'upcoming_occasions' => (int) ($data['storefront_status']['upcoming_occasions'] ?? 0),
                'nearest_occasion' => $data['storefront_status']['nearest_occasion'] ?: 'Kayıt yok',
                'published_at' => $publishedAt,
                'draft_updated_at' => $draftUpdatedAt,
                'url' => $data['storefront_status']['url'] ?? '#',
            ],
            'recovery' => [
                'title' => 'Geri Kazanım ve Sadakat',
                'summary' => 'Sepet hatırlatma, etkinlik ve puan sinyallerini birlikte yönetin.',
                'cards' => [
                    [
                        'label' => 'Hatırlatma adayı sepet',
                        'value' => number_format($recoveryCount),
                        'meta' => 'Toplam değer TL '.number_format((float) ($data['recovery_status']['eligible_cart_value'] ?? 0), 2, ',', '.'),
                        'tone' => $recoveryCount > 0 ? 'warning' : 'success',
                    ],
                    [
                        'label' => 'Zamanı gelen etkinlik',
                        'value' => number_format($dueEvents),
                        'meta' => 'Özel gün hatırlatması bekleyen kayıt',
                        'tone' => $dueEvents > 0 ? 'warning' : 'neutral',
                    ],
                    [
                        'label' => 'Puan bakiyeli müşteri',
                        'value' => number_format($loyaltyAccounts),
                        'meta' => 'Sadakat görünürlüğü olan hesaplar',
                        'tone' => 'neutral',
                    ],
                ],
                'url' => $data['recovery_status']['abandoned_url'] ?? '#',
            ],
            'production_readiness' => [
                'title' => $data['production_readiness']['title'] ?? 'Canlıya Hazırlık',
                'summary' => $data['production_readiness']['summary'] ?? 'Üretim girdilerinin tamamlanma durumu.',
                'state' => $data['production_readiness']['state'] ?? 'Kontrol bekliyor',
                'tone' => $data['production_readiness']['tone'] ?? 'neutral',
                'ready_count' => (int) ($data['production_readiness']['ready_count'] ?? 0),
                'total_count' => (int) ($data['production_readiness']['total_count'] ?? 0),
                'items' => collect($data['production_readiness']['items'] ?? [])->map(fn (array $item): array => [
                    'label' => $item['label'] ?? 'Başlık',
                    'status' => $item['status'] ?? 'Kontrol',
                    'tone' => $item['tone'] ?? 'neutral',
                    'message' => $item['message'] ?? '',
                    'missing' => array_values($item['missing'] ?? []),
                    'url' => $item['url'] ?? '#',
                ])->all(),
            ],
            'daily_summary' => [
                'title' => 'Günlük Ticari Özet',
                'summary' => 'Bugünün performansını kısa görün; detay için raporlara inin.',
                'cards' => [
                    [
                        'label' => 'Bugünkü ciro',
                        'value' => 'TL '.number_format($todayRevenue, 2, ',', '.'),
                        'meta' => 'Gün içi net gelir',
                        'tone' => 'success',
                    ],
                    [
                        'label' => 'Bugünkü sipariş',
                        'value' => number_format($todayOrders),
                        'meta' => 'Yeni sipariş adedi',
                        'tone' => 'neutral',
                    ],
                    [
                        'label' => 'Ortalama sepet',
                        'value' => 'TL '.number_format($averageOrder, 2, ',', '.'),
                        'meta' => 'Bugünkü sepet ortalaması',
                        'tone' => 'neutral',
                    ],
                    [
                        'label' => 'Filtre aralığı',
                        'value' => number_format($windowOrders),
                        'meta' => 'Aralık içindeki sipariş',
                        'tone' => 'neutral',
                    ],
                ],
                'reports_url' => $data['daily_summary']['reports_url'] ?? '#',
            ],
            'quick_actions' => [
                [
                    'label' => 'Siparişler',
                    'description' => 'Ödeme, hazırlama ve teslimat akışına doğrudan girin.',
                    'url' => $data['quick_actions'][0]['url'] ?? '#',
                    'tone' => 'danger',
                ],
                [
                    'label' => 'Ürünler',
                    'description' => 'Stok, görsel ve vitrindeki ürün kalitesini yönetin.',
                    'url' => $data['quick_actions'][1]['url'] ?? '#',
                    'tone' => 'neutral',
                ],
                [
                    'label' => 'Geri kazanım',
                    'description' => 'Terk edilmiş sepetleri ve reminder akışını kontrol edin.',
                    'url' => $data['quick_actions'][2]['url'] ?? '#',
                    'tone' => 'warning',
                ],
                [
                    'label' => 'Raporlar',
                    'description' => 'Ticari desenleri ve sipariş ritmini ayrıntılı inceleyin.',
                    'url' => $data['quick_actions'][3]['url'] ?? '#',
                    'tone' => 'success',
                ],
            ],
            'ops_health' => [
                'title' => 'Operasyon Sağlığı',
                'summary' => 'Yalnız yetkili kullanıcılar için sistem ve kuyruk sinyalleri.',
                'cards' => collect($data['ops_health'] ?? [])->map(fn (array $card): array => [
                    'label' => $card['label'] ?? 'Sistem',
                    'value' => $card['value'] ?? '-',
                    'meta' => $card['meta'] ?? 'İzleme bilgisi',
                    'tone' => $card['tone'] ?? 'neutral',
                ])->all(),
            ],
            'is_ops' => (bool) ($data['is_ops'] ?? false),
        ];
    }

    private function formatDateTime(mixed $date): string
    {
        if (! $date instanceof Carbon) {
            return 'Kayıt yok';
        }

        return $date->format('d.m.Y H:i');
    }

    private function miniBars(array $values): array
    {
        return collect($values)
            ->map(fn (mixed $value): int => max(16, min(100, (int) round((float) $value))))
            ->values()
            ->all();
    }

}
