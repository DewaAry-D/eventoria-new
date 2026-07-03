@props([
    'id',
    'title',
    'wireAction',
    'variant' => 'success'
])

@php
    // Mapping warna semantik tombol dan lingkaran ikon berdasarkan props variant
    $colorClasses = match($variant) {
        'primary' => [
            'bg_icon' => 'bg-primary/10 text-primary',
            'btn_confirm' => 'bg-primary hover:bg-primary/90 text-on-primary border border-primary'
        ],
        default => [
            'bg_icon' => 'bg-success/10 text-success',
            'btn_confirm' => 'bg-success hover:bg-success/90 text-on-primary border border-success'
        ]
    };
@endphp

<div wire:ignore
    x-data="{ open: false, targetId: null, targetName: '' }"
    @open-modal-{{ $id }}.window="open = true; targetId = $event.detail.id; targetName = $event.detail.name"
    @close-modal-{{ $id }}.window="open = false"
    @keydown.escape.window="if (open) open = false"
    :class="open ? 'pointer-events-auto' : 'pointer-events-none'"
    class="fixed inset-0 z-50 flex items-center justify-center p-md select-none"
    x-cloak>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="open = false"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm">
    </div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative bg-surface-container-lowest max-w-md w-full p-lg sm:p-xl rounded-[28px] shadow-2xl border border-outline-variant/20 z-10 text-center">

        <div class="mx-auto w-16 h-16 rounded-full flex items-center justify-center mb-md {{ $colorClasses['bg_icon'] }}">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
            </svg>
        </div>

        <h3 class="text-title-lg font-bold text-primary tracking-tight mb-md">{{ $title }}</h3>

        <p class="text-body-md text-secondary/70 font-medium leading-relaxed mb-lg max-w-sm mx-auto">
            {{ $slot }}
        </p>

        <div class="flex items-center justify-center gap-sm">
            <button type="button"
                @click="open = false"
                class="flex-1 px-md py-2.5 text-body-sm font-bold text-secondary/80 hover:bg-surface-container rounded-full transition-colors active:scale-95 cursor-pointer">
                Batal
            </button>

            <button type="button"
                @click="
                    open = false;
                    $nextTick(() => $wire.{{ $wireAction }}(targetId))
                "
                class="flex-1 px-md py-2.5 text-body-sm font-bold rounded-full shadow-md hover:shadow transition-all active:scale-95 cursor-pointer {{ $colorClasses['btn_confirm'] }}">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>