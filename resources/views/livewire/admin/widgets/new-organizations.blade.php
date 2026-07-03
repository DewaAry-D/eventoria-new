<div class="bg-surface-container-lowest p-md sm:p-lg rounded-[28px] border border-outline-variant/30 shadow-sm flex flex-col justify-between w-full h-full min-h-[280px]">
    
    <div>
        <div class="mb-md select-none">
            <h4 class="text-title-sm sm:text-title-md font-bold sm:font-bold text-primary tracking-tight">
                Organisasi Baru
            </h4>
            <p class="text-caption text-on-surface-variant font-medium mt-0.5 leading-normal">
                Daftar ormawa terverifikasi yang baru saja bergabung di sistem
            </p>
        </div>

        <div class="space-y-sm">
            @forelse($organizations as $org)
                <div class="flex items-center justify-between p-sm bg-surface-container/[0.15] rounded-2xl border border-outline-variant/30 select-none">
                    <div class="flex items-center gap-md min-w-0 w-full">
                        <div class="w-10 h-10 rounded-full bg-primary/[0.06] border border-primary/20 text-primary flex items-center justify-center font-bold text-body-md shrink-0">
                            {{ strtoupper(substr($org->nama_organisasi, 0, 2)) }}
                        </div>
                        
                        <div class="flex flex-col min-w-0 pr-xs">
                            <span class="text-body-sm font-bold text-primary truncate tracking-tight">
                                {{ $org->nama_organisasi }}
                            </span>
                            <span class="text-[11px] text-on-surface-variant font-medium mt-0.5 truncate">
                                {{ $org->fakultas->nama_fakultas ?? 'Tingkat Universitas' }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <x-admin.empty-state
                    title="Belum Ada Organisasi"
                    description="Belum ada organisasi baru yang disetujui untuk tingkat ini."
                />
            @endforelse
        </div>
    </div>

    <div class="mt-md pt-xs border-t border-outline-variant/20">
        <a href="{{ route('admin.moderasi.organisasi') }}" wire:navigate 
            class="w-full py-2.5 flex items-center justify-center gap-xs text-body-sm font-bold text-primary bg-primary/[0.02] border border-primary/10 hover:bg-primary/[0.08] hover:border-primary/20 active:scale-[0.98] rounded-xl transition-all select-none shadow-sm group">
            
            <span>Lihat Semua Organisasi</span>
            
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" 
                class="mt-[1px] transform group-hover:translate-x-0.5 transition-transform duration-200">
                <path d="M9 5l7 7-7 7"/>
            </svg>
    
        </a>
    </div>

</div>