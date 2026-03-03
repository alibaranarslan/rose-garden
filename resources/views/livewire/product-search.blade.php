<div class="relative">
    <input wire:model.live.debounce.300ms="query" type="text" placeholder="Urun ara..." class="w-full border rounded-btn px-3 py-2">
    @if ($results->isNotEmpty())
        <div class="absolute z-20 mt-1 w-full rounded-card border border-rg-lightLavender bg-white shadow-lg max-h-80 overflow-auto">
            @foreach ($results as $product)
                <a href="{{ route('products.show', ['slug' => $product->slug]) }}" class="flex items-center gap-3 px-3 py-2 hover:bg-rg-cream">
                    <img src="{{ $product->images->first()?->image_path ?? asset('images/placeholder.svg') }}" alt="{{ $product->name }}" class="w-10 h-10 rounded object-cover">
                    <span class="text-sm">{{ $product->name }}</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
