@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-3 bg-white border border-gray-100 rounded-xl px-4 shadow-sm">
        {{-- Progress text --}}
        <div>
            <p class="text-sm text-gray-500">
                Menampilkan 
                <span class="font-semibold text-indigo-900">{{ $paginator->firstItem() }}</span> 
                sampai 
                <span class="font-semibold text-indigo-900">{{ $paginator->lastItem() }}</span> 
                dari 
                <span class="font-semibold text-indigo-900">{{ $paginator->total() }}</span> 
                event
            </p>
        </div>

        {{-- Pagination Buttons --}}
        <div class="flex items-center gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="p-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <button wire:click="previousPage('{{ $paginator->getPageName() }}')" 
                        class="p-2 rounded-lg border border-indigo-200 bg-white text-indigo-600 hover:bg-indigo-50 hover:border-indigo-600 transition shadow-sm focus:outline-none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-1.5 text-gray-400 font-semibold">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-4 py-2 rounded-lg bg-indigo-600 border border-indigo-600 text-white font-bold shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <button wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" 
                                    class="px-4 py-2 rounded-lg border border-indigo-600 bg-white text-indigo-600 font-semibold hover:bg-indigo-50 hover:text-indigo-700 transition shadow-sm focus:outline-none">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage('{{ $paginator->getPageName() }}')" 
                        class="p-2 rounded-lg border border-indigo-200 bg-white text-indigo-600 hover:bg-indigo-50 hover:border-indigo-600 transition shadow-sm focus:outline-none">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <span class="p-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </div>
@endif