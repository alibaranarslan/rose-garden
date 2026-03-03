<button
    type="button"
    wire:click="toggle"
    class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 border border-rg-lightLavender text-sm"
    aria-label="Toggle favorite"
>
    {{ $isFavorited ? '❤' : '♡' }}
</button>
