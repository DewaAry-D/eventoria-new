@props([
    'id' => 'reject-event',
    'title' => 'Tolak Pendaftaran?',
    'description' => 'Berikan alasan penolakan agar organisasi dapat melakukan perbaikan.',
    'wireAction'
])

<div wire:ignore
    x-data="{ open: false, eventId: null, alasan: '', error: '' }"
    @open-modal-{{ $id }}.window="open = true; eventId = $event.detail.id; alasan = ''; error = ''"
    @close-modal-{{ $id }}.window="open = false"
    @keydown.escape.window="if (open) open = false"
    :class="open ? 'pointer-events-auto' : 'pointer-events-none'"
    class="fixed inset-0 z-50 flex items-center justify-center p-md select-none"
    x-cloak>

    <!-- Backdrop -->
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

    <!-- Modal Content -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
        class="relative bg-surface-container-lowest max-w-md w-full p-lg sm:p-xl rounded-[28px] shadow-2xl border border-outline-variant/20 z-10 text-center">

        <div class="mx-auto w-16 h-16 bg-error-container/40 text-error rounded-full flex items-center justify-center mb-md">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <h3 class="text-title-lg font-bold text-primary tracking-tight mb-sm">{{ $title }}</h3>
        <p class="text-body-sm text-secondary/70 font-medium leading-relaxed mb-lg max-w-xs mx-auto">{{ $description }}</p>

        <div class="text-left mb-md">
            <label class="text-body-sm font-bold text-primary select-none mb-xs block">
                Alasan Penolakan
            </label>
            <textarea
                x-model="alasan"
                @input="error = ''"
                placeholder="Tulis alasan di sini..."
                rows="4"
                class="w-full text-body-md p-sm bg-surface-container/20 border rounded-2xl focus:outline-none text-primary placeholder-secondary/40 font-medium resize-none transition-all"
                :class="error ? 'border-error/50 focus:border-error' : 'border-outline-variant/50 focus:border-primary/30'">
            </textarea>

            <template x-if="error">
                <span class="text-caption font-semibold text-error mt-xs block" x-text="error"></span>
            </template>

            <div class="flex items-start gap-xs text-secondary/50 mt-sm leading-tight">
                <svg class="w-3.5 h-3.5 shrink-0 mt-[2px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-[11px] font-medium">Alasan ini akan dikirimkan ke email pendaftar sebagai feedback.</span>
            </div>
        </div>

        <div class="flex items-center justify-center gap-sm mt-lg">
            <button type="button"
                @click="open = false"
                class="flex-1 px-md py-2.5 text-body-sm font-bold text-secondary/80 hover:bg-surface-container rounded-full transition-colors active:scale-95">
                Batal
            </button>

            <button type="button"
                @click="
                    if (alasan.trim().length < 5) {
                        error = 'Alasan terlalu singkat, minimal 5 karakter.';
                    } else {
                        error = '';
                        open = false;
                        $nextTick(() => $wire.{{ $wireAction }}(eventId, alasan));
                    }
                "
                class="flex-1 px-md py-2.5 text-body-sm font-bold text-on-error bg-error hover:bg-error/90 rounded-full shadow-md transition-all active:scale-95">
                Kirim Penolakan
            </button>
        </div>
    </div>
</div>