<div class="bg-surface-container-lowest p-md sm:p-lg rounded-2xl border border-outline-variant/30 shadow-sm w-full">
    
    <div class="flex justify-between items-start gap-md mb-lg select-none">
        <div>
            <h3 class="text-title-lg md:text-headline-md font-bold md:font-bold text-primary tracking-tight">
                {{ $title ?? 'Jumlah Event Per Bulan' }}
            </h3>
            @if(isset($description))
                <p class="text-xs text-secondary/70 font-medium mt-xs">
                    {{ $description }}
                </p>
            @endif
        </div>
        
        <span class="inline-flex items-center gap-xs sm:gap-sm px-sm sm:px-md py-1 sm:py-xs bg-surface-container text-primary font-bold text-xs sm:text-body-sm rounded-lg border border-outline-variant/20 select-none whitespace-nowrap shrink-0">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 13.3333V7.5H13.3333V13.3333H10ZM5 13.3333V0H8.33333V13.3333H5ZM0 13.3333V4.16667H3.33333V13.3333H0Z" fill="currentColor"/></svg>
            <span class="hidden sm:block">6 Bulan Terakhir</span>
        </span>
    </div>

    <div x-data="eventBarChart(@js($chartConfig))" class="w-full h-56 sm:h-64 md:h-80">
        <canvas x-ref="canvas"></canvas>
    </div>

</div>