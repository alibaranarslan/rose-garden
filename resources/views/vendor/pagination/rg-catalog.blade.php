@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Sayfalama') }}" class="mt-10 md:mt-12">
        <div class="flex flex-col items-stretch gap-4 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-center text-sm text-rg-grayText dark:text-white/72 sm:text-left">
                @if ($paginator->total() > 0)
                    {{ __('Sayfa') }}
                    <span class="font-semibold tabular-nums text-rg-darkText dark:text-white">{{ $paginator->currentPage() }}</span>
                    /
                    <span class="tabular-nums">{{ $paginator->lastPage() }}</span>
                    <span class="mx-1">&middot;</span>
                    <span class="tabular-nums">{{ $paginator->total() }}</span>
                    {{ __('ürün') }}
                @endif
            </p>

            <div class="flex flex-wrap items-center justify-center gap-1.5 sm:justify-end">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex min-h-[44px] min-w-[44px] cursor-not-allowed items-center justify-center rounded-xl border border-rg-lightLavender bg-rg-lightLavender/40 px-4 text-sm font-medium text-rg-grayText/60 dark:border-white/10 dark:bg-white/10 dark:text-white/35">
                        {{ __('Önceki') }}
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                       rel="prev"
                       class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-xl border border-rg-lightLavender bg-white px-4 text-sm font-semibold text-rg-darkText shadow-sm transition-colors hover:border-rg-purple hover:text-rg-purple dark:border-white/15 dark:bg-rg-deepPurple/50 dark:text-white dark:hover:border-rg-lavender dark:hover:text-rg-lavender">
                        {{ __('Önceki') }}
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center px-2 text-sm text-rg-grayText dark:text-white/70">&hellip;</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page"
                                      class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-xl bg-rg-purple px-4 text-sm font-bold text-white shadow-md dark:bg-rg-lavender dark:text-rg-deepPurple">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-xl border border-rg-lightLavender bg-white px-4 text-sm font-semibold text-rg-darkText transition-colors hover:border-rg-purple hover:text-rg-purple dark:border-white/15 dark:bg-rg-deepPurple/40 dark:text-white dark:hover:border-rg-lavender">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                       rel="next"
                       class="inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-xl border border-rg-lightLavender bg-white px-4 text-sm font-semibold text-rg-darkText shadow-sm transition-colors hover:border-rg-purple hover:text-rg-purple dark:border-white/15 dark:bg-rg-deepPurple/50 dark:text-white dark:hover:border-rg-lavender dark:hover:text-rg-lavender">
                        {{ __('Sonraki') }}
                    </a>
                @else
                    <span class="inline-flex min-h-[44px] min-w-[44px] cursor-not-allowed items-center justify-center rounded-xl border border-rg-lightLavender bg-rg-lightLavender/40 px-4 text-sm font-medium text-rg-grayText/60 dark:border-white/10 dark:bg-white/10 dark:text-white/35">
                        {{ __('Sonraki') }}
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
