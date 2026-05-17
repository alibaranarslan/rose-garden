<x-filament-panels::page class="fi-dashboard-page">
    @php
        $toneClasses = [
            'amber' => 'border-amber-200/70 bg-amber-50/80 text-amber-900 dark:border-amber-400/20 dark:bg-amber-400/10 dark:text-amber-100',
            'slate' => 'border-slate-200/80 bg-white text-slate-900 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100',
            'emerald' => 'border-emerald-200/70 bg-emerald-50/80 text-emerald-900 dark:border-emerald-400/20 dark:bg-emerald-400/10 dark:text-emerald-100',
            'rose' => 'border-rose-200/70 bg-rose-50/80 text-rose-900 dark:border-rose-400/20 dark:bg-rose-400/10 dark:text-rose-100',
            'sky' => 'border-sky-200/70 bg-sky-50/80 text-sky-900 dark:border-sky-400/20 dark:bg-sky-400/10 dark:text-sky-100',
        ];
    @endphp

    <div class="space-y-6">
        <section data-tour-anchor="dashboard.hero" class="overflow-hidden rounded-3xl border border-rose-200 bg-gradient-to-br from-rose-50 via-white to-rose-100/80 px-6 py-6 shadow-[0_24px_60px_-34px_rgba(190,24,93,0.3)] dark:border-rose-400/10 dark:from-rose-950/70 dark:via-slate-950 dark:to-rose-950/60 lg:px-8">
            <div class="grid gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(0,0.95fr)]">
                <div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full border border-rose-200 bg-white/80 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-rose-700 dark:border-rose-300/15 dark:bg-rose-300/10 dark:text-rose-100">Butik operasyon yüzeyi</span>
                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white/70 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-slate-200">Sipariş, vitrin ve iletişim birlikte</span>
                    </div>

                    <h2 class="mt-4 text-3xl font-semibold tracking-tight text-rose-950 dark:text-white sm:text-[2rem]">Mağazayı sakin ve net biçimde yönetin</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-rose-900/75 dark:text-rose-100/80">
                        Bu ekran, müşteriye dokunan ticari kararları tek bakışta görünür kılmak için düzenlendi.
                        Siparişler, vitrin akışı ve müşteri iletişimi arasında kaybolmadan ilerleyin.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($statusCards as $card)
                        <div class="rounded-2xl border border-rose-200/70 bg-white/80 px-4 py-4 backdrop-blur dark:border-rose-300/10 dark:bg-white/5">
                            <p class="text-[11px] uppercase tracking-[0.22em] text-rose-700/70 dark:text-rose-100/70">{{ $card['label'] }}</p>
                            <p class="mt-3 text-lg font-semibold text-rose-950 dark:text-white">{{ $card['value'] }}</p>
                            <p class="mt-2 text-xs text-rose-900/60 dark:text-rose-100/70">{{ $card['meta'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(0,0.95fr)]">
            <div data-tour-anchor="dashboard.quick-actions" class="rounded-3xl border border-rose-200 bg-white p-5 shadow-sm dark:border-rose-400/10 dark:bg-slate-950">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Hızlı erişim</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">İşin akışına hız kazandıran temel admin alanları.</p>

                <div data-tour-anchor="dashboard.guide-entry" class="mt-4 rounded-2xl border border-rose-200/80 bg-rose-50/80 px-4 py-4 dark:border-rose-400/15 dark:bg-rose-400/10">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Yönetim Panelini Tanı</p>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Sipariş, vitrin ve ayar ekranlarını kısa bir turla öğrenmek için öğretici modu buradan başlatın.</p>
                        </div>

                        <button
                            type="button"
                            x-data="{}"
                            x-on:click="$dispatch('rg-admin-guide:start', { key: 'dashboard-overview' })"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-rose-400 dark:text-slate-950 dark:hover:bg-rose-300"
                        >
                            Turu Başlat
                        </button>
                    </div>
                </div>

                <div class="mt-5 grid gap-3 md:grid-cols-2">
                    @foreach ($quickActions as $action)
                        <a href="{{ $action['url'] }}" class="group rounded-2xl border px-4 py-4 transition hover:-translate-y-0.5 hover:shadow-sm {{ $toneClasses[$action['tone']] ?? $toneClasses['slate'] }}">
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-2">
                                    <p class="text-sm font-semibold">{{ $action['label'] }}</p>
                                    <p class="text-sm opacity-80">{{ $action['description'] }}</p>
                                </div>
                                <x-filament::icon :icon="$action['icon']" class="h-5 w-5 shrink-0 opacity-80" />
                            </div>

                            @if (filled($action['secondary_url'] ?? null))
                                <div class="mt-4">
                                    <span class="inline-flex items-center rounded-full border border-current/15 px-3 py-1 text-xs font-medium opacity-80">
                                        {{ $action['secondary_label'] }}
                                    </span>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <div data-tour-anchor="dashboard.workflow" class="rounded-3xl border border-rose-200 bg-white p-5 shadow-sm dark:border-rose-400/10 dark:bg-slate-950">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white">Önerilen çalışma akışı</h3>
                <div class="mt-5 space-y-4">
                    @foreach ($workflowSteps as $step)
                        <div class="rounded-2xl border border-rose-100 bg-rose-50/70 px-4 py-4 dark:border-rose-400/10 dark:bg-rose-400/5">
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $step['title'] }}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $step['text'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <div data-tour-anchor="dashboard.system">
            <x-filament-widgets::widgets
                :columns="$this->getColumns()"
                :data="$this->getWidgetData()"
                :widgets="$this->getVisibleWidgets()"
            />
        </div>
    </div>
</x-filament-panels::page>
