<div class="w-full p-md sm:p-lg xl:p-xl space-y-lg sm:space-y-xl select-none">

    <x-admin.header-info title="Manajemen Pengajuan Organisasi">
        <x-slot name="action">
            <button type="button" 
                wire:click="$dispatch('trigger-global-refresh')"
                wire:loading.attr="disabled"
                class="inline-flex items-center justify-center gap-sm px-md sm:px-lg py-2.5 sm:py-md bg-primary text-white font-bold sm:font-bold rounded-lg shadow-sm hover:bg-primary/90 disabled:opacity-50 transition-colors text-xs sm:text-body-md group cursor-pointer">
                
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white transform group-hover:rotate-45 transition-transform duration-300" 
                    wire:loading.class="animate-spin"
                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span>Refresh</span>
            </button>
        </x-slot>
        <p class="text-xs sm:text-body-md text-on-surface-variant/80 font-medium leading-relaxed mt-1">
            Tinjau dan verifikasi berkas legalitas akun organisasi mahasiswa secara berkala.
        </p>
    </x-admin.header-info>

    <!-- Card Information -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-sm sm:gap-md lg:gap-lg w-full">
        <x-admin.cards.stat-card-action title="Menunggu Persetujuan" value="{{ $stats['pending'] }}" unit="Organisasi" footerLabel="Perlu Verifikasi" footerType="warning" iconType="warning">
            <x-slot:icon><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-slot:icon>
        </x-admin.cards.stat-card-action>

        <x-admin.cards.stat-card-action title="Organisasi Aktif" value="{{ $stats['approved'] }}" unit="Terdaftar" footerLabel="Beroperasi Normal" footerType="success" iconType="success">
            <x-slot:icon><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg></x-slot:icon>
        </x-admin.cards.stat-card-action>

        <x-admin.cards.stat-card-action title="Organisasi Ditolak" value="{{ $stats['rejected'] }}" unit="Total" footerLabel="Gagal Verifikasi" footerType="error" iconType="error">
            <x-slot:icon><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-slot:icon>
        </x-admin.cards.stat-card-action>
    </div>

    <!-- Flash Alert -->
    <x-admin.modals.toast-alert />
    
    <div class="bg-surface-container-lowest p-md sm:p-lg rounded-2xl border border-outline-variant/30 shadow-sm w-full">
        
        <div class="flex items-center justify-between gap-md mb-lg select-none w-full">
            <div>
                <h4 class="text-title-sm sm:text-title-md font-bold text-primary tracking-tight">
                    Daftar Pendaftaran Baru
                </h4>
            </div>

            <div class="items-center gap-sm w-full sm:w-auto hidden sm:flex">
                <div class="relative flex-1 sm:w-64" x-data="{ search: @entangle('search').live }">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-sm text-secondary/50">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/>
                        </svg>
                    </span>
                    
                    <input type="text" 
                            wire:model.live.debounce.300ms="search" 
                            placeholder="Cari organisasi atau email..." 
                            class="w-full text-body-md pl-9 pr-8 py-1.5 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary placeholder-secondary/40 font-medium">
                
                    <button type="button" 
                            x-show="search.length > 0"
                            @click="@this.set('search', ''); $dispatch('trigger-global-refresh')"
                            x-transition
                            class="absolute inset-y-0 right-0 flex items-center pr-sm text-secondary/40 hover:text-error transition-colors cursor-pointer"
                            title="Bersihkan pencarian"
                            x-cloak>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <button type="button" 
                        @click="$dispatch('open-modal-filter-organisasi')"
                        class="inline-flex items-center justify-center gap-xs px-md py-2 border border-outline-variant/30 bg-surface-container-lowest hover:bg-surface-container/30 text-primary rounded-xl transition-all font-bold text-body-md active:scale-95 shadow-sm cursor-pointer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Filter</span>
                </button>
            </div>

            <button type="button" 
                    @click="$dispatch('open-modal-filter-organisasi')"
                    class="inline-flex sm:hidden items-center justify-center w-8 h-8 border border-outline-variant/30 bg-surface-container-lowest hover:bg-surface-container/30 text-primary rounded-xl transition-all active:scale-95 shadow-sm shrink-0 cursor-pointer"
                    title="Buka Filter">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
            </button>
        </div>

        <div class="flex items-center gap-sm w-full mb-lg sm:hidden">
            <div class="relative flex-1 sm:w-64" x-data="{ search: @entangle('search').live }">
                <span class="absolute inset-y-0 left-0 flex items-center pl-sm text-secondary/50">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/>
                    </svg>
                </span>
                
                <input type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari organisasi atau email..." 
                        class="w-full text-body-md pl-9 pr-8 py-1.5 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary placeholder-secondary/40 font-medium">
            
                <button type="button" 
                        x-show="search.length > 0"
                        @click="@this.set('search', ''); $dispatch('trigger-global-refresh')"
                        x-transition
                        class="absolute inset-y-0 right-0 flex items-center pr-sm text-secondary/40 hover:text-error transition-colors cursor-pointer"
                        title="Bersihkan pencarian"
                        x-cloak>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-md lg:hidden w-full">
            @forelse($daftar_organisasi as $org)
                @php
                    $currentStatus = is_object($org->status) ? $org->status->value : $org->status;
                    $statusConfig = match($currentStatus) {
                        'pending'  => ['color' => 'bg-amber-500/[0.08] text-amber-700 border-amber-500/20', 'label' => 'Pending', 'pulse' => true],
                        'approved' => ['color' => 'bg-emerald-500/[0.08] text-emerald-700 border-emerald-500/20', 'label' => 'Aktif', 'pulse' => false],
                        'rejected' => ['color' => 'bg-red-500/[0.08] text-red-700 border-red-500/20', 'label' => 'Ditolak', 'pulse' => false],
                    };
                @endphp
                <div wire:key="org-card-{{ $org->id }}" class="border border-outline-variant/20 rounded-2xl p-md bg-surface-container-lowest shadow-sm flex flex-col justify-between gap-md h-full transition-all duration-200 hover:shadow-md">
                    <div class="flex flex-col gap-sm">
                        <div class="flex items-center justify-between gap-xs">
                            <span class="px-sm py-0.5 bg-primary/[0.06] text-primary/80 font-extrabold text-[10px] rounded-md border border-primary/10 uppercase tracking-wide">
                                {{ $org->tingkat_organisasi }}
                            </span>
                            <span class="inline-flex items-center gap-xs px-sm py-0.5 font-bold text-[11px] rounded-full border {{ $statusConfig['color'] }}">
                                @if($statusConfig['pulse']) <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> @endif
                                {{ $statusConfig['label'] }}
                            </span>
                        </div>

                        <div class="flex items-center gap-md mt-sm">
                            <div class="w-11 h-11 rounded-xl border border-outline-variant/30 bg-surface-container overflow-hidden shadow-2xs flex items-center justify-center font-black text-xs text-primary shrink-0">
                                @if($org->logo_url)
                                    <img src="{{ asset('storage/logo/' . $org->logo_url) }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($org->nama_organisasi, 0, 2)) }}
                                @endif
                            </div>
                            <div class="min-w-0 flex flex-col gap-0.5">
                                <h5 class="text-body-lg font-bold text-primary tracking-tight leading-snug truncate">{{ $org->nama_organisasi }}</h5>
                                <span class="text-caption text-secondary/60 font-sans font-medium truncate">{{ $org->user?->email ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-sm border-t border-outline-variant/10 pt-sm mt-xs">
                        <div class="flex items-center gap-xs text-caption text-secondary/50 font-medium font-sans">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-secondary/40"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span>Diajukan: {{ $org->created_at ? \Carbon\Carbon::parse($org->created_at)->translatedFormat('d M Y') : '-' }}</span>
                        </div>

                        <div class="flex items-center justify-end gap-sm mt-xs">
                            @if($currentStatus === 'pending')
                                <button type="button" 
                                        @click="$dispatch('open-modal-approve-organisasi', { id: {{ $org->id }}, name: '{{ addslashes($org->nama_organisasi) }}' })"
                                        class="flex-1 py-1.5 rounded-xl border border-emerald-500/30 text-emerald-600 hover:bg-emerald-500/5 font-bold text-xs transition-all active:scale-95 cursor-pointer">
                                    Setujui
                                </button>
                                <button type="button" 
                                        @click="$dispatch('open-modal-reject-organisasi', { id: {{ $org->id }}, name: '{{ addslashes($org->nama_organisasi) }}' })"
                                        class="flex-1 py-1.5 rounded-xl border border-red-500/30 text-red-600 hover:bg-red-500/5 font-bold text-xs transition-all active:scale-95 cursor-pointer">
                                    Tolak
                                </button>
                            @endif
                            <a href="{{ route('admin.organisasi.detail', $org->id) }}" wire:navigate
                                class="flex-1 py-1.5 rounded-xl bg-[#000666] text-white font-bold text-xs flex items-center justify-center transition-all active:scale-95 text-center cursor-pointer">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <x-admin.empty-state title="Tidak Ada Pengajuan" description="Seluruh antrean pengajuan verifikasi berkas organisasi kosong." />
                </div>
            @endforelse
        </div>

        <!-- Screen >= lg -->
        <div class="hidden lg:block w-full overflow-hidden rounded-xl border border-outline-variant/10">
            <table class="w-full text-left border-collapse table-fixed bg-surface-container-lowest">
                <thead>
                    <tr class="border-b border-outline-variant/30 text-caption text-secondary/50 font-bold uppercase tracking-wider select-none bg-surface-container-lowest">
                        <th class="py-md pl-md font-bold text-left w-[7%]">Logo</th>
                        <th class="py-md font-bold text-left w-[24%]">Nama Organisasi</th>
                        <th class="py-md font-bold px-md text-left w-[22%]">Email Akun</th>
                        <th class="py-md font-bold px-md text-center w-[12%]">Tgl Daftar</th>
                        <th class="py-md font-bold px-md text-center w-[10%]">Tingkat</th>
                        <th class="py-md font-bold px-md text-center w-[11%]">Status</th>
                        <th class="py-md font-bold text-right pr-md w-[14%]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10 text-body-md text-on-surface font-medium">
                    @forelse($daftar_organisasi as $org)
                        @php
                            $currentStatus = is_object($org->status) ? $org->status->value : $org->status;
                            $tdStatusColor = match($currentStatus) {
                                'approved' => 'text-success bg-success/10 border-success/10',
                                'rejected' => 'text-error bg-error/10 border-error/10',
                                'pending'  => 'text-warning bg-warning/10 border-warning/10',
                            };
                        @endphp
                        <tr wire:key="org-row-{{ $org->id }}" class="transition-colors duration-150 bg-transparent even:bg-surface-container/30 hover:bg-primary/[0.01]">
                            
                            <td class="py-lg pl-md text-left align-middle">
                                <div class="w-9 h-9 rounded-xl border border-outline-variant/30 bg-surface-container overflow-hidden shadow-2xs flex items-center justify-center font-black text-[11px] text-primary">
                                    @if($org->logo_url)
                                        <img src="{{ asset('storage/logos/' . $org->logo_url) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($org->nama_organisasi, 0, 2)) }}
                                    @endif
                                </div>
                            </td>
        
                            <td class="py-lg text-left align-middle pr-md">
                                <div class="w-full block font-bold text-primary tracking-tight leading-relaxed break-words whitespace-normal" title="{{ $org->nama_organisasi }}">
                                    {{ $org->nama_organisasi }}
                                </div>
                            </td>
        
                            <td class="py-lg px-md text-left align-middle text-secondary/80 font-sans pr-md">
                                <div class="w-full block break-words whitespace-normal leading-normal font-normal">
                                    {{ $org->user?->email ?? '-' }}
                                </div>
                            </td>
        
                            <td class="py-lg px-md text-center align-middle text-secondary/70 font-sans whitespace-nowrap">
                                {{ $org->created_at ? $org->created_at->format('d M Y') : '-' }}
                            </td>
        
                            <td class="py-lg px-md text-center align-middle select-none">
                                <div class="inline-flex justify-center w-full">
                                    <span class="inline-flex px-2.5 py-0.5 bg-surface-container text-primary uppercase text-[10px] rounded-xl border border-outline-variant/30 font-extrabold tracking-wide shadow-2xs whitespace-nowrap">
                                        {{ $org->tingkat_organisasi }}
                                    </span>
                                </div>
                            </td>
        
                            <td class="py-lg px-md text-center align-middle select-none">
                                <div class="inline-flex justify-center w-full">
                                    <div class="px-2.5 py-1 rounded-xl border text-[11px] font-bold tracking-wide inline-flex items-center gap-xs shadow-2xs whitespace-nowrap {{ $tdStatusColor }}">
                                        @if($currentStatus === 'approved')
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @elseif($currentStatus === 'rejected')
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        @endif
                                        <span>{{ $currentStatus === 'pending' ? 'Pending' : ($currentStatus === 'approved' ? 'Terbit' : 'Revisi') }}</span>
                                    </div>
                                </div>
                            </td>
        
                            <td class="py-lg pr-md text-right align-middle">
                                <div class="inline-flex items-center justify-end gap-xs w-full">
                                    @if($currentStatus === 'pending')
                                        <button type="button" 
                                                @click="$dispatch('open-modal-approve-organisasi', { id: {{ $org->id }}, name: '{{ addslashes($org->nama_organisasi) }}' })"
                                                class="w-7 h-7 rounded-lg text-emerald-600 hover:bg-emerald-500/10 flex items-center justify-center transition-all active:scale-95 cursor-pointer shrink-0" title="Setujui Akun">
                                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.6 14.6L15.65 7.55L14.25 6.15L8.6 11.8L5.75 8.95L4.35 10.35L8.6 14.6ZM10 20C8.61667 20 7.31667 19.7375 6.1 19.2125C4.88333 18.6875 3.825 17.975 2.925 17.075C2.025 16.175 1.3125 15.1167 0.7875 13.9C0.2625 12.6833 0 11.3833 0 10C0 8.61667 0.2625 7.31667 0.7875 6.1C1.3125 4.88333 2.025 3.825 2.925 2.925C3.825 2.025 4.88333 1.3125 6.1 0.7875C7.31667 0.2625 8.61667 0 10 0C11.3833 0 12.6833 0.2625 13.9 0.7875C15.1167 1.3125 16.175 2.025 17.075 2.925C17.975 3.825 18.6875 4.88333 19.2125 6.1C19.7375 7.31667 20 8.61667 20 10C20 11.3833 19.7375 12.6833 19.2125 13.9C18.6875 15.1167 17.975 16.175 17.075 17.075C16.175 17.975 15.1167 18.6875 13.9 19.2125C12.6833 19.7375 11.3833 20 10 20ZM10 18C12.2333 18 14.125 17.225 15.675 15.675C17.225 14.125 18 12.2333 18 10C18 7.76667 17.225 5.875 15.675 4.325C14.125 2.775 12.2333 2 10 2C7.76667 2 5.875 2.775 4.325 4.325C2.775 5.875 2 7.76667 2 10C2 12.2333 2.775 14.125 4.325 15.675C5.875 17.225 7.76667 18 10 18Z" fill="currentColor"/></svg>
                                        </button>
                                        <div class="w-[1px] h-3.5 bg-outline-variant/30 self-center shrink-0"></div>
                                        <button type="button" 
                                                @click="$dispatch('open-modal-reject-organisasi', { id: {{ $org->id }}, name: '{{ addslashes($org->nama_organisasi) }}' })"
                                                class="w-7 h-7 rounded-lg text-red-600 hover:bg-red-500/10 flex items-center justify-center transition-all active:scale-95 cursor-pointer shrink-0" title="Tolak Berkas">
                                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.4 15L10 11.4L13.6 15L15 13.6L11.4 10L15 6.4L13.6 5L10 8.6L6.4 5L5 6.4L8.6 10L5 13.6L6.4 15ZM10 20C8.61667 20 7.31667 19.7375 6.1 19.2125C4.88333 18.6875 3.825 17.975 2.925 17.075C2.025 16.175 1.3125 15.1167 0.7875 13.9C0.2625 12.6833 0 11.3833 0 10C0 8.61667 0.2625 7.31667 0.7875 6.1C1.3125 4.88333 2.025 3.825 2.925 2.925C3.825 2.025 4.88333 1.3125 6.1 0.7875C7.31667 0.2625 8.61667 0 10 0C11.3833 0 12.6833 0.2625 13.9 0.7875C15.1167 1.3125 16.175 2.025 17.075 2.925C17.975 3.825 18.6875 4.88333 19.2125 6.1C19.7375 7.31667 20 8.61667 20 10C20 11.3833 19.7375 12.6833 19.2125 13.9C18.6875 15.1167 17.975 16.175 17.075 17.075C16.175 17.975 15.1167 18.6875 13.9 19.2125C12.6833 19.7375 11.3833 20 10 20ZM10 18C12.2333 18 14.125 17.225 15.675 15.675C17.225 14.125 18 12.2333 18 10C18 7.76667 17.225 5.875 15.675 4.325C14.125 2.775 12.2333 2 10 2C7.76667 2 5.875 2.775 4.325 4.325C2.775 5.875 2 7.76667 2 10C2 12.2333 2.775 14.125 4.325 15.675C5.875 17.225 7.76667 18 10 18Z" fill="currentColor"/></svg>
                                        </button>
                                        <div class="w-[1px] h-3.5 bg-outline-variant/30 self-center shrink-0"></div>
                                    @endif
                                    <a href="{{ route('admin.organisasi.detail', $org->id) }}" wire:navigate 
                                        class="w-8 h-8 rounded-xl text-[#000666] bg-[#000666]/[0.06] hover:bg-[#000666] hover:text-white flex items-center justify-center transition-all active:scale-95 cursor-pointer shrink-0" title="Lihat Detail Organisasi">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/></svg>
                                    </a>
                                </div>
                            </td>
        
                        </tr>
                    @empty
                        <x-admin.empty-state :inTable="true" colspan="7" title="Belum Ada Pengajuan" description="Tidak ada antrean registrasi organisasi mahasiswa baru saat ini." />
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-lg">
            <x-admin.pagination-links :paginationData="$paginationData" />
        </div>
    </div>

    <x-admin.modals.confirm-modal 
        id="approve-organisasi"
        title="Setujui Verifikasi Organisasi"
        wireAction="approve">

        Apakah Anda yakin ingin menyetujui pendaftaran berkas 
        <strong class="text-primary font-bold">"<span x-text="targetName"></span>"</strong>? 
        Organisasi ini akan berstatus aktif di dalam sistem.
    </x-admin.modals.confirm-modal>

    <x-admin.modals.reject-modal 
        id="reject-organisasi"
        title="Tolak Registrasi Organisasi?"
        description="Berikan alasan penolakan berkas legalitas agar organisasi dapat memperbaiki datanya."
        wireModel="pesanPenolakan"
        wireAction="reject"
    />

    @include('livewire.admin.modals.filter-organisasi-modal')
</div>