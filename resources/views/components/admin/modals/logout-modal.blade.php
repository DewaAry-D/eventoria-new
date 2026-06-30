@props(['target' => 'organisasi', 'action' => route('logout')])

<template x-teleport="body">
    <div x-show="showLogoutModal"
            x-cloak
            class="fixed inset-0 z-[150] flex items-center justify-center p-md select-none"
            style="display: none;"
            @keydown.escape.window="showLogoutModal = false">

        <div x-show="showLogoutModal"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @click="showLogoutModal = false"
                class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <div x-show="showLogoutModal"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                class="relative bg-surface-container-lowest max-w-sm w-full p-lg sm:p-xl rounded-[28px] shadow-2xl border border-outline-variant/20 z-10 text-center">

            <div class="mx-auto w-16 h-16 bg-error/10 text-error rounded-full flex items-center justify-center mb-md">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </div>

            <h3 class="text-title-md font-bold text-primary tracking-tight mb-sm">Keluar dari Sistem?</h3>
            <p class="text-body-sm text-secondary/70 font-medium leading-relaxed mb-lg max-w-xs mx-auto">
                @if($target === 'admin')
                    Apakah Anda yakin ingin mengakhiri sesi ini? Anda harus login kembali untuk mengakses dasbor admin Eventoria.
                @elseif($target === 'organisasi')
                    Apakah Anda yakin ingin mengakhiri sesi ini? Anda harus login kembali untuk mengakses dasbor organisasi Eventoria.
                @else
                    Apakah Anda yakin ingin mengakhiri sesi ini? Anda harus login kembali untuk mengakses dasbor mahasiswa Eventoria.
                @endif
            </p>

            <form method="POST" action="{{ $action }}" class="flex items-center justify-center gap-sm">
                @csrf
                <button type="button"
                        @click="showLogoutModal = false"
                        class="flex-1 px-md py-2.5 text-body-sm font-bold text-secondary/80 hover:bg-surface-container rounded-full transition-colors active:scale-95">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-md py-2.5 text-body-sm font-bold text-white bg-error hover:bg-error/90 rounded-full shadow-md hover:shadow transition-all active:scale-95">
                    Ya, Keluar
                </button>
            </form>
        </div>
    </div>
</template>