<div class="w-full p-md sm:p-lg xl:p-xl space-y-lg select-none">
    
    <!-- Navigasi -->
    <nav class="flex items-center gap-xs text-body-sm font-medium text-secondary/60 tracking-tight select-none">
        <a href="{{ route('admin.dashboard') }}" wire:navigate class="hover:text-primary transition-colors">
            Dashboard
        </a>
        
        <svg class="w-4 h-4 text-secondary/40 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>

        <a href="{{ route('admin.moderasi.event') }}" wire:navigate class="hover:text-primary transition-colors">
            Moderasi Event
        </a>
        
        <svg class="w-4 h-4 text-secondary/40 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>

        <span class="font-bold text-primary">
            Detail Pengajuan
        </span>
    </nav>

    @if(($event->status->value ?? $event->status) === 'revision' && $event->catatan_revisi)
        <div class="w-full p-md bg-error/5 border border-error/15 rounded-2xl flex gap-md items-start animate-fade-in">
            <!-- Icon Indicator -->
            <div class="p-xs bg-error/10 text-error rounded-xl shrink-0 mt-0.5">
                <svg class="w-5 h-5 stroke-[2.2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <div class="space-y-sm flex-1 min-w-0">
                <h5 class="text-body-sm font-extrabold text-error uppercase tracking-wider">
                    Catatan Instruksi Revisi
                </h5>
                
                <p class="text-body-md font-semibold text-on-surface/80 bg-surface-container-lowest/50 p-lg rounded-xl border border-outline-variant/10 leading-relaxed text-left">
                    {{ trim($event->catatan_revisi) }}
                </p>
            </div>
        </div>
    @endif

    <!-- Summary Card -->
    <div class="w-full">
        @include('livewire.admin.partials.event-detail.summary-card')
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-md lg:gap-lg items-start">
        
        <div class="lg:col-span-12 space-y-md lg:space-y-lg min-w-0">
            
            <!-- Navigasi Tab -->
            <div class="flex items-center justify-center gap-lg sm:gap-xl border-b border-outline-variant/30 text-body-sm font-semibold select-none overflow-x-auto pb-px [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
                <button wire:click="$set('activeTab', 'detail')" 
                        class="py-sm cursor-pointer border-b-2 transition-all duration-200 whitespace-nowrap {{ $activeTab === 'detail' ? 'border-primary text-primary font-bold' : 'border-transparent text-secondary/60 hover:text-secondary' }}">
                    Detail & Fasilitas
                </button>

                <button wire:click="$set('activeTab', 'lokasi')" 
                        class="py-sm cursor-pointer border-b-2 transition-all duration-200 whitespace-nowrap {{ $activeTab === 'lokasi' ? 'border-primary text-primary font-bold' : 'border-transparent text-secondary/60 hover:text-secondary' }}">
                    Lokasi Acara
                </button>
                
                <button wire:click="$set('activeTab', 'pendaftaran')" 
                        class="py-sm cursor-pointer border-b-2 transition-all duration-200 whitespace-nowrap {{ $activeTab === 'pendaftaran' ? 'border-primary text-primary font-bold' : 'border-transparent text-secondary/60 hover:text-secondary' }}">
                    Pendaftaran
                </button>
                
                <button wire:click="$set('activeTab', 'sertifikat')" 
                        class="py-sm cursor-pointer border-b-2 transition-all duration-200 whitespace-nowrap {{ $activeTab === 'sertifikat' ? 'border-primary text-primary font-bold' : 'border-transparent text-secondary/60 hover:text-secondary' }}">
                    Sertifikat
                </button>
            </div>

            <!-- Panel Konten Tab Aktif -->
            <div class="w-full transition-all duration-300">
                @if($activeTab === 'detail')
                    @include('livewire.admin.partials.event-detail.tab-detail-fasilitas')
                @elseif($activeTab === 'lokasi')
                    @include('livewire.admin.partials.event-detail.tab-lokasi-acara')
                @elseif($activeTab === 'pendaftaran')
                    @include('livewire.admin.partials.event-detail.tab-pendaftaran')
                @elseif($activeTab === 'sertifikat')
                    @include('livewire.admin.partials.event-detail.tab-sertifikat')
                @endif
            </div>
            
        </div>

        <div class="lg:col-span-12 w-full min-w-0 pt-md border-t border-outline-variant/20">
            @include('livewire.admin.partials.event-detail.organisasi-info')
        </div>

    </div>

</div>