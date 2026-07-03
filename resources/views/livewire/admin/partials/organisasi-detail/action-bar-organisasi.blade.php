<div class="w-full flex flex-col gap-sm select-none animate-fade-in mt-xs">
    
    <button type="button" 
            @click="$dispatch('open-modal-approve-event', { id: {{ $org->id }}, name: '{{ addslashes($org->nama_organisasi) }}' })"
            class="w-full py-3 rounded-2xl text-on-primary bg-success hover:bg-success/90 border border-success font-bold text-body-sm flex items-center justify-center gap-xs transition-all active:scale-[0.98] shadow-md hover:shadow cursor-pointer">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Setujui Organisasi</span>
    </button>

    <button type="button" 
            @click="$dispatch('open-modal-reject-event', { id: {{ $org->id }} })"
            class="w-full py-2.5 rounded-2xl border border-error/30 text-error hover:bg-error/5 font-bold text-body-sm flex items-center justify-center gap-xs transition-all active:scale-[0.98] shadow-2xs cursor-pointer">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Tolak Registrasi</span>
    </button>

    <p class="text-[11px] text-secondary/50 font-medium text-center leading-tight mt-xs px-xs">
        Pastikan keabsahan seluruh berkas fisik sebelum memberikan keputusan final.
    </p>
</div>

<x-admin.modals.confirm-modal 
    id="approve-event" 
    title="Setujui Verifikasi Organisasi" 
    wireAction="approve">
    Apakah Anda yakin ingin menyetujui pendaftaran berkas <strong class="text-primary font-bold">"<span x-text="targetName"></span>"</strong>? Organisasi ini akan berstatus aktif di dalam sistem.
</x-admin.modals.confirm-modal>

<x-admin.modals.reject-modal 
    id="reject-event" 
    title="Tolak Berkas Registrasi?" 
    description="Berikan alasan penolakan yang jelas agar pengurus ormawa dapat melakukan revisi perbaikan." 
    wireModel="pesanPenolakan" 
    wireAction="reject" 
/>