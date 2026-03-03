<div wire:poll.5s="refreshCount">
    <a href="{{ route('cart') }}" class="relative hover:text-rg-purple" aria-label="Cart">
        Sepet
        <span class="absolute -top-2 -right-3 bg-rg-purple text-white text-[10px] rounded-full px-1.5 py-0.5">{{ $count }}</span>
    </a>
</div>
