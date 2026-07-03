@props([
    'title'
])

<div class="flex flex-col md:flex-row md:justify-between items-center md:items-center gap-md w-full mb-md sm:mb-lg select-none text-center md:text-left">
    
    <div class="space-y-1 min-w-0 w-full md:w-auto flex flex-col items-center md:items-start">
        <!-- Judul Halaman -->
        <h3 class="text-headline-md md:text-headline-lg font-bold sm:font-bold md:font-bold text-primary tracking-tight leading-snug md:leading-none">
            {{ $title }}
        </h3>
        
        <!-- Slot Deskripsi -->
        <div class="text-caption sm:text-body-md text-on-surface-variant/80 font-medium sm:font-medium leading-relaxed">
            {{ $slot }}
        </div>
    </div>

    <!-- Bagian Tombol Aksi-->
    @if(isset($action))
        <div class="shrink-0 flex items-center justify-center self-center md:self-center w-full md:w-auto pt-xs md:pt-0">
            {{ $action }}
        </div>
    @endif
</div>