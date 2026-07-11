<button
    type="button"
    wire:click="toggle"
    class="rg-favorite-toggle inline-flex h-9 w-9 items-center justify-center rounded-full border border-rg-lightLavender bg-white/92 text-base leading-none text-rg-deepPurple shadow-sm backdrop-blur transition hover:border-rg-purple/30 hover:bg-white hover:text-rg-purple focus:outline-none focus-visible:ring-2 focus-visible:ring-rg-purple/50 dark:border-white/16 dark:bg-[#241a2e]/92 dark:text-rg-lavender dark:hover:border-rg-lavender/45 dark:hover:bg-[#2f2439] dark:hover:text-white"
    aria-label="Toggle favorite"
>
    {{ $isFavorited ? '❤' : '♡' }}
</button>
