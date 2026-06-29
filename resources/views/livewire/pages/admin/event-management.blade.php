<div class="w-full p-md sm:p-lg xl:p-xl space-y-lg sm:space-y-xl">

    <x-admin.header-info title="Manajemen Pengajuan Event">
    
        <x-slot name="action">
            <button type="button" 
                wire:click="$dispatch('trigger-global-refresh')"
                wire:loading.attr="disabled"
                class="inline-flex items-center justify-center gap-sm px-md sm:px-lg py-2.5 sm:py-md bg-[#000666] text-white font-bold sm:font-bold rounded-lg shadow-sm hover:bg-[#000666]/90 disabled:opacity-50 transition-colors text-xs sm:text-body-md group">
                
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white transform group-hover:rotate-45 transition-transform duration-300" 
                    wire:loading.class="animate-spin"
                    wire:target="refreshComponent"
                    fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
    
                <span>Refresh</span>
            </button>
        </x-slot>
    
        <p class="text-xs sm:text-body-md text-on-surface-variant/80 font-medium leading-relaxed mt-1">
            Tinjau dan verifikasi pengajuan event dari organisasi mahasiswa secara berkala.
        </p>
    
    </x-admin.header-info>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-md sm:gap-lg select-none w-full">
        
        <!-- Card 1: Total Pengajuan -->
        <x-admin.cards.stat-card-action 
            title="Total Pengajuan"
            value="{{ $statCards['total'] }}"
            unit="Event"
            footerLabel="Semua Riwayat"
            footerType="primary"
            iconType="primary">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            </x-slot>
        </x-admin.cards.stat-card-action>

        <!-- Card 2: Event Disetujui + Selesai -->
        <x-admin.cards.stat-card-action 
            title="Event Disetujui"
            value="{{ $statCards['published'] + $statCards['completed'] }}"
            unit="Event"
            footerLabel="{{ $statCards['published'] }} Aktif & {{ $statCards['completed'] }} Selesai"
            footerType="success"
            iconType="success">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </x-slot>
        </x-admin.cards.stat-card-action>

        <!-- Card 3: Menunggu Persetujuan -->
        <x-admin.cards.stat-card-action 
            title="Menunggu Persetujuan"
            value="{{ $statCards['pending'] }}"
            unit="Event"
            footerLabel="Butuh Review"
            footerType="warning"
            iconType="warning">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </x-slot>
        </x-admin.cards.stat-card-action>

        <!-- Card 4: Event Ditolak/Revisi -->
        <x-admin.cards.stat-card-action 
            title="Butuh Revisi"
            value="{{ $statCards['revision'] }}"
            unit="Event"
            footerLabel="Menunggu Perbaikan"
            footerType="error"
            iconType="error">
            <x-slot name="icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            </x-slot>
        </x-admin.cards.stat-card-action>

    </div>

    <div class="w-full">
        <livewire:admin.event-master 
            :isDashboard="false" 
            title="Daftar Pengajuan Masuk"
            :fakultasId="$fakultasId"
        />
    </div>
</div>