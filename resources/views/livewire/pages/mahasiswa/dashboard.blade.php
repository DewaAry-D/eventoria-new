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
            ->where(function($q) {
        // Hanya tampilkan event yang masa pendaftarannya belum habis
        $q->whereHas('timeLines', function($q2) {
            // Kurung kondisi nama-nama alternatif di sini
            $q2->where(function($sub) {
                $sub->where('nama_timeline', 'like', '%Pendaftaran%')
                    ->orWhere('nama_timeline', 'like', '%Registrasi%')
                    ->orWhere('nama_timeline', 'like', '%Registration%');
            })
            // Filter tanggal ini akan berlaku untuk ketiga kondisi di atas
            ->where('tanggal_selesai', '>=', now());
        })
        
                // ATAU jika belum memiliki timeline (fallback untuk dev/testing)
                ->orWhereDoesntHave('timeLines');
            })
            ->where(function($q) use ($mahasiswa) {
                // Event tingkat Universitas (Terbuka untuk semua)
                $q->where('tingkat_event', 'universitas')
                  // ATAU Event tingkat Fakultas (Hanya untuk fakultas yang sama)
                  ->orWhere(function($q2) use ($mahasiswa) {
                      $q2->where('tingkat_event', 'fakultas')
                            ->whereHas('organisasi', fn($q3) => $q3->where('fakultas_id', $mahasiswa?->prodi?->fakultas_id));
                  })
                  // ATAU Event tingkat Prodi (Hanya untuk prodi yang sama persis)
                  ->orWhere(function($q2) use ($mahasiswa) {
                      $q2->where('tingkat_event', 'prodi')
                            ->whereHas('organisasi', fn($q3) => $q3->where('prodi_id', $mahasiswa?->prodi_id));
                  });
            });

        // 1. Ambil 9 Rekomendasi (Bisa dikembangkan AI kedepannya, sementara ambil terbaru)
        $rekomendasi = (clone $baseQuery)->latest()->take(9)->get();

        // 2. Ambil Semua Event dengan filter Pencarian & Kategori
        $events = (clone $baseQuery)
            ->when($this->search, fn($q) => $q->where('nama_event', 'like', '%'.$this->search.'%'))
            ->when($this->filterKategori !== 'semua', fn($q) => $q->where('kategori_id', $this->filterKategori))
            ->latest()
            ->paginate(9);

        // 3. Ambil Event Terdaftar
        $registeredEvents = \App\Models\EventRegistration::with(['event.kategori', 'event.timeLines'])
            ->where('mahasiswa_id', $mahasiswa->id)
            ->latest()
            ->take(4)
            ->get();

        $totalTerdaftar = \App\Models\EventRegistration::where('mahasiswa_id', $mahasiswa->id)->count();
        $totalSertifikat = \App\Models\EventRegistration::where('mahasiswa_id', $mahasiswa->id)
            ->where('status_pendaftaran', \App\Enums\RegistrationStatus::COMPLETED)
            ->count();

        return [
            'nama_mahasiswa' => $mahasiswa->nama ?? 'Mahasiswa',
            'rekomendasi' => $rekomendasi,
            'events' => $events,
            'registeredEvents' => $registeredEvents,
            'totalTerdaftar' => $totalTerdaftar,
            'totalSertifikat' => $totalSertifikat,
            'categories' => \App\Models\Kategori::all(),
        ];
    }
}; ?>

