<div class="relative">
    <input wire:model.live.debounce.300ms="query" type="text" placeholder="{{ __('Ürün ara...') }}" class="w-full rounded-full border border-rg-lightLavender bg-white/90 px-4 py-2.5 text-sm text-rg-darkText outline-none transition-all placeholder:text-rg-grayText focus:border-rg-purple focus:ring-2 focus:ring-rg-purple/25 dark:border-white/12 dark:bg-white/14 dark:text-white dark:placeholder:text-white/62">
    @if ($results->isNotEmpty())
        <div class="absolute z-20 mt-2 max-h-80 w-full overflow-auto rounded-[1.5rem] border border-rg-lightLavender bg-white/96 p-1 shadow-[0_18px_40px_rgba(34,24,40,0.14)] backdrop-blur-xl dark:border-white/12 dark:bg-[#23182c]/96">
            @foreach ($results as $product)
        <a href="{{ \App\Support\StorefrontLocale::route('products.show', ['slug' => $product->slug]) }}" class="flex items-center gap-3 rounded-[1.15rem] px-3 py-2.5 transition-colors hover:bg-rg-cream dark:hover:bg-white/6">
                    <img src="{{ \App\Support\StorefrontImage::publicImgSrc(\App\Support\StorefrontImage::resolveProduct($product->primaryImage, $product->slug, $product->name)) }}" alt="{{ $product->name }}" class="h-10 w-10 rounded-xl bg-rg-lightLavender/35 object-contain p-1">
                    <span class="text-sm text-rg-darkText dark:text-white">{{ $product->name }}</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
