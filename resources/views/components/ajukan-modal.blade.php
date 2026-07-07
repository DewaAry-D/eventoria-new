<template x-teleport="body">
    <div x-show="showAjukanModal"
         x-cloak
         class="fixed inset-0 z-[150] flex items-center justify-center p-md select-none"
         style="display: none;"
         @keydown.escape.window="showAjukanModal = false">

        <div x-show="showAjukanModal"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="showAjukanModal = false"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <div x-show="showAjukanModal"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
             class="relative bg-surface-container-lowest max-w-sm w-full p-lg sm:p-xl rounded-[28px] shadow-2xl border border-outline-variant/20 z-10 text-center">

            <div class="mx-auto w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mb-md">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </div>

            <h3 class="text-title-md font-bold text-primary tracking-tight mb-sm">Ajukan Event ke DPM?</h3>
            <p class="text-body-sm text-secondary/70 font-medium leading-relaxed mb-lg max-w-xs mx-auto">
                Apakah Anda yakin ingin mengajukan event ini untuk direview? Pastikan Anda sudah menyusun <span class="font-bold text-secondary">Form Pendaftaran</span> dengan lengkap.
            </p>

            <div class="flex items-center justify-center gap-sm">
                <button type="button"
                        @click="showAjukanModal = false"
                        class="flex-1 px-md py-2.5 text-body-sm font-bold text-secondary/80 hover:bg-surface-container rounded-full transition-colors active:scale-95">
                    Batal
                </button>
                
                <button type="button"
                        @click="$wire.ajukanEvent(selectedEventId); showAjukanModal = false"
                        class="flex-1 px-md py-2.5 text-body-sm font-bold text-white bg-primary hover:bg-primary/90 rounded-full shadow-md hover:shadow transition-all active:scale-95">
                    Ya, Ajukan
                </button>
            </div>
        </div>
    </div>
</template>