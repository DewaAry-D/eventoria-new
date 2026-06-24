@props([
    'title'
])

<div class="flex flex-col gap-md sm:flex-row sm:items-center sm:justify-between w-full mb-lg gap-y-sm">
    
    <div class="space-y-1">
        <h3 class="text-headline-lg font-bold text-primary tracking-tight leading-none">
            {{ $title }}
        </h3>
        
        {{ $slot }}
    </div>

    @if(isset($action))
        <div class="flex items-center gap-xs sm:gap-sm shrink-0 w-full sm:w-auto">
            {{ $action }}
        </div>
    @endif
</div>