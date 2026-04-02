<div x-data="{ open: false }" class="relative">
    <button type="button" @click="open = !open" class="text-xs border border-rg-lightLavender rounded px-2 py-1" aria-label="Language">
        {{ strtoupper(app()->getLocale()) }}
    </button>
    <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-2 w-20 bg-white rounded shadow z-40">
        @php
            $currentPath = request()->path();
            $strippedPath = preg_replace('/^(tr|en|ku)(\/|$)/', '', $currentPath);
        @endphp
        @foreach (['tr', 'en', 'ku'] as $lang)
            <a href="{{ url($lang . '/' . $strippedPath) }}" class="block px-3 py-2 text-xs hover:bg-rg-lightLavender/40 {{ app()->getLocale() === $lang ? 'font-semibold text-rg-purple' : '' }}">
                {{ strtoupper($lang) }}
            </a>
        @endforeach
    </div>
</div>
