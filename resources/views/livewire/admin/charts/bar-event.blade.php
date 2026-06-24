<div class="bg-surface-container-lowest p-md sm:p-lg rounded-2xl border border-outline-variant/30 shadow-sm w-full">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-sm mb-lg select-none">
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
        
        <span class="self-start sm:self-center inline-flex items-center gap-xs sm:gap-sm px-sm sm:px-md py-1 sm:py-xs bg-surface-container text-primary font-bold text-xs sm:text-body-sm rounded-lg border border-outline-variant/20 select-none whitespace-nowrap">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 13.3333V7.5H13.3333V13.3333H10ZM5 13.3333V0H8.33333V13.3333H5ZM0 13.3333V4.16667H3.33333V13.3333H0Z" fill="currentColor"/></svg>
            <span class="hidden sm:block">6 Bulan Terakhir</span>
        </span>
    </div>

    <div class="w-full h-[220px] sm:h-[280px] md:h-[320px] relative"
        x-data="eventBarChart(@js($chartConfig))">
        <canvas x-ref="canvas"></canvas>
    </div>

</div>