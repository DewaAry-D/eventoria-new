<div x-data="{ open: false }" 
        x-on:open-modal-filter-organisasi.window="open = true" 
        x-on:modal-closed.window="open = false"
        x-show="open" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
        x-cloak>
    
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-md text-center">
        <div class="relative transform overflow-hidden rounded-3xl bg-surface-container-lowest p-lg text-left shadow-xl transition-all w-full max-w-md border border-outline-variant/30"
            @click.away="open = false">
            
            <div class="flex items-center justify-between border-b border-outline-variant/10 pb-md mb-md select-none">
                <h3 class="text-title-md font-bold text-primary tracking-tight">
                    Filter Pengajuan Organisasi
                </h3>
                <button type="button" @click="open = false" class="text-secondary/50 hover:text-secondary cursor-pointer">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <div class="space-y-md">
                @if($isFakultasAdmin)
                    <div class="flex flex-col gap-xs">
                        <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide">Tingkat Struktur</label>
                        <select wire:model="filterTingkat" class="w-full text-body-md px-sm py-2 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary font-semibold cursor-pointer">
                            <option value="">Semua Tingkat</option>
                                <option value="prodi">Tingkat Program Studi (Prodi)</option>
                                <option value="fakultas">Tingkat Fakultas</option>
                        </select>
                    </div>
                @endif

                <div class="flex flex-col gap-xs">
                    <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide">Status Validasi Akun</label>
                    <select wire:model="filterStatus" class="w-full text-body-md px-sm py-2 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary font-semibold cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending (Menunggu Persetujuan)</option>
                        <option value="approved">Aktif (Disetujui)</option>
                        <option value="rejected">Ditolak (Butuh Revisi)</option>
                    </select>
                </div>

                <div class="flex flex-col gap-xs">
                    <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide">Periode Pengajuan Akun</label>
                    <select wire:model="filterPeriode" class="w-full text-body-md px-sm py-2 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary font-semibold cursor-pointer">
                        <option value="">Semua Waktu</option>
                        <option value="today">Hari Ini</option>
                        <option value="this_week">Minggu Ini</option>
                        <option value="this_month">Bulan Ini</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-sm mt-lg pt-md border-t border-t-outline-variant/10">
                <button type="button" wire:click="resetFilter" @click="open = false" class="flex-1 py-2 rounded-xl border border-outline-variant text-secondary hover:bg-surface-container font-bold text-body-md transition-all active:scale-95 cursor-pointer">
                    Reset Filter
                </button>
                
                <button type="button" wire:click="applyFilter" @click="open = false" class="flex-1 py-2 rounded-xl bg-[#000666] text-white font-bold text-body-md hover:bg-[#000666]/90 transition-all active:scale-95 cursor-pointer shadow-2xs">
                    Terapkan
                </button>
            </div>

        </div>
    </div>
</div>