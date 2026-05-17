@props([
    'question',
])

<div {{ $attributes->merge(['class' => 'overflow-hidden rounded-xl border border-rg-lightLavender bg-white shadow-sm dark:border-white/10 dark:bg-rg-deepPurple/35']) }} x-data="{ open: false }">
    <button
        type="button"
        class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left transition hover:bg-rg-cream/60 dark:hover:bg-white/5"
        @click="open = !open"
        :aria-expanded="open"
    >
        <span class="pr-2 text-sm font-semibold text-rg-darkText dark:text-white md:text-base">{{ $question }}</span>
        <svg
            class="h-5 w-5 shrink-0 text-rg-purple transition-transform duration-300 ease-out dark:text-rg-lavender"
            :class="open ? 'rotate-180' : ''"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
            aria-hidden="true"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        x-cloak
        class="border-t border-rg-lightLavender/80 dark:border-white/10"
    >
        <div class="px-5 py-4 text-sm leading-relaxed text-rg-grayText dark:text-white/75 md:text-[15px] md:leading-7">
            {{ $slot }}
        </div>
    </div>
</div>