<div class="space-y-8">
    <style>
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    {{-- Banner Selamat Datang --}}
    <section class="relative overflow-hidden flex flex-col gap-4 bg-primary-container rounded-2xl p-8 md:p-12 shadow-md text-white">
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-primary rounded-full opacity-35 z-0"></div>
        
        <div class="relative z-10">
            <h1 class="text-3xl md:text-4xl font-extrabold text-white leading-tight">Selamat Datang, {{ $nama_mahasiswa }}</h1>
            <p class="text-base text-on-primary-container mt-3 max-w-xl">
                Ada event akademik baru minggu ini. Jangan lewatkan kesempatan untuk menambah wawasan dan portofolio Anda!
            </p>
            <div class="flex gap-3 mt-6">
                <button @click="document.getElementById('eksplorasi-section').scrollIntoView({ behavior: 'smooth' })" class="bg-surface text-primary px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-surface-container transition-colors">
                    Eksplorasi Event
                </button>
                <a href="{{ route('mahasiswa.schedule') }}" wire:navigate class="border border-outline-variant text-white px-6 py-2.5 rounded-lg text-sm font-semibold hover:bg-primary transition-colors inline-flex items-center justify-center">
                    Jadwal Saya
                </a>
            </div>
        </div>
    </section>

    {{-- Stats Cards --}}
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-surface-container-lowest shadow-sm rounded-xl p-6 flex items-center gap-4 border border-outline-variant">
            <div class="w-12 h-12 rounded-full bg-surface-container-low flex items-center justify-center text-primary">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <div class="text-xs text-on-surface-variant uppercase tracking-wider font-semibold">Event Terdaftar</div>
                <div class="text-3xl font-bold text-primary leading-none mt-1">{{ sprintf('%02d', $totalTerdaftar) }}</div>
            </div>
        </div>
        <div class="bg-surface-container-lowest shadow-sm rounded-xl p-6 flex items-center gap-4 border border-outline-variant">
            <div class="w-12 h-12 rounded-full bg-success bg-opacity-10 text-success flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="text-xs text-on-surface-variant uppercase tracking-wider font-semibold">Sertifikat Diperoleh</div>
                <div class="text-3xl font-bold text-primary leading-none mt-1">{{ sprintf('%02d', $totalSertifikat) }}</div>
            </div>
        </div>
    </section>

    {{-- Event Terdaftar --}}
    <section class="flex flex-col gap-4">
        <div class="flex justify-between items-end pb-3">
            <div>
                <h2 class="text-xl font-bold text-primary">Event Terdaftar</h2>
                <p class="text-sm text-on-surface-variant mt-1">Pantau jadwal event yang akan Anda hadiri.</p>
            </div>
            <a href="{{ route('mahasiswa.my-events') }}" wire:navigate class="text-primary text-sm font-semibold flex items-center gap-1 hover:underline">
                Lihat Semua <span aria-hidden="true">&rarr;</span>
            </a>
        </div>

        <div class="flex overflow-x-auto pb-4 gap-6 scrollbar-thin scrollbar-thumb-outline-variant">
            @forelse($registeredEvents as $reg)
                <div class="bg-surface-container-lowest shadow-sm rounded-xl p-4 flex flex-col sm:flex-row gap-4 flex-shrink-0 w-80 sm:w-96 group transition-all duration-300 ease-out hover:-translate-y-1.5 hover:shadow-md hover:border-primary hover:border-opacity-35">
                    <div class="overflow-hidden rounded-lg w-full sm:w-32 h-32 flex-shrink-0">
                        <img src="{{ $reg->event->flyer_url ?? 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80' }}" alt="Flyer" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                    </div>
                    <div class="flex flex-col flex-1 justify-between">
                        <div>
                            <div class="text-xs text-outline mb-1 flex justify-between items-center">
                                <span>{{ $reg->event->timeLines->where('nama_timeline', 'Pelaksanaan')->first()?->tanggal_mulai?->format('d M Y, H:i') ?? 'Segera Hadir' }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold {{ $reg->status_pendaftaran->value === 'approved' ? 'bg-success bg-opacity-10 text-success' : ($reg->status_pendaftaran->value === 'pending' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-error bg-opacity-10 text-error') }}">
                                    {{ ucfirst($reg->status_pendaftaran->value) }}
                                </span>
                            </div>
                            <h3 class="text-base font-semibold text-primary leading-tight">{{ $reg->event->nama_event }}</h3>
                            <p class="text-xs text-on-surface-variant mt-1">{{ $reg->event->nama_lokasi ?? 'Daring' }} • {{ $reg->event->penyelenggara }}</p>
                        </div>
                        <a href="{{ route('mahasiswa.event-detail', $reg->event->slug) }}" wire:navigate class="block text-center bg-primary text-on-primary w-full py-2 rounded-lg mt-3 text-xs font-semibold hover:bg-primary-container transition-colors">
                            Lihat Event
                        </a>
                    </div>
                </div>
            @empty
                <div class="w-full p-8 text-center text-on-surface-variant bg-surface-container-lowest border border-outline-variant rounded-xl">
                    Anda belum mendaftar di event apa pun.
                </div>
            @endforelse
        </div>
    </section>

    {{-- Eksplorasi Event --}}
    <section id="eksplorasi-section" class="flex flex-col gap-4">
        <div class="flex flex-col md:flex-row md:items-end justify-between pb-3 gap-4">
            <div>
                <h2 class="text-xl font-bold text-primary">Eksplorasi Event</h2>
                <p class="text-sm text-on-surface-variant mt-1">Cari dan temukan event menarik di kampus.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-center">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-outline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="search" 
                           class="block w-full p-2.5 pl-10 text-sm text-on-surface border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-primary bg-surface-container-lowest" 
                           placeholder="Cari event...">
                </div>

                {{-- Dropdown Filter Kategori --}}
                <div x-data="{ open: false }" class="relative w-full sm:w-48">
                    <button @click="open = !open" 
                            class="flex items-center justify-between w-full p-2.5 text-sm text-on-surface bg-surface-container-lowest border border-outline rounded-lg hover:bg-surface-container-low focus:ring-2 focus:ring-primary focus:border-primary focus:outline-none transition shadow-sm">
                        <span class="font-semibold text-on-surface">
                            @if($filterKategori === 'semua')
                                Semua Kategori
                            @else
                                {{ $categories->firstWhere('id', $filterKategori)->nama_kategori ?? 'Semua Kategori' }}
                            @endif
                        </span>
                        <svg class="w-4 h-4 text-outline transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" 
                          @click.away="open = false" 
                          x-transition:enter="transition ease-out duration-100"
                          x-transition:enter-start="transform opacity-0 scale-95"
                          x-transition:enter-end="transform opacity-100 scale-100"
                          x-transition:leave="transition ease-in duration-75"
                          x-transition:leave-start="transform opacity-100 scale-100"
                          x-transition:leave-end="transform opacity-0 scale-95"
                          class="absolute right-0 mt-2 w-full bg-surface-container-lowest rounded-lg shadow-lg border border-outline-variant py-1 z-30 max-h-60 overflow-y-auto">
                        
                        <button wire:click="$set('filterKategori', 'semua')" @click="open = false"
                                class="block w-full text-left px-4 py-2.5 text-sm transition duration-150 {{ $filterKategori === 'semua' ? 'bg-primary-fixed text-primary font-bold' : 'text-on-surface hover:bg-surface-container-low' }}">
                            Semua Kategori
                        </button>

                        @foreach($categories as $cat)
                            <button wire:click="$set('filterKategori', '{{ $cat->id }}')" @click="open = false"
                                    class="block w-full text-left px-4 py-2.5 text-sm transition duration-150 {{ $filterKategori == $cat->id ? 'bg-primary-fixed text-primary font-bold' : 'text-on-surface hover:bg-surface-container-low' }}">
                                {{ $cat->nama_kategori }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
                <div class="bg-surface-container-lowest shadow-sm rounded-xl overflow-hidden flex flex-col h-full group transition-all duration-300 ease-out hover:-translate-y-1.5 hover:shadow-lg">
                    <div class="relative h-40 bg-surface-container-low overflow-hidden">
                        <img src="{{ $event->flyer_url ?? 'https://images.unsplash.com/photo-1518770660439-4636190af475?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' }}" alt="Flyer" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                        <span class="absolute top-3 right-3 bg-primary text-on-primary px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">
                            {{ $event->kategori->nama_kategori ?? 'Umum' }}
                        </span>
                    </div>
                    <div class="p-5 flex flex-col flex-1 gap-3">
                        <h3 class="text-base font-bold text-primary leading-snug line-clamp-2">{{ $event->nama_event }}</h3>
                        <div class="text-xs text-on-surface-variant flex flex-col gap-2">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $event->timeLines->where('nama_timeline', 'Pelaksanaan')->first()?->tanggal_mulai?->format('l, d M Y') ?? 'Segera Hadir' }}
                            </span>
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $event->nama_lokasi ?? 'Daring' }}
                            </span>
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                {{ $event->penyelenggara }}
                            </span>
                        </div>
                        <div class="flex justify-between items-end mt-auto pt-4 ">
                            <div>
                                <div class="text-[10px] text-outline uppercase tracking-wide">Kuota</div>
                                @if($event->sisa_kuota <= 0)
                                    <div class="text-xs font-bold text-error">Penuh (0/{{ $event->kuota }})</div>
                                @elseif($event->sisa_kuota <= 5)
                                    <div class="text-xs font-bold text-warning">Hampir Penuh ({{ $event->sisa_kuota }}/{{ $event->kuota }})</div>
                                @else
                                    <div class="text-xs font-bold text-primary">{{ $event->sisa_kuota }}/{{ $event->kuota }}</div>
                                @endif
                            </div>
                            <a href="{{ route('mahasiswa.event-detail', $event->slug) }}" wire:navigate class="bg-primary text-on-primary px-4 py-2 rounded-lg text-xs font-semibold hover:bg-primary-container transition">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 p-10 text-center text-on-surface-variant bg-surface-container-lowest border border-outline-variant rounded-xl">
                    <svg class="mx-auto h-12 w-12 text-outline mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p class="text-lg font-semibold text-on-surface">Event tidak ditemukan</p>
                    <p class="text-sm">Coba gunakan kata kunci pencarian yang lain.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $events->links('livewire.pages.mahasiswa.pagination') }}
        </div>
    </section>

    {{-- Rekomendasi Event --}}
    <section x-data="{}" class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 shadow-sm flex flex-col gap-4">
        <div class="flex justify-between items-center pb-3">
            <div>
                <h2 class="text-xl font-bold text-primary">Rekomendasi Event</h2>
            </div>
            
            <div class="flex items-center gap-3">
                {{-- Carousel Nav Buttons --}}
                <div class="flex gap-1.5">
                    <button @click="$refs.rekomendasiCarousel.scrollBy({ left: -320, behavior: 'smooth' })" 
                            class="p-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-primary hover:bg-surface-container-low hover:text-primary transition shadow-sm focus:outline-none">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button @click="$refs.rekomendasiCarousel.scrollBy({ left: 320, behavior: 'smooth' })" 
                            class="p-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-primary hover:bg-surface-container-low hover:text-primary transition shadow-sm focus:outline-none">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-ref="rekomendasiCarousel" class="flex items-stretch overflow-x-hidden pb-4 gap-6 no-scrollbar snap-x snap-mandatory scroll-smooth">
            @forelse($rekomendasi as $event)
                <div class="bg-surface-container-lowest shadow-sm rounded-xl overflow-hidden flex flex-col group transition-all duration-300 ease-out hover:-translate-y-1.5 hover:shadow-lg flex-shrink-0 w-80 snap-start">
                    <div class="relative h-40 bg-surface-container-low overflow-hidden">
                        <img src="{{ $event->flyer_url ?? 'https://images.unsplash.com/photo-1518770660439-4636190af475?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' }}" alt="Flyer" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                        <span class="absolute top-3 right-3 bg-primary text-on-primary px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">
                            {{ $event->kategori->nama_kategori ?? 'Umum' }}
                        </span>
                    </div>
                    <div class="p-5 flex flex-col flex-1 gap-3">
                        <h3 class="text-base font-bold text-primary leading-snug line-clamp-2">{{ $event->nama_event }}</h3>
                        <div class="text-xs text-on-surface-variant flex flex-col gap-2">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $event->timeLines->where('nama_timeline', 'Pelaksanaan')->first()?->tanggal_mulai?->format('l, d M Y') ?? 'Segera Hadir' }}
                            </span>
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $event->nama_lokasi ?? 'Daring' }}
                            </span>
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                {{ $event->penyelenggara }}
                            </span>
                        </div>
                        <div class="flex justify-between items-end mt-auto pt-4">
                            <div>
                                <div class="text-[10px] text-outline uppercase tracking-wide">Kuota</div>
                                @if($event->sisa_kuota <= 0)
                                    <div class="text-xs font-bold text-error">Penuh (0/{{ $event->kuota }})</div>
                                @elseif($event->sisa_kuota <= 5)
                                    <div class="text-xs font-bold text-warning">Hampir Penuh ({{ $event->sisa_kuota }}/{{ $event->kuota }})</div>
                                @else
                                    <div class="text-xs font-bold text-primary">{{ $event->sisa_kuota }}/{{ $event->kuota }}</div>
                                @endif
                            </div>
                            <a href="{{ route('mahasiswa.event-detail', $event->slug) }}" wire:navigate class="bg-primary text-on-primary px-4 py-2 rounded-lg text-xs font-semibold hover:bg-primary-container transition">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="w-full p-10 text-center text-on-surface-variant bg-surface-container-lowest border border-outline-variant rounded-xl">
                    Belum ada rekomendasi event saat ini.
                </div>
            @endforelse
    </section>
</div>