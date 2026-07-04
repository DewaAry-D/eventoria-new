<div class="w-full p-md sm:p-lg xl:p-xl bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-sm flex flex-col sm:flex-row md:flex-col xl:flex-row gap-lg xl:gap-xl items-start">
    
    <!-- Flyer -->
    <div x-data="{ imgFailed: false, showLightbox: false }"
        class="w-full max-w-[280px] sm:max-w-none sm:w-[200px] md:w-full md:max-w-[300px] xl:max-w-none xl:w-[220px] mx-auto sm:mx-0 md:mx-auto xl:mx-0 shrink-0">
    
        <div class="w-full aspect-[3/4] bg-surface-container shadow-md rounded-2xl overflow-hidden relative border border-outline-variant/20 flex flex-col items-center justify-center group cursor-zoom-in select-none"
                @click="if(!imgFailed) showLightbox = true">
            
            <img x-show="!imgFailed"
                src="{{ asset('storage/' . $event->flyer_url) }}" 
                alt="Flyer {{ $event->nama_event }}" 
                x-on:error="imgFailed = true"
                class="w-full h-full object-cover transition-all duration-500 group-hover:scale-105 group-hover:brightness-75">
    
            <!-- Ketika di hover -->
            <div x-show="!imgFailed" 
                class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center text-white gap-xs p-xs">
                <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30 transform scale-90 group-hover:scale-100 transition-transform duration-300 shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/>
                    </svg>
                </div>
                <span class="text-[10px] font-bold tracking-wide drop-shadow-sm bg-black/40 px-sm py-0.5 rounded-full">Lihat Penuh</span>
            </div>
    
            <!-- Placeholder gagal muat gambar -->
            <div x-show="imgFailed" 
                class="absolute inset-0 w-full h-full flex flex-col items-center justify-center text-secondary/40 gap-xs p-md bg-surface-container"
                x-cloak>
                <div class="w-10 h-10 rounded-full bg-secondary/5 flex items-center justify-center mb-xs border border-outline-variant/20">
                    <svg class="w-5 h-5 text-secondary/60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                    </svg>
                </div>
                <span class="text-[11px] font-extrabold tracking-tight text-secondary/70">Berkas Poster Digital</span>
                <span class="text-[10px] text-secondary/40 leading-none -mt-1 font-medium">Gagal Dimuat</span>
            </div>
        </div>
    
        <!-- Lightbox Modal Overlay -->
        <template x-teleport="body">
            <div x-show="showLightbox" 
                    class="fixed inset-0 z-[9999] flex items-center justify-center p-md bg-black/80 backdrop-blur-md"
                    @click="showLightbox = false"
                    @keydown.window.escape="showLightbox = false"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    x-cloak>
                
                <button class="absolute top-md right-md w-11 h-11 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-full flex items-center justify-center transition-colors cursor-pointer shadow-lg"
                        @click="showLightbox = false">
                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
    
                <div class="relative max-w-full max-h-[85vh] md:max-h-[90vh] aspect-[3/4] rounded-2xl overflow-hidden bg-surface-container shadow-2xl border border-white/10" @click.stop>
                        <img src="{{ asset('storage/' . $event->flyer_url) }}" alt="Flyer {{ $event->nama_event }} Full" class="w-full h-full object-contain select-none">
                </div>
            </div>
        </template>
    </div>

    <!-- Metadata & Detail Informasi -->
    <div class="flex-1 min-w-0 w-full flex flex-col justify-between self-stretch pt-md sm:pt-0 md:pt-md xl:pt-0">

        <div>
            <div class="flex flex-wrap items-center gap-xs sm:gap-sm text-caption font-semibold mb-sm select-none">
                <span class="px-md py-1 bg-primary text-white rounded-xl font-bold uppercase tracking-wider text-[10px]">
                    {{ $event->kategori?->nama_kategori ?? 'Umum' }}
                </span>
                <span class="text-secondary/60 tracking-tight text-[11px] sm:text-caption">
                    Pengajuan ID: #EVT-{{ date('Y') }}-{{ str_pad($event->id, 3, '0', STR_PAD_LEFT) }}
                </span>
            </div>
        
            <h2 class="text-title-md sm:text-title-xl font-bold sm:font-bold text-primary tracking-tight leading-tight mb-md lg:mb-lg max-w-[95%]">
                {{ $event->nama_event }}
            </h2>
        
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-md gap-x-lg max-w-[560px] mb-lg">
        
                <div class="flex items-center gap-xs sm:gap-sm min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0 border border-primary/5 select-none">
                        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 2H15V1C15 0.734784 14.8946 0.48043 14.7071 0.292893C14.5196 0.105357 14.2652 0 14 0C13.7348 0 13.4804 0.105357 13.2929 0.292893C13.1054 0.48043 13 0.734784 13 1V2H7V1C7 0.734784 6.89464 0.48043 6.70711 0.292893C6.51957 0.105357 6.26522 0 6 0C5.73478 0 5.48043 0.105357 5.29289 0.292893C5.10536 0.48043 5 0.734784 5 1V2H3C2.20435 2 1.44129 2.31607 0.87868 2.87868C0.316071 3.44129 0 4.20435 0 5V17C0 17.7956 0.316071 18.5587 0.87868 19.1213C1.44129 19.6839 2.20435 20 3 20H17C17.7956 20 18.5587 19.6839 19.1213 19.1213C19.6839 18.5587 20 17.7956 20 17V5C20 4.20435 19.6839 3.44129 19.1213 2.87868C18.5587 2.31607 17.7956 2 17 2ZM18 17C18 17.2652 17.8946 17.5196 17.7071 17.7071C17.5196 17.8946 17.2652 18 17 18H3C2.73478 18 2.48043 17.8946 2.29289 17.7071C2.10536 17.5196 2 17.2652 2 17V10H18V17ZM18 8H2V5C2 4.73478 2.10536 4.48043 2.29289 4.29289C2.48043 4.10536 2.73478 4 3 4H5V5C5 5.26522 5.10536 5.51957 5.29289 5.70711C5.48043 5.89464 5.73478 6 6 6C6.26522 6 6.51957 5.89464 6.70711 5.70711C6.89464 5.51957 7 5.26522 7 5V4H13V5C13 5.26522 13.1054 5.51957 13.2929 5.70711C13.4804 5.89464 13.7348 6 14 6C14.2652 6 14.5196 5.89464 14.7071 5.70711C14.8946 5.51957 15 5.26522 15 5V4H17C17.2652 4 17.5196 4.10536 17.7071 4.29289C17.8946 4.48043 18 4.73478 18 5V8Z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="flex flex-col min-w-0 flex-1">
                        <span class="text-[10px] sm:text-[11px] text-on-surface-variant/50 tracking-wider font-bold leading-none mb-1 select-none">Tanggal Pendaftaran</span>
                        <p class="text-body-sm sm:text-body-md font-bold sm:font-bold text-on-surface leading-tight truncate" title="{{ $tanggalPelaksanaan }}">
                            {{ $tanggalPelaksanaan }}
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center gap-xs sm:gap-sm min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0 border border-primary/5 select-none">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 16 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 10C8.55 10 9.02083 9.80417 9.4125 9.4125C9.80417 9.02083 10 8.55 10 8C10 7.45 9.80417 6.97917 9.4125 6.5875C9.02083 6.19583 8.55 6 8 6C7.45 6 6.97917 6.19583 6.5875 6.5875C6.19583 6.97917 6 7.45 6 8C6 8.55 6.19583 9.02083 6.5875 9.4125C6.97917 9.80417 7.45 10 8 10ZM8 17.35C10.0333 15.4833 11.5417 13.7875 12.525 12.2625C13.5083 10.7375 14 9.38333 14 8.2C14 6.38333 13.4208 4.89583 12.2625 3.7375C11.1042 2.57917 9.68333 2 8 2C6.31667 2 4.89583 2.57917 3.7375 3.7375C2.57917 4.89583 2 6.38333 2 8.2C2 9.38333 2.49167 10.7375 3.475 12.2625C4.45833 13.7875 5.96667 15.4833 8 17.35ZM8 20C5.31667 17.7167 3.3125 15.5958 1.9875 13.6375C0.6625 11.6792 0 9.86667 0 8.2C0 5.7 0.804167 3.70833 2.4125 2.225C4.02083 0.741667 5.88333 0 8 0C10.1167 0 11.9792 0.741667 13.5875 2.225C15.1958 3.70833 16 5.7 16 8.2C16 9.86667 15.3375 11.6792 14.0125 13.6375C12.6875 15.5958 10.6833 17.7167 8 20Z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="flex flex-col min-w-0 flex-1">
                        <span class="text-[10px] sm:text-[11px] text-on-surface-variant/50 tracking-wider font-bold leading-none mb-1 select-none">Lokasi</span>
                        <p class="text-body-sm sm:text-body-md font-bold sm:font-bold text-on-surface leading-tight truncate" title="{{ $event->nama_lokasi }}">
                            {{ $event->nama_lokasi }}
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center gap-xs sm:gap-sm min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0 border border-primary/5 select-none">
                        <svg width="24" height="12" viewBox="0 0 24 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-auto">
                            <path d="M0 12V10.425C0 9.70833 0.366667 9.125 1.1 8.675C1.83333 8.225 2.8 8 4 8C4.21667 8 4.425 8.00417 4.625 8.0125C4.825 8.02083 5.01667 8.04167 5.2 8.075C4.96667 8.425 4.79167 8.79167 4.675 9.175C4.55833 9.55833 4.5 9.95833 4.5 10.375V12H0ZM6 12V10.375C6 9.84167 6.14583 9.35417 6.4375 8.9125C6.72917 8.47083 7.14167 8.08333 7.675 7.75C8.20833 7.41667 8.84583 7.16667 9.5875 7C10.3292 6.83333 11.1333 6.75 12 6.75C12.8833 6.75 13.6958 6.83333 14.4375 7C15.1792 7.16667 15.8167 7.41667 16.35 7.75C16.8833 8.08333 17.2917 8.47083 17.575 8.9125C17.8583 9.35417 18 9.84167 18 10.375V12H6ZM19.5 12V10.375C19.5 9.94167 19.4458 9.53333 19.3375 9.15C19.2292 8.76667 19.0667 8.40833 18.85 8.075C19.0333 8.04167 19.2208 8.02083 19.4125 8.0125C19.6042 8.00417 19.8 8 20 8C21.2 8 22.1667 8.22083 22.9 8.6625C23.6333 9.10417 24 9.69167 24 10.425V12H19.5ZM8.125 10H15.9C15.7333 9.66667 15.2708 9.375 14.5125 9.125C13.7542 8.875 12.9167 8.75 12 8.75C11.0833 8.75 10.2458 8.875 9.4875 9.125C8.72917 9.375 8.275 9.66667 8.125 10ZM4 7C3.45 7 2.97917 6.80417 2.5875 6.4125C2.19583 6.02083 2 5.55 2 5C2 4.43333 2.19583 3.95833 2.5875 3.575C2.97917 3.19167 3.45 3 4 3C4.56667 3 5.04167 3.19167 5.425 3.575C5.80833 3.95833 6 4.43333 6 5C6 5.55 5.80833 6.02083 5.425 6.4125C5.04167 6.80417 4.56667 7 4 7ZM20 7C19.45 7 18.9792 6.80417 18.5875 6.4125C18.1958 6.02083 18 5.55 18 5C18 4.43333 18.1958 3.95833 18.5875 3.575C18.9792 3.19167 19.45 3 20 3C20.5667 3 21.0417 3.19167 21.425 3.575C21.8083 3.95833 22 4.43333 22 5C22 5.55 21.8083 6.02083 21.425 6.4125C21.0417 6.80417 20.5667 7 20 7ZM12 6C11.1667 6 10.4583 5.70833 9.875 5.125C9.29167 4.54167 9 3.83333 9 3C9 2.15 9.29167 1.4375 9.875 0.8625C10.4583 0.2875 11.1667 0 12 0C12.85 0 13.5625 0.2875 14.1375 0.8625C14.7125 1.4375 15 2.15 15 3C15 3.83333 14.7125 4.54167 14.1375 5.125C13.5625 5.70833 12.85 6 12 6ZM12 4C12.2833 4 12.5208 3.90417 12.7125 3.7125C12.9042 3.52083 13 3.28333 13 3C13 2.71667 12.9042 2.47917 12.7125 2.2875C12.5208 2.09583 12.2833 2 12 2C11.7167 2 11.4792 2.09583 11.2875 2.2875C11.0958 2.47917 11 2.71667 11 3C11 3.28333 11.0958 3.52083 11.2875 3.7125C11.4792 3.90417 11.7167 4 12 4Z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="flex flex-col min-w-0 flex-1">
                        <span class="text-[10px] sm:text-[11px] text-on-surface-variant/50 tracking-wider font-bold leading-none mb-1 select-none">Kuota</span>
                        <p class="text-body-sm sm:text-body-md font-bold sm:font-bold text-on-surface leading-tight truncate font-sans" title="{{ number_format($event->kuota, 0, ',', '.') }} Peserta">
                            {{ number_format($event->kuota, 0, ',', '.') }} Peserta
                        </p>
                    </div>
                </div>
                
                <div class="flex items-center gap-xs sm:gap-sm min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-primary/5 text-primary flex items-center justify-center shrink-0 border border-primary/5 select-none">
                        <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5">
                            <path d="M13 9C12.1667 9 11.4583 8.70833 10.875 8.125C10.2917 7.54167 10 6.83333 10 6C10 5.16667 10.2917 4.45833 10.875 3.875C11.4583 3.29167 12.1667 3 13 3C13.8333 3 14.5417 3.29167 15.125 3.875C15.7083 4.45833 16 5.16667 16 6C16 6.83333 15.7083 7.54167 15.125 8.125C14.5417 8.70833 13.8333 9 13 9ZM6 12C5.45 12 4.97917 11.8042 4.5875 11.4125C4.19583 11.0208 4 10.55 4 10V2C4 1.45 4.19583 0.979167 4.5875 0.5875C4.97917 0.195833 5.45 0 6 0H20C20.55 0 21.0208 0.195833 21.4125 0.5875C21.8042 0.979167 22 1.45 22 2V10C22 10.55 21.8042 11.0208 21.4125 11.4125C21.0208 11.8042 20.55 12 20 12H6ZM8 10H18C18 9.45 18.1958 8.97917 18.5875 8.5875C18.9792 8.19583 19.45 8 20 8V4C19.45 4 18.9792 3.80417 18.5875 3.4125C18.1958 3.02083 18 2.55 18 2H8C8 2.55 7.80417 3.02083 7.4125 3.4125C7.02083 3.80417 6.55 4 6 4V8C6.55 8 7.02083 8.19583 7.4125 8.5875C7.80417 8.97917 8 9.45 8 10ZM19 16H2C1.45 16 0.979167 15.8042 0.5875 15.4125C0.195833 15.0208 0 14.55 0 14V3H2V14H19V16ZM6 10V2V10Z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div class="flex flex-col min-w-0 flex-1">
                        <span class="text-[10px] sm:text-[11px] text-on-surface-variant/50 tracking-wider font-bold sm:font-bold leading-none mb-1 select-none">Biaya Event</span>
                        <p class="text-body-sm sm:text-body-md font-bold sm:font-bold text-on-surface leading-tight truncate font-sans" title="{{ $biayaTeks }}">
                            {{ $biayaTeks }}
                        </p>
                    </div>
                </div>
        
            </div>
        </div>

        <!-- Footer Card Layout -->
        <div class="border-t border-outline-variant/20 pt-md mt-sm flex flex-col sm:flex-row sm:items-center justify-between gap-md w-full">
            <a href="{{ route('admin.organisasi.detail', $event->organisasi->id) }}" wire:navigate
                    class="flex items-center gap-xs min-w-0 select-none">
                <div class="w-8 h-8 rounded-full bg-surface-container border border-outline-variant/30 flex items-center justify-center overflow-hidden shrink-0 shadow-sm">
                    @if($event->organisasi?->logo_url)
                        <img src="{{ asset('storage/' . $event->organisasi->logo_url) }}" 
                                alt="Logo {{ $event->organisasi->nama_organisasi }}" 
                                class="w-full h-full object-cover">
                    @else
                        <span class="text-primary text-[10px] font-extrabold tracking-tight">
                            {{ strtoupper(substr($event->organisasi->nama_organisasi ?? 'OM', 0, 2)) }}
                        </span>
                    @endif
                </div>

                <span class="text-body-sm font-bold text-secondary hover:text-primary transition-colors cursor-pointer truncate pl-1 max-w-[200px] sm:max-w-[240px]" title="{{ $event->organisasi->nama_organisasi ?? 'Organisasi Mahasiswa' }}">
                    {{ $event->organisasi->nama_organisasi ?? 'Organisasi Mahasiswa' }}
                </span>
            </a>

            @php
                $currentStatus = is_object($event->status) ? $event->status->value : $event->status;

                $statusColor = match($currentStatus) {
                    'published'        => 'text-success bg-success/10 border-success/10',
                    'completed'        => 'text-primary bg-primary/10 border-primary/10',
                    'revision'         => 'text-error bg-error/10 border-error/10',
                    'pending_approval' => 'text-warning bg-warning/10 border-warning/10',
                    default            => 'text-secondary bg-surface-container border-outline-variant/30'
                };

                // Penyesuaian label teks informasi
                $statusText = match($currentStatus) {
                    'published'        => 'Telah Disetujui',
                    'completed'        => 'Kegiatan Selesai',
                    'revision'         => 'Butuh Revisi',
                    'pending_approval' => 'Menunggu Review',
                    default            => ucfirst($currentStatus)
                };
            @endphp

            <div class="px-md py-1.5 rounded-xl border text-caption font-bold tracking-wide flex items-center gap-xs shadow-2xs {{ $statusColor }} select-none whitespace-nowrap shrink-0 self-start sm:self-auto transition-transform duration-300 hover:scale-105">
                
                @if($currentStatus === 'published')
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @elseif($currentStatus === 'completed')
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013-3h.375a2.625 2.625 0 000-5.25H20.25m-3.75 8.25v-3m0 3h3.75m-11.25-3c0-2.25 1.5-4.5 3.75-4.5h.75m-4.5 4.5H4.875a2.625 2.625 0 010-5.25H5.25m3.75 8.25v-3m0 3H5.25M12 3v9m0 0l3-3m-3 3L9 9"/>
                    </svg>
                @elseif($currentStatus === 'revision')
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                @elseif($currentStatus === 'pending_approval')
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @else
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
                    </svg>
                @endif

                <span>{{ $statusText }}</span>
            </div>
        </div>
    </div>

</div>

