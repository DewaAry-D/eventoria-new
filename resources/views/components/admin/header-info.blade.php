@props([
    'title'
])

<div class="flex justify-between items-start gap-md w-full mb-lg select-none">
    
    <div class="space-y-1 pr-4">
        <h3 class="text-headline-md md:text-headline-lg font-bold md:font-bold text-primary tracking-tight leading-none">
            {{ $title }}
        </h3>
        
        {{ $slot }}
    </div>

    <div class="shrink-0 flex items-center self-center">
        {{ $action }}
    </div>
</div>