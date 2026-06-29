@props([
    'disetujui' => 0,
    'menunggu' => 0,
    'revisi' => 0,
    'title' => 'Statistik Pengajuan Event'
])

@php
    // Menghitung total untuk kalkulasi persentase
    $total = $disetujui + $menunggu + $revisi;
    
    $pDisetujui = $total > 0 ? round(($disetujui / $total) * 100) : 0;
    $pMenunggu  = $total > 0 ? round(($menunggu / $total) * 100) : 0;
    $pRevisi    = $total > 0 ? round(($revisi / $total) * 100) : 0;
@endphp

<div class="bg-surface-container-lowest p-md sm:p-lg rounded-2xl border border-outline-variant/30 shadow-sm h-full flex flex-col justify-between w-full">
    
    <div class="select-none">
        <h4 class="text-body-md md:text-body-lg font-bold md:font-bold text-primary tracking-tight">
            {{ $title }}
        </h4>
    </div>

    <div class="flex-1 flex flex-col justify-center space-y-md mt-sm">
        
        <div class="flex items-center justify-between gap-md group/bar">
            <span class="text-body-sm sm:text-body-md font-medium text-secondary/80 w-20 sm:w-24 select-none group-hover/bar:text-success transition-colors">Disetujui</span>
            <div class="flex-1 h-2 bg-surface-container rounded-xl overflow-hidden">
                <div class="h-full bg-success rounded-xl transition-all duration-500 shadow-2xs" style="width: {{ $pDisetujui }}%"></div>
            </div>
            <span class="text-body-sm sm:text-body-md font-bold text-primary w-10 text-right font-sans">{{ $pDisetujui }}%</span>
        </div>

        <div class="flex items-center justify-between gap-md group/bar">
            <span class="text-body-sm sm:text-body-md font-medium text-secondary/80 w-20 sm:w-24 select-none group-hover/bar:text-warning transition-colors">Review</span>
            <div class="flex-1 h-2 bg-surface-container rounded-xl overflow-hidden">
                <div class="h-full bg-warning rounded-xl transition-all duration-500 shadow-2xs" style="width: {{ $pMenunggu }}%"></div>
            </div>
            <span class="text-body-sm sm:text-body-md font-bold text-primary w-10 text-right font-sans">{{ $pMenunggu }}%</span>
        </div>

        <div class="flex items-center justify-between gap-md group/bar">
            <span class="text-body-sm sm:text-body-md font-medium text-secondary/80 w-20 sm:w-24 select-none group-hover/bar:text-error transition-colors">Revisi</span>
            <div class="flex-1 h-2 bg-surface-container rounded-xl overflow-hidden">
                <div class="h-full bg-error rounded-xl transition-all duration-500 shadow-2xs" style="width: {{ $pRevisi }}%"></div>
            </div>
            <span class="text-body-sm sm:text-body-md font-bold text-primary w-10 text-right font-sans">{{ $pRevisi }}%</span>
        </div>

    </div>
</div>