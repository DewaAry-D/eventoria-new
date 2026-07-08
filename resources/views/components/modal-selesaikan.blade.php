<template x-teleport="body">
    <div x-show="showSelesaikanModal"
         x-cloak
         class="fixed inset-0 z-[150] flex items-center justify-center p-4 select-none"
         style="display: none;"
         @keydown.escape.window="showSelesaikanModal = false">

        <div x-show="showSelesaikanModal"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="showSelesaikanModal = false"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <div x-show="showSelesaikanModal"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
             class="relative bg-white max-w-sm w-full p-6 sm:p-8 rounded-2xl shadow-2xl border border-gray-100 z-10 text-center">

            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-emerald-100 text-emerald-600">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h3 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Tandai Event Selesai?</h3>
            <p class="max-w-xs mx-auto mb-6 text-sm font-medium leading-relaxed text-gray-500">
                Apakah Anda yakin event ini telah selesai dilaksanakan? Status tidak dapat dikembalikan ke <span class="font-bold text-gray-700">Publikasi</span> setelah diselesaikan.
            </p>

            <div class="flex items-center justify-center gap-3">
                <button type="button"
                        @click="showSelesaikanModal = false"
                        class="flex-1 px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-100 rounded-full transition-colors active:scale-95">
                    Batal
                </button>
                
                <button type="button"
                        @click="$wire.selesaikanEvent(selectedEventId); showSelesaikanModal = false"
                        class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-full shadow-md hover:shadow transition-all active:scale-95">
                    Ya, Selesai
                </button>
            </div>
        </div>
    </div>
</template>