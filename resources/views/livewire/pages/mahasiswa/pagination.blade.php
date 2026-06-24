@if ($paginator->hasPages())
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-3 bg-surface-container-lowest border border-outline-variant/30 rounded-xl px-4 shadow-sm select-none w-full">
        
        <div>
            <p class="text-body-sm font-medium text-secondary/60">
                Menampilkan 
                <span class="font-bold text-primary">{{ $paginator->firstItem() }}</span> 
                sampai 
                <span class="font-bold text-primary">{{ $paginator->lastItem() }}</span> 
                dari 
                <span class="font-bold text-primary">{{ $paginator->total() }}</span> 
                pengajuan
            </p>
        </div>

        <!-- Pagination Control -->
        <div class="inline-flex items-center gap-xs text-body-sm">
            
            <!-- Button Previous -->
            @if ($paginator->onFirstPage())
                <span class="w-8 h-8 rounded-xl border border-outline-variant/30 flex items-center justify-center text-secondary/40 bg-surface-container/10 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </span>
            @else
                <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" 
                        class="w-8 h-8 rounded-xl border border-outline-variant/30 flex items-center justify-center text-secondary/70 bg-surface-container-lowest font-medium transition-all hover:bg-[#000666]/[0.06] hover:border-[#000666]/30 hover:text-[#000666] active:scale-95 focus:outline-none shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="w-8 h-8 flex items-center justify-center text-secondary/40 font-bold select-none">{{ $element }}</span>
                @endif

                <!-- Array Koleksi Halaman Terdekat -->
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-8 h-8 rounded-xl bg-[#000666] text-white flex items-center justify-center font-bold shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" 
                                    class="w-8 h-8 rounded-xl border border-outline-variant/30 flex items-center justify-center text-secondary/70 bg-surface-container-lowest font-medium transition-all duration-150 hover:bg-[#000666]/[0.06] hover:border-[#000666]/30 hover:text-[#000666] active:scale-95 focus:outline-none">
                                {{ $page }}
                            </button>
                        @endif
                    @endforeach
                @endif
            @endforeach

            <!-- Button Next -->
            @if ($paginator->hasMorePages())
                <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" 
                        class="w-8 h-8 rounded-xl border border-outline-variant/30 flex items-center justify-center text-secondary/70 bg-surface-container-lowest font-medium transition-all hover:bg-[#000666]/[0.06] hover:border-[#000666]/30 hover:text-[#000666] active:scale-95 focus:outline-none shadow-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            @else
                <span class="w-8 h-8 rounded-xl border border-outline-variant/30 flex items-center justify-center text-secondary/40 bg-surface-container/10 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            @endif
        </div>
    </div>
@endif