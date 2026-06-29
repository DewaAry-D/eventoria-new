<div class="bg-surface-container-lowest p-md sm:p-lg rounded-2xl border border-outline-variant/30 shadow-sm w-full">
    
    <div class="flex items-center justify-between gap-md mb-lg select-none w-full">
        <div>
            <h4 class="text-title-sm sm:text-title-md font-bold sm:font-bold text-primary tracking-tight">
                {{ $title }}
            </h4>
        </div>
        
        @if(!$isDashboard)
            <div class="items-center gap-sm w-full sm:w-auto hidden sm:flex">
                <!-- Input Cari Desktop -->
                <div class="relative flex-1 sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-sm text-secondary/50">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/></svg>
                    </span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari pengajuan event..." class="w-full text-body-md pl-9 pr-sm py-1.5 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary placeholder-secondary/40 font-medium">
                </div>

                <!-- Tombol Filter Desktop -->
                <button type="button" 
                        @click="$dispatch('open-modal-filter-event')"
                        class="inline-flex items-center justify-center gap-xs px-md py-2 border border-outline-variant/30 bg-surface-container-lowest hover:bg-surface-container/30 text-primary rounded-xl transition-all font-bold text-body-md active:scale-95 shadow-sm">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                    <span>Filter</span>
                </button>
            </div>

            <!-- Tombol Filter Mobile -->
            <button type="button" 
                    @click="$dispatch('open-modal-filter-event')"
                    class="inline-flex sm:hidden items-center justify-center w-8 h-8 border border-outline-variant/30 bg-surface-container-lowest hover:bg-surface-container/30 text-primary rounded-xl transition-all active:scale-95 shadow-sm shrink-0"
                    title="Buka Filter">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
            </button>
        @else
            <!-- Nav Lihat Semua di Dashboard -->
            <a href="{{ route('admin.event.master') }}" wire:navigate 
                class="inline-flex items-center gap-x-1 text-body-sm sm:text-body-md font-bold text-primary hover:text-primary/80 group transition-all whitespace-nowrap flex-shrink-0">
                <span>Lihat Semua</span>
                <svg class="w-4 h-4 transform group-hover:translate-x-0.5 transition-transform duration-200" 
                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        @endif
    </div>

    <!-- Input Search Mobile -->
    @if(!$isDashboard)
        <div class="flex items-center gap-sm w-full mb-lg sm:hidden">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-sm text-secondary/50">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/></svg>
                </span>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari pengajuan..." class="w-full text-body-md pl-9 pr-sm py-1.5 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary placeholder-secondary/40 font-medium">
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-md lg:hidden select-none w-full">
        @forelse($events as $event)
            @php 
                $eStatus = is_object($event->status) ? $event->status->value : $event->status; 
            @endphp
            <div wire:key="event-card-{{ $event->id }}" class="border border-outline-variant/20 rounded-2xl p-md bg-surface-container-lowest shadow-sm flex flex-col justify-between gap-md h-full transition-all duration-200 hover:shadow-md">
                
                <div class="flex flex-col gap-sm">
                    <div class="flex items-center justify-between gap-xs">
                        <span class="px-sm py-0.5 bg-primary/[0.06] text-primary/80 font-bold text-[10px] rounded-md border border-primary/10 truncate max-w-[120px]">
                            {{ $event->kategori->nama_kategori ?? 'Event' }}
                        </span>
                        
                        @if($eStatus === 'pending_approval' || $eStatus === 'pending')
                            <span class="inline-flex items-center gap-xs px-sm py-0.5 bg-amber-500/[0.08] text-amber-700 font-bold text-[11px] rounded-full border border-amber-500/20 flex-shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                            </span>
                        @elseif($eStatus === 'published')
                            <span class="inline-flex items-center gap-xs px-sm py-0.5 bg-emerald-500/[0.08] text-emerald-700 font-bold text-[11px] rounded-full border border-emerald-500/20 flex-shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Diterima
                            </span>
                        @else
                            <span class="inline-flex items-center gap-xs px-sm py-0.5 bg-red-500/[0.08] text-red-700 font-bold text-[11px] rounded-full border border-red-500/20 flex-shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Revisi
                            </span>
                        @endif
                    </div>

                    <div class="mt-xs">
                        <h5 class="text-body-lg font-bold text-primary tracking-tight leading-snug break-words">
                            {{ $event->nama_event }}
                        </h5>
                    </div>

                    <div class="flex items-center gap-sm mt-xs">
                        <div class="w-7 h-7 rounded-lg bg-surface-container border border-outline-variant/30 flex items-center justify-center text-secondary/60 flex-shrink-0 overflow-hidden">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle></svg>
                        </div>
                        <span class="text-body-sm text-secondary/80 font-semibold tracking-tight leading-tight truncate max-w-[160px]">
                            {{ $event->organisasi->nama_organisasi ?? '-' }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col gap-sm border-t border-outline-variant/10 pt-sm">
                    <div class="flex flex-wrap items-center gap-x-md gap-y-1 text-caption text-secondary/60 font-medium font-sans">
                        <div class="flex items-center gap-xs flex-shrink-0">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-secondary/50"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            <span>{{ $event->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center gap-xs min-w-0">
                            <svg width="12" height="12" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 7.5C6.4125 7.5 6.76562 7.35312 7.05937 7.05937C7.35312 6.76562 7.5 6.4125 7.5 6C7.5 5.5875 7.35312 5.23438 7.05937 4.94063C6.76562 4.64688 6.4125 4.5 6 4.5C5.5875 4.5 5.23438 4.64688 4.94063 4.94063C4.64688 5.23438 4.5 5.5875 4.5 6C4.5 6.4125 4.64688 6.76562 4.94063 7.05937C5.23438 7.35312 5.5875 7.5 6 7.5ZM6 13.0125C7.525 11.6125 8.65625 10.3406 9.39375 9.19687C10.1313 8.05312 10.5 7.0375 10.5 6.15C10.5 4.7875 10.0656 3.67188 9.19687 2.80312C8.32812 1.93437 7.2625 1.5 6 1.5C4.7375 1.5 3.67188 1.93437 2.80312 2.80312C1.93437 3.67188 1.5 4.7875 1.5 6.15C1.5 7.0375 1.86875 8.05312 2.60625 9.19687C3.34375 10.3406 4.475 11.6125 6 13.0125ZM6 15C3.9875 13.2875 2.48438 11.6969 1.49063 10.2281C0.496875 8.75937 0 7.4 0 6.15C0 4.275 0.603125 2.78125 1.80938 1.66875C3.01562 0.55625 4.4125 0 6 0C7.5875 0 8.98438 0.55625 10.1906 1.66875C11.3969 2.78125 12 4.275 12 6.15C12 7.4 11.5031 8.75937 10.5094 10.2281C9.51562 11.6969 8.0125 13.2875 6 15Z" fill="currentColor"/>
                            </svg>
                            <span class="truncate max-w-[120px]">{{ $event->nama_lokasi }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-sm mt-xs">
                        @if($eStatus === 'pending_approval')
                            <button type="button" 
                                    @click="$dispatch('open-modal-approve-event', { id: {{ $event->id }}, name: '{{ addslashes($event->nama_event) }}' })"
                                    class="flex-1 py-1.5 rounded-xl border border-emerald-500/30 text-emerald-600 hover:bg-emerald-500/5 font-bold text-xs flex items-center justify-center transition-all active:scale-95">
                                Setujui
                            </button>

                            <button type="button" 
                                    @click="$dispatch('open-modal-reject-event', { id: {{ $event->id }}, name: '{{ addslashes($event->nama_event) }}' })"
                                    class="flex-1 py-1.5 rounded-xl border border-red-500/30 text-red-600 hover:bg-red-500/5 font-bold text-xs flex items-center justify-center transition-all active:scale-95">
                                Tolak
                            </button>
                        @endif
                        <a href="{{ route('admin.event.detail', $event->id) }}"
                            wire:navigate
                            class="flex-1 py-1.5 rounded-xl bg-[#000666] text-white font-bold text-xs flex items-center justify-center transition-all active:scale-95 text-center">
                            Detail
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full">
                <x-admin.empty-state
                    title="Tidak Ada Pengajuan"
                    description="Seluruh antrean pengajuan aktivitas event kosong atau tidak ditemukan."
                />
            </div>
        @endforelse
    </div>

    <div class="hidden lg:block w-full overflow-x-auto">
        <table class="w-full text-left border-collapse table-auto">
            <thead>
                <tr class="border-b border-outline-variant/30 text-caption text-secondary/50 font-bold uppercase tracking-wider select-none">
                    <th class="pb-md font-bold text-left">Nama Event</th>
                    <th class="pb-md font-bold px-md text-left">Organisasi</th>
                    @if(!$isDashboard) 
                        <th class="pb-md font-bold px-md text-center">Kategori</th> 
                    @endif
                    <th class="pb-md font-bold px-md text-center">Tgl Pengajuan</th>
                    <th class="pb-md font-bold px-md text-center">Status</th>
                    <th class="pb-md font-bold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant/10 text-body-md text-on-surface font-medium">
                @forelse($events as $event)
                    @php 
                    $eStatus = is_object($event->status) ? $event->status->value : $event->status; 
                
                    $tdStatusColor = match($eStatus) {
                        'published'        => 'text-success bg-success/10 border-success/10',
                        'completed'        => 'text-primary bg-primary/10 border-primary/10',
                        'revision'         => 'text-error bg-error/10 border-error/10',
                        'pending_approval' => 'text-warning bg-warning/10 border-warning/10',
                        default            => 'text-warning bg-warning/10 border-warning/10'
                    };
                
                    $tdStatusText = match($eStatus) {
                        'published'        => 'Terbit',
                        'completed'        => 'Selesai',
                        'revision'         => 'Revisi',
                        'pending_approval' => 'Pending',
                        default            => ucfirst($currentStatus)
                    };
                @endphp

                    <tr wire:key="event-row-{{ $event->id }}" class="transition-colors duration-150 bg-transparent even:bg-surface-container/30 hover:bg-primary/[0.01]">
                        
                        <td class="py-lg pr-md text-left alignment-left">
                            <div class="font-bold text-primary tracking-tight leading-relaxed whitespace-normal max-w-xs">
                                {{ $event->nama_event }}
                            </div>
                        </td>
        
                        <td class="py-lg px-md text-left text-secondary/80 whitespace-normal max-w-[160px] leading-tight">
                            {{ $event->organisasi->nama_organisasi ?? '-' }}
                        </td>
        
                        @if(!$isDashboard)
                            <td class="py-lg px-md text-center">
                                <span class="inline-flex px-sm py-0.5 bg-primary/[0.06] text-primary/80 font-bold text-[11px] rounded-md border border-primary/10">
                                    {{ $event->kategori->nama_kategori ?? 'Event' }}
                                </span>
                            </td>
                        @endif
        
                        <td class="py-lg px-md text-center text-secondary/70 font-sans">
                            {{ $event->created_at->format('d M Y') }}
                        </td>
        
                        <td class="py-lg px-md text-center select-none">
                            <div class="inline-flex justify-center w-full">
                                <div class="px-2.5 py-1 rounded-xl border text-[11px] font-bold tracking-wide inline-flex items-center gap-xs shadow-2xs {{ $tdStatusColor }}">
                                    
                                    @if($eStatus === 'published')
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @elseif($eStatus === 'completed')
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013-3h.375a2.625 2.625 0 000-5.25H20.25m-3.75 8.25v-3m0 3h3.75m-11.25-3c0-2.25 1.5-4.5 3.75-4.5h.75m-4.5 4.5H4.875a2.625 2.625 0 010-5.25H5.25m3.75 8.25v-3m0 3H5.25M12 3v9m0 0l3-3m-3 3L9 9"/>
                                        </svg>
                                    @elseif($eStatus === 'revision')
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                        
                                    <span>{{ $tdStatusText }}</span>
                                </div>
                            </div>
                        </td>
        
                        <td class="py-lg pl-md text-right">
                            <div class="inline-flex items-center justify-end gap-xs">
                                @if($eStatus === 'pending_approval' || $eStatus === 'pending')
                                    <button type="button" 
                                            @click="$dispatch('open-modal-approve-event', { id: {{ $event->id }}, name: '{{ addslashes($event->nama_event) }}' })" 
                                            class="w-7 h-7 rounded-lg text-emerald-600 hover:bg-emerald-500/10 flex items-center justify-center transition-all active:scale-95" 
                                            title="Setujui Event">
                                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.6 14.6L15.65 7.55L14.25 6.15L8.6 11.8L5.75 8.95L4.35 10.35L8.6 14.6ZM10 20C8.61667 20 7.31667 19.7375 6.1 19.2125C4.88333 18.6875 3.825 17.975 2.925 17.075C2.025 16.175 1.3125 15.1167 0.7875 13.9C0.2625 12.6833 0 11.3833 0 10C0 8.61667 0.2625 7.31667 0.7875 6.1C1.3125 4.88333 2.025 3.825 2.925 2.925C3.825 2.025 4.88333 1.3125 6.1 0.7875C7.31667 0.2625 8.61667 0 10 0C11.3833 0 12.6833 0.2625 13.9 0.7875C15.1167 1.3125 16.175 2.025 17.075 2.925C17.975 3.825 18.6875 4.88333 19.2125 6.1C19.7375 7.31667 20 8.61667 20 10C20 11.3833 19.7375 12.6833 19.2125 13.9C18.6875 15.1167 17.975 16.175 17.075 17.075C16.175 17.975 15.1167 18.6875 13.9 19.2125C12.6833 19.7375 11.3833 20 10 20ZM10 18C12.2333 18 14.125 17.225 15.675 15.675C17.225 14.125 18 12.2333 18 10C18 7.76667 17.225 5.875 15.675 4.325C14.125 2.775 12.2333 2 10 2C7.76667 2 5.875 2.775 4.325 4.325C2.775 5.875 2 7.76667 2 10C2 12.2333 2.775 14.125 4.325 15.675C5.875 17.225 7.76667 18 10 18Z" fill="currentColor"/></svg>
                                    </button>
                                    
                                    <div class="w-[1px] h-3.5 bg-outline-variant/30 self-center"></div>
                                    
                                    <button type="button" 
                                            @click="$dispatch('open-modal-reject-event', { id: {{ $event->id }}, name: '{{ addslashes($event->nama_event) }}' })" 
                                            class="w-7 h-7 rounded-lg text-red-600 hover:bg-red-500/10 flex items-center justify-center transition-all active:scale-95" 
                                            title="Tolak / Minta Revisi">
                                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.4 15L10 11.4L13.6 15L15 13.6L11.4 10L15 6.4L13.6 5L10 8.6L6.4 5L5 6.4L8.6 10L5 13.6L6.4 15ZM10 20C8.61667 20 7.31667 19.7375 6.1 19.2125C4.88333 18.6875 3.825 17.975 2.925 17.075C2.025 16.175 1.3125 15.1167 0.7875 13.9C0.2625 12.6833 0 11.3833 0 10C0 8.61667 0.2625 7.31667 0.7875 6.1C1.3125 4.88333 2.025 3.825 2.925 2.925C3.825 2.025 4.88333 1.3125 6.1 0.7875C7.31667 0.2625 8.61667 0 10 0C11.3833 0 12.6833 0.2625 13.9 0.7875C15.1167 1.3125 16.175 2.025 17.075 2.925C17.975 3.825 18.6875 4.88333 19.2125 6.1C19.7375 7.31667 20 8.61667 20 10C20 11.3833 19.7375 12.6833 19.2125 13.9C18.6875 15.1167 17.975 16.175 17.075 17.075C16.175 17.975 15.1167 18.6875 13.9 19.2125C12.6833 19.7375 11.3833 20 10 20ZM10 18C12.2333 18 14.125 17.225 15.675 15.675C17.225 14.125 18 12.2333 18 10C18 7.76667 17.225 5.875 15.675 4.325C14.125 2.775 12.2333 2 10 2C7.76667 2 5.875 2.775 4.325 4.325C2.775 5.875 2 7.76667 2 10C2 12.2333 2.775 14.125 4.325 15.675C5.875 17.225 7.76667 18 10 18Z" fill="currentColor"/></svg>
                                    </button>
                                @endif

                                <a href="{{ route('admin.event.detail', $event->id) }}" 
                                    wire:navigate 
                                    class="w-8 h-8 rounded-xl text-[#000666] bg-[#000666]/[0.06] hover:bg-[#000666] hover:text-white flex items-center justify-center transition-all active:scale-95" 
                                    title="Lihat Detail Event">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                            <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/>
                                        </svg>
                                    </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-admin.empty-state
                        :inTable="true"
                        :colspan="$isDashboard ? 5 : 6"
                        title="Belum Ada Pengajuan"
                        description="Tidak ada antrean pengajuan event yang perlu ditinjau saat ini."
                    />
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(!$isDashboard && $paginationData && $paginationData['totalPages'] > 1)
        <div class="flex flex-col items-center justify-center gap-md sm:flex-row sm:items-center sm:justify-between border-t border-outline-variant/20 mt-lg pt-md font-medium select-none">
            
            <!-- Info Text Kiri Bawah -->
            <div class="hidden sm:block text-body-sm text-secondary/60">
                Menampilkan <span class="font-bold text-primary/80">{{ $paginationData['from'] }}-{{ $paginationData['to'] }}</span> dari <span class="font-bold text-primary/80">{{ $paginationData['total'] }}</span> pengajuan event
            </div>
            
            <!-- Tombol Kontrol Paginasi -->
            <div class="inline-flex items-center gap-xs text-body-sm">
                
                <!-- Tombol Previous -->
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

                <!-- Tampilkan Angka 1 Permanen di Awal -->
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

                <!-- Tampilkan Angka Terakhir Permanen di Ujung Kanan -->
                @if($endPage < $totalPages)
                    @if($endPage < $totalPages - 1)
                        <span class="w-8 h-8 flex items-center justify-center text-secondary/40 font-bold text-xs">...</span>
                    @endif
                    <button type="button" wire:click="gotoPage({{ $totalPages }})" class="w-8 h-8 rounded-xl border border-outline-variant/30 flex items-center justify-center text-secondary/70 bg-surface-container-lowest font-medium transition-all duration-150 hover:bg-[#000666]/[0.06] hover:border-[#000666]/30 hover:text-[#000666] active:scale-95">
                        {{ $totalPages }}
                    </button>
                @endif
                
                <!-- Tombol Next (>) -->
                <button type="button" 
                        wire:click="nextPage"
                        class="w-8 h-8 rounded-xl border border-outline-variant/30 flex items-center justify-center transition-colors {{ $paginationData['currentPage'] == $paginationData['totalPages'] ? 'text-secondary/40 bg-surface-container/10 cursor-not-allowed' : 'text-secondary/70 hover:bg-surface-container/40' }}"
                        nameTitle="Previous"
                        {{ $paginationData['currentPage'] == $paginationData['totalPages'] ? 'disabled' : '' }}>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z" fill="currentColor"/></svg>
                </button>

            </div>
        </div>
    @endif

    <!-- Confirm Modal -->
    <x-admin.modals.confirm-modal 
        id="approve-event"
        title="Setujui Pengajuan Event"
        wireAction="approveEvent"
    />

    <!-- Reject Modal -->
    <x-admin.modals.reject-modal 
    id="reject-event"
    title="Tolak Pendaftaran?"
    wireAction="rejectEvent"
    />
    
    <!-- Filter Modal -->
    <livewire:admin.modals.event-filter-modal :currentFakultasId="$fakultasId" />
</div>

