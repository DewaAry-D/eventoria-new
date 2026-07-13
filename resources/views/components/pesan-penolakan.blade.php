<!-- Modal Alpine.js untuk Catatan Penolakan -->
<div x-data="{ show: false, pesan: '' }"
     x-show="show"
     @pesan-penolakan.window="show = true; pesan = $event.detail.pesan"
     @keydown.escape.window="show = false"
     style="display: none;"
     class="fixed inset-0 z-[200] flex items-center justify-center bg-black/40 p-4 backdrop-blur-sm transition-opacity"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <!-- Konten Modal -->
    <div @click.away="show = false" 
         class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-2xl transition-all"
         x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        
        <!-- Header Modal -->
        <div class="mb-4 flex items-center justify-between border-b border-gray-100 pb-4">
            <h3 class="flex items-center gap-2 text-lg font-bold text-red-600">
                <i class="fa-solid fa-circle-exclamation"></i> 
                Catatan Penolakan Admin
            </h3>
            <button @click="show = false" class="text-gray-400 transition-colors hover:text-gray-700">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <!-- Isi Pesan -->
        <div class="mb-8 mt-4 rounded-lg bg-red-50/50 p-4 border border-red-100">
            <p class="text-sm leading-relaxed text-gray-700" x-text="pesan"></p>
        </div>

        <!-- Footer Modal -->
        <div class="flex justify-end">
            <button @click="show = false" type="button" 
                    class="rounded-md bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-1">
                Tutup Peringatan
            </button>
        </div>
    </div>
</div>