<?php

use App\Enums\RegistrationStatus;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.mahasiswa')] class extends Component
{
    public EventRegistration $registration;
    public $sertifikatLainnya;
    public string $linkedinShareUrl = '';

    public function mount(EventRegistration $registration_id): void
    {
        $this->registration = $registration_id->load('event.templateSertifikat', 'mahasiswa');

        $mahasiswaId = Auth::user()->mahasiswa->id ?? null;
        if ($this->registration->mahasiswa_id !== $mahasiswaId) {
            abort(403, 'Akses ditolak.');
        }

        if ($this->registration->status_pendaftaran !== RegistrationStatus::COMPLETED) {
            abort(403, 'Sertifikat belum tersedia.');
        }

        $this->sertifikatLainnya = EventRegistration::with('event')
            ->where('mahasiswa_id', $this->registration->mahasiswa_id)
            ->where('status_pendaftaran', RegistrationStatus::COMPLETED)
            ->where('id', '!=', $this->registration->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // URL share LinkedIn dibangun di server, bukan dari JS
        $shareUrl  = route('mahasiswa.sertifikat.show', $this->registration->id);
        $shareTitle = 'Sertifikat ' . $this->registration->event->nama_event
            . ' — ' . ($this->registration->nama_cetak_sertifikat ?? $this->registration->mahasiswa->nama);

        $this->linkedinShareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url='
            . urlencode($shareUrl) . '&title=' . urlencode($shareTitle);
    }
}; ?>

<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('mahasiswa.sertifikat.index') }}"
               class="w-10 h-10 bg-white rounded-full flex items-center justify-center border border-gray-200 text-gray-600 hover:bg-gray-50 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-indigo-900">Pratinjau Sertifikat</h1>
                <p class="text-gray-500 text-sm">{{ $registration->event->nama_event }}</p>
            </div>
        </div>

        {{-- Hanya satu tombol: Buka Sertifikat (stream PDF di tab baru) --}}
        {{-- Tidak ada Download JPG karena mahasiswa tidak perlu template mentah --}}
        <div class="flex items-center gap-3">
            @if ($registration->event->templateSertifikat?->file_template)
                <a href="{{ route('mahasiswa.sertifikat.download', $registration->id) }}"
                   target="_blank"
                   class="px-5 py-2.5 bg-indigo-900 text-white font-medium text-sm rounded-lg hover:bg-indigo-800 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Buka Sertifikat (PDF)
                </a>
            @else
                <button disabled
                        class="px-5 py-2.5 bg-gray-200 text-gray-400 font-medium text-sm rounded-lg cursor-not-allowed">
                    Sertifikat belum tersedia
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Area utama: tidak ada preview HTML yang bisa diinspect --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Card penjelasan --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                @if ($registration->event->templateSertifikat?->file_template)
                    {{--
                        Sertifikat ditampilkan sebagai PDF di tab baru (stream),
                        bukan sebagai HTML. Tidak ada nama yang bisa dimanipulasi
                        via inspect element karena rendering dilakukan di server (dompdf).
                    --}}
                    <div class="bg-indigo-50 border-b border-indigo-100 px-6 py-4 flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-indigo-900">Sertifikat Siap Dibuka</p>
                            <p class="text-xs text-indigo-600 mt-0.5">Klik tombol "Buka Sertifikat (PDF)" untuk melihat sertifikat Anda.</p>
                        </div>
                    </div>

                    <div class="p-8 flex flex-col items-center justify-center min-h-[280px] text-center">
                        <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-gray-800 mb-2">
                            {{ $registration->event->nama_event }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">
                            Atas nama: <span class="font-semibold text-indigo-900">{{ $registration->nama_cetak_sertifikat ?? $registration->mahasiswa->nama }}</span>
                        </p>
                        <a href="{{ route('mahasiswa.sertifikat.download', $registration->id) }}"
                           target="_blank"
                           class="px-6 py-2.5 bg-indigo-900 text-white font-medium text-sm rounded-lg hover:bg-indigo-800 transition shadow-sm inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Buka Sertifikat (PDF)
                        </a>
                    </div>
                @else
                    <div class="p-12 flex flex-col items-center justify-center text-center min-h-[280px]">
                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-base font-bold text-gray-700 mb-1">Sertifikat belum tersedia</h3>
                        <p class="text-sm text-gray-500">Panitia belum mengunggah template sertifikat untuk event ini.</p>
                    </div>
                @endif
            </div>

            {{-- Sertifikat lainnya --}}
            <div>
                <h3 class="text-base font-bold text-indigo-900 mb-4">Sertifikat Lainnya</h3>
                @if ($sertifikatLainnya->isEmpty())
                    <div class="bg-white rounded-xl border border-gray-200 p-6 text-center text-gray-500 text-sm shadow-sm">
                        Belum ada sertifikat lain.
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach ($sertifikatLainnya as $lain)
                            <a href="{{ route('mahasiswa.sertifikat.show', $lain->id) }}"
                               class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-3 hover:shadow-md transition">
                                <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-sm font-bold text-indigo-900 truncate">{{ $lain->event->nama_event }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $lain->created_at->format('d M Y') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Informasi dokumen --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-base font-bold text-indigo-900 mb-5">Informasi Dokumen</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">ID Sertifikat</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">
                            EVT-{{ str_pad($registration->event_id, 4, '0', STR_PAD_LEFT) }}-{{ str_pad($registration->id, 4, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Nama Penerima</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">
                            {{ $registration->nama_cetak_sertifikat ?? $registration->mahasiswa->nama }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Metode Verifikasi</p>
                        <div class="flex items-center gap-1.5 mt-1">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm font-bold text-gray-900">Database Eventoria</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 flex flex-col items-center">
                        <div class="bg-white p-2 rounded shadow-sm border border-gray-200">
                            <svg class="w-24 h-24 text-gray-800" viewBox="0 0 100 100" fill="currentColor">
                                <path d="M10,10 h30 v30 h-30 z M15,15 h20 v20 h-20 z M55,10 h30 v30 h-30 z M60,15 h20 v20 h-20 z M10,55 h30 v30 h-30 z M15,60 h20 v20 h-20 z M60,60 h10 v10 h-10 z M75,75 h10 v10 h-10 z M60,75 h10 v10 h-10 z M75,60 h10 v10 h-10 z"/>
                                <rect x="20" y="20" width="10" height="10"/>
                                <rect x="65" y="20" width="10" height="10"/>
                                <rect x="20" y="65" width="10" height="10"/>
                                <rect x="55" y="45" width="40" height="10"/>
                                <rect x="45" y="10" width="5" height="40"/>
                                <rect x="45" y="55" width="10" height="30"/>
                            </svg>
                        </div>
                        <p class="text-[10px] text-center text-gray-400 mt-3">Pindai untuk verifikasi keaslian sertifikat.</p>
                    </div>
                </div>
            </div>

            {{-- LinkedIn Share --}}
            <div class="bg-indigo-950 rounded-xl p-6 text-white shadow-sm relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold mb-2">Sertifikat Terverifikasi</h3>
                <p class="text-indigo-200 text-xs leading-relaxed mb-5">
                    Dokumen ini telah divalidasi oleh sistem administrasi Eventoria.
                </p>
                <a href="{{ $linkedinShareUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="w-full py-2.5 bg-indigo-100 text-indigo-900 font-bold text-sm rounded-lg hover:bg-white transition shadow-sm flex justify-center items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/>
                    </svg>
                    Bagikan ke LinkedIn
                </a>
            </div>

            {{-- Bantuan --}}
            <div class="bg-gray-50 rounded-xl border border-dashed border-gray-200 p-5">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-bold text-gray-700">Bantuan</h4>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Mengalami masalah? Hubungi panitia event terkait.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>