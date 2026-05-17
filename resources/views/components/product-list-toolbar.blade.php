@props([
    'total' => 0,
    'sort' => 'recommended',
    'sortMode' => null,
])

<div class="mb-4 flex min-w-0 flex-col gap-3 rounded-[1.35rem] border border-black/6 bg-white/82 px-4 py-3 shadow-[0_10px_26px_rgba(34,24,40,0.05)] dark:border-white/10 dark:bg-white/8 sm:flex-row sm:items-center sm:justify-between">
    <p class="min-w-0 text-sm text-rg-grayText dark:text-white/82">
        <span class="font-semibold tabular-nums text-rg-darkText dark:text-white">{{ number_format($total, 0, ',', '.') }}</span>
        {{ __('ürün bulunuyor') }}
    </p>
    <div class="flex min-w-0 flex-col gap-2 sm:flex-row sm:items-center sm:gap-3 sm:justify-end">
        <span class="text-xs font-semibold uppercase tracking-wide text-rg-midPurple dark:text-rg-lavender sm:sr-only">{{ __('Sırala') }}</span>
        <div class="flex min-w-0 w-full items-center gap-2 sm:w-auto sm:gap-3">
            <span class="hidden shrink-0 text-sm font-medium text-rg-darkText dark:text-white/85 sm:inline">{{ __('Sırala') }}:</span>
            <x-sort-dropdown :sort="$sort" :sort-mode="$sortMode" />
        </div>
    </div>
</div>
