<?php

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.organisasi')] class extends Component
{
    public function with(): array
    {
        $organisasi = Auth::user()->load('organisasi')->organisasi;

        // Mengambil semua event milik organisasi ini
        $events = Event::with('kategori')
                    ->withCount('registrations') // Menghitung total pendaftar di setiap event
                    ->where('organisasi_id', $organisasi->id)
                    ->latest()
                    ->get();

        // Metrik
        $totalEvent = $events->count();
        $totalPendaftar = $events->sum('registrations_count');
        $menungguPersetujuan = $events->where('status', 'pending_approval')->count();
        $butuhRevisi = $events->where('status', 'revision')->count();

        return [
            'organisasi' => $organisasi,
            'events' => $events,
            'totalEvent' => $totalEvent,
            'totalPendaftar' => $totalPendaftar,
            'menungguPersetujuan' => $menungguPersetujuan,
            'butuhRevisi' => $butuhRevisi,
        ];
    }

    // Fungsi Rekayasa Logika Ekstraksi Data Lengkap ke Berkas CSV
    public function downloadReport(): StreamedResponse
    {
        $organisasi = Auth::user()->load('organisasi')->organisasi;
        
        // Eager loading kategori dan biayaEvent untuk kalkulasi finansial
        $events = Event::with(['kategori'])
            ->withCount('registrations')
            ->where('organisasi_id', $organisasi->id)
            ->latest()
            ->get();

        $filename = "Laporan_Lengkap_" . str_replace(' ', '_', $organisasi->nama_organisasi) . "_" . now()->format('Ymd') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($events) {
            $file = fopen('php://output', 'w');
            
            // Header Kolom Lengkap
            fputcsv($file, [
                'Nama Event', 
                'Status Birokrasi DPM',
                'Kategori', 
                'Tingkat Wilayah', 
                'Target Kuota', 
                'Total Pendaftar', 
                'Sisa Kuota',
                'Catatan Revisi',
                'Lokasi URL',
                'Narasumber',
            ]);

            foreach ($events as $event) {

                fputcsv($file, [
                    $event->nama_event,
                    strtoupper($event->status->value),
                    $event->kategori->nama_kategori ?? 'Umum',
                    ucfirst($event->tingkat_event->value),
                    $event->kuota ?? 'Tidak Terbatas',
                    $event->registrations_count,
                    $event->sisa_kuota,
                    $event->catatan_revisi ?? '-',
                    $event->lokasi_url ?? '-',
                    $event->narasumber ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadReportPdf()
    {
        $organisasi = Auth::user()->load('organisasi')->organisasi;

        $events = Event::with(['kategori'])
            ->withCount('registrations')
            ->where('organisasi_id', $organisasi->id)
            ->latest()
            ->get();

        $totalEvent = $events->count();
        $totalPendaftar = $events->sum('registrations_count');
        $menungguPersetujuan = $events->where('status', 'pending_approval')->count();
        $butuhRevisi = $events->where('status', 'revision')->count();

        $filename = "Laporan_Lengkap_" . str_replace(' ', '_', $organisasi->nama_organisasi) . "_" . now()->format('Ymd') . ".pdf";

        $pdf = Pdf::loadView('pdf.laporan-event-organisasi', [
            'organisasi'          => $organisasi,
            'events'              => $events,
            'totalEvent'          => $totalEvent,
            'totalPendaftar'      => $totalPendaftar,
            'menungguPersetujuan' => $menungguPersetujuan,
            'butuhRevisi'         => $butuhRevisi,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename);
    }
}; ?>

<div id="printable-dashboard-area">
    @if($organisasi->status->value === 'pending')
        <div class="mb-6 p-4 border-l-4 border-warning bg-warning/10 text-on-surface rounded-r-lg shadow-sm print:hidden">
            <div class="flex items-center mb-1">
                <svg class="w-5 h-5 mr-2 text-warning" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                <h3 class="font-bold text-lg">Akun Sedang Diverifikasi</h3>
            </div>
            <p class="text-sm ml-7 text-on-surface-variant">Akun organisasi Anda saat ini sedang dalam proses peninjauan oleh DPM. Fitur pembuatan event akan diaktifkan setelah akun disetujui.</p>
        </div>
    @elseif($organisasi->status->value === 'rejected')
        <div class="mb-6 p-4 border-l-4 border-error bg-error-container text-on-error-container rounded-r-lg shadow-sm print:hidden">
            <div class="flex items-center mb-1">
                <svg class="w-5 h-5 mr-2 text-error" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                <h3 class="font-bold text-lg">Pendaftaran Akun Ditolak</h3>
            </div>
            <p class="text-sm ml-7 mb-2">Pendaftaran organisasi Anda ditolak oleh DPM dengan catatan berikut:</p>
            <div class="ml-7 p-3 bg-surface-container-lowest border border-error/30 rounded text-on-surface-variant text-sm font-medium italic">
                "{{ $organisasi->pesan_penolakan ?? 'Dokumen pendirian tidak valid.' }}"
            </div>
        </div>
    @endif

    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-headline-md text-on-surface font-bold tracking-tight">Dashboard Hub Organisasi</h1>
            <p class="text-body-md text-on-surface-variant">Kelola kegiatan dan pantau antusiasme pendaftar program kerja Anda.</p>
        </div>
        
        <div class="flex items-center gap-2 print:hidden">
            <!-- SOP Button -->
            <div x-data="{ showSopModal: false }" class="contents">
                <button type="button" @click="showSopModal = true" class="p-2.5 bg-surface-container-lowest border border-outline-variant text-on-surface-variant rounded-xl hover:bg-surface-container transition shadow-sm cursor-pointer" title="Lihat SOP Pengajuan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </button>
                <x-sop-modal />
            </div>

            <div class="relative" x-data="{ dropdownOpen: false }" @click.away="dropdownOpen = false">
                <button type="button" @click="dropdownOpen = !dropdownOpen" class="p-2.5 bg-surface-container-lowest border border-outline-variant text-primary rounded-xl hover:bg-surface-container transition shadow-sm inline-flex items-center gap-1 cursor-pointer" title="Ekstrak Berkas Laporan">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <svg class="w-3.5 h-3.5 text-secondary/40 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown List Options Panel -->
                <div class="absolute right-0 mt-xs z-50 w-48 rounded-2xl bg-surface-container-lowest border border-outline-variant/40 shadow-lg p-xs space-y-[2px]" x-show="dropdownOpen" x-transition x-cloak>
                    <!-- Opsi 1: CSV -->
                    <button type="button" wire:click="downloadReport" @click="dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-emerald-500/[0.04] hover:text-emerald-700 font-bold transition-colors cursor-pointer inline-flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Unduh Berkas CSV
                    </button>
                    <!-- Opsi 2: PDF -->
                    <button type="button" wire:click="downloadReportPdf" @click="dropdownOpen = false" class="w-full text-left text-body-md px-md py-2 rounded-xl text-secondary hover:bg-blue-500/[0.04] hover:text-blue-700 font-bold transition-colors cursor-pointer inline-flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        Unduh Berkas PDF
                    </button>
                </div>
            </div>

            <!-- Create Event Button -->
            @if($organisasi->status->value === 'approved')
                <a href="{{ route('organisasi.events.create') }}" wire:navigate class="px-4 py-2 bg-primary text-on-primary font-bold rounded-xl hover:bg-primary/90 transition shadow-sm text-sm inline-flex items-center gap-2 cursor-pointer">
                    + Buat Event Baru
                </a>
            @else
                <button disabled class="px-4 py-2 bg-primary text-on-primary font-bold rounded-xl shadow-sm text-sm opacity-50 cursor-not-allowed inline-flex items-center gap-2">
                    + Buat Event Baru
                </button>
            @endif
        </div>
    </div>

    <!-- Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-card flex items-center gap-4">
            <div class="p-4 bg-primary/10 text-primary rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-sm text-on-surface-variant font-medium">Total Event</p>
                <p class="text-2xl font-bold text-on-surface">{{ $totalEvent }}</p>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-card flex items-center gap-4">
            <div class="p-4 bg-success/10 text-success rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <p class="text-sm text-on-surface-variant font-medium">Total Pendaftar</p>
                <p class="text-2xl font-bold text-on-surface">{{ $totalPendaftar }}</p>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-card flex items-center gap-4">
            <div class="p-4 bg-warning/10 text-warning rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <p class="text-sm text-on-surface-variant font-medium">Menunggu ACC / Revisi</p>
                <div class="flex items-center gap-2">
                    <p class="text-2xl font-bold text-on-surface">{{ $menungguPersetujuan }}</p>
                    @if($butuhRevisi > 0)
                        <span class="px-2 py-0.5 bg-error-container text-on-error-container text-xs font-bold rounded-full border border-error/30">{{ $butuhRevisi }} Revisi</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Section -->
    <div class="bg-surface-container-lowest rounded-xl shadow-card border border-outline-variant overflow-hidden">
        <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center select-none">
            <h2 class="text-title-lg font-bold text-on-surface">Event Terbaru Anda</h2>
            <a href="{{ route('organisasi.events')}}" class="text-sm text-primary hover:text-primary/80 font-medium print:hidden">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-on-surface-variant">
                <thead class="text-xs text-on-surface-variant uppercase bg-surface-container border-b border-outline-variant select-none">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama Event</th>
                        <th scope="col" class="px-6 py-3">Status Birokrasi</th>
                        <th scope="col" class="px-6 py-3">Pendaftar / Kuota</th>
                        <th scope="col" class="px-6 py-3 text-right print:hidden">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events->take(5) as $event)
                        <tr class="bg-surface-container-lowest border-b border-outline-variant hover:bg-surface-container-low">
                            <td class="px-6 py-4 font-medium text-on-surface w-1/2">
                                <div class="line-clamp-1 font-bold tracking-tight text-primary">{{ $event->nama_event }}</div>
                                <div class="text-xs text-on-surface-variant font-normal mt-1">{{ $event->kategori->nama_kategori ?? 'Umum' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($event->status->value === 'draft')
                                    <span class="bg-surface-container text-on-surface-variant text-xs font-bold px-2.5 py-0.5 rounded-xl border border-outline-variant">Draft</span>
                                @elseif($event->status->value === 'pending_approval')
                                    <span class="bg-warning/10 text-warning text-xs font-bold px-2.5 py-0.5 rounded-xl border border-warning/30">Diproses DPM</span>
                                @elseif($event->status->value === 'revision')
                                    <span class="bg-error-container text-on-error-container text-xs font-bold px-2.5 py-0.5 rounded-xl border border-error/30">Revisi Proposal</span>
                                @elseif($event->status->value === 'published')
                                    <span class="bg-success/10 text-success text-xs font-bold px-2.5 py-0.5 rounded-xl border border-success/30">Dipublikasi</span>
                                @else
                                    <span class="bg-primary/10 text-primary text-xs font-bold px-2.5 py-0.5 rounded-xl border border-primary/30">Selesai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-semibold">
                                <div class="flex items-center gap-2">
                                    <div class="w-full bg-surface-container-high rounded-full h-2.5 max-w-[100px] print:hidden">
                                        <div class="bg-primary h-2.5 rounded-full" style="width: {{ $event->kuota > 0 ? ($event->registrations_count / $event->kuota) * 100 : 0 }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-on-surface-variant">{{ $event->registrations_count }} / {{ $event->kuota ?? '~' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right print:hidden">
                                <a class="font-bold text-sm text-primary hover:text-primary/80 cursor-pointer" href="{{ route('organisasi.events.pendaftar', $event->id) }}">Kelola</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-on-surface-variant">
                                Belum ada event yang dibuat. Mulai rencanakan kegiatan pertama Anda!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-admin.modals.reject-modal 
        id="reject-event"
        title="Tolak Pendaftaran?"
        wireAction="rejectEvent"
    />

    
</div>