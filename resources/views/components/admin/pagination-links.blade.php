@props([
    'paginationData' // Menerima data array paginasi dari controller
])

@if($paginationData && $paginationData['totalPages'] > 1)
    <div class="flex flex-col items-center justify-center gap-md sm:flex-row sm:items-center sm:justify-between border-t border-outline-variant/20 mt-lg pt-md font-medium select-none w-full">
        
        <div class="hidden sm:block text-body-sm text-secondary/60">
            Menampilkan <span class="font-bold text-primary/80">{{ $paginationData['from'] }}-{{ $paginationData['to'] }}</span> dari <span class="font-bold text-primary/80">{{ $paginationData['total'] }}</span> baris data
        </div>
        
        <div class="inline-flex items-center gap-xs text-body-sm">
            
            <button type="button" 
                    wire:click="previousPage"
                    class="w-8 h-8 rounded-xl border border-outline-variant/30 flex items-center justify-center transition-colors {{ $paginationData['currentPage'] == 1 ? 'text-secondary/40 bg-surface-container/10 cursor-not-allowed' : 'text-secondary/70 hover:bg-surface-container/40' }}"
                    {{ $paginationData['currentPage'] == 1 ? 'disabled' : '' }}>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" fill="currentColor"/></svg>
            </button>
            
            @php
                $currentPage = $paginationData['currentPage'];
                $totalPages = $paginationData['totalPages'];
                
                // Radius halaman aktif (1 kanan, 1 kiri)
                $sidePages = 1; 
                
                $startPage = max(1, $currentPage - $sidePages);
                $endPage = min($totalPages, $currentPage + $sidePages);
            @endphp

            @if($startPage > 1)
                <button type="button" wire:click="gotoPage(1)" class="w-8 h-8 rounded-xl border border-outline-variant/30 flex items-center justify-center text-secondary/70 bg-surface-container-lowest font-medium transition-all duration-150 hover:bg-[#000666]/[0.06] hover:border-[#000666]/30 hover:text-[#000666] active:scale-95">
                    1
                </button>

                @if($startPage > 2)
                    <span class="w-8 h-8 flex items-center justify-center text-secondary/40 font-bold text-xs">...</span>
                @endif
            @endif

            {{-- Perulangan Angka Utama Sekitar Current Page --}}
            @for($i = $startPage; $i <= $endPage; $i++)
                @if($i == $currentPage)
                    <button type="button" class="w-8 h-8 rounded-xl bg-[#000666] text-white flex items-center justify-center font-bold shadow-sm">
                        {{ $i }}
                    </button>
                @else
                    <button type="button" wire:click="gotoPage({{ $i }})" class="w-8 h-8 rounded-xl border border-outline-variant/30 flex items-center justify-center text-secondary/70 bg-surface-container-lowest font-medium transition-all duration-150 hover:bg-[#000666]/[0.06] hover:border-[#000666]/30 hover:text-[#000666] active:scale-95">
                        {{ $i }}
                    </button>
                @endif
            @endfor

            @if($endPage < $totalPages)
                @if($endPage < $totalPages - 1)
                    <span class="w-8 h-8 flex items-center justify-center text-secondary/40 font-bold text-xs">...</span>
                @endif
                <button type="button" wire:click="gotoPage({{ $totalPages }})" class="w-8 h-8 rounded-xl border border-outline-variant/30 flex items-center justify-center text-secondary/70 bg-surface-container-lowest font-medium transition-all duration-150 hover:bg-[#000666]/[0.06] hover:border-[#000666]/30 hover:text-[#000666] active:scale-95">
                    {{ $totalPages }}
                </button>
            @endif
            
            <button type="button" 
                    wire:click="nextPage"
                    class="w-8 h-8 rounded-xl border border-outline-variant/30 flex items-center justify-center transition-colors {{ $paginationData['currentPage'] == $paginationData['totalPages'] ? 'text-secondary/40 bg-surface-container/10 cursor-not-allowed' : 'text-secondary/70 hover:bg-surface-container/40' }}"
                    {{ $paginationData['currentPage'] == $paginationData['totalPages'] ? 'disabled' : '' }}>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z" fill="currentColor"/></svg>
            </button>

        </div>
    </div>
@endif