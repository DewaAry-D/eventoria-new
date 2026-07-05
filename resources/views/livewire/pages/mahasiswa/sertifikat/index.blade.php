<?php

use App\Enums\RegistrationStatus;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.mahasiswa')] class extends Component
{
    public string $tahun = 'Semua Tahun';

    public function with(): array
    {
        $mahasiswaId = Auth::user()->mahasiswa->id ?? null;

        // Query utama: sertifikat yang tampil di grid (bisa difilter per tahun)
        $query = EventRegistration::with(['event.templateSertifikat'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('status_pendaftaran', RegistrationStatus::COMPLETED)
            ->orderBy('created_at', 'desc');

        if ($this->tahun !== 'Semua Tahun') {
            $query->whereYear('created_at', $this->tahun);
        }

        // Query stats: selalu ambil semua (tidak ikut filter tahun)
        // agar angka total dan statistik tidak berubah saat filter aktif
        $allSertifikats = EventRegistration::where('mahasiswa_id', $mahasiswaId)
            ->where('status_pendaftaran', RegistrationStatus::COMPLETED)
            ->get();

        $years = $allSertifikats
            ->pluck('created_at')
            ->map(fn ($d) => $d->format('Y'))
            ->unique()
            ->sortDesc()
            ->values();

        // Hitung jumlah sertifikat per tahun (ambil maks 2 tahun untuk ditampilkan di stat card)
        $yearStats = [];
        foreach ($years->take(2) as $y) {
            $yearStats[$y] = $allSertifikats->filter(fn ($r) => $r->created_at->format('Y') === $y)->count();
        }

        return [
            'sertifikats'     => $query->get(),
            'totalSertifikat' => $allSertifikats->count(),
            'years'           => $years,
            'yearStats'       => $yearStats,
        ];
    }
}; ?>

<div>
    {{-- Header & Filter --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Sertifikat Saya</h1>
            <p class="text-gray-500 text-sm mt-1">Kumpulan pencapaian akademik dan non-akademik Anda.</p>
        </div>

        <select wire:model.live="tahun"
                class="border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-10">
            <option value="Semua Tahun">Semua Tahun</option>
            @foreach ($years as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
    </div>

    {{-- Grid Sertifikat --}}
    @if ($sertifikats->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center shadow-sm">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Belum ada sertifikat</h3>
            <p class="text-gray-500 mt-1 text-sm">Anda belum memiliki sertifikat yang tersedia pada rentang waktu ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach ($sertifikats as $reg)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">

                    {{-- Preview thumbnail template --}}
                    <div class="relative h-48 bg-gray-100 border-b border-gray-200">
                        @if ($reg->event->templateSertifikat?->file_template)
                            <img src="{{ Storage::url($reg->event->templateSertifikat->file_template) }}"
                                 class="w-full h-full object-cover"
                                 alt="Template Sertifikat {{ $reg->event->nama_event }}">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-xs">Template belum tersedia</span>
                            </div>
                        @endif

                        <div class="absolute top-3 right-3">
                            @if ($reg->event->templateSertifikat?->file_template)
                                <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wide shadow-sm">
                                    TERSEDIA
                                </span>
                            @else
                                <span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wide shadow-sm">
                                    MENUNGGU
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Info & tombol aksi --}}
                    <div class="p-5 flex flex-col h-[180px]">
                        <div class="flex items-center gap-1.5 text-gray-500 text-sm mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $reg->created_at->format('d M Y') }}</span>
                        </div>

                        <h3 class="font-bold text-indigo-900 text-base mb-auto line-clamp-2"
                            title="{{ $reg->event->nama_event }}">
                            {{ $reg->event->nama_event }}
                        </h3>

                        <div class="mt-4">
                            @if ($reg->event->templateSertifikat?->file_template)
                                <a href="{{ route('mahasiswa.sertifikat.download', $reg->id) }}"
                                target="_blank"
                                class="w-full text-center px-4 py-2 bg-indigo-900 text-white font-medium text-sm rounded-lg hover:bg-indigo-800 transition flex justify-center items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    Download Sertifikat
                                </a>
                            @else
                                <button disabled
                                        class="w-full text-center px-4 py-2 bg-gray-200 text-gray-400 font-medium text-sm rounded-lg cursor-not-allowed">
                                    Belum Tersedia
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-indigo-950 rounded-xl p-6 text-white shadow-sm flex flex-col justify-between h-32 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-10">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3z"/>
                </svg>
            </div>
            <p class="text-indigo-200 text-sm font-medium z-10">Total Sertifikat</p>
            <h2 class="text-4xl font-bold z-10">{{ str_pad($totalSertifikat, 2, '0', STR_PAD_LEFT) }}</h2>
        </div>

        @foreach ($yearStats as $year => $count)
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm flex flex-col justify-between h-32">
                <p class="text-gray-500 text-sm font-medium">Tahun {{ $year }}</p>
                <h2 class="text-4xl font-bold text-indigo-900">{{ str_pad($count, 2, '0', STR_PAD_LEFT) }}</h2>
            </div>
        @endforeach

        @for ($i = count($yearStats); $i < 2; $i++)
            <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm flex flex-col justify-between h-32">
                <p class="text-gray-500 text-sm font-medium">Tahun -</p>
                <h2 class="text-4xl font-bold text-gray-300">00</h2>
            </div>
        @endfor

        <div class="bg-[#EAEFF8] rounded-xl p-6 border border-indigo-100 shadow-sm flex items-center justify-between h-32">
            <div>
                <p class="text-gray-500 text-sm font-medium">Kredibilitas</p>
                <h2 class="text-lg font-bold text-indigo-900 mt-1">Terverifikasi</h2>
            </div>
            <svg class="w-8 h-8 text-indigo-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
        </div>
    </div>
</div>