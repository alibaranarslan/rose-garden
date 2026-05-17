@php
    $trustItems = [
        [
            'title' => __('Aynı gün teslimat'),
            'body' => __('Uygun saat aralığında hazırlanan siparişler şehir içinde aynı gün çıkabilir.'),
        ],
        [
            'title' => __('Not kartı ve yönlendirme'),
            'body' => __('Mesaj, teslim notu ve kısa tercih bilgileri sipariş akışında net biçimde eklenir.'),
        ],
        [
            'title' => __('İnsana yakın destek'),
            'body' => __('Kararsız kalan müşteriler için WhatsApp ve iletişim hattı görünür tutulur.'),
        ],
    ];
@endphp

<section class="rg-section">
    <div class="rounded-[1.75rem] border border-black/6 bg-white/86 px-5 py-5 shadow-[0_16px_38px_rgba(34,24,40,0.06)] dark:border-white/10 dark:bg-[#211927] md:px-6">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <span class="rg-kicker">{{ __('Sipariş Güvencesi') }}</span>
                <h2 class="mt-3 font-display text-3xl leading-tight text-rg-deepPurple dark:text-white">{{ __('Kararı destekleyen kısa ama net güven sinyalleri') }}</h2>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ \App\Support\StorefrontLocale::route('delivery.info') }}" class="inline-flex items-center justify-center rounded-full bg-rg-deepPurple px-5 py-3 text-sm font-semibold text-white transition-colors hover:bg-rg-purple">
                    {{ __('Teslimat bilgileri') }}
                </a>
                <a href="{{ \App\Support\StorefrontLocale::route('contact') }}" class="rg-button-secondary">
                    {{ __('Destek al') }}
                </a>
            </div>
        </div>

        <div class="mt-5 grid gap-3 md:grid-cols-3">
            @foreach ($trustItems as $item)
                <article class="rounded-[1.3rem] border border-black/6 bg-rg-cream/76 px-4 py-4 dark:border-white/10 dark:bg-white/8">
                    <h3 class="text-base font-semibold text-rg-deepPurple dark:text-white">{{ $item['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-rg-grayText dark:text-white/80">{{ $item['body'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
