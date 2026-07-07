@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-3 bg-surface-container-lowest border border-outline-variant rounded-xl px-4 shadow-sm">
        {{-- Progress text --}}
        <div>
            <p class="text-sm text-on-surface-variant">
                Menampilkan 
                <span class="font-semibold text-primary">{{ $paginator->firstItem() }}</span> 
                sampai 
                <span class="font-semibold text-primary">{{ $paginator->lastItem() }}</span> 
                dari 
                <span class="font-semibold text-primary">{{ $paginator->total() }}</span> 
                event
            </p>
        </div>

        {{-- Pagination Buttons --}}
        <div class="flex items-center gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="p-2 rounded-lg border border-outline-variant bg-surface-container-low text-outline cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <button wire:click="previousPage('{{ $paginator->getPageName() }}')" 
                        class="p-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-primary hover:bg-surface-container-low hover:border-primary transition shadow-sm focus:outline-none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-1.5 text-outline font-semibold">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-4 py-2 rounded-lg bg-primary border border-primary text-on-primary font-bold shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" 
                                    class="px-4 py-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-primary font-semibold hover:bg-surface-container-low hover:text-primary transition shadow-sm focus:outline-none">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage('{{ $paginator->getPageName() }}')" 
                        class="p-2 rounded-lg border border-outline-variant bg-surface-container-lowest text-primary hover:bg-surface-container-low hover:border-primary transition shadow-sm focus:outline-none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <span class="p-2 rounded-lg border border-outline-variant bg-surface-container-low text-outline cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </div>
@endif
