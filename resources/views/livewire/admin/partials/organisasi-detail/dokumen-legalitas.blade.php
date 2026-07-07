<div class="w-full p-md sm:p-lg bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-card flex flex-col gap-md lg:gap-lg select-none animate-fade-in">
    
    <div class="flex flex-row items-center justify-between border-b border-surface-container/60 pb-sm w-full gap-sm">
        <div class="flex items-center gap-xs sm:gap-sm text-primary min-w-0">
            <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <h4 class="text-body-lg sm:text-title-sm font-bold tracking-tight truncate">Dokumen Legalitas</h4>
        </div>
        <span class="text-[11px] font-extrabold text-secondary/60 font-sans shrink-0 text-right">
            {{ $jumlah_dokumen }} Berkas
        </span>
    </div>

    <div class="w-full flex flex-col gap-sm">
        
        <div class="w-full p-md bg-surface-container-lowest border border-outline-variant/30 rounded-2xl flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-md transition-all hover:border-outline-variant/60 shadow-2xs">
            <div class="flex items-center gap-md min-w-0 flex-1">
                <div class="w-11 h-11 rounded-xl bg-error/5 border border-error/10 flex items-center justify-center shrink-0 text-error">
                    <svg width="20" height="20" class="w-5 h-5 stroke-[2.2]" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 10.5H8V8.5H9C9.28333 8.5 9.52083 8.40417 9.7125 8.2125C9.90417 8.02083 10 7.78333 10 7.5V6.5C10 6.21667 9.90417 5.97917 9.7125 5.7875C9.52083 5.59583 9.28333 5.5 9 5.5H7V10.5ZM8 7.5V6.5H9V7.5H8ZM11 10.5H13C13.2833 10.5 13.5208 10.4042 13.7125 10.2125C13.9042 10.0208 14 9.78333 14 9.5V6.5C14 6.21667 13.9042 5.97917 13.7125 5.7875C13.5208 5.59583 13.2833 5.5 13 5.5H11V10.5ZM12 9.5V6.5H13V9.5H12ZM15 10.5H16V8.5H17V7.5H16V6.5H17V5.5H15V10.5ZM6 16C5.45 16 4.97917 15.8042 4.5875 15.4125C4.19583 15.0208 4 14.55 4 14V2C4 1.45 4.19583 0.979167 4.5875 0.5875C4.97917 0.195833 5.45 0 6 0H18C18.55 0 19.0208 0.195833 19.4125 0.5875C19.8042 0.979167 20 1.45 20 2V14C20 14.55 19.8042 15.0208 19.4125 15.4125C19.0208 15.8042 18.55 16 18 16H6ZM6 14H18V2H6V14ZM2 20C1.45 20 0.979167 19.8042 0.5875 19.4125C0.195833 19.0208 0 18.55 0 18V4H2V18H16V20H2ZM6 2V14V2Z" fill="currentColor"/>
                    </svg>
                </div>
                <div class="flex flex-col min-w-0 flex-1">
                    <span class="text-body-md font-bold text-primary tracking-tight break-words whitespace-normal leading-tight" title="AD/ART {{ $org->nama_organisasi }}">
                        AD/ART {{ $org->nama_organisasi }}
                    </span>
                    <span class="text-caption font-semibold text-secondary/50 font-sans mt-1 uppercase">
                        PDF • {{ $ad_art_size }}
                    </span>
                </div>
            </div>

            @if($org->ad_art)
                <a href="{{ asset('storage/' . $org->ad_art) }}" 
                    download="AD_ART_{{ str_replace(' ', '_', $org->nama_organisasi) }}.pdf"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-xs px-md py-2.5 sm:py-2 bg-primary text-on-primary font-bold text-body-sm rounded-xl border border-primary hover:bg-primary-container hover:border-primary-container transition-all active:scale-95 shadow-sm cursor-pointer select-none shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    <span>Unduh</span>
                </a>
            @else
                <button disabled class="w-full sm:w-auto px-md py-2.5 sm:py-2 bg-surface-container text-secondary/40 font-bold text-body-sm rounded-xl border border-outline-variant/20 cursor-not-allowed shrink-0">
                    Kosong
                </button>
            @endif
        </div>

        <div class="w-full p-md bg-surface-container-lowest border border-outline-variant/30 rounded-2xl flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-md transition-all hover:border-outline-variant/60 shadow-2xs">
            <div class="flex items-center gap-md min-w-0 flex-1">
                <div class="w-11 h-11 rounded-xl bg-error/5 border border-error/10 flex items-center justify-center shrink-0 text-error">
                    <svg width="20" height="20" class="w-5 h-5 stroke-[2.2]" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 10.5H8V8.5H9C9.28333 8.5 9.52083 8.40417 9.7125 8.2125C9.90417 8.02083 10 7.78333 10 7.5V6.5C10 6.21667 9.90417 5.97917 9.7125 5.7875C9.52083 5.59583 9.28333 5.5 9 5.5H7V10.5ZM8 7.5V6.5H9V7.5H8ZM11 10.5H13C13.2833 10.5 13.5208 10.4042 13.7125 10.2125C13.9042 10.0208 14 9.78333 14 9.5V6.5C14 6.21667 13.9042 5.97917 13.7125 5.7875C13.5208 5.59583 13.2833 5.5 13 5.5H11V10.5ZM12 9.5V6.5H13V9.5H12ZM15 10.5H16V8.5H17V7.5H16V6.5H17V5.5H15V10.5ZM6 16C5.45 16 4.97917 15.8042 4.5875 15.4125C4.19583 15.0208 4 14.55 4 14V2C4 1.45 4.19583 0.979167 4.5875 0.5875C4.97917 0.195833 5.45 0 6 0H18C18.55 0 19.0208 0.195833 19.4125 0.5875C19.8042 0.979167 20 1.45 20 2V14C20 14.55 19.8042 15.0208 19.4125 15.4125C19.0208 15.8042 18.55 16 18 16H6ZM6 14H18V2H6V14ZM2 20C1.45 20 0.979167 19.8042 0.5875 19.4125C0.195833 19.0208 0 18.55 0 18V4H2V18H16V20H2ZM6 2V14V2Z" fill="currentColor"/>
                    </svg>
                </div>
                <div class="flex items-center min-w-0 flex-1">
                    <div class="flex flex-col min-w-0 flex-1">
                        <span class="text-body-md font-bold text-primary tracking-tight break-words whitespace-normal leading-tight">
                            SK Kepengurusan Organisasi
                        </span>
                        <span class="text-caption font-semibold text-secondary/50 font-sans mt-1 uppercase">
                            PDF • {{ $sk_size }}
                        </span>
                    </div>
                </div>
            </div>

            @if($org->sk)
                <a href="{{ asset('storage/' . $org->sk) }}" 
                    download="SK_Kepengurusan_{{ str_replace(' ', '_', $org->nama_organisasi) }}.pdf"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-xs px-md py-2.5 sm:py-2 bg-primary text-on-primary font-bold text-body-sm rounded-xl border border-primary hover:bg-primary-container hover:border-primary-container transition-all active:scale-95 shadow-sm cursor-pointer select-none shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    <span>Unduh</span>
                </a>
            @else
                <button disabled class="w-full sm:w-auto px-md py-2.5 sm:py-2 bg-surface-container text-secondary/40 font-bold text-body-sm rounded-xl border border-outline-variant/20 cursor-not-allowed shrink-0">
                    Kosong
                </button>
            @endif
        </div>

    </div>
</div>