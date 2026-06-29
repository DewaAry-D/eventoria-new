<div class="p-md sm:p-lg bg-surface-container-lowest rounded-3xl shadow-card flex flex-col gap-md select-none w-full animate-fade-in">
    
    <div class="flex items-center gap-sm text-primary border-b border-surface-container/60 pb-sm select-none">
        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25s-7.5-4.108-7.5-11.25a7.5 7.5 0 1115 0z"/>
        </svg>
        <h4 class="text-title-sm font-bold tracking-tight">Lokasi Pelaksanaan Kegiatan</h4>
    </div>

    @php
        $isOnline = Str::contains(strtolower($event->nama_lokasi), ['online', 'zoom', 'meet', 'daring', 'youtube']);
    @endphp
    
    <div class="w-full h-56 sm:h-64 md:h-72 lg:h-80 bg-surface-container-low rounded-2xl overflow-hidden relative group flex flex-col items-center justify-center border border-outline-variant/10">
        
        @if($isOnline)
            <div class="w-full h-full flex flex-col items-center justify-center p-md text-center max-w-sm transition-all duration-300">
                <div class="w-14 h-14 rounded-2xl bg-primary/5 text-primary flex items-center justify-center mb-md shadow-2xs group-hover:scale-110 transition-transform duration-300">
                    <svg width="24" height="24" viewBox="0 0 20 14" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 stroke-[2]">
                        <path d="M13 5L17.553 2.724C17.7054 2.64784 17.8748 2.61188 18.045 2.61955C18.2152 2.62721 18.3806 2.67825 18.5256 2.76781C18.6706 2.85736 18.7902 2.98248 18.8733 3.13127C18.9563 3.28007 18.9999 3.44761 19 3.618V10.382C18.9999 10.5524 18.9563 10.7199 18.8733 10.8687C18.7902 11.0175 18.6706 11.1426 18.5256 11.2322C18.3806 11.3218 18.2152 11.3728 18.045 11.3805C17.8748 11.3881 17.7054 11.3522 17.553 11.276L13 9V5ZM1 3C1 2.46957 1.21071 1.96086 1.58579 1.58579C1.96086 1.21071 2.46957 1 3 1H11C11.5304 1 12.0391 1.21071 12.4142 1.58579C12.7893 1.96086 13 2.46957 13 3V11C13 11.5304 12.7893 12.0391 12.4142 12.4142C12.0391 12.7893 11.5304 13 11 13H3C2.46957 13 1.96086 12.7893 1.58579 12.4142C1.21071 12.0391 1 11.5304 1 11V3Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h5 class="text-title-sm font-bold text-on-surface tracking-tight mb-xs">
                    Konferensi Pertemuan Daring
                </h5>
                <p class="text-body-sm text-on-surface-variant font-medium leading-relaxed">
                    Sesi materi dilaksanakan secara virtual. Pastikan koneksi internet Anda stabil sebelum bergabung ke ruang pertemuan.
                </p>
            </div>

        @elseif($event->lokasi_url && filter_var($event->lokasi_url, FILTER_VALIDATE_URL))
            <iframe src="{{ $cleanMapsUrl }}" 
                    class="w-full h-full border-0 grayscale opacity-90 content-contrast-[1.05] transition-all duration-500 group-hover:grayscale-0 group-hover:opacity-100" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
            </iframe>

        @else
            <div class="w-full h-full flex items-center justify-center p-md bg-surface-container-low">
                <x-admin.empty-state 
                    title="Visualisasi Peta Belum Tersedia" 
                    description="Pihak panitia ormawa tidak melampirkan tautan sematan Google Maps resmi yang valid pada proposal kegiatan." 
                />
            </div>
        @endif
    </div>

    <div class="p-md bg-surface-container-low rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-md w-full min-w-0 border border-outline-variant/10">
        
        <div class="flex items-center gap-md min-w-0 flex-1">
            <div class="w-9 h-9 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25s-7.5-4.108-7.5-11.25a7.5 7.5 0 1115 0z"/>
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-caption text-secondary/50 font-bold block tracking-wider uppercase leading-none">Nama Ruangan / Tempat</span>
                <span class="text-body-md font-bold text-on-surface block truncate mt-1.5 leading-tight" title="{{ $event->nama_lokasi }}">
                    {{ $event->nama_lokasi ?? 'Nama Lokasi Belum Ditentukan' }}
                </span>
            </div>
        </div>

        @if($event->lokasi_url)
            <a href="{{ $event->lokasi_url }}" target="_blank" 
                class="px-md py-2.5 bg-primary text-white hover:bg-primary/90 rounded-xl text-body-sm font-bold tracking-wide flex items-center justify-center gap-xs transition-all duration-300 shrink-0 group/link shadow-2xs self-stretch sm:self-auto hover:scale-[1.02]">
                <span>{{ $isOnline ? 'Gabung Pertemuan Virtual' : 'Buka Google Maps' }}</span>
                <svg class="w-4 h-4 transition-transform duration-300 group-hover/link:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                </svg>
            </a>
        @endif
    </div>

</div>