<div wire:ignore class="fixed top-0 right-0 w-0 h-0 overflow-hidden z-[9999]"
        x-data="{
            show: false,
            message: '',
            type: 'success',
            timeout: null,
            
            init() {
                {{-- Tangkap dari Session Flash (Hasil Redirect Halaman Lain) --}}
                @if(session()->has('success'))
                    this.triggerToast(@js(session('success')), 'success');
                @endif
                @if(session()->has('error'))
                    this.triggerToast(@js(session('error')), 'error');
                @endif
            },
            
            triggerToast(msg, type) {
                this.message = msg;
                this.type = type;
                this.show = true;
                
                if (this.timeout) clearTimeout(this.timeout);
                this.timeout = setTimeout(() => { this.show = false }, 4000);
            }
        }"
        {{-- Tangkap dari Livewire Dispatch Aktif (Tanpa Redirect) --}}
        @show-toast.window="triggerToast($event.detail.message || $event.detail[0].message, $event.detail.type || $event.detail[0].type)"
        @toast-alert.window="triggerToast($event.detail.message || $event.detail[0].message, $event.detail.type || $event.detail[0].type)">
    
    <!-- Kotak Toast Melayang -->
    <div x-show="show"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="opacity-0 -translate-y-4 sm:translate-y-0 sm:translate-x-4"
        x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        :class="type === 'success' 
            ? 'bg-success/10 border-success/20 text-success' 
            : 'bg-error/10 border-error/20 text-error'"
        class="fixed top-md right-md w-full max-w-sm p-md rounded-2xl border shadow-card flex items-center justify-between gap-sm select-none backdrop-blur-md"
        x-cloak>

        <div class="flex items-center gap-sm min-w-0">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                :class="type === 'success' ? 'bg-success/10' : 'bg-error/10'">

                <svg x-show="type === 'success'" class="w-5 h-5 text-success" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>

                <svg x-show="type === 'error'" class="w-5 h-5 text-error" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
            </div>

            <p class="text-body-sm font-bold truncate leading-tight pr-sm" x-text="message"></p>
        </div>

        <button type="button" @click="show = false"
            class="transition-colors cursor-pointer shrink-0 opacity-60 hover:opacity-100"
            :class="type === 'success' ? 'text-success' : 'text-error'">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>