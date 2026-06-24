<div class="bg-surface-container-lowest p-md sm:p-lg rounded-2xl border border-outline-variant/30 shadow-sm w-full h-full flex flex-col justify-between">
    
    <div class="mb-md select-none">
        <h4 class="text-body-md md:text-body-lg font-bold md:font-bold text-primary tracking-tight">
            {{ $title }}
        </h4>
        @if(isset($description))
            <p class="text-xs text-secondary/70 font-medium mt-1">
                {{ $description }}
            </p>
        @endif
    </div>

    <div class="flex-1 min-h-0 relative w-full h-[140px] sm:h-[160px]"
        x-data="eventLineChart(@js($config))">
        <canvas x-ref="canvas"></canvas>
    </div>

</div>