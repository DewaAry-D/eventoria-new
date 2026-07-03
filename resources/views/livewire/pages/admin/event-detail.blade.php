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

    <!-- Summary Card -->
    <div class="w-full">
        @include('livewire.admin.partials.event-detail.summary-card')
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-md lg:gap-lg items-start">
        
        <!-- Bagian Kiri -->
        <div class="lg:col-span-8 space-y-md lg:space-y-lg min-w-0">
            <div class="flex items-center justify-center gap-xl border-b border-outline-variant/30 text-body-sm font-semibold select-none">
                
                <button wire:click="$set('activeTab', 'detail')" 
                        class="py-sm cursor-pointer border-b-2 transition-all duration-200 {{ $activeTab === 'detail' ? 'border-primary text-primary font-bold' : 'border-transparent text-secondary/60 hover:text-secondary' }}">
                    Detail & Fasilitas
                </button>

                <button wire:click="$set('activeTab', 'lokasi')" 
                        class="py-sm cursor-pointer border-b-2 transition-all duration-200 {{ $activeTab === 'lokasi' ? 'border-primary text-primary font-bold' : 'border-transparent text-secondary/60 hover:text-secondary' }}">
                    Lokasi Acara
                </button>
                
                <button wire:click="$set('activeTab', 'pendaftaran')" 
                        class="py-sm cursor-pointer border-b-2 transition-all duration-200 {{ $activeTab === 'pendaftaran' ? 'border-primary text-primary font-bold' : 'border-transparent text-secondary/60 hover:text-secondary' }}">
                    Pendaftaran
                </button>
                
                <button wire:click="$set('activeTab', 'sertifikat')" 
                        class="py-sm cursor-pointer border-b-2 transition-all duration-200 {{ $activeTab === 'sertifikat' ? 'border-primary text-primary font-bold' : 'border-transparent text-secondary/60 hover:text-secondary' }}">
                    Sertifikat
                </button>
            </div>

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

        <!-- Bagian Kanan -->
        <div class="lg:col-span-4 w-full min-w-0">
            @include('livewire.admin.partials.event-detail.sidebar-right')
        </div>

    </div>

</div>