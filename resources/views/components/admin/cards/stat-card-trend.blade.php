@props([
    'title',
    'value',
    'trendText',                  // Teks info di bagian bawah (contoh: '+12 dari bulan lalu')
    'trendType' => 'success',     // success (hijau), error/danger (merah), neutral (abu-abu/biru)
    'iconType' => 'neutral'       // primary, success, error, neutral
])

<div class="bg-surface-container-lowest py-md px-md sm:p-lg rounded-xl sm:rounded-2xl border border-outline-variant/30 flex flex-row sm:flex-col justify-between items-center sm:items-stretch w-full min-h-[5.5rem] sm:min-h-[11rem] shadow-card hover:shadow-md hover:-translate-y-1 transition-all duration-300 ease-out group select-none gap-md sm:gap-0">

    <!-- BAGIAN MOBILE ONLY (Flat Row Horizontal) -->
    <div class="flex sm:hidden flex-row items-center justify-between gap-sm w-full">
        <div class="flex items-center gap-sm min-w-0 flex-1">
            <div class="p-sm rounded-xl shrink-0
                {{ $iconType === 'primary' ? 'bg-primary/5 text-primary' : '' }}
                {{ $iconType === 'success' ? 'bg-success/10 text-success' : '' }}
                {{ $iconType === 'error' ? 'bg-error-container/40 text-error' : '' }}
                {{ $iconType === 'neutral' ? 'bg-surface-container text-secondary' : '' }}
            ">
                <div class="w-5 h-5 [&>svg]:w-full [&>svg]:h-full">
                    {{ $icon }}
                </div>
            </div>
            
            <div class="flex flex-col min-w-0 pr-xs">
                <p class="text-[10px] font-bold text-on-surface-variant/50 uppercase tracking-wider leading-none mb-1.5 truncate">
                    {{ $title }}
                </p>
                <p class="text-xl font-bold text-on-surface tracking-tight leading-none">
                    {{ $value }}
                </p>
            </div>
        </div>
        
        <div class="shrink-0 text-[10px] font-semibold flex items-center gap-xs
            {{ $trendType === 'success' ? 'text-success' : '' }}
            {{ $trendType === 'error' ? 'text-error' : '' }}
            {{ $trendType === 'neutral' ? 'text-on-surface-variant/70' : '' }}
        ">
            @if(isset($trendIcon))
                <div class="scale-90 flex items-center justify-center">{{ $trendIcon }}</div>
            @endif
            <span>{{ $trendText }}</span>
        </div>
    </div>

    <!-- BAGIAN TABLET & DESKTOP ONLY (Bento) -->
    <div class="hidden sm:flex flex-col h-full justify-between">
        <div>
            <div class="flex items-start justify-between mb-md">
                <p class="text-caption font-bold text-on-surface-variant/50 uppercase tracking-wider pt-1 leading-none">
                    {{ $title }}
                </p>
                <div class="p-sm rounded-xl shrink-0 transition-transform duration-300 group-hover:scale-105
                    {{ $iconType === 'primary' ? 'bg-primary/5 text-primary' : '' }}
                    {{ $iconType === 'success' ? 'bg-success/10 text-success' : '' }}
                    {{ $iconType === 'error' ? 'bg-error-container/40 text-error' : '' }}
                    {{ $iconType === 'neutral' ? 'bg-surface-container text-secondary' : '' }}
                ">
                    <div class="w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full">
                        {{ $icon }}
                    </div>
                </div>
            </div>

            <p class="text-display-lg font-bold text-on-surface tracking-tight leading-none transition-colors duration-300 group-hover:text-primary mb-md">
                {{ $value }}
            </p>
        </div>

        <div class="pt-sm border-t border-outline-variant/10 flex items-center gap-xs text-caption font-bold mt-auto
            {{ $trendType === 'success' ? 'text-success' : '' }}
            {{ $trendType === 'error' ? 'text-error' : '' }}
            {{ $trendType === 'neutral' ? 'text-on-surface-variant/70' : '' }}
        ">
            @if(isset($trendIcon))
                {{ $trendIcon }}
            @endif
            <span>{{ $trendText }}</span>
        </div>
    </div>

</div>