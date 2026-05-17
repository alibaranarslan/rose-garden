@props([
    'clearUrl' => null,
])

<div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-rg-lightLavender bg-rg-lightLavender/20 px-6 py-16 text-center dark:border-white/15 dark:bg-white/10">
    <div class="mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-inner dark:bg-rg-deepPurple/80">
        <svg class="h-8 w-8 text-rg-midPurple dark:text-rg-lavender" fill="none" stroke="currentColor" stroke-width="1.35" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2h-3M4 13V7a2 2 0 012-2h3m-4 9v4a2 2 0 002 2h12a2 2 0 002-2v-4M9 5h6m-6 4h6m-9 4h.01M15 16h.01"/>
        </svg>
    </div>
    <h2 class="font-display text-lg font-semibold text-rg-darkText dark:text-white">{{ __('Aradığınız kriterlere uygun ürün bulunamadı') }}</h2>
    <p class="mt-2 max-w-sm text-sm text-rg-grayText dark:text-white/82">{{ __('Filtreleri veya sıralamayı değiştirerek tekrar deneyebilirsiniz.') }}</p>
    @if($clearUrl)
        <a href="{{ $clearUrl }}"
           class="mt-6 inline-flex items-center justify-center rounded-xl bg-rg-purple px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-rg-darkPlum focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-lavender">
            {{ __('Filtreleri Temizle') }}
        </a>
    @endif
</div>
