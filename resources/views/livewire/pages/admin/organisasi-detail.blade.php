<div class="w-full p-md sm:p-lg xl:p-xl space-y-lg select-none min-w-0 relative">

    <!-- Navigasi -->
    <nav class="flex items-center gap-xs text-body-sm font-medium text-secondary/60 tracking-tight select-none">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-primary transition-colors">
            Dashboard
        </a>
        
        <svg class="w-4 h-4 text-secondary/40 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>

        <a href="{{ route('admin.moderasi.organisasi') }}" wire:navigate class="hover:text-primary transition-colors">
            Moderasi Organisasi
        </a>
        
        <svg class="w-4 h-4 text-secondary/40 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>

        <span class="font-bold text-primary">
            Detail Pengajuan
        </span>
    </nav>

    <div class="w-full grid grid-cols-1 lg:grid-cols-3 gap-md lg:gap-lg items-start min-w-0">
        
        <div class="lg:col-span-2 flex flex-col gap-md lg:gap-lg min-w-0">
            @include('livewire.admin.partials.organisasi-detail.header-organisasi')
            @include('livewire.admin.partials.organisasi-detail.profil-organisasi')
            @include('livewire.admin.partials.organisasi-detail.dokumen-legalitas')
        </div>

        <div class="lg:col-span-1 flex flex-col gap-md lg:gap-lg min-w-0">
            @include('livewire.admin.partials.organisasi-detail.media-sosial-card')
            @include('livewire.admin.partials.organisasi-detail.riwayat-registrasi')
            @include('livewire.admin.partials.organisasi-detail.ringkasan-moderasi')

            @if((is_object($org->status) ? $org->status->value : $org->status) === "pending")
                @include('livewire.admin.partials.organisasi-detail.action-bar-organisasi')
            @endif
        </div>

    </div>
</div>

