<div x-data="{ open: false }" 
        x-on:open-modal-filter-organisasi.window="open = true" 
        x-on:modal-closed.window="open = false"
        x-show="open" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
        x-cloak>
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="open = false"></div>

    <div class="flex min-h-full items-center justify-center p-md text-center">
        <div class="relative transform overflow-hidden rounded-3xl bg-surface-container-lowest p-lg text-left shadow-xl transition-all w-full max-w-md border border-outline-variant/30"
            @click.away="open = false">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between border-b border-outline-variant/10 pb-md mb-md select-none">
                <h3 class="text-title-md font-bold text-primary tracking-tight">
                    Filter Pengajuan Organisasi
                </h3>
                <button type="button" @click="open = false" class="text-secondary/50 hover:text-secondary cursor-pointer">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Form Filter (Space-y) -->
            <div class="space-y-md">
                
                <!-- Filter Tingkat Struktur -->
                @if($isFakultasAdmin)
                    <div class="flex flex-col gap-xs" x-data="{ 
                        dropdownOpen: false, 
                        selectedLabel: 'Semua Tingkat'
                    }" 
                    x-on:reset-filter-labels.window="selectedLabel = 'Semua Tingkat'"
                    @click.away="dropdownOpen = false">
                        <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide select-none">Tingkat Struktur</label>
                        <div class="relative w-full">
                            <button type="button" @click="dropdownOpen = !dropdownOpen"
                                    class="w-full inline-flex items-center justify-between text-body-md px-md py-2.5 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none text-primary font-semibold cursor-pointer transition-all hover:bg-surface-container/70 text-left"
                                    :class="dropdownOpen ? 'border-primary/40 ring-2 ring-primary/[0.08]' : ''">
                                <span x-text="selectedLabel"></span>
                                <svg class="w-4 h-4 text-secondary/50 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180 text-primary' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="absolute left-0 right-0 mt-xs z-50 rounded-2xl bg-surface-container-lowest border border-outline-variant/40 shadow-lg p-xs space-y-[2px]" x-show="dropdownOpen" x-transition x-cloak>
                                <button type="button" @click="@this.set('tempTingkat', ''); selectedLabel = 'Semua Tingkat'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer">
                                    Semua Tingkat
                                </button>
                                <button type="button" @click="@this.set('tempTingkat', 'prodi'); selectedLabel = 'Tingkat Program Studi (Prodi)'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('tempTingkat') === 'prodi' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                    Tingkat Program Studi (Prodi)
                                </button>
                                <button type="button" @click="@this.set('tempTingkat', 'fakultas'); selectedLabel = 'Tingkat Fakultas'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('tempTingkat') === 'fakultas' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                    Tingkat Fakultas
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Filter Status Validasi Akun -->
                <div class="flex flex-col gap-xs" x-data="{ 
                    dropdownOpen: false, 
                    selectedLabel: 'Semua Status'
                }" 
                x-on:reset-filter-labels.window="selectedLabel = 'Semua Status'"
                @click.away="dropdownOpen = false">
                    <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide select-none">Status Validasi Akun</label>
                    <div class="relative w-full">
                        <button type="button" @click="dropdownOpen = !dropdownOpen"
                                class="w-full inline-flex items-center justify-between text-body-md px-md py-2.5 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none text-primary font-semibold cursor-pointer transition-all hover:bg-surface-container/70 text-left"
                                :class="dropdownOpen ? 'border-primary/40 ring-2 ring-primary/[0.08]' : ''">
                            <span x-text="selectedLabel"></span>
                            <svg class="w-4 h-4 text-secondary/50 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180 text-primary' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="absolute left-0 right-0 mt-xs z-50 rounded-2xl bg-surface-container-lowest border border-outline-variant/40 shadow-lg p-xs space-y-[2px]" x-show="dropdownOpen" x-transition x-cloak>
                            <button type="button" @click="@this.set('tempStatus', ''); selectedLabel = 'Semua Status'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer">
                                Semua Status
                            </button>
                            <button type="button" @click="@this.set('tempStatus', 'pending'); selectedLabel = 'Pending (Menunggu Persetujuan)'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('tempStatus') === 'pending' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                Pending (Menunggu Persetujuan)
                            </button>
                            <button type="button" @click="@this.set('tempStatus', 'approved'); selectedLabel = 'Aktif (Disetujui)'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('tempStatus') === 'approved' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                Aktif (Disetujui)
                            </button>
                            <button type="button" @click="@this.set('tempStatus', 'rejected'); selectedLabel = 'Ditolak (Butuh Revisi)'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('tempStatus') === 'rejected' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                Ditolak (Butuh Revisi)
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Periode Pengajuan Akun -->
                <div class="flex flex-col gap-xs" x-data="{ 
                    dropdownOpen: false, 
                    selectedLabel: 'Semua Waktu'
                }" 
                x-on:reset-filter-labels.window="selectedLabel = 'Semua Waktu'"
                @click.away="dropdownOpen = false">
                    <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide select-none">Periode Pengajuan Akun</label>
                    <div class="relative w-full">
                        <button type="button" @click="dropdownOpen = !dropdownOpen"
                                class="w-full inline-flex items-center justify-between text-body-md px-md py-2.5 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none text-primary font-semibold cursor-pointer transition-all hover:bg-surface-container/70 text-left"
                                :class="dropdownOpen ? 'border-primary/40 ring-2 ring-primary/[0.08]' : ''">
                            <span x-text="selectedLabel"></span>
                            <svg class="w-4 h-4 text-secondary/50 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180 text-primary' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div class="absolute left-0 right-0 bottom-full mb-xs z-50 rounded-2xl bg-surface-container-lowest border border-outline-variant/40 shadow-lg p-xs space-y-[2px]" x-show="dropdownOpen" x-transition x-cloak>
                            <button type="button" @click="@this.set('tempPeriode', ''); selectedLabel = 'Semua Waktu'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer">
                                Semua Waktu
                            </button>
                            <button type="button" @click="@this.set('tempPeriode', 'today'); selectedLabel = 'Hari Ini'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('tempPeriode') === 'today' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                Hari Ini
                            </button>
                            <button type="button" @click="@this.set('tempPeriode', 'this_week'); selectedLabel = 'Minggu Ini'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('tempPeriode') === 'this_week' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                Minggu Ini
                            </button>
                            <button type="button" @click="@this.set('tempPeriode', 'this_month'); selectedLabel = 'Bulan Ini'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('tempPeriode') === 'this_month' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                Bulan Ini
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Modal Actions -->
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