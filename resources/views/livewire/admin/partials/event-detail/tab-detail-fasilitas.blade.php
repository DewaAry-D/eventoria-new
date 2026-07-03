<div class="w-full flex flex-col gap-md lg:gap-lg animate-fade-in select-none">

    <!-- 4 Card Utama -->
    <div class="grid grid-cols-2 gap-sm sm:gap-md w-full">
        @if($isLiveMode)
            <!-- Kuota Total -->
            <div class="p-md bg-surface-container-lowest rounded-2xl shadow-card flex items-center gap-md min-w-0 transition-all duration-300 hover:translate-y-[-2px]">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0">
                    <svg width="20" height="20" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="2" y="3.5" width="18" height="13" rx="2" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M6.5 18.5h9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M11 15.5v3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <circle cx="11" cy="10" r="2" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M7.5 10h1.5M13 10h1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M11 6.5V8M11 12v1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-caption text-secondary/50 font-bold block tracking-wider uppercase">Kuota Total</span>
                    <span class="text-title-sm sm:text-title-md font-extrabold text-primary block mt-0.5 font-sans truncate">{{ number_format($kuotaTotal, 0, ',', '.') }}</span>
                </div>
            </div>
    
            <!-- Pendaftar -->
            <div class="p-md bg-surface-container-lowest rounded-2xl shadow-card flex items-center gap-md min-w-0 transition-all duration-300 hover:translate-y-[-2px]">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-success/5 text-success flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 sm:w-5.5 sm:h-5.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-caption text-secondary/50 font-bold block tracking-wider uppercase">Pendaftar</span>
                    <span class="text-title-sm sm:text-title-md font-extrabold text-success block mt-0.5 font-sans truncate">{{ number_format($pendaftarAktif, 0, ',', '.') }}</span>
                </div>
            </div>
    
            <!-- Sisa Slot -->
            <div class="p-md bg-surface-container-lowest rounded-2xl shadow-card flex items-center gap-md min-w-0 transition-all duration-300 hover:translate-y-[-2px]">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-warning/5 text-warning flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 sm:w-5.5 sm:h-5.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-caption text-secondary/50 font-bold block tracking-wider uppercase">Sisa Slot</span>
                    <span class="text-title-sm sm:text-title-md font-extrabold text-warning block mt-0.5 font-sans truncate">{{ number_format($sisaKuota, 0, ',', '.') }}</span>
                </div>
            </div>
    
            <!-- Keterisian -->
            <div class="p-md bg-surface-container-lowest rounded-2xl shadow-card flex items-center gap-md min-w-0 transition-all duration-300 hover:translate-y-[-2px]">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-primary-fixed/40 text-primary flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 sm:w-5.5 sm:h-5.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z"/><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-caption text-secondary/50 font-bold block tracking-wider uppercase">Keterisian</span>
                    <span class="text-title-sm sm:text-title-md font-extrabold text-primary block mt-0.5 font-sans truncate">{{ $persenTerisi }}%</span>
                </div>
            </div>
        @else
            <!-- Skala Wilayah -->
            <div class="p-md bg-surface-container-lowest rounded-2xl shadow-card flex items-center gap-md min-w-0">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 sm:w-5.5 sm:h-5.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-caption text-secondary/50 font-bold block tracking-wider uppercase">Skala Wilayah</span>
                    <span class="text-body-md font-bold text-on-surface block mt-0.5 capitalize truncate">Tingkat {{ $event->tingkat_event ?? 'Prodi' }}</span>
                </div>
            </div>
    
            <!-- Target Kuota -->
            <div class="p-md bg-surface-container-lowest rounded-2xl shadow-card flex items-center gap-md min-w-0">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-secondary-fixed/50 text-on-secondary-fixed-variant flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 sm:w-5.5 sm:h-5.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-caption text-secondary/50 font-bold block tracking-wider uppercase">Target Kuota</span>
                    <span class="text-body-md font-bold text-on-surface block mt-0.5 font-sans truncate">{{ number_format($event->kuota ?? 0, 0, ',', '.') }} Mahasiswa</span>
                </div>
            </div>
    
            <!-- Media Poster -->
            @php $hasPoster = !empty($event->flyer_url); @endphp
            <div class="p-md bg-surface-container-lowest rounded-2xl shadow-card flex items-center gap-md min-w-0">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl flex items-center justify-center shrink-0 {{ $hasPoster ? 'bg-success/5 text-success' : 'bg-error/5 text-error' }}">
                    <svg class="w-5 h-5 sm:w-5.5 sm:h-5.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-caption text-secondary/50 font-bold block tracking-wider uppercase">Media Poster</span>
                    <span class="text-body-md font-bold block mt-0.5 truncate {{ $hasPoster ? 'text-success' : 'text-error' }}">{{ $hasPoster ? 'Poster Ready' : 'Belum Tersedia' }}</span>
                </div>
            </div>
    
            <!-- Lokasi/Ruangan -->
            @php $hasMapUrl = !empty($event->lokasi_url); @endphp
            <div class="p-md bg-surface-container-lowest rounded-2xl shadow-card flex items-center gap-md min-w-0">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0">
                    @if($isOnline)
                        <svg class="w-5 h-5 sm:w-5.5 sm:h-5.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    @else
                        <svg class="w-5 h-5 sm:w-5.5 sm:h-5.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25s-7.5-4.108-7.5-11.25a7.5 7.5 0 1115 0z"/>
                        </svg>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-caption text-secondary/50 font-bold block tracking-wider uppercase">{{ $isOnline ? 'Tautan Ruangan' : 'Lokasi Maps' }}</span>
                    <span class="text-body-md font-bold block mt-0.5 truncate {{ $hasMapUrl ? 'text-success' : 'text-on-surface/40' }}">{{ $hasMapUrl ? ($isOnline ? 'Link Ready' : 'Peta Aktif') : 'Belum Diatur' }}</span>
                </div>
            </div>
        @endif
    </div>

    <div class="w-full bg-surface-container-lowest p-md sm:p-lg rounded-3xl border border-outline-variant/30 shadow-card flex flex-col gap-sm">
        <div class="space-y-2">
            <h4 class="text-title-md font-bold text-primary tracking-tight">Deskripsi Event</h4>
            <div class="text-body-md text-on-surface-variant font-medium leading-relaxed max-w-[98%] select-text">
                <p class="whitespace-pre-line">{{ $event->deskripsi }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-md sm:gap-lg w-full items-stretch">
    
        <div class="w-full bg-surface-container-lowest p-md sm:p-lg rounded-3xl shadow-card flex flex-col gap-md min-h-[240px]">
            <div class="flex items-center gap-sm text-primary border-b border-surface-container/60 pb-sm">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 3H4C3.73478 3 3.48043 3.10536 3.29289 3.29289C3.10536 3.48043 3 3.73478 3 4V10C3 10.2652 3.10536 10.5196 3.29289 10.7071C3.48043 10.8946 3.73478 11 4 11H10C10.2652 11 10.5196 10.8946 10.7071 10.7071C10.8946 10.5196 11 10.2652 11 10V4C11 3.73478 10.8946 3.48043 10.7071 3.29289C10.5196 3.10536 10.2652 3 10 3ZM9 9H5V5H9V9ZM20 3H14C13.7348 3 13.4804 3.10536 13.2929 3.29289C13.1054 3.48043 13 3.73478 13 4V10C13 10.2652 13.1054 10.5196 13.2929 10.7071C13.4804 10.8946 13.7348 11 14 11H20C20.2652 11 20.5196 10.8946 20.7071 10.7071C20.8946 10.5196 21 10.2652 21 10V4C21 3.73478 20.8946 3.48043 20.7071 3.29289C20.5196 3.10536 20.2652 3 20 3ZM19 9H15V5H19V9ZM10 13H4C3.73478 13 3.48043 13.1054 3.29289 13.2929C3.10536 13.4804 3 13.7348 3 14V20C3 20.2652 3.10536 20.5196 3.29289 20.7071C3.48043 20.8946 3.73478 21 4 21H10C10.2652 21 10.5196 20.8946 10.7071 20.7071C10.8946 20.5196 11 20.2652 11 20V14C11 13.7348 10.8946 13.4804 10.7071 13.2929C10.5196 13.1054 10.2652 13 10 13ZM9 19H5V15H9V19ZM17 13C14.794 13 13 14.794 13 17C13 19.206 14.794 21 17 21C19.206 21 21 19.206 21 17C21 14.794 19.206 13 17 13ZM17 19C15.897 19 15 18.103 15 17C15 15.897 15.897 15 17 15C18.103 15 19 15.897 19 17C19 18.103 18.103 19 17 19Z" fill="currentColor"/>
                </svg>
                <h4 class="text-title-sm font-bold tracking-tight">Skema Kategori Tiket</h4>
            </div>
        
            <div class="w-full flex flex-col gap-sm flex-1 justify-start overflow-y-auto max-h-[220px] pr-xs
                    [&::-webkit-scrollbar]:w-1
                    [&::-webkit-scrollbar-track]:bg-transparent
                    [&::-webkit-scrollbar-track]:rounded-full
                    [&::-webkit-scrollbar-thumb]:bg-outline-variant/60
                    [&::-webkit-scrollbar-thumb]:rounded-full
                    hover:[&::-webkit-scrollbar-thumb]:bg-outline">
                @forelse($event->biayaEvents as $tiket)
                    <div class="p-md bg-surface-container-low rounded-2xl flex items-center justify-between gap-sm transition-all duration-200 hover:bg-surface-container-high shrink-0">
                        <div class="flex items-center gap-md min-w-0">
                            <span class="w-2.5 h-2.5 rounded-full {{ $tiket->biaya > 0 ? 'bg-primary' : 'bg-success' }} shrink-0"></span>
                            <span class="text-body-md text-on-surface font-bold truncate pr-xs" title="{{ $tiket->kategori }}">{{ $tiket->kategori }}</span>
                        </div>
                        <span class="text-body-md font-extrabold text-primary font-sans shrink-0">
                            {{ $tiket->biaya > 0 ? 'Rp ' . number_format($tiket->biaya, 0, ',', '.') : 'Gratis' }}
                        </span>
                    </div>
                @empty
                    <div class="my-auto py-md w-full">
                        <x-admin.empty-state 
                            title="Skema Tarif Kosong" 
                            description="Pihak panitia pelaksana belum melampirkan atau mengatur rincian skema tarif pendaftaran untuk event ini." 
                        />
                    </div>
                @endforelse
            </div>
        </div>
    
        <div class="w-full flex flex-col gap-md justify-between">
            
            @php 
                $totalBank = $event->tujuanTransfer?->count() ?? 0;
                $bankPertama = $event->tujuanTransfer?->first();
            @endphp
            <div class="p-md sm:p-lg bg-surface-container-lowest rounded-3xl shadow-card flex flex-col gap-md w-full relative flex-1"
                    x-data="{ selectedBank: 0, dropdownOpen: false, banks: {{ $listBankJson }} }">
            
                <div class="flex items-center justify-between border-b border-surface-container/60 pb-sm relative z-30">
                    <span class="text-caption text-secondary/50 font-bold block tracking-wider uppercase">Metode Pembayaran</span>
                    
                    @if($totalBank > 1)
                        <div class="relative">
                            <button @click="dropdownOpen = !dropdownOpen" @click.away="dropdownOpen = false"
                                    class="px-sm py-1.5 bg-surface-container border border-outline-variant/20 text-caption font-bold text-primary rounded-xl flex items-center gap-xs hover:bg-surface-container-high transition-colors cursor-pointer shadow-2xs">
                                <span x-text="banks[selectedBank].nama"></span>
                                <svg class="w-3 h-3 transform transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </button>
        
                            <div x-show="dropdownOpen" x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95 transform -translate-y-1"
                                    x-transition:enter-end="opacity-100 scale-100 transform translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    class="absolute right-0 mt-2 w-44 bg-surface-container-lowest border border-outline-variant/20 rounded-2xl shadow-xl py-1.5 flex flex-col z-50 overflow-hidden">
    
                                <template x-for="(bank, index) in banks" :key="index">
                                    <button @click="selectedBank = index; dropdownOpen = false"
                                            class="w-full px-md py-2.5 text-left text-body-sm font-semibold text-secondary hover:text-primary hover:bg-primary/5 transition-colors cursor-pointer flex items-center justify-between"
                                            :class="selectedBank === index ? 'bg-primary/[0.02] text-primary font-bold' : ''">
                                        <span x-text="bank.nama"></span>
                                        <span x-show="selectedBank === index" class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    @else
                        <span class="px-sm py-1 bg-primary/5 text-primary border border-primary/10 text-caption font-extrabold rounded-lg tracking-wide uppercase">
                            {{ $bankPertama->nama_bank ?? 'Bank' }}
                        </span>
                    @endif
                </div>
                
                @if($totalBank > 0)
                    <div class="my-auto animate-fade-in flex flex-col gap-xs py-sm"> 
                        <span class="text-caption text-secondary/50 font-bold tracking-wider uppercase">Nomor Rekening</span>
                        <div class="flex items-center justify-between" x-data="{ copied: false }">
                            <span class="text-title-lg sm:text-headline-md font-extrabold text-on-surface font-sans tracking-tight truncate pr-sm select-text" 
                                x-text="banks[selectedBank] ? banks[selectedBank].nomor : '-'">
                                {{ $bankPertama->no_rekening }}
                            </span>
                            <button @click="navigator.clipboard.writeText(banks[selectedBank].nomor); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="p-2 bg-surface-container hover:bg-primary/5 text-secondary hover:text-primary rounded-xl transition-all cursor-pointer shrink-0" 
                                    title="Salin Rekening">
                                <svg width="14" height="14" viewBox="0 0 10 12" fill="none" xmlns="http://www.w3.org/2000/svg" :class="copied ? 'text-success' : ''" class="transition-colors">
                                    <path d="M3.5 9.33333C3.17917 9.33333 2.90451 9.2191 2.67604 8.99063C2.44757 8.76215 2.33333 8.4875 2.33333 8.16667V1.16667C2.33333 0.845833 2.44757 0.571181 2.67604 0.342708C2.90451 0.114236 3.17917 0 3.5 0H8.75C9.07083 0 9.34549 0.114236 9.57396 0.342708C9.80243 0.571181 9.91667 0.845833 9.91667 1.16667V8.16667C9.91667 8.4875 9.80243 8.76215 9.57396 8.99063C9.34549 9.2191 9.07083 9.33333 8.75 9.33333H3.5ZM3.5 8.16667H8.75V1.16667H3.5V8.16667ZM1.16667 11.6667C0.845833 11.6667 0.571181 11.5524 0.342708 11.324C0.114236 11.0955 0 10.8208 0 10.5V2.33333H1.16667V10.5H7.58333V11.6667H1.16667ZM3.5 8.16667V1.16667V8.16667Z" fill="currentColor"/>
                                </svg>
                            </button>
                        </div>
                    </div>
        
                    <div class="mt-auto border-t border-surface-container/60 pt-sm">
                        <span class="text-caption text-secondary/50 font-bold block tracking-wider uppercase">Atas Nama Pemilik</span>
                        <p class="text-body-md font-extrabold text-on-surface truncate mt-1" x-text="'a.n. ' + banks[selectedBank].atas_nama">
                            a.n. {{ $bankPertama->atas_nama }}
                        </p>
                    </div>
                @else
                    <div class="text-center py-xl text-secondary/40 text-body-sm font-semibold select-none animate-fade-in my-auto">
                        Tidak ada rekening transfer terlampir.
                    </div>
                @endif
            </div>
        
            @php $cpUtama = $event->narahubung?->first(); @endphp
            <div class="p-md bg-surface-container-lowest rounded-2xl shadow-card flex items-center justify-between gap-md min-w-0 w-full animate-fade-in shrink-0">
                <div class="flex items-center gap-md min-w-0 flex-1">
                    <div class="w-10 h-10 rounded-xl bg-surface-container flex items-center justify-center shrink-0">
                        <svg width="18" height="18" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-primary">
                            <path d="M8 8C6.9 8 5.95833 7.60833 5.175 6.825C4.39167 6.04167 4 5.1 4 4C4 2.9 4.39167 1.95833 5.175 1.175C5.95833 0.391667 6.9 0 8 0C9.1 0 10.0417 0.391667 10.825 1.175C11.6083 1.95833 12 2.9 12 4C12 5.1 11.6083 6.04167 10.825 6.825C10.0417 7.60833 9.1 8 8 8ZM0 16V13.2C0 12.6333 0.145833 12.1125 0.4375 11.6375C0.729167 11.1625 1.11667 10.8 1.6 10.55C2.63333 10.0333 3.68333 9.64583 4.75 9.3875C5.81667 9.12917 6.9 9 8 9C9.1 9 10.1833 9.12917 11.25 9.3875C12.3167 9.64583 13.3667 10.0333 14.4 10.55C14.8833 10.8 15.2708 11.1625 15.5625 11.6375C15.8542 12.1125 16 12.6333 16 13.2V16H0ZM2 14H14V13.2C14 13.0167 13.9542 12.85 13.8625 12.7C13.7708 12.55 13.65 12.4333 13.5 12.35C12.6 11.9 11.6917 11.5625 10.775 11.3375C9.85833 11.1125 8.93333 11 8 11C7.06667 11 6.14167 11.1125 5.225 11.3375C4.30833 11.5625 3.4 11.9 2.5 12.35C2.35 12.4333 2.22917 12.55 2.1375 12.7C2.04583 12.85 2 13.0167 2 13.2V14ZM8 6C8.55 6 9.02083 5.80417 9.4125 5.4125C9.80417 5.02083 10 4.55 10 4C10 3.45 9.80417 2.97917 9.4125 2.5875C9.02083 2.19583 8.55 2 8 2C7.45 2 6.97917 2.19583 6.5875 2.5875C6.19583 2.97917 6 3.45 6 4C6 4.55 6.19583 5.02083 6.5875 5.4125C6.97917 5.80417 7.45 6 8 6Z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[10px] text-secondary/50 font-bold tracking-wider uppercase block leading-none">Narahubung Pelaksana</span>
                        <span class="text-body-md font-bold text-on-surface block truncate mt-1.5 leading-none">{{ $cpUtama?->nama ?? 'Panitia Pelaksana' }}</span>
                    </div>
                </div>
                @if(!empty($waNumber))
                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" 
                        class="w-10 h-10 bg-success text-white hover:bg-success/90 rounded-xl flex items-center justify-center transition-all shrink-0 select-none shadow-2xs hover:scale-105"
                        title="Hubungi WhatsApp">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19.0498 4.91005C18.1329 3.98416 17.0408 3.25002 15.8373 2.75042C14.6338 2.25081 13.3429 1.99574 12.0398 2.00005C6.5798 2.00005 2.1298 6.45005 2.1298 11.9101C2.1298 13.6601 2.5898 15.3601 3.4498 16.8601L2.0498 22.0001L7.2998 20.6201C8.7498 21.4101 10.3798 21.8301 12.0398 21.8301C17.4998 21.8301 21.9498 17.3801 21.9498 11.9201C21.9498 9.27005 20.9198 6.78005 19.0498 4.91005ZM12.0398 20.1501C10.5598 20.1501 9.1098 19.7501 7.8398 19.0001L7.5398 18.8201L4.4198 19.6401L5.2498 16.6001L5.0498 16.2901C4.22735 14.9771 3.79073 13.4593 3.7898 11.9101C3.7898 7.37005 7.4898 3.67005 12.0298 3.67005C14.2298 3.67005 16.2998 4.53005 17.8498 6.09005C18.6174 6.85392 19.2257 7.7626 19.6394 8.76338C20.0531 9.76417 20.264 10.8371 20.2598 11.9201C20.2798 16.4601 16.5798 20.1501 12.0398 20.1501ZM16.5598 13.9901C16.3098 13.8701 15.0898 13.2701 14.8698 13.1801C14.6398 13.1001 14.4798 13.0601 14.3098 13.3001C14.1398 13.5501 13.6698 14.1101 13.5298 14.2701C13.3898 14.4401 13.2398 14.4601 12.9898 14.3301C12.7398 14.2101 11.9398 13.9401 10.9998 13.1001C10.2598 12.4401 9.7698 11.6301 9.6198 11.3801C9.4798 11.1301 9.5998 11.0001 9.7298 10.8701C9.8398 10.7601 9.9798 10.5801 10.0998 10.4401C10.2198 10.3001 10.2698 10.1901 10.3498 10.0301C10.4298 9.86005 10.3898 9.72005 10.3298 9.60005C10.2698 9.48005 9.7698 8.26005 9.5698 7.76005C9.3698 7.28005 9.1598 7.34005 9.0098 7.33005H8.5298C8.3598 7.33005 8.0998 7.39005 7.8698 7.64005C7.6498 7.89005 7.0098 8.49005 7.0098 9.71005C7.0098 10.9301 7.8998 12.1101 8.0198 12.2701C8.1398 12.4401 9.7698 14.9401 12.2498 16.0101C12.8398 16.2701 13.2998 16.4201 13.6598 16.5301C14.2498 16.7201 14.7898 16.6901 15.2198 16.6301C15.6998 16.5601 16.6898 16.0301 16.8898 15.4501C17.0998 14.8701 17.0998 14.3801 17.0298 14.2701C16.9598 14.1601 16.8098 14.1101 16.5598 13.9901Z" fill="currentColor"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Timeline Acara -->
    <div class="bg-surface-container-lowest p-md sm:p-lg rounded-3xl border border-outline-variant/30 shadow-card flex flex-col gap-md select-none w-full animate-fade-in">
    
        <div class="flex items-center justify-between border-b border-surface-container/60 pb-sm">
            <div class="flex items-center gap-sm text-primary">
                <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h4 class="text-title-sm font-bold tracking-tight">Linimasa & Rangkaian Kegiatan</h4>
            </div>
            <span class="text-caption font-extrabold text-primary bg-primary/5 px-3 py-1 rounded-xl font-sans border border-primary/10">
                {{ $event->timeLines?->count() ?? 0 }} Fase Acara
            </span>
        </div>
    
        <div class="relative pl-xs sm:pl-sm flex flex-col gap-md max-h-[440px] overflow-y-auto pr-sm
                    [&::-webkit-scrollbar]:w-1
                    [&::-webkit-scrollbar-track]:bg-transparent
                    [&::-webkit-scrollbar-thumb]:bg-outline-variant/60
                    [&::-webkit-scrollbar-thumb]:rounded-full
                    hover:[&::-webkit-scrollbar-thumb]:bg-outline">
            
            <div class="absolute left-[15px] sm:left-[19px] top-6 bottom-6 w-[2.5px] bg-gradient-to-b from-primary/30 via-primary/10 to-transparent rounded-full"></div>
        
            @forelse($event->timeLines as $index => $timeline)
                <div class="flex items-center gap-md relative group/time flex-row min-w-0 shrink-0">
                    
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-surface-container-lowest border-4 border-surface-container-lowest flex items-center justify-center shrink-0 z-10 shadow-2xs transition-transform duration-300 group-hover/time:scale-110">
                        <span class="w-2.5 h-2.5 rounded-full bg-primary ring-4 ring-primary/10 transition-all group-hover/time:bg-on-primary-container"></span>
                    </div>
    
                    <div class="flex-1 bg-surface-container-low p-md rounded-2xl border border-outline-variant/10 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-md transition-all duration-300 min-w-0 hover:bg-surface-container hover:shadow-sm hover:border-primary/20">
                        
                        <div class="flex flex-col min-w-0 flex-1">
                            <span class="text-[10px] font-extrabold text-primary tracking-widest font-sans bg-primary-fixed px-2.5 py-0.5 rounded-lg border border-primary/5 w-fit uppercase">
                                Fase #{{ $index + 1 }}
                            </span>
                            
                            <h5 class="text-body-md font-bold text-on-surface tracking-tight mt-sm leading-tight break-words">
                                {{ $timeline->nama_timeline ?? 'Agenda Kegiatan' }}
                            </h5>
                            
                            @if($timeline->deskripsi_timeline)
                                <p class="text-caption text-on-surface-variant font-medium mt-1.5 leading-normal break-words max-w-[95%]">
                                    {{ $timeline->deskripsi_timeline }}
                                </p>
                            @endif
                        </div>
                        
                        <div class="flex flex-col items-start sm:items-end shrink-0 h-fit select-none font-sans sm:pl-md border-l-0 sm:border-l border-outline-variant/20">
                            <span class="text-body-sm font-extrabold text-on-surface tracking-tight">
                                {{ $timeline->tanggal_mulai->translatedFormat('d M Y') }}
                            </span>
                            @if($timeline->tanggal_selesai && \Carbon\Carbon::parse($timeline->tanggal_mulai)->notEqualTo(\Carbon\Carbon::parse($timeline->tanggal_selesai)))
                                <span class="text-[10px] font-bold text-secondary/60 leading-none mt-1">
                                    s.d. {{ $timeline->tanggal_selesai->translatedFormat('d M Y') }}
                                </span>
                            @endif
                        </div>
    
                    </div>
                </div>
            @empty
                <div class="py-md w-full">
                    <x-admin.empty-state 
                        title="Linimasa Belum Diatur" 
                        description="Pihak panitia pelaksana ormawa belum menyusun atau melampirkan rangkaian linimasa pelaksanaan kegiatan." 
                    />
                </div>
            @endforelse
        </div>
    </div>
</div>