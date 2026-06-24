<div class="bg-surface-container-lowest p-md sm:p-lg rounded-2xl border border-outline-variant/30 shadow-sm w-full">

    <div class="mb-md">
        <h4 class="text-title-sm sm:text-title-md font-bold sm:font-bold text-primary tracking-tight">
            {{ $title }}
        </h4>
        
        <p class="text-caption text-on-surface-variant font-medium mt-0.5 leading-normal">
            Perbandingan kategori event di lingkungan <strong class="text-primary font-semibold">{{ $scopeName }}</strong>.
        </p>
    </div>

    <div class="w-full h-44 sm:h-52"
        x-data="eventPieChart(@js($chartConfig))">
        <canvas x-ref="canvas"></canvas>
    </div>

    <div class="border-t border-outline-variant/20 my-md"></div>

    <div class="grid grid-cols-2 gap-x-md gap-y-sm">
        @foreach($chartConfig['legend'] as $item)
            <div class="flex items-center gap-sm min-w-0 select-none">
                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                    style="background-color: {{ $item['color'] }}"></span>

                <div class="flex flex-col min-w-0">
                    <span class="text-caption sm:text-caption font-bold sm:font-bold text-primary truncate leading-none">
                        {{ $item['label'] }}
                    </span>
                    <span class="text-[10px] sm:text-caption text-secondary font-medium leading-none mt-0.5">
                        {{ $item['count'] }} Event
                    </span>
                </div>
            </div>
        @endforeach
    </div>

</div>