@props([
    'value',
    'title',
    'badgeText' => null,
    'badgeType' => 'neutral', // success, error, neutral
    'iconBg' => 'neutral'      // primary, success, error, neutral
])

<div class="bg-surface-container-lowest p-md sm:p-lg rounded-xl border border-outline-variant/30 flex flex-row sm:flex-col justify-between items-center sm:items-start w-full min-h-[5.5rem] sm:min-h-[11rem] shadow-card hover:shadow-md hover:-translate-y-1 transition-all duration-300 ease-out group select-none">
    
    <div class="flex items-center sm:justify-between sm:items-start sm:w-full gap-md sm:gap-0">
        <div class="p-sm rounded-xl shrink-0 transition-transform duration-300 group-hover:scale-105
            {{ $iconBg === 'primary' ? 'bg-primary/5 text-primary' : '' }}
            {{ $iconBg === 'success' ? 'bg-success/10 text-success' : '' }}
            {{ $iconBg === 'error' ? 'bg-error-container/40 text-error' : '' }}
            {{ $iconBg === 'neutral' ? 'bg-surface-container text-secondary' : '' }}
        ">
            <div class="w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:object-contain">
                {{ $icon }}
            </div>
        </div>

        <div class="flex flex-col sm:hidden">
            <p class="text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-wider leading-none mb-1">
                {{ $title }}
            </p>
            <p class="text-2xl font-bold text-on-surface tracking-tight leading-none">
                {{ $value }}
            </p>
        </div>

        @if($badgeText)
            <span class="hidden sm:inline-flex text-label-md px-sm py-1 rounded-full font-bold items-center gap-xs tracking-wide shrink-0
                {{ $badgeType === 'success' ? 'bg-success/10 text-success' : '' }}
                {{ $badgeType === 'error' ? 'bg-error-container text-on-error-container' : '' }}
                {{ $badgeType === 'neutral' ? 'bg-surface-container-low text-on-secondary-container' : '' }}
            ">
                @if(isset($badgeIcon))
                    {{ $badgeIcon }}
                @endif
                <span>{{ $badgeText }}</span>
            </span>
        @endif
    </div>

    <div class="mt-0 sm:mt-xl flex flex-col items-end sm:items-start justify-center sm:justify-end shrink-0">
        
        @if($badgeText)
            <span class="inline-flex sm:hidden text-[10px] px-2 py-0.5 rounded-full font-bold items-center gap-[2px] tracking-wide mb-1
                {{ $badgeType === 'success' ? 'bg-success/10 text-success' : '' }}
                {{ $badgeType === 'error' ? 'bg-error-container text-on-error-container' : '' }}
                {{ $badgeType === 'neutral' ? 'bg-surface-container-low text-on-secondary-container' : '' }}
            ">
                @if(isset($badgeIcon))
                    <div class="scale-90 flex items-center justify-center">{{ $badgeIcon }}</div>
                @endif
                <span>{{ $badgeText }}</span>
            </span>
        @endif

        <p class="hidden sm:block text-display-lg font-bold text-on-surface tracking-tight leading-none transition-colors duration-300 group-hover:text-primary">
            {{ $value }}
        </p>
        <p class="hidden sm:block text-body-md font-medium text-secondary/80 tracking-wide mt-sm leading-none">
            {{ $title }}
        </p>
    </div>

</div>