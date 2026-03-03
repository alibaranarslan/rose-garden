@props(['category'])

<a href="{{ route('products.category', ['slug' => data_get($category, 'slug')]) }}"
   class="group block relative overflow-hidden rounded-card aspect-square shadow-sm hover:shadow-md transition-all duration-300">
    {{-- Background image --}}
    <img src="{{ data_get($category, 'image') ?? asset('images/placeholder.svg') }}"
         alt="{{ data_get($category, 'name') }}"
         loading="lazy"
         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
    {{-- Gradient overlay --}}
    <div class="absolute inset-0 bg-gradient-to-t from-rg-deepPurple/80 via-rg-deepPurple/20 to-transparent group-hover:from-rg-darkPlum/85 transition-all duration-300"></div>
    {{-- Category name --}}
    <div class="absolute bottom-0 inset-x-0 p-3 text-center">
        <span class="text-white font-semibold text-sm md:text-base drop-shadow-sm">{{ data_get($category, 'name') }}</span>
    </div>
</a>
