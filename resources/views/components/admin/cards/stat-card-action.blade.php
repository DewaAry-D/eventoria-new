@props([
    'title',
    'value',
    'unit', // Teks kecil pendamping angka (contoh: 'Organisasi', 'Terdaftar')
    'footerLabel', // Teks di badge bawah (contoh: 'Perlu Verifikasi')
    'footerType' => 'primary', // 'primary', 'success', 'warning', 'error'
    'iconType' => 'primary' // 'primary', 'success', 'warning', 'error'
])

<div class="bg-surface-container-lowest p-md sm:p-lg rounded-xl sm:rounded-2xl border border-outline-variant/30 shadow-card hover:shadow-md hover:-translate-y-1 transition-all duration-300 ease-out group select-none w-full min-h-[6.5rem] sm:min-h-[11rem] flex flex-row sm:flex-col justify-between items-center sm:items-stretch">

    <div class="flex items-center sm:justify-between sm:items-start sm:w-full gap-md sm:gap-0 min-w-0 flex-1 sm:flex-initial">
        
        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0 order-1 sm:order-2 transition-all duration-300 group-hover:scale-105
            {{ $iconType === 'primary' ? 'bg-primary/10 text-primary' : '' }}
            {{ $iconType === 'success' ? 'bg-success/10 text-success' : '' }}
            {{ $iconType === 'warning' ? 'bg-warning/10 text-warning' : '' }}
            {{ $iconType === 'error' ? 'bg-error/10 text-error' : '' }}
        ">
            <div class="w-5 h-5 [&>svg]:w-full [&>svg]:h-full">
                {{ $icon }}
            </div>
        </div>

        <div class="flex flex-col min-w-0 sm:hidden order-2">
            <p class="text-caption font-bold text-on-surface-variant/50 uppercase tracking-wider leading-none mb-1 truncate">
                {{ $title }}
            </p>
            <div class="flex items-baseline gap-1 leading-none">
                <span class="text-2xl font-bold text-on-surface tracking-tight">{{ $value }}</span>
                <span class="text-caption text-on-surface-variant font-medium pl-px">{{ $unit }}</span>
            </div>
        </div>

        <div class="hidden sm:block order-1 flex-1">
            <p class="text-label-md font-bold text-on-surface-variant/60 tracking-wide pt-0.5 uppercase min-h-[2.5rem] mb-md max-w-[85%]">
                {{ $title }}
            </p>
            <div class="flex items-baseline gap-xs mt-auto">
                <span class="text-display-lg font-bold text-on-surface tracking-tight leading-none transition-colors duration-300 group-hover:text-primary">
                    {{ $value }}
                </span>
                <span class="text-body-md text-on-surface-variant font-medium">
                    {{ $unit }}
                </span>
            </div>
        </div>
    </div>

    <div class="shrink-0 mt-0 sm:mt-md pl-sm sm:pl-0">
        <span class="text-caption sm:text-label-md px-2.5 sm:px-md py-1.5 sm:py-1 rounded-xl font-bold inline-flex items-center justify-center tracking-wide leading-none shadow-2xs w-fit transition-transform duration-300 group-hover:scale-105
            {{ $footerType === 'primary' ? 'bg-primary/10 text-primary border border-primary/10' : '' }}
            {{ $footerType === 'success' ? 'bg-success/10 text-success border border-success/10' : '' }}
            {{ $footerType === 'warning' ? 'bg-warning/10 text-warning border border-warning/10' : '' }}
            {{ $footerType === 'error' ? 'bg-error/10 text-error border border-error/10' : '' }}
        ">
            {{ $footerLabel }}
        </span>
    </div>

</div>