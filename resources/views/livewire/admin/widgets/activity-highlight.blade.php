<div class="bg-primary/[0.04] p-md sm:p-lg rounded-[28px] border border-primary/10 shadow-sm space-y-md w-full">
    
    <div class="flex items-center gap-sm text-title-sm font-bold text-primary select-none pl-xs">
        <span class="text-primary">
            <svg width="4" height="18" viewBox="0 0 4 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2 18C1.45 18 0.979167 17.8042 0.5875 17.4125C0.195833 17.0208 0 16.55 0 16C0 15.45 0.195833 14.9792 0.5875 14.5875C0.979167 14.1958 1.45 14 2 14C2.55 14 3.02083 14.1958 3.4125 14.5875C3.80417 14.9792 4 15.45 4 16C4 16.55 3.80417 17.0208 3.4125 17.4125C3.02083 17.8042 2.55 18 2 18ZM0 12V0H4V12H0Z" fill="currentColor"/></svg>
        </span>
        Sorotan Aktivitas
    </div>

    <a href="{{ route('admin.moderasi.event') }}" wire:navigate 
        class="flex items-center justify-between p-md bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 hover:bg-surface-container/20 transition-all group active:scale-[0.99]">
        <div class="flex items-center gap-md min-w-0">
            <span class="w-2.5 h-2.5 rounded-full bg-red-600 flex-shrink-0 shadow-sm"></span>
            <div class="flex flex-col min-w-0">
                <span class="text-body-md font-bold text-primary tracking-tight">Kategori Event Terpopuler</span>
                <span class="text-caption text-on-surface-variant font-medium mt-0.5 truncate leading-relaxed">
                    Kategori <span class="text-primary font-bold bg-secondary/[0.06] px-1.5 py-0.5 rounded-md border border-secondary/10">{{ $kategoriNama }}</span> mendominasi bulan ini
                </span>
            </div>
        </div>
        <span class="text-secondary/40 group-hover:text-primary group-hover:translate-x-0.5 transition-transform duration-200 pl-xs">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 5l7 7-7 7"/></svg>
        </span>
    </a>

    <a href="{{ route('admin.moderasi.organisasi') }}" wire:navigate 
        class="flex items-center justify-between p-md bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant/10 hover:bg-surface-container/20 transition-all group active:scale-[0.99]">
        <div class="flex items-center gap-md min-w-0">
            <span class="w-2.5 h-2.5 rounded-full bg-primary flex-shrink-0 shadow-sm"></span>
            <div class="flex flex-col min-w-0">
                <span class="text-body-md font-bold text-primary tracking-tight">Organisasi Teraktif</span>
                <span class="text-caption text-on-surface-variant font-medium mt-0.5 truncate leading-relaxed">
                    <span class="text-primary font-bold bg-primary/[0.06] px-1.5 py-0.5 rounded-md border border-primary/10">{{ $organisasiNama }}</span> menjadi organisasi paling aktif
                </span>
            </div>
        </div>
        <span class="text-secondary/40 group-hover:text-primary group-hover:translate-x-0.5 transition-transform duration-200 pl-xs">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 5l7 7-7 7"/></svg>
        </span>
    </a>

</div>