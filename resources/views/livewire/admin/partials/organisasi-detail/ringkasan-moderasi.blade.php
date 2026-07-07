@php
    $validasiColor = match($ringkasan['validasi']) {
        'Terverifikasi' => 'text-success font-extrabold',
        'Butuh Review'  => 'text-warning font-extrabold',
        default         => 'text-white'
    };
    
    $urgensiColor = match($ringkasan['urgensi']) {
        'Tinggi' => 'text-error font-extrabold animate-pulse',
        'Medium' => 'text-warning font-extrabold',
        'Normal' => 'text-white font-bold',
        default  => 'text-white'
    };
@endphp

<div class="w-full p-md sm:p-lg bg-primary rounded-3xl border border-primary-container/40 shadow-card flex flex-col gap-md select-none animate-fade-in text-white">
    
    <div class="flex items-center gap-sm border-b border-white/10 pb-sm select-none">
        <svg width="24" height="24" viewBox="0 0 24 24" class="w-5 h-5 text-white/80 shrink-0" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 4H7M18 16L21 19L18 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M3 4V17C3 17.5304 3.21071 18.0391 3.58579 18.4142C3.96086 18.7893 4.46957 19 5 19H21M7 14H14M7 9H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h4 class="text-caption font-extrabold tracking-widest uppercase text-white">Ringkasan Moderasi</h4>
    </div>

    <div class="w-full flex flex-col gap-sm font-medium">
        
        <div class="flex items-center justify-between py-xs border-b border-white/5 w-full gap-md">
            <span class="text-body-sm text-white/70 tracking-tight shrink-0">Kelengkapan</span>
            <span class="text-body-md font-extrabold text-white font-sans text-right">
                {{ $ringkasan['kelengkapan'] }}
            </span>
        </div>

        <div class="flex items-center justify-between py-xs border-b border-white/5 w-full gap-md">
            <span class="text-body-sm text-white/70 tracking-tight shrink-0">Validasi Sistem</span>
            <span class="text-body-md text-right font-sans {{ $validasiColor }}">
                {{ $ringkasan['validasi'] }}
            </span>
        </div>

        <div class="flex items-center justify-between py-xs w-full gap-md">
            <span class="text-body-sm text-white/70 tracking-tight shrink-0">Tingkat Urgensi</span>
            <span class="text-body-md text-right font-sans {{ $urgensiColor }}">
                {{ $ringkasan['urgensi'] }}
            </span>
        </div>

    </div>
</div>