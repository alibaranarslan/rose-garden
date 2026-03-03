@props(['items' => []])

<nav aria-label="Breadcrumb" class="text-xs text-rg-grayText mb-5">
    <ol class="flex flex-wrap items-center gap-1.5">
        @foreach ($items as $index => $item)
            <li class="flex items-center gap-1.5">
                @if (!empty($item['url']))
                    <a href="{{ $item['url'] }}" class="hover:text-rg-purple transition-colors duration-150">{{ $item['label'] }}</a>
                @else
                    <span class="text-rg-darkText font-medium">{{ $item['label'] }}</span>
                @endif
                @if ($index !== count($items) - 1)
                    <svg class="w-3 h-3 text-rg-lightLavender" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
