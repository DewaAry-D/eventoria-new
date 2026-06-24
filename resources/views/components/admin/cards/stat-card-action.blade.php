@props([
    'title',
    'value',
    'unit', // Teks kecil pendamping angka (contoh: 'Organisasi', 'Terdaftar')
    'footerLabel', // Teks di badge bawah (contoh: 'Perlu Verifikasi')
    'footerType' => 'primary', // 'primary', 'success', 'error'
    'iconType' => 'primary' // 'primary', 'success', 'error'
])

<div class="bg-surface-container-lowest py-md px-md sm:p-lg rounded-xl sm:rounded-2xl border border-outline-variant/30 shadow-card hover:shadow-md hover:-translate-y-1 transition-all duration-300 ease-out group select-none w-full sm:min-h-[11rem] flex flex-col items-stretch sm:justify-between">

    {{-- ================= MOBILE ONLY (flat row) ================= --}}
    <div class="flex sm:hidden flex-row items-center justify-between gap-sm">
        <div class="flex items-center gap-sm min-w-0 flex-1">
            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0
                {{ $iconType === 'primary' ? 'bg-primary/10 text-primary' : '' }}
                {{ $iconType === 'success' ? 'bg-success/10 text-success' : '' }}
                {{ $iconType === 'error' ? 'bg-error-container/40 text-error' : '' }}
            ">
                <div class="w-4 h-4 [&>svg]:w-full [&>svg]:h-full">
                    {{ $icon }}
                </div>
            </div>
            
            <div class="flex flex-col min-w-0 pr-xs">
                <p class="text-[10px] font-bold text-on-surface-variant/50 uppercase tracking-wider leading-none mb-1 truncate">
                    {{ $title }}
                </p>
                <div class="flex items-baseline gap-[3px] leading-none">
                    <span class="text-xl font-bold text-on-surface tracking-tight">{{ $value }}</span>
                    <span class="text-[10px] text-on-surface-variant font-medium pl-px">{{ $unit }}</span>
                </div>
            </div>
        </div>
        
        <span class="text-[9px] px-2.5 py-1 rounded-full font-bold inline-flex items-center justify-center tracking-wide leading-none shrink-0 shadow-sm/50
            {{ $footerType === 'primary' ? 'bg-primary text-on-primary' : '' }}
            {{ $footerType === 'success' ? 'bg-success/10 text-success' : '' }}
            {{ $footerType === 'error' ? 'bg-error-container text-on-error-container' : '' }}
        ">
            {{ $footerLabel }}
        </span>
    </div>

    {{-- ================= TABLET & DESKTOP (bento vertikal) ================= --}}
    <div class="hidden sm:flex flex-col h-full justify-between">
        <div>
            <div class="flex items-start justify-between mb-md">
                <p class="text-label-md font-bold text-on-surface-variant/60 tracking-wide pt-1 uppercase">
                    {{ $title }}
                </p>
                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 transition-transform duration-300 group-hover:scale-105
                    {{ $iconType === 'primary' ? 'bg-primary/10 text-primary' : '' }}
                    {{ $iconType === 'success' ? 'bg-success/10 text-success' : '' }}
                    {{ $iconType === 'error' ? 'bg-error-container/40 text-error' : '' }}
                ">
                    <div class="w-5 h-5 [&>svg]:w-full [&>svg]:h-full">
                        {{ $icon }}
                    </div>
                </div>
            </div>
            <div class="flex items-baseline gap-xs">
                <span class="text-display-lg font-bold text-on-surface tracking-tight leading-none transition-colors duration-300 group-hover:text-primary">
                    {{ $value }}
                </span>
                <span class="text-body-md text-on-surface-variant font-medium">
                    {{ $unit }}
                </span>
            </div>
        </div>

        <span class="text-label-md px-md py-1.5 rounded-full font-bold inline-flex items-center tracking-wide leading-none w-fit mt-md
            {{ $footerType === 'primary' ? 'bg-primary text-on-primary' : '' }}
            {{ $footerType === 'success' ? 'bg-success/10 text-success' : '' }}
            {{ $footerType === 'error' ? 'bg-error-container text-on-error-container' : '' }}
        ">
            {{ $footerLabel }}
        </span>
    </div>
</div>