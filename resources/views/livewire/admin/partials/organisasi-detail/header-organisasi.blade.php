@php
    $currentStatus = is_object($org->status) ? $org->status->value : $org->status;

    $statusColor = match($currentStatus) {
        'approved' => 'text-success bg-success/10 border-success/20',
        'rejected' => 'text-error bg-error/10 border-error/20',
        'pending'  => 'text-warning bg-warning/10 border-warning/20',
        default    => 'text-warning bg-warning/10 border-warning/20'
    };

    $statusTeks = match($currentStatus) {
        'approved' => 'Terverifikasi / Aktif',
        'rejected' => 'Ditolak / Revisi',
        'pending'  => 'Menunggu Verifikasi',
        default    => 'Menunggu Verifikasi'
    };
@endphp

<div class="w-full p-md sm:p-lg bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-card flex flex-col sm:flex-row items-center sm:items-start gap-md sm:gap-lg select-none animate-fade-in">

    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-surface-container-low border border-outline-variant/30 flex items-center justify-center p-sm overflow-hidden shrink-0 relative group shadow-xs">
        @if($org->logo_url)
            <img src="{{ asset('storage/' . $org->logo_url) }}" 
                    alt="Logo {{ $org->nama_organisasi }}" 
                    class="w-full h-full object-contain transition-all duration-500 group-hover:scale-[1.03] mix-blend-multiply relative z-10">
            
            <div class="absolute inset-0 rounded-2xl ring-1 ring-black/[0.02] pointer-events-none z-20"></div>
        @else
            <span class="tracking-tighter uppercase select-none text-primary font-black text-headline-md relative z-10">
                {{ strtoupper(substr($org->nama_organisasi ?? 'O', 0, 2)) }}
            </span>
        @endif
    </div>

    <div class="flex-1 min-w-0 flex flex-col items-center sm:items-start text-center sm:text-left h-full justify-center pt-xs">
        
        <h2 class="text-title-md sm:text-title-lg font-bold sm:font-bold text-primary tracking-tight whitespace-normal break-words w-full" title="{{ $org->nama_organisasi }}">
            {{ $org->nama_organisasi }}
        </h2>

        <p class="text-body-md text-on-surface-variant font-medium mt-1 leading-snug">
            <span class="text-secondary font-semibold">
                {{ $org->fakultas->nama_fakultas ?? 'Universitas Udayana' }}
            </span>
        </p>

        <div class="flex flex-col sm:flex-row sm:items-center gap-sm sm:gap-md text-secondary/60 mt-md font-sans text-caption font-bold border-t border-outline-variant/20 pt-sm w-full select-none">
            
            <div class="flex items-center justify-center sm:justify-start gap-xs min-w-0">
                <svg class="w-3.5 h-3.5 text-secondary/40 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                </svg>
                <a href="mailto:{{ $org->user->email ?? 'hima-if@univ-example.ac.id' }}" 
                    class="hover:text-primary transition-colors truncate max-w-[240px] sm:max-w-xs font-semibold tracking-wide text-secondary/80">
                    {{ $org->user->email ?? 'hima-if@univ-example.ac.id' }}
                </a>
            </div>

            <span class="hidden sm:inline text-secondary/30 font-bold">•</span>

            <div class="px-2.5 py-0.5 rounded-full border text-[10px] font-extrabold tracking-wide inline-flex items-center gap-xs shadow-2xs w-fit shrink-0 self-center sm:self-auto {{ $statusColor }}">
                @if($currentStatus === 'approved')
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @elseif($currentStatus === 'rejected')
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                @else
                    <svg class="w-3.5 h-3.5 shrink-0 animate-spin" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                @endif
                <span>{{ $statusTeks }}</span>
            </div>

        </div>

    </div>
</div>