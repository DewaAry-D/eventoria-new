<template x-teleport="body">
    <div x-show="showSopModal"
            x-cloak
            class="fixed inset-0 z-[150] flex items-center justify-center p-md"
            style="display: none;"
            @keydown.escape.window="showSopModal = false">

        <div x-show="showSopModal"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @click="showSopModal = false"
                class="fixed inset-0 bg-inverse-surface/50 backdrop-blur-sm"></div>

        <div x-show="showSopModal"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative bg-surface-container-lowest max-w-2xl w-full max-h-[85vh] rounded-2xl shadow-2xl border border-outline-variant/20 z-10 flex flex-col overflow-hidden">

            <div class="flex items-center justify-between px-lg py-md border-b border-outline-variant shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary/10 text-primary rounded-full flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-title-md font-bold text-on-surface">SOP Pengajuan Event</h3>
                </div>
                <button type="button" @click="showSopModal = false" class="p-1.5 text-on-surface-variant hover:bg-surface-container rounded-full transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-lg py-lg overflow-y-auto space-y-5">
                <p class="text-body-sm text-on-surface-variant">
                    Ikuti tahapan berikut agar pengajuan event organisasi Anda dapat diproses dengan lancar oleh DPM.
                </p>

                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="shrink-0 w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-sm">1</div>
                        <div>
                            <h4 class="font-bold text-on-surface text-sm">Pastikan Akun Organisasi Terverifikasi</h4>
                            <p class="text-body-sm text-on-surface-variant mt-1">Fitur pembuatan event hanya aktif setelah status akun organisasi Anda disetujui (approved) oleh DPM.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="shrink-0 w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-sm">2</div>
                        <div>
                            <h4 class="font-bold text-on-surface text-sm">Lengkapi Detail Event</h4>
                            <p class="text-body-sm text-on-surface-variant mt-1">Isi informasi utama, lokasi, timeline, biaya, narahubung, dan rekening pembayaran (jika berbayar) selengkap mungkin.</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="shrink-0 w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-sm">3</div>
                        <div>
                            <h4 class="font-bold text-on-surface text-sm">Buat Form Pendaftaran</h4>
                            <p class="text-body-sm text-on-surface-variant mt-1">Susun pertanyaan pada form pendaftaran (Setup Form) untuk event Anda. Tahap ini wajib diselesaikan sebelum event dapat diajukan untuk ditinjau oleh DPM.</p>
                        </div>
                    </div>
    
                    <div class="flex gap-4">
                        <div class="shrink-0 w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-sm">3</div>
                        <div>
                            <h4 class="font-bold text-on-surface text-sm">Ajukan untuk Ditinjau</h4>
                            <p class="text-body-sm text-on-surface-variant mt-1">Event yang sudah lengkap akan berstatus "Diproses DPM" dan menunggu peninjauan dari pihak DPM.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="shrink-0 w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-sm">4</div>
                        <div>
                            <h4 class="font-bold text-on-surface text-sm">Perbaiki Jika Diminta Revisi</h4>
                            <p class="text-body-sm text-on-surface-variant mt-1">Jika DPM memberikan catatan revisi, event akan berstatus "Revisi Proposal". Perbaiki sesuai catatan lalu ajukan ulang.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="shrink-0 w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-sm">5</div>
                        <div>
                            <h4 class="font-bold text-on-surface text-sm">Event Dipublikasikan</h4>
                            <p class="text-body-sm text-on-surface-variant mt-1">Setelah disetujui, event berstatus "Dipublikasi" dan dapat menerima pendaftar dari mahasiswa.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-lg py-md border-t border-outline-variant flex justify-end shrink-0">
                <button type="button" @click="showSopModal = false"
                        class="px-md py-2.5 text-body-sm font-bold text-on-primary bg-primary hover:bg-primary/90 rounded-full transition active:scale-95">
                    Mengerti
                </button>
            </div>
        </div>
    </div>
</template>