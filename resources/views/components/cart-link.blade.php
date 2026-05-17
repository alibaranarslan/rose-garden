@props([
    'showGuestCount' => false,
])

@php
    $count = 0;

    if (auth()->check()) {
        $count = (int) \App\Models\CartItem::query()
            ->where('user_id', auth()->id())
            ->sum('quantity');
    } elseif ($showGuestCount) {
        $sessionId = session('cart_session_id');

        if ($sessionId) {
            $count = (int) \App\Models\CartItem::query()
                ->where('session_id', $sessionId)
                ->sum('quantity');
        }
    }
@endphp

<a href="{{ \App\Support\StorefrontLocale::route('cart', prefixDefault: true) }}"
   class="relative inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-btn text-rg-grayText transition-colors hover:bg-rg-lightLavender/50 hover:text-rg-purple dark:text-white/80 dark:hover:bg-white/10 dark:hover:text-rg-lavender"
   aria-label="{{ __('Sepet') . ($count > 0 ? ' (' . $count . ')' : '') }}">
    <span class="sr-only">{{ __('Sepet') }}</span>
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
              d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12C20.99 21.998 20.31 22.5 19.51 22.5h-15.02c-.81 0-1.49-.501-1.63-1.485l1.263-12A1.875 1.875 0 015.018 7.5h13.964a1.875 1.875 0 011.863 2.007z"/>
    </svg>

    @if ($count > 0)
        <span class="absolute right-0 top-0 flex h-[18px] min-w-[18px] translate-x-1 -translate-y-0.5 items-center justify-center rounded-full bg-rg-purple px-1 text-[10px] font-semibold tabular-nums leading-none text-white ring-2 ring-white dark:ring-rg-deepPurple">
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</a>

