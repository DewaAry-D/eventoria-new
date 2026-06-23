<?php

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.mahasiswa')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterKategori = 'semua';

    // Reset halaman paginasi jika sedang mencari sesuatu
    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterKategori() { $this->resetPage(); }

    public function with(): array
    {
        $user = Auth::user()->load('mahasiswa.prodi');
        $mahasiswa = $user->mahasiswa;

        // LOGIKA PERSONALIZED FEED BERDASARKAN ERD
        $baseQuery = Event::with(['organisasi', 'kategori'])
            ->where('status', 'published')
            ->whereHas('timeLines', function($q) {
                // Hanya tampilkan event yang masa pendaftarannya belum habis
                $q->where('nama_timeline', 'Pendaftaran')
                  ->where('tanggal_selesai', '>=', now());
            })
            ->where(function($q) use ($mahasiswa) {
                // Event tingkat Universitas (Terbuka untuk semua)
                $q->where('tingkat_event', 'univ')
                  // ATAU Event tingkat Fakultas (Hanya untuk fakultas yang sama)
                  ->orWhere(function($q2) use ($mahasiswa) {
                      $q2->where('tingkat_event', 'fakultas')
                         ->whereHas('organisasi', fn($q3) => $q3->where('fakultas_id', $mahasiswa->prodi->fakultas_id));
                  })
                  // ATAU Event tingkat Prodi (Hanya untuk prodi yang sama persis)
                  ->orWhere(function($q2) use ($mahasiswa) {
                      $q2->where('tingkat_event', 'prodi')
                         ->whereHas('organisasi', fn($q3) => $q3->where('prodi_id', $mahasiswa->prodi_id));
                  });
            });

        // 1. Ambil 3 Rekomendasi (Bisa dikembangkan AI kedepannya, sementara ambil terbaru)
        $rekomendasi = (clone $baseQuery)->latest()->take(3)->get();

        // 2. Ambil Semua Event dengan filter Pencarian
        $events = (clone $baseQuery)
            ->when($this->search, fn($q) => $q->where('nama_event', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(6);

        return [
            'nama_mahasiswa' => $mahasiswa->nama ?? 'Mahasiswa',
            'rekomendasi' => $rekomendasi,
            'events' => $events,
        ];
    }
}; ?>

<div>
    <div class="relative px-8 py-10 overflow-hidden text-white bg-indigo-900 rounded-2xl shadow-lg mb-8">
        <div class="relative z-10 md:w-2/3">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Selamat Datang, {{ $nama_mahasiswa }}</h1>
            <p class="text-indigo-200 mb-6 text-lg">Ada event akademik baru minggu ini. Jangan lewatkan kesempatan untuk mengembangkan portofolio dan koneksi Anda!</p>
            <div class="flex gap-4">
                <button class="px-6 py-2 bg-white text-indigo-900 font-semibold rounded-lg hover:bg-gray-100 transition">Eksplorasi Event</button>
                <button class="px-6 py-2 border border-indigo-400 text-white font-semibold rounded-lg hover:bg-indigo-800 transition">Jadwal Saya</button>
            </div>
        </div>
        <div class="absolute top-0 right-0 -mt-10 -mr-10 text-indigo-700 opacity-50">
            <svg width="300" height="300" viewBox="0 0 200 200" fill="currentColor"><path d="M45.7,-76.3C58.9,-69.3,69.2,-55.4,78.5,-41C87.8,-26.6,96.1,-11.7,94.9,2.4C93.7,16.6,83,30.1,72.6,42.6C62.2,55.1,52,66.6,39.3,73.4C26.6,80.2,11.3,82.3,-3.1,87.3C-17.5,92.3,-35,100.2,-48.5,95.1C-62,90,-71.5,71.9,-79.6,54C-87.7,36.1,-94.4,18.1,-93.6,0.5C-92.8,-17.1,-84.5,-34.2,-74,-48.8C-63.5,-63.4,-50.8,-75.5,-36.5,-81.4C-22.2,-87.3,-6.3,-87,-8.4,-81.9L45.7,-76.3Z" transform="translate(100 100)" /></svg>
        </div>
    </div>

    <div class="mb-10">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Event Rekomendasi</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($rekomendasi as $event)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                    <div class="h-40 bg-gray-200 relative">
                        <img src="{{ $event->flyer_url ?? 'https://placehold.co/600x400/E0E7FF/4338CA?text=Event+Flyer' }}" class="w-full h-full object-cover" alt="Flyer">
                        <span class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm text-xs font-bold px-3 py-1 rounded-full text-indigo-700 uppercase tracking-wide">
                            {{ $event->kategori->nama_kategori ?? 'Kategori' }}
                        </span>
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-gray-900 leading-tight mb-2 line-clamp-2">{{ $event->nama_event }}</h3>
                        <div class="text-sm text-gray-500 mb-4 flex flex-col gap-2">
                            <div class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Segera Hadir</div>
                            <div class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> {{ $event->nama_lokasi ?? 'Daring' }}</div>
                            <div class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg> {{ $event->penyelenggara }}</div>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <span class="text-sm font-semibold text-indigo-700">{{ $event->sisa_kuota }}/{{ $event->kuota }} Kuota</span>
                            <button class="px-4 py-2 bg-indigo-900 text-white text-sm font-medium rounded-lg hover:bg-indigo-800 transition">Lihat Detail</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 p-6 text-center text-gray-500 bg-white border border-gray-200 rounded-xl">
                    Belum ada rekomendasi event saat ini.
                </div>
            @endforelse
        </div>
    </div>

    <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <h2 class="text-xl font-bold text-gray-900">Eksplorasi Event</h2>
        
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
            <div class="relative w-full md:w-72">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input wire:model.live.debounce.500ms="search" type="search" class="block w-full p-2.5 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" placeholder="Cari bootcamp data science, seminar...">
            </div>
            
            <select wire:model.live="filterKategori" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
                <option value="semua">Urutkan: Terdekat</option>
                <option value="terbaru">Urutkan: Terbaru</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse($events as $event)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                <div class="h-40 bg-gray-200 relative">
                    <img src="{{ $event->flyer_url ?? 'https://placehold.co/600x400/E0E7FF/4338CA?text=Event+Flyer' }}" class="w-full h-full object-cover" alt="Flyer">
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-lg text-gray-900 leading-tight mb-2 line-clamp-2">{{ $event->nama_event }}</h3>
                    <div class="flex items-center justify-between mt-4">
                        <span class="text-sm font-semibold text-indigo-700">{{ $event->tingkat_event }}</span>
                        <button class="px-4 py-2 bg-indigo-50 text-indigo-700 text-sm font-bold rounded-lg hover:bg-indigo-100 transition">Lihat Detail</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 p-10 text-center text-gray-500 bg-white border border-gray-200 rounded-xl">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <p class="text-lg font-medium text-gray-900">Event tidak ditemukan</p>
                <p>Coba gunakan kata kunci pencarian yang lain.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $events->links() }}
    </div>
</div>