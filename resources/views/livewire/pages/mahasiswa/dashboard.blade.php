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
                    $q2->where('nama_timeline', 'Pendaftaran')
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
                         ->whereHas('organisasi', fn($q3) => $q3->where('fakultas_id', $mahasiswa->prodi->fakultas_id));
                  })
                  // ATAU Event tingkat Prodi (Hanya untuk prodi yang sama persis)
                  ->orWhere(function($q2) use ($mahasiswa) {
                      $q2->where('tingkat_event', 'prodi')
                         ->whereHas('organisasi', fn($q3) => $q3->where('prodi_id', $mahasiswa->prodi_id));
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

<div class="space-y-8 bg-background min-h-screen">
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
    <section class="relative overflow-hidden flex flex-col gap-md bg-primary rounded-xl p-lg md:p-xl shadow-card text-on-primary">
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-primary-container rounded-full opacity-50 z-0"></div>
        
        <div class="relative z-10">
            <h1 class="text-headline-lg-mobile md:text-display-lg text-on-primary">Selamat Datang, {{ $nama_mahasiswa }}</h1>
            <p class="text-body-lg text-inverse-primary mt-sm max-w-xl">
                Ada event akademik baru minggu ini. Jangan lewatkan kesempatan untuk menambah wawasan dan portofolio Anda!
            </p>
            <div class="flex gap-sm mt-md">
                <button class="bg-surface text-primary px-6 py-2.5 rounded-lg text-title-sm hover:bg-surface-dim transition-colors">
                    Eksplorasi Event
                </button>
                <button class="border border-outline-variant text-on-primary px-6 py-2.5 rounded-lg text-title-sm hover:bg-primary-container hover:text-on-primary-container transition-colors">
                    Jadwal Saya
                </button>
            </div>
        </div>
    </section>

    {{-- Stats Cards --}}
    <section class="grid grid-cols-1 md:grid-cols-2 gap-md">
        <div class="bg-surface-container-lowest shadow-card rounded-xl p-lg flex items-center gap-md border border-outline-variant">
            <div class="w-12 h-12 rounded-full bg-primary-fixed text-on-primary-fixed flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <div class="text-caption text-on-surface-variant uppercase tracking-wider">Event Terdaftar</div>
                <div class="text-headline-lg text-primary mt-1">{{ sprintf('%02d', $totalTerdaftar) }}</div>
            </div>
        </div>
        <div class="bg-surface-container-lowest shadow-card rounded-xl p-lg flex items-center gap-md border border-outline-variant">
            <div class="w-12 h-12 rounded-full bg-success/20 text-success flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <div class="text-caption text-on-surface-variant uppercase tracking-wider">Sertifikat Diperoleh</div>
                <div class="text-headline-lg text-primary mt-1">{{ sprintf('%02d', $totalSertifikat) }}</div>
            </div>
        </div>
    </section>

    {{-- Event Terdaftar --}}
    <section class="flex flex-col gap-md">
        <div class="flex justify-between items-end border-b border-outline-variant pb-xs">
            <div>
                <h2 class="text-headline-md text-primary">Event Terdaftar</h2>
                <p class="text-body-sm text-on-surface-variant mt-1">Pantau jadwal event yang akan Anda hadiri.</p>
            </div>
            <a href="#" class="text-primary text-title-sm flex items-center gap-1 hover:underline">
                Lihat Semua <span aria-hidden="true">&rarr;</span>
            </a>
        </div>

        <div class="flex overflow-x-auto pb-4 gap-md scrollbar-thin scrollbar-thumb-primary-fixed-dim">
            @forelse($registeredEvents as $reg)
                <div class="bg-surface-container-lowest shadow-card border border-outline-variant rounded-xl p-md flex flex-col sm:flex-row gap-md flex-shrink-0 w-80 sm:w-96">
                    <img src="{{ $reg->event->flyer_url ?? 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80' }}" alt="Flyer" class="w-full sm:w-32 h-32 rounded-lg object-cover">
                    <div class="flex flex-col flex-1 justify-between">
                        <div>
                            <div class="text-label-md text-outline mb-1 flex justify-between items-center">
                                <span>{{ $reg->event->timeLines->where('nama_timeline', 'Pelaksanaan')->first()?->tanggal_mulai?->format('d M Y, H:i') ?? 'Segera Hadir' }}</span>
                                <span class="px-2 py-0.5 rounded text-caption font-semibold {{ $reg->status_pendaftaran->value === 'approved' ? 'bg-success/20 text-success' : ($reg->status_pendaftaran->value === 'pending' ? 'bg-warning/20 text-warning' : 'bg-error-container text-on-error-container') }}">
                                    {{ ucfirst($reg->status_pendaftaran->value) }}
                                </span>
                            </div>
                            <h3 class="text-title-md text-on-surface leading-tight">{{ $reg->event->nama_event }}</h3>
                            <p class="text-body-sm text-on-surface-variant mt-1">{{ $reg->event->nama_lokasi ?? 'Daring' }} • {{ $reg->event->penyelenggara }}</p>
                        </div>
                        <button class="bg-primary text-on-primary w-full py-2 rounded-lg mt-3 text-label-md hover:bg-primary-container hover:text-on-primary-container transition-colors">
                            Lihat Event
                        </button>
                    </div>
                </div>
            @empty
                <div class="w-full p-xl text-center text-on-surface-variant bg-surface-container-lowest border border-outline-variant rounded-xl">
                    Anda belum mendaftar di event apa pun.
                </div>
            @endforelse
        </div>
    </section>

    {{-- Eksplorasi Event --}}
    <section class="flex flex-col gap-md">
        <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-outline-variant pb-xs gap-md">
            <div>
                <h2 class="text-headline-md text-primary">Eksplorasi Event</h2>
                <p class="text-body-sm text-on-surface-variant mt-1">Cari dan temukan event menarik di kampus.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-sm w-full md:w-auto items-center">
                <div class="relative w-full sm:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-outline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="search" 
                           class="block w-full p-2.5 pl-10 text-body-md text-on-surface border border-outline rounded-lg focus:ring-primary focus:border-primary bg-surface-container-lowest" 
                           placeholder="Cari event...">
                </div>

                <div class="flex bg-surface-container p-1 rounded-lg text-label-md w-full sm:w-auto justify-center">
                    <button wire:click="$set('filterKategori', 'semua')" 
                            class="px-4 py-1.5 rounded-md transition {{ $filterKategori === 'semua' ? 'bg-surface-container-lowest text-primary shadow-sm' : 'text-on-surface-variant hover:text-primary' }}">
                        Semua
                    </button>
                    @foreach($categories as $cat)
                        <button wire:click="$set('filterKategori', '{{ $cat->id }}')" 
                                class="px-4 py-1.5 rounded-md transition {{ $filterKategori == $cat->id ? 'bg-surface-container-lowest text-primary shadow-sm' : 'text-on-surface-variant hover:text-primary' }}">
                            {{ $cat->nama_kategori }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-md">
            @forelse($events as $event)
                <div class="bg-surface-container-lowest shadow-card rounded-xl overflow-hidden flex flex-col h-full border border-outline-variant hover:shadow-md transition duration-200">
                    <div class="relative h-40 bg-surface-container">
                        <img src="{{ $event->flyer_url ?? 'https://images.unsplash.com/photo-1518770660439-4636190af475?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' }}" alt="Flyer" class="w-full h-full object-cover">
                        <span class="absolute top-3 right-3 bg-primary text-on-primary px-2 py-1 rounded text-caption uppercase tracking-wider">
                            {{ $event->kategori->nama_kategori ?? 'Umum' }}
                        </span>
                    </div>
                    <div class="p-lg flex flex-col flex-1 gap-sm">
                        <h3 class="text-title-md text-on-surface leading-snug line-clamp-2">{{ $event->nama_event }}</h3>
                        <div class="text-body-sm text-on-surface-variant flex flex-col gap-2">
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
                        <div class="flex justify-between items-end mt-auto pt-4 border-t border-outline-variant">
                            <div>
                                <div class="text-caption text-outline uppercase tracking-wide">Kuota</div>
                                @if($event->sisa_kuota <= 0)
                                    <div class="text-label-md text-error">Penuh (0/{{ $event->kuota }})</div>
                                @elseif($event->sisa_kuota <= 5)
                                    <div class="text-label-md text-error">Hampir Penuh ({{ $event->sisa_kuota }}/{{ $event->kuota }})</div>
                                @else
                                    <div class="text-label-md text-primary">{{ $event->sisa_kuota }}/{{ $event->kuota }}</div>
                                @endif
                            </div>
                            <button class="bg-primary text-on-primary px-4 py-2 rounded-lg text-label-md hover:bg-primary-container hover:text-on-primary-container transition">Lihat Detail</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 p-xl text-center text-on-surface-variant bg-surface-container-lowest border border-outline-variant rounded-xl">
                    <svg class="mx-auto h-12 w-12 text-outline mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p class="text-title-md text-on-surface">Event tidak ditemukan</p>
                    <p class="text-body-sm mt-1">Coba gunakan kata kunci pencarian yang lain.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-sm">
            {{ $events->links('livewire.pages.mahasiswa.pagination') }}
        </div>
    </section>

    {{-- Rekomendasi Event --}}
    <section x-data="{}" class="flex flex-col gap-md">
        <div class="flex justify-between items-center border-b border-outline-variant pb-xs">
            <div>
                <h2 class="text-headline-md text-primary">Rekomendasi Event</h2>
            </div>
            
            <div class="flex items-center gap-sm">
                {{-- Carousel Nav Buttons --}}
                <div class="flex gap-1.5">
                    <button @click="$refs.rekomendasiCarousel.scrollBy({ left: -320, behavior: 'smooth' })" 
                            class="p-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container hover:text-primary transition shadow-sm focus:outline-none">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button @click="$refs.rekomendasiCarousel.scrollBy({ left: 320, behavior: 'smooth' })" 
                            class="p-1.5 rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface-variant hover:bg-surface-container hover:text-primary transition shadow-sm focus:outline-none">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
                <a href="#" class="text-primary text-title-sm flex items-center gap-1 hover:underline ml-2">
                    Lihat Semua <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>

        <div x-ref="rekomendasiCarousel" class="flex overflow-x-hidden pb-4 gap-md no-scrollbar snap-x snap-mandatory scroll-smooth">
            @forelse($rekomendasi as $event)
                <div class="bg-surface-container-lowest shadow-card rounded-xl overflow-hidden flex flex-col h-full border border-outline-variant hover:shadow-md transition duration-200 flex-shrink-0 w-80 snap-start">
                    <div class="relative h-40 bg-surface-container">
                        <img src="{{ $event->flyer_url ?? 'https://images.unsplash.com/photo-1518770660439-4636190af475?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' }}" alt="Flyer" class="w-full h-full object-cover">
                        <span class="absolute top-3 right-3 bg-primary text-on-primary px-2 py-1 rounded text-caption uppercase tracking-wider">
                            {{ $event->kategori->nama_kategori ?? 'Umum' }}
                        </span>
                    </div>
                    <div class="p-lg flex flex-col flex-1 gap-sm">
                        <h3 class="text-title-md text-on-surface leading-snug line-clamp-2">{{ $event->nama_event }}</h3>
                        <div class="text-body-sm text-on-surface-variant flex flex-col gap-2">
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
                        <div class="flex justify-between items-end mt-auto pt-4 border-t border-outline-variant">
                            <div>
                                <div class="text-caption text-outline uppercase tracking-wide">Kuota</div>
                                @if($event->sisa_kuota <= 0)
                                    <div class="text-label-md text-error">Penuh (0/{{ $event->kuota }})</div>
                                @elseif($event->sisa_kuota <= 5)
                                    <div class="text-label-md text-error">Hampir Penuh ({{ $event->sisa_kuota }}/{{ $event->kuota }})</div>
                                @else
                                    <div class="text-label-md text-primary">{{ $event->sisa_kuota }}/{{ $event->kuota }}</div>
                                @endif
                            </div>
                            <button class="bg-primary text-on-primary px-4 py-2 rounded-lg text-label-md hover:bg-primary-container hover:text-on-primary-container transition">Lihat Detail</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="w-full p-xl text-center text-on-surface-variant bg-surface-container-lowest border border-outline-variant rounded-xl">
                    Belum ada rekomendasi event saat ini.
                </div>
            @endforelse
        </div>
    </section>
</div>