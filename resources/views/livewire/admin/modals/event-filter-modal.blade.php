<div x-data="{ open: false }" 
        x-on:open-modal-filter-event.window="open = true" 
        x-on:close-modal.window="if($event.detail.id === 'filter-event-modal') open = false"
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
                    Filter Pengajuan Event
                </h3>
                <button type="button" @click="open = false" class="text-secondary/50 hover:text-secondary cursor-pointer">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Form Filter (Space-y) -->
            <div class="space-y-md">
                
                <!-- Filter Kategori Event -->
                <div class="flex flex-col gap-xs" x-data="{ 
                    dropdownOpen: false, 
                    selectedLabel: 'Semua Kategori'
                }" @click.away="dropdownOpen = false">
                    <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide select-none">Kategori Event</label>
                    <div class="relative w-full">
                        <button type="button" @click="dropdownOpen = !dropdownOpen"
                                class="w-full inline-flex items-center justify-between text-body-md px-md py-2.5 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none text-primary font-semibold cursor-pointer transition-all hover:bg-surface-container/70 text-left"
                                :class="dropdownOpen ? 'border-primary/40 ring-2 ring-primary/[0.08]' : ''">
                            <span x-text="selectedLabel"></span>
                            <svg class="w-4 h-4 text-secondary/50 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180 text-primary' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="absolute left-0 right-0 mt-xs z-50 max-h-52 overflow-y-auto rounded-2xl bg-surface-container-lowest border border-outline-variant/40 shadow-lg p-xs space-y-[2px]" x-show="dropdownOpen" x-transition x-cloak>
                            <button type="button" @click="@this.set('kategoriId', null); selectedLabel = 'Semua Kategori'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer">
                                Semua Kategori
                            </button>
                            @foreach($listKategori as $kat)
                                <button type="button" @click="@this.set('kategoriId', {{ $kat->id }}); selectedLabel = '{{ addslashes($kat->nama_kategori) }}'; dropdownOpen = false" 
                                        class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer"
                                        :class="@this.get('kategoriId') == {{ $kat->id }} ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                    {{ $kat->nama_kategori }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Filter Status Kelayakan -->
                <div class="flex flex-col gap-xs" x-data="{ 
                    dropdownOpen: false, 
                    selectedLabel: 'Semua Status'
                }" @click.away="dropdownOpen = false">
                    <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide select-none">Status Kelayakan</label>
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
                            <button type="button" @click="@this.set('status', ''); selectedLabel = 'Semua Status'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer">
                                Semua Status
                            </button>
                            <button type="button" @click="@this.set('status', 'completed'); selectedLabel = 'Selesai (Kegiatan Selesai)'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('status') === 'completed' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                Selesai (Kegiatan Selesai)
                            </button>
                            <button type="button" @click="@this.set('status', 'published'); selectedLabel = 'Terbit (Telah Disetujui / Aktif)'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('status') === 'published' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                Terbit (Telah Disetujui / Aktif)
                            </button>
                            <button type="button" @click="@this.set('status', 'pending_approval'); selectedLabel = 'Pending (Menunggu Review)'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('status') === 'pending_approval' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                Pending (Menunggu Review)
                            </button>
                            <button type="button" @click="@this.set('status', 'revision'); selectedLabel = 'Revisi (Butuh Revisi)'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('status') === 'revision' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                Revisi (Butuh Revisi)
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Periode Waktu -->
                <div class="flex flex-col gap-xs" x-data="{ 
                    dropdownOpen: false, 
                    selectedLabel: 'Semua Waktu'
                }" @click.away="dropdownOpen = false">
                    <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide select-none">Periode Pengajuan</label>
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
                            <button type="button" @click="@this.set('periode', ''); selectedLabel = 'Semua Waktu'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer">
                                Semua Waktu
                            </button>
                            <button type="button" @click="@this.set('periode', 'today'); selectedLabel = 'Hari Ini'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('periode') === 'today' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                Hari Ini
                            </button>
                            <button type="button" @click="@this.set('periode', 'this_week'); selectedLabel = 'Minggu Ini'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('periode') === 'this_week' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                Minggu Ini
                            </button>
                            <button type="button" @click="@this.set('periode', 'this_month'); selectedLabel = 'Bulan Ini'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer" :class="@this.get('periode') === 'this_month' ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                Bulan Ini
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Wilayah Program Studi (Hanya Tampil Jika Akun Fakultas) -->
                @php
                    $currentAdminFakultasId = \App\Models\AdminDpm::where('user_id', auth()->id())->value('fakultas_id');
                @endphp

                @if($currentAdminFakultasId !== null)
                    <div class="flex flex-col gap-xs" x-data="{ 
                        dropdownOpen: false, 
                        selectedLabel: 'Semua Program Studi'
                    }" @click.away="dropdownOpen = false">
                        <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide select-none">Lingkup Program Studi</label>
                        <div class="relative w-full">
                            <button type="button" @click="dropdownOpen = !dropdownOpen"
                                    class="w-full inline-flex items-center justify-between text-body-md px-md py-2.5 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none text-primary font-semibold cursor-pointer transition-all hover:bg-surface-container/70 text-left"
                                    :class="dropdownOpen ? 'border-primary/40 ring-2 ring-primary/[0.08]' : ''">
                                <span x-text="selectedLabel"></span>
                                <svg class="w-4 h-4 text-secondary/50 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180 text-primary' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="absolute left-0 right-0 mt-xs z-50 max-h-52 overflow-y-auto rounded-2xl bg-surface-container-lowest border border-outline-variant/40 shadow-lg p-xs space-y-[2px]" x-show="dropdownOpen" x-transition x-cloak>
                                <button type="button" @click="@this.set('prodiId', null); selectedLabel = 'Semua Program Studi'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer">
                                    Semua Program Studi
                                </button>
                                @foreach($listProdi as $prodi)
                                    <button type="button" @click="@this.set('prodiId', {{ $prodi->id }}); selectedLabel = '{{ addslashes($prodi->nama_prodi) }}'; dropdownOpen = false" 
                                            class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer"
                                            :class="@this.get('prodiId') == {{ $prodi->id }} ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                        {{ $prodi->nama_prodi }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Filter Organisasi Penyelenggara -->
                <div class="flex flex-col gap-xs" x-data="{ 
                    dropdownOpen: false, 
                    selectedLabel: 'Semua Organisasi'
                }" @click.away="dropdownOpen = false">
                    <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide select-none">Organisasi Penyelenggara</label>
                    <div class="relative w-full">
                        <button type="button" @click="dropdownOpen = !dropdownOpen"
                                class="w-full inline-flex items-center justify-between text-body-md px-md py-2.5 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none text-primary font-semibold cursor-pointer transition-all hover:bg-surface-container/70 text-left"
                                :class="dropdownOpen ? 'border-primary/40 ring-2 ring-primary/[0.08]' : ''">
                            <span x-text="selectedLabel"></span>
                            <svg class="w-4 h-4 text-secondary/50 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180 text-primary' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        
                        <div class="absolute left-0 right-0 bottom-full mb-xs z-50 max-h-48 overflow-y-auto rounded-2xl bg-surface-container-lowest border border-outline-variant/40 shadow-lg p-xs space-y-[2px]" 
                                x-show="dropdownOpen" 
                                x-transition 
                                x-cloak>
                            <button type="button" @click="@this.set('organisasiId', null); selectedLabel = 'Semua Organisasi'; dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/50 hover:text-primary font-medium transition-colors cursor-pointer">
                                Semua Organisasi
                            </button>
                            @foreach($listOrganisasi as $org)
                                <button type="button" @click="@this.set('organisasiId', {{ $org->id }}); selectedLabel = '{{ addslashes($org->nama_organisasi) }}'; dropdownOpen = false" 
                                        class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-surface-container/80 hover:text-primary font-medium transition-colors cursor-pointer"
                                        :class="@this.get('organisasiId') == {{ $org->id }} ? 'bg-primary/[0.04] !text-primary !font-bold' : ''">
                                    {{ $org->nama_organisasi }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Modal Actions -->
            <div class="flex items-center gap-sm mt-lg pt-md border-t border-outline-variant/10">
                <button type="button" wire:click="resetFilter" class="flex-1 py-2 rounded-xl border border-outline-variant text-secondary hover:bg-surface-container font-bold text-body-md transition-all active:scale-95 cursor-pointer">
                    Reset Filter
                </button>
                <button type="button" wire:click="applyFilter" class="flex-1 py-2 rounded-xl bg-[#000666] text-white font-bold text-body-md hover:bg-[#000666]/90 transition-all active:scale-95 cursor-pointer">
                    Terapkan
                </button>
            </div>

        </div>
    </div>
</div>