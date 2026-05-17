@php
    $locale = app()->getLocale();
    $title = data_get($settings, "title_override.$locale")
        ?: (filled(data_get($homeContent, 'home_intro_heading')) ? data_get($homeContent, 'home_intro_heading') : __('Koleksiyona kategoriler üzerinden girin.'));
    $subtitle = data_get($settings, "subtitle_override.$locale")
        ?: (filled(data_get($homeContent, 'home_intro_body')) ? data_get($homeContent, 'home_intro_body') : __('Buketler, saksı bitkileri ve özel gün seçimleri arasında hızlı geçiş yapın.'));
    $discoveryProducts = collect($discoveryProducts ?? [])->take(3);
    $primaryCategory = $categories->first();
    $quickLinks = collect([
        [
            'label' => __('Çok Satanlar'),
            'url' => \App\Support\StorefrontLocale::route('products.index', ['sort' => 'best_sellers']),
        ],
        [
            'label' => __('Yeni Gelenler'),
            'url' => \App\Support\StorefrontLocale::route('products.index', ['sort' => 'newest']),
        ],
        [
            'label' => __('Özel Günler'),
            'url' => \App\Support\StorefrontLocale::route('special-occasions.index'),
        ],
    ]);
@endphp

<section class="rg-section">
    <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
        <div class="max-w-3xl">
            <span class="rg-kicker">{{ __('Hızlı Keşif') }}</span>
            <h2 class="mt-3 text-balance font-display text-3xl text-rg-deepPurple dark:text-white md:text-4xl">{{ $title }}</h2>
        </div>
        <div class="max-w-xl space-y-3">
            <p class="text-sm leading-relaxed text-rg-grayText dark:text-white/82">{{ $subtitle }}</p>
            <div class="flex flex-wrap gap-2.5">
                @foreach ($quickLinks as $link)
                    <a href="{{ $link['url'] }}" class="inline-flex items-center rounded-full border border-black/6 bg-white/82 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.16em] text-rg-deepPurple transition-colors hover:border-rg-purple/30 hover:text-rg-purple dark:border-white/10 dark:bg-white/8 dark:text-white/88 dark:hover:text-rg-lavender">
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-[minmax(0,1.1fr)_minmax(20rem,0.9fr)]">
        <div class="grid grid-cols-2 gap-3 md:grid-cols-3 md:gap-4">
            @foreach ($categories as $category)
                <x-category-card :category="$category" :featured="false" />
            @endforeach
        </div>

        <aside class="rounded-[1.65rem] border border-black/6 bg-white/88 px-5 py-5 shadow-[0_16px_40px_rgba(34,24,40,0.06)] dark:border-white/10 dark:bg-[#211927] md:px-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-rg-midPurple dark:text-rg-lavender/80">{{ __('Alışverişe Başla') }}</p>
                    <h3 class="mt-3 font-display text-[2rem] leading-tight text-rg-deepPurple dark:text-white">{{ __('Kategori seç, ürünü daha hızlı bul.') }}</h3>
                </div>
                @if ($primaryCategory)
                    <a href="{{ \App\Support\StorefrontLocale::route('products.category', ['slug' => $primaryCategory->slug]) }}" class="hidden shrink-0 rounded-full border border-black/6 bg-rg-cream/75 px-3 py-2 text-xs font-semibold text-rg-deepPurple transition-colors hover:border-rg-purple/35 hover:bg-rg-cream dark:border-white/10 dark:bg-white/8 dark:text-white md:inline-flex">
                        {{ $primaryCategory->name }}
                    </a>
                @endif
            </div>

            <p class="mt-3 text-sm leading-relaxed text-rg-grayText dark:text-white/82">
                {{ __('İlk bakışta kategori ve gerçek ürün birlikte görünür; satın alma akışı destek bloklarıyla değil vitrinin kendisiyle kurulur.') }}
            </p>

            <div class="mt-4 flex flex-wrap gap-2">
                <span class="rounded-full bg-rg-lightLavender px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.16em] text-rg-deepPurple dark:bg-white/10 dark:text-rg-lavender">{{ __('Hızlı Giriş') }}</span>
                <span class="rounded-full border border-black/6 bg-white/76 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.16em] text-rg-grayText dark:border-white/10 dark:bg-white/8 dark:text-white/76">{{ __('Gerçek Ürün Proof') }}</span>
            </div>

            @if ($discoveryProducts->isNotEmpty())
                <div class="mt-4 grid gap-3">
                    @foreach ($discoveryProducts as $product)
                        <x-product-card-mini :product="$product" />
                    @endforeach
                </div>
            @endif

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <a href="{{ \App\Support\StorefrontLocale::route('products.index', ['sort' => 'best_sellers']) }}" class="flex items-center justify-between rounded-[1.2rem] border border-black/6 bg-rg-cream/75 px-4 py-3 text-sm font-semibold text-rg-deepPurple transition-colors hover:border-rg-purple/35 hover:bg-rg-cream dark:border-white/10 dark:bg-white/8 dark:text-white">
                    <span>{{ __('Çok satanları gör') }}</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
                <a href="{{ \App\Support\StorefrontLocale::route('special-occasions.index') }}" class="flex items-center justify-between rounded-[1.2rem] border border-black/6 bg-rg-cream/75 px-4 py-3 text-sm font-semibold text-rg-deepPurple transition-colors hover:border-rg-purple/35 hover:bg-rg-cream dark:border-white/10 dark:bg-white/8 dark:text-white">
                    <span>{{ __('Özel gün seçkileri') }}</span>
                    <span aria-hidden="true">&rarr;</span>
                </a>
            </div>

            <div class="mt-4 rounded-[1.2rem] border border-black/6 bg-white/76 px-4 py-3 text-sm leading-relaxed text-rg-grayText dark:border-white/10 dark:bg-white/8 dark:text-white/82">
                {{ __('Önce kategori yönü alın, ardından çok satanlar veya özel gün seçkisiyle satın alma kararını hızlandırın.') }}
            </div>
        </aside>
    </div>
</section>
