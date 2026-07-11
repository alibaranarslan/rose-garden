<x-filament-panels::page class="fi-dashboard-page">
    @php
        $tones = [
            'danger' => 'admin-tone admin-tone--danger',
            'warning' => 'admin-tone admin-tone--warning',
            'success' => 'admin-tone admin-tone--success',
            'neutral' => 'admin-tone admin-tone--neutral',
        ];
    @endphp

    <div class="admin-workspace rg-workspace space-y-5">
        <section class="admin-panel-surface admin-masthead admin-masthead--rg" data-tour-anchor="dashboard.header">
            <div class="admin-masthead__content">
                <div>
                    <div class="admin-masthead__chips">
                        <span class="admin-chip">{{ $hero['window_label'] }}</span>
                        <span class="admin-chip admin-chip--accent">{{ $hero['lens_label'] }}</span>
                    </div>
                    <h2 class="admin-masthead__title">{{ $hero['title'] }}</h2>
                    <p class="admin-masthead__summary">{{ $hero['summary'] }}</p>
                    <div class="admin-masthead__meta">
                        <span>Son yenilenme {{ $hero['last_refreshed_at']->format('d.m.Y H:i') }}</span>
                        <span>{{ count($attention['items']) > 0 ? count($attention['items']).' kritik kayıt' : 'Kritik kayıt yok' }}</span>
                    </div>
                </div>

                <div class="admin-masthead__actions" data-tour-anchor="dashboard.guide-entry">
                    <a href="{{ $hero['primary_action']['url'] }}" class="admin-btn admin-btn--primary">{{ $hero['primary_action']['label'] }}</a>
                    <a href="{{ $hero['secondary_action']['url'] }}" class="admin-btn admin-btn--ghost">{{ $hero['secondary_action']['label'] }}</a>
                    <button type="button" x-data="{}" x-on:click="$dispatch('rg-admin-guide:start', { key: 'dashboard-overview' })" class="admin-btn admin-btn--subtle">{{ $hero['guide_label'] }}</button>
                </div>
            </div>
        </section>

        <section class="admin-signal-strip">
            @foreach ($signals as $signal)
                <article class="admin-signal {{ $tones[$signal['tone']] ?? $tones['neutral'] }}">
                    <div>
                        <p class="admin-signal__label">{{ $signal['label'] }}</p>
                        <p class="admin-signal__value">{{ $signal['value'] }}</p>
                        <p class="admin-signal__meta">{{ $signal['meta'] }}</p>
                    </div>
                    <div class="admin-mini-bars" aria-hidden="true">
                        @foreach ($signal['bars'] as $bar)
                            <span style="height: {{ $bar }}%"></span>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </section>

        <section class="admin-dashboard-grid admin-dashboard-grid--primary">
            <div class="admin-panel-surface" data-tour-anchor="dashboard.queue">
                <div class="admin-section-head">
                    <div>
                        <h3>{{ $primary_queue['title'] }}</h3>
                        <p>{{ $primary_queue['summary'] }}</p>
                    </div>
                    <span class="admin-counter">{{ count($primary_queue['rows']) }} kayıt</span>
                </div>

                <div class="admin-table-shell">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Sipariş</th>
                                <th>Müşteri</th>
                                <th>Durum</th>
                                <th>Teslimat</th>
                                <th>Toplam</th>
                                <th class="text-right">İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($primary_queue['rows'] as $row)
                                <tr>
                                    <td class="admin-table__title">{{ $row['order_number'] }}</td>
                                    <td>{{ $row['customer'] }}</td>
                                    <td><span class="admin-inline-pill {{ $tones[$row['status_tone']] ?? $tones['neutral'] }}">{{ $row['status'] }}</span></td>
                                    <td>{{ $row['delivery'] }}</td>
                                    <td>{{ $row['total'] }}</td>
                                    <td class="text-right"><a href="{{ $row['url'] }}" class="admin-link">Aç</a></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="admin-empty-state">Bu filtrede acil sipariş kaydı görünmüyor.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="admin-panel-surface" data-tour-anchor="dashboard.attention">
                <div class="admin-section-head">
                    <div>
                        <h3>{{ $attention['title'] }}</h3>
                        <p>{{ $attention['summary'] }}</p>
                    </div>
                </div>

                <div class="admin-rail-list">
                    @forelse ($attention['items'] as $item)
                        <article class="admin-rail-item {{ $tones[$item['tone']] ?? $tones['neutral'] }}">
                            <div>
                                <div class="admin-rail-item__meta-row">
                                    <p class="admin-rail-item__title">{{ $item['title'] }}</p>
                                    <span class="admin-inline-kicker">{{ $item['meta'] }}</span>
                                </div>
                                <p class="admin-rail-item__body">{{ $item['body'] }}</p>
                            </div>
                            <a href="{{ $item['url'] }}" class="admin-link">{{ $item['action_label'] }}</a>
                        </article>
                    @empty
                        <div class="admin-empty-state admin-empty-state--calm">Şu anda müdahale gerektiren kayıt görünmüyor.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <div class="admin-dashboard-grid admin-dashboard-grid--secondary">
            <section class="admin-panel-surface" data-tour-anchor="dashboard.fulfillment">
                <div class="admin-section-head">
                    <div>
                        <h3>{{ $fulfillment['title'] }}</h3>
                        <p>{{ $fulfillment['summary'] }}</p>
                    </div>
                </div>

                <div class="admin-dashboard-grid admin-dashboard-grid--secondary admin-dashboard-grid--tight">
                    <div class="admin-inset-surface">
                        <p class="admin-subhead">Stok riski</p>
                        <div class="admin-list-cards">
                            @forelse ($fulfillment['stock_risk'] as $item)
                                <a href="{{ $item['url'] }}" class="admin-list-card">
                                    <div>
                                        <p class="admin-list-card__title">{{ $item['title'] }}</p>
                                        <p class="admin-list-card__meta">{{ $item['meta'] }}</p>
                                    </div>
                                </a>
                            @empty
                                <div class="admin-empty-state">Öne çıkan stok riski görünmüyor.</div>
                            @endforelse
                        </div>
                    </div>
                    <div class="admin-inset-surface">
                        <p class="admin-subhead">Bugün teslimatlar</p>
                        <div class="admin-list-cards">
                            @forelse ($fulfillment['today_deliveries'] as $item)
                                <a href="{{ $item['url'] }}" class="admin-list-card">
                                    <div>
                                        <p class="admin-list-card__title">{{ $item['title'] }}</p>
                                        <p class="admin-list-card__meta">{{ $item['meta'] }}</p>
                                    </div>
                                </a>
                            @empty
                                <div class="admin-empty-state">Bugün teslimat baskısı görünmüyor.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <section class="admin-panel-surface" data-tour-anchor="dashboard.storefront">
                <div class="admin-section-head">
                    <div>
                        <h3>{{ $storefront['title'] }}</h3>
                        <p>{{ $storefront['summary'] }}</p>
                    </div>
                    <a href="{{ $storefront['url'] }}" class="admin-link">Yerleşim Stüdyosu</a>
                </div>

                <article class="admin-inline-status {{ $tones[$storefront['tone']] ?? $tones['neutral'] }}">
                    <div>
                        <p class="admin-inline-status__title">{{ $storefront['state'] }}</p>
                        <p class="admin-inline-status__meta">Aktif kupon {{ $storefront['active_coupons'] }} / yaklaşan özel gün {{ $storefront['upcoming_occasions'] }}</p>
                    </div>
                </article>

                <div class="admin-kpi-grid admin-kpi-grid--double">
                    <article class="admin-kpi admin-tone admin-tone--neutral">
                        <p class="admin-kpi__label">En yakın kampanya</p>
                        <p class="admin-kpi__value">{{ $storefront['nearest_occasion'] }}</p>
                        <p class="admin-kpi__meta">Takvimde en yakın operasyon baskısı</p>
                    </article>
                    <article class="admin-kpi admin-tone admin-tone--neutral">
                        <p class="admin-kpi__label">Son yayın</p>
                        <p class="admin-kpi__value">{{ $storefront['published_at'] }}</p>
                        <p class="admin-kpi__meta">Storefront’a alınan son sürüm</p>
                    </article>
                    <article class="admin-kpi admin-tone admin-tone--neutral admin-kpi--full">
                        <p class="admin-kpi__label">Taslak güncelleme</p>
                        <p class="admin-kpi__value">{{ $storefront['draft_updated_at'] }}</p>
                        <p class="admin-kpi__meta">Yayınlanmamış vitrin değişikliği için son iz</p>
                    </article>
                </div>
            </section>
        </div>

        <div class="admin-dashboard-grid admin-dashboard-grid--secondary">
            <section class="admin-panel-surface">
                <div class="admin-section-head">
                    <div>
                        <h3>{{ $payment_exceptions['title'] }}</h3>
                        <p>{{ $payment_exceptions['summary'] }}</p>
                    </div>
                    <a href="{{ $payment_exceptions['payments_url'] }}" class="admin-link">Ödemeler</a>
                </div>
                <div class="admin-dashboard-grid admin-dashboard-grid--secondary admin-dashboard-grid--tight">
                    <div class="admin-inset-surface">
                        <p class="admin-subhead">Bekleyen ödemeler</p>
                        <div class="admin-list-cards">
                            @forelse ($payment_exceptions['pending_payments'] as $item)
                                <a href="{{ $item['url'] }}" class="admin-list-card">
                                    <div>
                                        <p class="admin-list-card__title">{{ $item['title'] }}</p>
                                        <p class="admin-list-card__meta">{{ $item['meta'] }}</p>
                                    </div>
                                </a>
                            @empty
                                <div class="admin-empty-state">Bekleyen ödeme kaydı görünmüyor.</div>
                            @endforelse
                        </div>
                    </div>
                    <div class="admin-inset-surface">
                        <p class="admin-subhead">Başarısız bildirimler</p>
                        <div class="admin-list-cards">
                            @forelse ($payment_exceptions['failed_notifications'] as $item)
                                <a href="{{ $item['url'] }}" class="admin-list-card">
                                    <div>
                                        <p class="admin-list-card__title">{{ $item['title'] }}</p>
                                        <p class="admin-list-card__meta">{{ $item['meta'] }}</p>
                                    </div>
                                </a>
                            @empty
                                <div class="admin-empty-state">Son kayıtlarda başarısız bildirim görünmüyor.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </section>

            <section class="admin-panel-surface" data-tour-anchor="dashboard.revenue">
                <div class="admin-section-head">
                    <div>
                        <h3>{{ $daily_summary['title'] }}</h3>
                        <p>{{ $daily_summary['summary'] }}</p>
                    </div>
                    <a href="{{ $daily_summary['reports_url'] }}" class="admin-link">Raporlar</a>
                </div>
                <div class="admin-kpi-grid admin-kpi-grid--double">
                    @foreach ($daily_summary['cards'] as $card)
                        <article class="admin-kpi {{ $tones[$card['tone']] ?? $tones['neutral'] }}">
                            <p class="admin-kpi__label">{{ $card['label'] }}</p>
                            <p class="admin-kpi__value">{{ $card['value'] }}</p>
                            <p class="admin-kpi__meta">{{ $card['meta'] }}</p>
                            @if (! empty($card['url']))
                                <a href="{{ $card['url'] }}" class="admin-link">Kayıtları aç</a>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="admin-dashboard-grid admin-dashboard-grid--secondary">
            <section class="admin-panel-surface" data-tour-anchor="dashboard.quick-actions">
                <div class="admin-section-head">
                    <div>
                        <h3>Hızlı Müdahale</h3>
                        <p>Derin sayfalara inmeden bugünün öncelikli operasyon aksiyonları.</p>
                    </div>
                </div>
                <div class="admin-action-grid">
                    @foreach ($quick_actions as $action)
                        <a href="{{ $action['url'] }}" class="admin-action-card {{ $tones[$action['tone']] ?? $tones['neutral'] }}">
                            <p class="admin-action-card__title">{{ $action['label'] }}</p>
                            <p class="admin-action-card__meta">{{ $action['description'] }}</p>
                        </a>
                    @endforeach
                </div>
            </section>

            <section class="admin-panel-surface">
                <div class="admin-section-head">
                    <div>
                        <h3>{{ $recovery['title'] }}</h3>
                        <p>{{ $recovery['summary'] }}</p>
                    </div>
                    <a href="{{ $recovery['url'] }}" class="admin-link">Geri kazanım</a>
                </div>
                <div class="admin-kpi-grid admin-kpi-grid--triple">
                    @foreach ($recovery['cards'] as $card)
                        <article class="admin-kpi {{ $tones[$card['tone']] ?? $tones['neutral'] }}">
                            <p class="admin-kpi__label">{{ $card['label'] }}</p>
                            <p class="admin-kpi__value">{{ $card['value'] }}</p>
                            <p class="admin-kpi__meta">{{ $card['meta'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        </div>

        <section class="admin-panel-surface" data-tour-anchor="dashboard.production-readiness">
            <div class="admin-section-head">
                <div>
                    <h3>{{ $production_readiness['title'] }}</h3>
                    <p>{{ $production_readiness['summary'] }}</p>
                </div>
                <span class="admin-inline-pill {{ $tones[$production_readiness['tone']] ?? $tones['neutral'] }}">
                    {{ $production_readiness['state'] }}
                </span>
            </div>

            <div class="admin-kpi-grid admin-kpi-grid--quad">
                @foreach ($production_readiness['items'] as $item)
                    <article class="admin-kpi {{ $tones[$item['tone']] ?? $tones['neutral'] }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="admin-kpi__label">{{ $item['label'] }}</p>
                                <p class="admin-kpi__value">{{ $item['status'] }}</p>
                            </div>
                            <a href="{{ $item['url'] }}" class="admin-link">Ayarla</a>
                        </div>
                        <p class="admin-kpi__meta">{{ $item['message'] }}</p>
                        @if (count($item['missing']) > 0)
                            <p class="admin-kpi__meta">Eksik: {{ implode(', ', $item['missing']) }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        @if ($is_ops)
            <section class="admin-panel-surface">
                <div class="admin-section-head">
                    <div>
                        <h3>{{ $ops_health['title'] }}</h3>
                        <p>{{ $ops_health['summary'] }}</p>
                    </div>
                </div>
                <div class="admin-kpi-grid admin-kpi-grid--quad">
                    @foreach ($ops_health['cards'] as $card)
                        <article class="admin-kpi {{ $tones[$card['tone']] ?? $tones['neutral'] }}">
                            <p class="admin-kpi__label">{{ $card['label'] }}</p>
                            <p class="admin-kpi__value">{{ $card['value'] }}</p>
                            <p class="admin-kpi__meta">{{ $card['meta'] }}</p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-filament-panels::page>
