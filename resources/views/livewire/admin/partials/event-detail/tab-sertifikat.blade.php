<div class="w-full flex flex-col gap-md sm:gap-lg animate-fade-in select-none">
    
    <div class="w-full bg-surface-container-lowest p-md sm:p-lg rounded-3xl border border-outline-variant/30 shadow-card flex flex-col gap-md">
        
        <div class="flex items-center gap-sm text-primary border-b border-surface-container/60 pb-sm">
            <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            <h4 class="text-title-sm font-bold tracking-tight">Desain Berkas Sertifikat</h4>
        </div>

        @if($event->templateSertifikat && $event->templateSertifikat->file_template)
            <div x-data="{ imgFailed: false, showLightbox: false }" class="w-full flex flex-col justify-center">
                <div class="w-full max-h-[480px] aspect-[16/9] bg-surface-container-low rounded-2xl overflow-hidden relative border border-outline-variant/10 flex flex-col items-center justify-center group shadow-2xs">
                    <div class="relative w-full h-full flex items-center justify-center cursor-zoom-in" @click="if(!imgFailed) showLightbox = true">
                        <img x-show="!imgFailed"
                                src="{{ asset('storage/sertifikat-templates/' . $event->templateSertifikat->file_template) }}" 
                                alt="Template Sertifikat {{ $event->nama_event }}" 
                                x-on:error="imgFailed = true"
                                class="w-full h-full object-contain transition-all duration-500 group-hover:scale-[1.01] group-hover:brightness-95">

                        {{-- Test Preview --}}
                        @if(!$event->templateSertifikat->posisi_x == null && !$event->templateSertifikat->posisi_y == null)
                            <div class="absolute pointer-events-none select-none font-bold text-center uppercase tracking-wide opacity-90 transition-transform duration-300 group-hover:scale-[1.01] whitespace-nowrap z-10"
                                style="
                                /* Langsung tembak menggunakan satuan % karena data database sudah berupa persen */
                                left: {{ $event->templateSertifikat->posisi_x }}%; 
                                top: {{ $event->templateSertifikat->posisi_y }}%;
                                
                                color: {{ $event->templateSertifikat->warna_font ?? '#000000' }};
                                font-family: {{ $event->templateSertifikat->jenis_font ?? 'sans-serif' }};
                                font-size: clamp(8px, 1.8vw, 28px);
                                transform: translate(-50%, -50%);
                                ">
                                [Nama Peserta]
                            </div>
                        @endif

                        <div x-show="!imgFailed" 
                                class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center text-white gap-xs pointer-events-none">
                            <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30 shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/>
                                </svg>
                            </div>
                            <span class="text-caption font-bold bg-black/40 px-sm py-0.5 rounded-full drop-shadow-sm">Perbesar Desain Sertifikat</span>
                        </div>
                    </div>

                    <div x-show="imgFailed" class="absolute inset-0 w-full h-full flex flex-col items-center justify-center text-secondary/40 p-md bg-surface-container" x-cloak>
                        <svg class="w-10 h-10 stroke-[1.8] mb-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                        </svg>
                        <span class="text-body-sm font-extrabold text-secondary/70">Gagal Memuat File Cetak</span>
                    </div>
                </div>

                <template x-teleport="body">
                    <div x-show="showLightbox" 
                            class="fixed inset-0 z-[9999] flex items-center justify-center p-md sm:p-xl bg-black/80 backdrop-blur-md"
                            @click="showLightbox = false"
                            @keydown.window.escape="showLightbox = false"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-end="opacity-0"
                            x-cloak>
                        
                        <button class="absolute top-md right-md w-11 h-11 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-full flex items-center justify-center cursor-pointer transition-colors z-10">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                
                        <div class="relative w-full max-w-4xl max-h-[85vh] bg-surface-container rounded-3xl overflow-hidden shadow-2xl border border-white/10 flex items-center justify-center" @click.stop>
                            
                            <img src="{{ asset('storage/sertifikat-templates/' . $event->templateSertifikat->file_template) }}" 
                                    alt="Sertifikat HD" 
                                    class="w-full h-auto max-h-[85vh] object-contain block select-none">
            
                            {{-- TEXT PREVIEW --}}
                            @if(!$event->templateSertifikat->posisi_x == null && !$event->templateSertifikat->posisi_y == null)
                                <div class="absolute pointer-events-none select-none font-bold text-center uppercase tracking-wide opacity-95 whitespace-nowrap z-10"
                                        style="
                                        left: {{ $event->templateSertifikat->posisi_x }}%; 
                                        top: {{ $event->templateSertifikat->posisi_y }}%;
                                        color: {{ $event->templateSertifikat->warna_font ?? '#000000' }};
                                        font-family: {{ $event->templateSertifikat->jenis_font ?? 'sans-serif' }};
                                        /* Menggunakan clamp ukuran sedikit lebih besar karena resolusi modal lebih luas */
                                        font-size: clamp(12px, 2.5vw, 42px);
                                        transform: translate(-50%, -50%);
                                        ">
                                    [Nama Peserta]
                                </div>
                            @endif
                        </div>
                    </div>
                </template>
            </div>
        @else
            <div class="py-xl w-full">
                <x-admin.empty-state 
                    title="Template Sertifikat Kosong" 
                    description="Pihak panitia ormawa pelaksana belum mengunggah berkas template desain sertifikat utama untuk ajang ini." 
                />
            </div>
        @endif
    </div>

    @if($event->templateSertifikat)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-md w-full items-stretch">

            {{-- Card Kiri: Tipografi --}}
            <div class="bg-surface-container-lowest p-md rounded-2xl border border-outline-variant/30 shadow-card flex flex-col gap-md">

                <div class="flex items-center gap-sm border-b border-outline-variant/20 pb-sm">
                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h12"/>
                    </svg>
                    <h4 class="text-label-md font-semibold text-on-surface tracking-tight">Konfigurasi Tipografi</h4>
                </div>

                <div class="flex flex-col gap-sm">

                    {{-- Jenis Font --}}
                    <div class="p-sm bg-surface-container-low rounded-xl flex items-center gap-sm border border-outline-variant/10">
                        <div class="w-8 h-8 rounded-lg bg-primary/8 text-primary flex items-center justify-center shrink-0 font-serif font-bold text-body-md select-none">
                            <svg width="18" height="18" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_40000874_166)">
                                <path d="M12 12.9997C11.5467 12.9997 11.1434 12.921 10.79 12.7637C10.4477 12.6135 10.1558 12.3681 9.94904 12.0567C9.74571 11.742 9.64404 11.352 9.64404 10.8867C9.64404 10.4927 9.71638 10.1617 9.86104 9.89367C10.0017 9.63261 10.2048 9.41036 10.452 9.24667C10.7014 9.08334 10.9847 8.96034 11.302 8.87767C11.622 8.79434 11.9587 8.736 12.312 8.70267C12.724 8.66 13.0567 8.62 13.31 8.58267C13.5627 8.54267 13.746 8.48434 13.86 8.40767C14.031 8.29267 14.031 8.21667 14.031 8.06567V8.03767C14.031 7.74567 13.9387 7.51934 13.754 7.35867C13.572 7.19867 13.3134 7.11867 12.978 7.11867C12.624 7.11867 12.3424 7.19734 12.133 7.35467C12.0465 7.4177 11.9688 7.49203 11.902 7.57567C11.755 7.76267 11.547 7.92367 11.31 7.90367L10.465 7.83567C10.164 7.81067 9.94904 7.52367 10.073 7.24867C10.1678 7.03968 10.2921 6.84537 10.442 6.67167C10.7127 6.355 11.0627 6.11167 11.492 5.94167C11.9227 5.769 12.4227 5.68234 12.992 5.68167C13.3731 5.6803 13.7528 5.72734 14.122 5.82167C14.4854 5.91434 14.807 6.05734 15.087 6.25067C15.3697 6.444 15.593 6.69334 15.757 6.99867C15.9204 7.29934 16.002 7.66267 16.002 8.08867V12.3687C16.002 12.5013 15.9494 12.6285 15.8556 12.7222C15.7618 12.816 15.6347 12.8687 15.502 12.8687H14.636C14.5034 12.8687 14.3763 12.816 14.2825 12.7222C14.1887 12.6285 14.136 12.5013 14.136 12.3687V11.8847H14.081C13.9667 12.1068 13.8116 12.3056 13.624 12.4707C13.4334 12.6367 13.204 12.7677 12.936 12.8637C12.6363 12.9597 12.3228 13.0063 12.008 13.0017L12 12.9997ZM12.563 11.6397C12.8524 11.6397 13.108 11.5827 13.33 11.4687C13.552 11.3513 13.726 11.1943 13.852 10.9977C13.978 10.801 14.041 10.5777 14.041 10.3277V9.57467C13.9797 9.61467 13.895 9.65167 13.787 9.68567C13.6824 9.71634 13.5637 9.74567 13.431 9.77367C13.299 9.79848 13.1666 9.82182 13.034 9.84367L12.674 9.89367C12.4662 9.92101 12.2628 9.97547 12.069 10.0557C11.9117 10.1199 11.7734 10.2231 11.667 10.3557C11.5683 10.4889 11.5178 10.6519 11.524 10.8177C11.524 11.0857 11.621 11.2903 11.815 11.4317C12.0117 11.5703 12.261 11.6397 12.563 11.6397Z" fill="currentColor"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.47009 1.33969C5.43555 1.24081 5.37089 1.15525 5.2852 1.09502C5.19951 1.03479 5.0971 1.00294 4.99237 1.00393C4.88763 1.00492 4.78584 1.03872 4.70131 1.10056C4.61678 1.1624 4.55375 1.24918 4.52109 1.34869L1.24109 11.6487C1.20888 11.7497 1.14541 11.8378 1.05985 11.9003C0.974295 11.9629 0.871076 11.9966 0.765094 11.9967H0.496094C0.363486 11.9967 0.236309 12.0494 0.14254 12.1431C0.0487722 12.2369 -0.00390625 12.3641 -0.00390625 12.4967C-0.00390625 12.6293 0.0487722 12.7565 0.14254 12.8502C0.236309 12.944 0.363486 12.9967 0.496094 12.9967H2.99609C3.1287 12.9967 3.25588 12.944 3.34965 12.8502C3.44342 12.7565 3.49609 12.6293 3.49609 12.4967C3.49609 12.3641 3.44342 12.2369 3.34965 12.1431C3.25588 12.0494 3.1287 11.9967 2.99609 11.9967H2.52209C2.48277 11.9967 2.444 11.9874 2.40895 11.9695C2.37389 11.9517 2.34353 11.9259 2.32035 11.8941C2.29716 11.8623 2.28179 11.8256 2.27549 11.7867C2.26919 11.7479 2.27214 11.7082 2.28409 11.6707L2.70609 10.3407C2.73831 10.2397 2.80178 10.1516 2.88734 10.0891C2.97289 10.0265 3.07611 9.99277 3.18209 9.99269H6.04209C6.14783 9.99263 6.25086 10.0261 6.33639 10.0883C6.42191 10.1504 6.48553 10.2381 6.51809 10.3387L6.94809 11.6687C6.96024 11.7062 6.96334 11.7461 6.95713 11.785C6.95092 11.824 6.93559 11.8609 6.91238 11.8928C6.88917 11.9247 6.85874 11.9506 6.82358 11.9685C6.78843 11.9864 6.74954 11.9957 6.71009 11.9957H6.49109C6.35849 11.9957 6.23131 12.0484 6.13754 12.1421C6.04377 12.2359 5.99109 12.3631 5.99109 12.4957C5.99109 12.6283 6.04377 12.7555 6.13754 12.8492C6.23131 12.943 6.35849 12.9957 6.49109 12.9957H9.49109C9.6237 12.9957 9.75088 12.943 9.84465 12.8492C9.93841 12.7555 9.99109 12.6283 9.99109 12.4957C9.99109 12.3631 9.93841 12.2359 9.84465 12.1421C9.75088 12.0484 9.6237 11.9957 9.49109 11.9957H9.45709C9.35274 11.9956 9.25102 11.9629 9.16619 11.9021C9.08137 11.8413 9.0177 11.7555 8.98409 11.6567L5.46409 1.35669L5.47009 1.33969ZM5.84609 8.99969C5.87367 8.99962 5.90083 8.99304 5.92537 8.98047C5.94991 8.96791 5.97114 8.94973 5.98732 8.9274C6.00351 8.90508 6.01418 8.87925 6.01849 8.85202C6.02279 8.82479 6.0206 8.79692 6.01209 8.77069L4.76209 4.92069C4.75071 4.8855 4.72846 4.85482 4.69854 4.83306C4.66863 4.81129 4.63259 4.79957 4.59559 4.79957C4.5586 4.79957 4.52256 4.81129 4.49265 4.83306C4.46273 4.85482 4.44048 4.8855 4.42909 4.92069L3.20909 8.77069C3.20056 8.797 3.19838 8.82496 3.20274 8.85227C3.2071 8.87958 3.21786 8.90546 3.23416 8.92781C3.25045 8.95015 3.27181 8.96832 3.29648 8.98081C3.32116 8.99331 3.34844 8.99978 3.37609 8.99969H5.84609Z" fill="currentColor"/>
                                </g>
                                <defs>
                                <clipPath id="clip0_40000874_166">
                                <rect width="16" height="16" fill="white"/>
                                </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="text-[10px] text-secondary/50 font-bold tracking-wider uppercase leading-none">Jenis Font</span>
                            <span class="text-body-sm text-on-surface font-bold mt-0.5 truncate">
                                {{ $event->templateSertifikat->jenis_font }}
                            </span>
                        </div>
                    </div>

                    {{-- Ukuran & Warna --}}
                    <div class="grid grid-cols-2 gap-sm">

                        <div class="p-sm bg-surface-container-low rounded-xl flex items-center gap-sm border border-outline-variant/10">
                            <div class="w-8 h-8 rounded-lg bg-surface-container text-primary flex items-center justify-center shrink-0 font-bold text-[10px] select-none">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18 21V11M20 18.5L18 21L16 18.5M20 13L18 11L16 13M9 5V17M9 17H7M9 17H11M15 7V5H3V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] text-secondary/50 font-bold tracking-wider uppercase leading-none">Ukuran</span>
                                <span class="text-body-sm text-on-surface font-bold mt-0.5">
                                    {{ $event->templateSertifikat->ukuran_font }} px
                                </span>
                            </div>
                        </div>

                        <div class="p-sm bg-surface-container-low rounded-xl flex items-center gap-sm border border-outline-variant/10">
                            <div class="w-8 h-8 rounded-lg border border-outline-variant/30 shrink-0 shadow-sm"
                                style="background-color: {{ $event->templateSertifikat->warna_font }};"></div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-[10px] text-secondary/50 font-bold tracking-wider uppercase leading-none">Warna</span>
                                <span class="text-body-sm text-on-surface font-bold mt-0.5 uppercase truncate">
                                    {{ $event->templateSertifikat->warna_font }}
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- Card Kanan: Koordinat --}}
            <div class="bg-surface-container-lowest p-md rounded-2xl border border-outline-variant/30 shadow-card flex flex-col gap-md">

                <div class="flex items-center gap-sm border-b border-outline-variant/20 pb-sm">
                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v20M2 12h20"/>
                        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <h4 class="text-label-md font-semibold text-on-surface tracking-tight">Posisi Koordinat</h4>
                </div>

                <div class="flex items-center gap-md flex-1">

                    {{-- Crosshair ilustrasi --}}
                    <div class="shrink-0 w-14 h-14">
                        <svg viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                            <circle cx="28" cy="28" r="26" stroke="currentColor" stroke-width="0.75" stroke-dasharray="3 3" class="text-outline-variant/40"/>
                            <line x1="28" y1="4" x2="28" y2="52" stroke="currentColor" stroke-width="0.75" class="text-outline-variant/40"/>
                            <line x1="4" y1="28" x2="52" y2="28" stroke="currentColor" stroke-width="0.75" class="text-outline-variant/40"/>
                            <circle cx="28" cy="28" r="4" class="fill-primary" opacity="0.85"/>
                            <circle cx="28" cy="28" r="8" stroke="currentColor" stroke-width="1" class="text-primary" opacity="0.25"/>
                        </svg>
                    </div>

                    {{-- Nilai X & Y --}}
                    <div class="grid grid-cols-2 gap-sm flex-1">

                        <div class="p-sm bg-surface-container-low rounded-lg flex flex-col border border-outline-variant/10">
                            <span class="text-[10px] text-secondary/50 font-bold tracking-wider uppercase flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 12 12">
                                    <path d="M1 6h10M8 3l3 3-3 3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Sumbu X
                            </span>
                            <span class="text-title-md font-extrabold text-primary mt-1 leading-none">
                                {{ $event->templateSertifikat->posisi_x }}<span class="text-caption font-normal text-secondary/40 ml-0.5">%</span>
                            </span>
                        </div>

                        <div class="p-sm bg-surface-container-low rounded-lg flex flex-col border border-outline-variant/10">
                            <span class="text-[10px] text-secondary/50 font-bold tracking-wider uppercase flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 12 12">
                                    <path d="M6 1v10M3 8l3 3 3-3" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Sumbu Y
                            </span>
                            <span class="text-title-md font-extrabold text-primary mt-1 leading-none">
                                {{ $event->templateSertifikat->posisi_y }}<span class="text-caption font-normal text-secondary/40 ml-0.5">%</span>
                            </span>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    @endif

</div>