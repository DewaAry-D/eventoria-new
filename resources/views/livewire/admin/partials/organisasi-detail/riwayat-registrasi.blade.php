@php
    $currentStatus = is_object($org->status) ? $org->status->value : $org->status;

    // Menentukan pewarnaan untuk titik node ke-2 berdasarkan status
    $node2Color = match($currentStatus) {
        'approved' => 'bg-success text-success ring-success/20',
        'rejected' => 'bg-error text-error ring-error/20',
        'pending'  => 'bg-surface-container-high text-secondary/40 ring-transparent',
        default    => 'bg-surface-container-high text-secondary/40 ring-transparent'
    };
@endphp

<div class="w-full p-md sm:p-lg bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-card flex flex-col gap-md select-none animate-fade-in">
    
    <div class="flex items-center gap-sm text-primary border-b border-surface-container/60 pb-xs">
        <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h4 class="text-title-sm font-bold tracking-tight">Riwayat Registrasi</h4>
    </div>

    <div class="relative flex flex-col gap-lg pl-sm pt-xs pb-xs w-full">
        
        <div class="absolute left-[13px] top-sm bottom-sm w-[2px] bg-surface-container rounded-full pointer-events-none"></div>

        <div class="relative flex items-start gap-md w-full">
            <div class="w-[12px] h-[12px] rounded-full bg-primary ring-4 ring-primary/10 mt-1.5 shrink-0 z-10"></div>
            
            <div class="flex flex-col min-w-0 leading-tight">
                <h5 class="text-body-md font-bold text-primary tracking-tight">
                    {{ $timeline['step_1']['title'] }}
                </h5>
                <span class="text-[11px] font-medium text-secondary/50 font-sans mt-0.5">
                    {{ $timeline['step_1']['time'] }}
                </span>
                <p class="text-body-sm text-on-surface-variant font-normal mt-1.5 leading-relaxed whitespace-normal break-words">
                    {{ $timeline['step_1']['desc'] }}
                </p>
            </div>
        </div>

        <div class="relative flex items-start gap-md w-full">
            <div class="w-[12px] h-[12px] rounded-full mt-1.5 shrink-0 z-10 ring-4 transition-all duration-300 {{ $node2Color }}">
                @if($currentStatus === 'pending')
                    <div class="w-full h-full rounded-full bg-secondary/30 animate-ping"></div>
                @endif
            </div>
            
            <div class="flex flex-col min-w-0 leading-tight">
                <h5 class="text-body-md font-bold tracking-tight 
                    {{ $currentStatus === 'approved' ? 'text-success' : ($currentStatus === 'rejected' ? 'text-error' : 'text-primary/70') }}">
                    {{ $timeline['step_2']['title'] }}
                </h5>
                
                <span class="text-[11px] font-medium font-sans mt-0.5 
                    {{ $currentStatus === 'pending' ? 'text-warning font-semibold italic' : 'text-secondary/50' }}">
                    {{ $timeline['step_2']['time'] }}
                </span>
                
                <p class="text-body-sm text-on-surface-variant font-normal mt-1.5 leading-relaxed whitespace-normal break-words">
                    {{ $timeline['step_2']['desc'] }}
                </p>
            </div>
        </div>

    </div>
</div>