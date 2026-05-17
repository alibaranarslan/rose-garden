@props(['items' => []])

<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'mb-5 text-xs text-rg-grayText dark:text-white/88']) }}>
    <ol class="flex min-w-0 items-center gap-1.5 overflow-x-auto pb-1 whitespace-nowrap">
        @foreach ($items as $index => $item)
            <li class="flex shrink-0 items-center gap-1.5">
                @if (!empty($item['url']))
                    <a href="{{ $item['url'] }}" class="transition-colors duration-150 hover:text-rg-purple">{{ $item['label'] }}</a>
                @else
                    <span class="max-w-[11rem] truncate font-medium text-rg-darkText dark:text-white sm:max-w-none">{{ $item['label'] }}</span>
                @endif
                @if ($index !== count($items) - 1)
                    <svg class="w-3 h-3 text-rg-lightLavender dark:text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
