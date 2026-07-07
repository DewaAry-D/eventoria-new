<div x-data="{ open: false }" 
        x-on:open-modal-filter-event.window="open = true" 
        x-on:close-modal.window="if($event.detail.id === 'filter-event-modal') open = false"
        x-show="open" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-md text-center">
        <div class="relative transform overflow-hidden rounded-2xl bg-surface-container-lowest p-lg text-left shadow-xl transition-all w-full max-w-md border border-outline-variant/30"
            @click.away="open = false">
            
            <div class="flex items-center justify-between border-b border-outline-variant/10 pb-md mb-md select-none">
                <h3 class="text-title-md font-bold text-primary tracking-tight">
                    Filter Pengajuan Event
                </h3>
                <button type="button" @click="open = false" class="text-secondary/50 hover:text-secondary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Form Filter -->
            <div class="space-y-md">
                
                <!-- Filter Kategori -->
                <div class="flex flex-col gap-xs">
                    <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide">Kategori Event</label>
                    <select wire:model.live="kategoriId" class="w-full text-body-md px-sm py-2 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary font-semibold cursor-pointer">
                        <option value="">Semua Kategori</option>
                        @foreach($listKategori as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="flex flex-col gap-xs">
                    <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide">Status Kelayakan</label>
                    <select wire:model.live="status" class="w-full text-body-md px-sm py-2 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary font-semibold cursor-pointer">
                        <option value="">Semua Status</option>
                        <option value="completed">Selesai (Kegiatan Selesai)</option>
                        <option value="published">Terbit (Telah Disetujui / Aktif)</option>
                        <option value="pending_approval">Pending (Menunggu Review)</option>
                        <option value="revision">Revisi (Butuh Revisi)</option>
                    </select>
                </div>

                <!-- Filter Periode Waktu -->
                <div class="flex flex-col gap-xs">
                    <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide">Periode Pengajuan</label>
                    <select wire:model.live="periode" class="w-full text-body-md px-sm py-2 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary font-semibold cursor-pointer">
                        <option value="">Semua Waktu</option>
                        <option value="today">Hari Ini</option>
                        <option value="this_week">Minggu Ini</option>
                        <option value="this_month">Bulan Ini</option>
                    </select>
                </div>

                <!-- Filter Wilayah Fakultas -->
                @if(!$isFakultasScope)
                    <div class="flex flex-col gap-xs">
                        <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide">Lingkup Wilayah / Fakultas</label>
                        <select wire:model.live="fakultasId" class="w-full text-body-md px-sm py-2 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary font-semibold cursor-pointer">
                            <option value="">Semua Fakultas / Universitas</option>
                            @foreach($listFakultas as $fak)
                                <option value="{{ $fak->id }}">{{ $fak->nama_fakultas }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Filter Organisasi Penyelenggara -->
                <div class="flex flex-col gap-xs">
                    <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide">Organisasi Penyelenggara</label>
                    <select wire:model.live="organisasiId" class="w-full text-body-md px-sm py-2 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary font-semibold cursor-pointer">
                        <option value="">Semua Organisasi</option>
                        @foreach($listOrganisasi as $org)
                            <option value="{{ $org->id }}">{{ $org->nama_organisasi }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="flex items-center gap-sm mt-lg pt-md border-t border-outline-variant/10">
                <button type="button" wire:click="resetFilter" class="flex-1 py-2 rounded-xl border border-outline-variant text-secondary hover:bg-surface-container font-bold text-body-md transition-all active:scale-95">
                    Reset Filter
                </button>
                <button type="button" wire:click="applyFilter" class="flex-1 py-2 rounded-xl bg-[#000666] text-white font-bold text-body-md hover:bg-[#000666]/90 transition-all active:scale-95">
                    Terapkan
                </button>
            </div>

        </div>
    </div>
</div>