<div class="w-full flex flex-col gap-md lg:gap-lg min-w-0">
    
    <div class="p-md sm:p-lg bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-card flex flex-col gap-md select-none animate-fade-in">
        
        <div class="flex items-center gap-sm">
            <div class="w-11 h-11 rounded-xl bg-surface-container border border-outline-variant/20 flex items-center justify-center text-primary font-bold overflow-hidden shadow-inner shrink-0">
                @if($event->organisasi?->logo_url)
                    <img src="{{ asset('storage/logos/' . $event->organisasi->logo_url) }}" alt="Logo {{ $event->organisasi->nama_organisasi }}" class="w-full h-full object-cover">
                @else
                    <span class="text-[11px] font-extrabold tracking-tight">
                        {{ strtoupper(substr($event->organisasi->nama_organisasi ?? 'O', 0, 2)) }}
                    </span>
                @endif
            </div>
            <div class="min-w-0 flex-1">
                <h4 class="text-body-md font-extrabold text-primary leading-tight truncate" title="{{ $event->organisasi->nama_organisasi }}">
                    {{ $event->organisasi->nama_organisasi ?? 'Nama Organisasi' }}
                </h4>
                <p class="text-[11px] text-on-surface-variant/60 font-bold leading-none mt-1.5 truncate">
                    {{ $event->organisasi->fakultas->nama_fakultas ?? 'Tingkat Universitas' }}
                </p>
            </div>
        </div>

        <p class="text-body-sm text-on-surface-variant leading-relaxed font-medium pl-xs">
            {{ $event->organisasi->deskripsi ?? 'Organisasi kemahasiswaan aktif yang berfokus pada pengembangan iklim inovasi, penalaran, dan profesionalisme mahasiswa.' }}
        </p>

        <div class="flex items-center justify-between gap-sm border-t border-surface-container pt-sm mt-xs">
            <div class="px-3 py-1.5 bg-primary/5 text-primary border border-primary/10 rounded-xl flex items-center gap-xs shadow-2xs">
                <span class="text-body-sm font-black tracking-tight font-sans">
                    {{ $totalEventSelesai }}
                </span>
                <span class="text-caption font-bold text-secondary">
                    Event Sukses
                </span>
            </div>

            <div class="flex items-center gap-xs">
                @if($event->organisasi?->ig_url)
                    <a href="{{ $event->organisasi->ig_url }}" target="_blank" 
                        class="w-8 h-8 rounded-xl bg-surface-container-low border border-outline-variant/20 text-secondary hover:text-primary flex items-center justify-center transition-all duration-300 ease-out hover:scale-105 active:scale-95 shadow-2xs" 
                        title="Instagram">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" fill="currentColor"/>
                        </svg>
                    </a>
                @endif

                @if($event->organisasi?->linkedin_url)
                    <a href="{{ $event->organisasi->linkedin_url }}" target="_blank" 
                        class="w-8 h-8 rounded-xl bg-surface-container-low border border-outline-variant/20 text-secondary hover:text-primary flex items-center justify-center transition-all duration-300 ease-out hover:scale-105 active:scale-95 shadow-2xs" 
                        title="LinkedIn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" fill="currentColor"/>
                        </svg>
                    </a>
                @endif

                @if(!$event->organisasi?->ig_url && !$event->organisasi?->linkedin_url)
                    <div class="text-[10px] font-bold text-secondary/30 border border-dashed border-outline-variant/40 px-2 py-1 rounded-lg" title="Tidak ada kontak eksternal">
                        No Links
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="p-md sm:p-lg bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-card flex flex-col gap-sm select-none">
    
        <div class="flex items-center gap-xs text-primary mb-xs pl-xs border-b border-surface-container/60 pb-sm">
            <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h4 class="text-title-sm font-bold tracking-tight">Riwayat Pengajuan Event</h4>
        </div>
    
        @if($riwayatPengajuan && $riwayatPengajuan->count() > 0)
            <div class="w-full flex flex-col max-h-[310px] overflow-y-auto pr-xs divide-y divide-surface-container
                        [&::-webkit-scrollbar]:w-1
                        [&::-webkit-scrollbar-track]:bg-transparent
                        [&::-webkit-scrollbar-track]:rounded-full
                        [&::-webkit-scrollbar-thumb]:bg-outline-variant/60
                        [&::-webkit-scrollbar-thumb]:rounded-full
                        hover:[&::-webkit-scrollbar-thumb]:bg-outline">
                @foreach($riwayatPengajuan as $riwayat)
                    @php
                        $currentStatus = is_object($riwayat->status) ? $riwayat->status->value : $riwayat->status;

                        $statusColor = match($currentStatus) {
                            'published'        => 'text-success bg-success/5 border-success/20',
                            'completed'        => 'text-primary bg-primary/5 border-primary/20',
                            'revision'         => 'text-error bg-error/5 border-error/20',
                            'pending_approval' => 'text-warning bg-warning/5 border-warning/20',
                            default            => 'text-warning bg-warning/5 border-warning/20'
                        };
                        
                        $statusTeks = match($currentStatus) {
                            'published'        => 'Terbit',
                            'completed'        => 'Selesai',
                            'revision'         => 'Revisi',
                            'pending_approval' => 'Pending',
                            default            => 'Pending'
                        };
                    @endphp
                    
                    <a href="{{ route('admin.event.detail', $riwayat->id) }}"
                        wire:navigate
                        class="py-md first:pt-0 last:pb-0 flex items-center justify-between gap-md group/item transition-all duration-200 cursor-pointer">
                        <div class="min-w-0 flex-1">
                            <h5 class="text-body-sm font-bold text-on-surface group-hover/item:text-primary transition-colors truncate" title="{{ $riwayat->nama_event }}">
                                {{ $riwayat->nama_event }}
                            </h5>
                            <p class="text-caption text-secondary/60 font-semibold leading-none mt-2 font-sans">
                                {{ \Carbon\Carbon::parse($riwayat->created_at)->translatedFormat('d M Y') }}
                            </p>
                        </div>
    
                        <span class="px-2.5 py-0.5 border text-[10px] font-extrabold rounded-xl shadow-2xs whitespace-nowrap shrink-0 transition-all duration-300 group-hover/item:scale-105 {{ $statusColor }}">
                            {{ $statusTeks }}
                        </span>
                    </a>
                @endforeach
            </div>
        @else
            <div class="py-md w-full">
                <x-admin.empty-state 
                    title="Belum Ada Riwayat" 
                    description="Seluruh berkas log proposal pengajuan kegiatan dari organisasi mahasiswa ini belum terekam di sistem." 
                />
            </div>
        @endif
    </div>

    @php
        $eStatus = is_object($event->status) ? $event->status->value : $event->status;
    @endphp

    @if($eStatus === 'pending_approval')
        <div class="w-full flex flex-col gap-sm select-none animate-fade-in mt-xs">
            
            <button type="button" 
                    @click="$dispatch('open-modal-approve-event', { id: {{ $event->id }}, name: '{{ addslashes($event->nama_event) }}' })"
                    class="w-full py-3 rounded-2xl text-on-primary bg-success hover:bg-success/90 border border-success font-bold text-body-sm flex items-center justify-center gap-xs transition-all duration-200 active:scale-[0.98] shadow-md hover:shadow cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Setujui Publikasi Event</span>
            </button>

            <button type="button" 
                    @click="$dispatch('open-modal-reject-event', { id: {{ $event->id }}, name: '{{ addslashes($event->nama_event) }}' })"
                    class="w-full py-2.5 rounded-2xl border border-error/30 text-error hover:bg-error/5 font-bold text-body-sm flex items-center justify-center gap-xs transition-all duration-200 active:scale-[0.98] shadow-2xs cursor-pointer">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Tolak Pengajuan Event</span>
            </button>

            <p class="text-[11px] text-secondary/50 font-medium text-center leading-tight mt-xs px-xs">
                Pastikan detail substansi, pamflet, dan tanggal pelaksanaan event telah sesuai dengan ketentuan institusi.
            </p>
        </div>
    @endif

    <!-- Confirm Modal -->
    <x-admin.modals.confirm-modal 
        id="approve-event"
        title="Setujui Pengajuan Event"
        wireAction="approveEvent">

        Apakah Anda yakin ingin menerbitkan event 
        <strong class="text-primary font-bold">"<span x-text="targetName"></span>"</strong>? 
        Event ini akan langsung dipublikasikan ke publik.
    </x-admin.modals.confirm-modal>

    <!-- Reject Modal -->
    <x-admin.modals.reject-modal 
    id="reject-event"
    title="Tolak Pendaftaran?"
    wireAction="rejectEvent"
    />
    
</div>