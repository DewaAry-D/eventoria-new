<?php

use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.mahasiswa')] class extends Component {
    public Event $event;

    public function mount(Event $event)
    {
        $this->event = $event->load(['kategori', 'timeLines', 'biayaEvents']);
    }

    public function getEmbedUrl($url)
    {
        if (!$url) return null;

        // Check if this is a Google Maps URL. If not, it's an online link, so don't try to embed it
        $isMapsUrl = str_contains($url, 'google.com/maps') || 
                     str_contains($url, 'maps.google') || 
                     str_contains($url, 'maps.app.goo.gl') || 
                     str_contains($url, 'goo.gl/maps');
                     
        if (!$isMapsUrl) {
            return null;
        }

        if (str_contains($url, 'output=embed') || str_contains($url, '/embed')) {
            return $url;
        }

        // If it's a short URL (maps.app.goo.gl or goo.gl/maps), resolve the redirect
        if (str_contains($url, 'maps.app.goo.gl') || str_contains($url, 'goo.gl/maps')) {
            $url = Cache::remember('map_url_' . md5($url), 86400, function() use ($url) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                $response = curl_exec($ch);
                curl_close($ch);
                
                if (preg_match('/^Location:\s+(.*)$/mi', $response, $matches)) {
                    return trim($matches[1]);
                }
                return $url;
            });
        }

        // Parse search path: /maps/search/Query
        if (preg_match('/\/maps\/search\/([^\/?#]+)/', $url, $matches)) {
            return "https://maps.google.com/maps?q=" . urlencode(urldecode($matches[1])) . "&t=&z=15&ie=UTF8&iwloc=&output=embed";
        }

        // Parse place in place path: /maps/place/PlaceName
        if (preg_match('/\/maps\/place\/([^\/@?#]+)/', $url, $matches)) {
            return "https://maps.google.com/maps?q=" . urlencode(urldecode($matches[1])) . "&t=&z=15&ie=UTF8&iwloc=&output=embed";
        }

        // Parse coordinates: @lat,lng
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
            return "https://maps.google.com/maps?q={$matches[1]},{$matches[2]}&t=&z=15&ie=UTF8&iwloc=&output=embed";
        }

        // Parse query parameter q
        $parsed = parse_url($url);
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $queryParts);
            if (isset($queryParts['q'])) {
                return "https://maps.google.com/maps?q=" . urlencode($queryParts['q']) . "&t=&z=15&ie=UTF8&iwloc=&output=embed";
            }
        }

        return "https://maps.google.com/maps?q=" . urlencode($url) . "&t=&z=15&ie=UTF8&iwloc=&output=embed";
    }

    public function with()
    {
        // Split timelines into registration (pendaftaran), execution (pelaksanaan), closing (penutupan)
        $pendaftaran = $this->event->timeLines->filter(function($t) {
            $name = strtolower($t->nama_timeline);
            return str_contains($name, 'daftar') || str_contains($name, 'registrasi');
        })->first();

        $pelaksanaan = $this->event->timeLines->filter(function($t) {
            $name = strtolower($t->nama_timeline);
            return str_contains($name, 'pelaksanaan') || str_contains($name, 'utama') || str_contains($name, 'mulai') || str_contains($name, 'acara');
        })->first();

        // If no execution timeline matches the above, take any timeline that is not registration
        if (!$pelaksanaan) {
            $pelaksanaan = $this->event->timeLines->filter(function($t) {
                $name = strtolower($t->nama_timeline);
                return !str_contains($name, 'daftar') && !str_contains($name, 'registrasi') && !str_contains($name, 'tutup') && !str_contains($name, 'selesai');
            })->first();
        }

        $penutupan = $this->event->timeLines->filter(function($t) {
            $name = strtolower($t->nama_timeline);
            return str_contains($name, 'tutup') || str_contains($name, 'selesai') || str_contains($name, 'akhir') || str_contains($name, 'penutupan');
        })->first();

        // Check registration deadline for countdown
        $daysRemaining = null;
        if ($pendaftaran && $pendaftaran->tanggal_selesai) {
            $daysRemaining = (int) now()->diffInDays($pendaftaran->tanggal_selesai, false);
        }

        // Determine if this is an online event
        $isOnline = false;
        $lokasiUrl = $this->event->lokasi_url;
        $namaLokasi = strtolower($this->event->nama_lokasi ?? '');

        if (str_contains($namaLokasi, 'online') || str_contains($namaLokasi, 'zoom') || str_contains($namaLokasi, 'meet') || str_contains($namaLokasi, 'daring')) {
            $isOnline = true;
        } elseif ($lokasiUrl && !str_contains($lokasiUrl, 'google.com/maps') && !str_contains($lokasiUrl, 'maps.google') && !str_contains($lokasiUrl, 'maps.app.goo.gl') && !str_contains($lokasiUrl, 'goo.gl/maps')) {
            $isOnline = true;
        }

        // Check registration status of current student
        $user = Auth::user();
        $isRegistered = false;
        $registrationStatus = null;

        if ($user && $user->mahasiswa) {
            $registration = \App\Models\EventRegistration::where('event_id', $this->event->id)
                ->where('mahasiswa_id', $user->mahasiswa->id)
                ->first();

            if ($registration) {
                $isRegistered = true;
                $registrationStatus = $registration->status_pendaftaran->value ?? (string)$registration->status_pendaftaran;
            }
        }

        return [
            'pendaftaran' => $pendaftaran,
            'pelaksanaan' => $pelaksanaan,
            'penutupan' => $penutupan,
            'daysRemaining' => $daysRemaining,
            'embedUrl' => $this->getEmbedUrl($this->event->lokasi_url),
            'isOnline' => $isOnline,
            'isRegistered' => $isRegistered,
            'registrationStatus' => $registrationStatus,
        ];
    }
}; ?>

<div class="space-y-6">
    {{-- Top Action & Category Badge --}}
    <div class="flex items-center justify-between pb-2">
        <a href="{{ route('mahasiswa.dashboard') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:text-primary-container transition group">
            <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Jelajah
        </a>
        <span class="px-4 py-1.5 text-[10px] font-bold text-primary bg-primary-fixed rounded-full uppercase tracking-wider">
            {{ $event->kategori->nama_kategori ?? 'Akademik' }}
        </span>
    </div>

    {{-- Main Column Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left Content Column (Span 2) --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Banner Card --}}
            <div class="relative h-[250px] sm:h-[350px] md:h-[400px] w-full rounded-2xl overflow-hidden shadow-md group">
                <img src="{{ asset('storage/' . $event->flyer_url) }}" 
                     alt="{{ $event->nama_event }}" 
                     class="w-full h-full object-cover">
                
                {{-- Dark gradient overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
                
                {{-- Title Overlay --}}
                <div class="absolute bottom-0 left-0 right-0 p-6 md:p-8 text-white space-y-2">
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight leading-tight">
                        {{ $event->nama_event }}
                    </h1>
                    <div class="flex items-center gap-2 text-xs md:text-sm text-gray-200">
                        <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>{{ $event->penyelenggara }}</span>
                    </div>
                </div>
            </div>

            {{-- Full Info Card --}}
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 md:p-8 shadow-sm space-y-6">
                
                {{-- Deskripsi Lengkap --}}
                <div class="space-y-3">
                    <h2 class="text-sm font-bold text-primary tracking-wide uppercase">Deskripsi Lengkap</h2>
                    <div class="text-sm text-on-surface-variant leading-relaxed space-y-3">
                        {!! nl2br(e($event->deskripsi)) !!}
                    </div>
                </div>

                <hr class="border-outline-variant">

                {{-- Rincian Biaya --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-sm font-bold text-primary tracking-wide uppercase">
                        <svg class="w-5 h-5 text-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span>Rincian Biaya</span>
                    </div>

                    <div class="bg-surface-container-low rounded-xl p-5 border border-outline-variant/50">
                        <div class="space-y-2">
                            @forelse($event->biayaEvents as $biaya)
                                <div class="flex justify-between items-center text-sm py-1.5 {{ !$loop->first ? 'border-t border-outline-variant/30' : '' }}">
                                    <span class="text-on-surface-variant font-medium">{{ $biaya->kategori }}</span>
                                    <span class="font-bold text-primary">
                                        {{ $biaya->biaya == 0 ? 'Gratis' : 'Rp ' . number_format($biaya->biaya, 0, ',', '.') }}
                                    </span>
                                </div>
                            @empty
                                <div class="flex justify-between items-center text-sm py-1.5">
                                    <span class="text-on-surface-variant font-medium">Mahasiswa Aktif</span>
                                    <span class="font-bold text-primary">Gratis</span>
                                </div>
                                <div class="flex justify-between items-center text-sm py-1.5 border-t border-outline-variant/30">
                                    <span class="text-on-surface-variant font-medium">Alumni & Civitas</span>
                                    <span class="font-bold text-primary">Rp 25.000</span>
                                </div>
                                <div class="flex justify-between items-center text-sm py-1.5 border-t border-outline-variant/30">
                                    <span class="text-on-surface-variant font-medium">Umum / Profesional</span>
                                    <span class="font-bold text-primary">Rp 50.000</span>
                                </div>
                            @endforelse
                        </div>


                    </div>
                </div>

                {{-- Lokasi & Kuota --}}
                <div class="grid grid-cols-2 gap-6 pt-2">
                    <div>
                        <span class="text-[10px] text-outline uppercase tracking-wider font-bold block">LOKASI</span>
                        @if($event->lokasi_url)
                            <a href="{{ $event->lokasi_url }}" target="_blank" class="inline-flex items-center gap-1.5 text-sm font-bold text-primary hover:underline mt-1.5 transition">
                                <svg class="w-4 h-4 text-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $event->nama_lokasi ?? 'Aula Gadjah Mada' }}
                            </a>
                        @else
                            <div class="flex items-center gap-1.5 text-sm font-bold text-primary mt-1.5">
                                <svg class="w-4 h-4 text-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $event->nama_lokasi ?? 'Aula Gadjah Mada' }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <span class="text-[10px] text-outline uppercase tracking-wider font-bold block">SISA KUOTA</span>
                        <div class="flex items-center gap-1.5 text-sm font-bold text-primary mt-1.5">
                            <svg class="w-4 h-4 text-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            {{ $event->sisa_kuota }} / {{ $event->kuota }}
                        </div>
                    </div>
                </div>

                <hr class="border-outline-variant">

                {{-- Combined Timeline Section --}}
                <div class="space-y-5">
                    <h3 class="flex items-center gap-2 text-sm font-bold text-primary tracking-wide uppercase">
                        <svg class="w-5 h-5 text-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Timeline Event
                    </h3>
                    
                    <div class="relative border-l-2 border-primary/20 ml-3 pl-6 space-y-6">
                        
                        {{-- Pendaftaran --}}
                        <div class="relative">
                            <div class="absolute -left-[31px] top-1 bg-white border-2 border-primary rounded-full w-4 h-4 shadow-sm"></div>
                            <span class="text-sm font-bold text-primary block">Pendaftaran</span>
                            <span class="text-xs text-on-surface-variant block mt-1">
                                @if($pendaftaran)
                                    {{ $pendaftaran->tanggal_mulai?->format('d M Y') }} - {{ $pendaftaran->tanggal_selesai?->format('d M Y') }}
                                @else
                                    10 Juni - 15 Juni 2026
                                @endif
                            </span>
                        </div>

                        {{-- Pelaksanaan --}}
                        <div class="relative">
                            <div class="absolute -left-[31px] top-1 bg-white border-2 border-primary rounded-full w-4 h-4 shadow-sm"></div>
                            <span class="text-sm font-bold text-primary block">Pelaksanaan</span>
                            <span class="text-xs text-on-surface-variant block mt-1">
                                @if($pelaksanaan)
                                    {{ $pelaksanaan->tanggal_mulai?->format('d M Y (H:i)') }} - {{ $pelaksanaan->tanggal_selesai?->format('d M Y (H:i)') }}
                                @else
                                    18 Juni 2026 (09:00 - 16:30)
                                @endif
                            </span>
                        </div>

                        {{-- Penutupan --}}
                        <div class="relative">
                            <div class="absolute -left-[31px] top-1 bg-white border-2 border-primary rounded-full w-4 h-4 shadow-sm"></div>
                            <span class="text-sm font-bold text-primary block">Penutupan</span>
                            <span class="text-xs text-on-surface-variant block mt-1">
                                @if($penutupan)
                                    {{ $penutupan->tanggal_selesai?->format('d M Y (H:i)') }}
                                @elseif($pelaksanaan)
                                    {{ $pelaksanaan->tanggal_selesai?->format('d M Y (H:i)') }}
                                @else
                                    19 Juni 2026 (16:30)
                                @endif
                            </span>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Speaker Profile Card --}}
            <div class="space-y-4">
                <h2 class="text-sm font-bold text-primary tracking-wide uppercase">Narasumber Utama</h2>
                <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 flex items-center gap-4 shadow-sm">
                    <img class="w-14 h-14 rounded-full object-cover bg-surface-container-low border border-outline-variant shadow-sm" 
                         src="{{ $event->narasumber ? 'https://ui-avatars.com/api/?name='.urlencode($event->narasumber).'&background=e0e0ff&color=000666&size=128' : 'https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&auto=format&fit=crop&w=256&q=80' }}" 
                         alt="{{ $event->narasumber ?? 'Dr. Aris Thorne' }}">
                    <div>
                        <h3 class="text-sm font-bold text-primary">{{ $event->narasumber ?? 'Dr. Aris Thorne' }}</h3>
                        <p class="text-xs text-on-surface-variant mt-0.5">{{ $event->narasumber_title ?? 'Kepala Pusat Studi Etika Digital & Kecerdasan Buatan' }}</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Column (Sidebar, Span 1) --}}
        <div class="space-y-6">
            
            {{-- Countdown & CTA Box --}}
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-2 text-sm font-bold text-primary">
                    <svg class="w-5 h-5 text-outline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>
                        {{ $daysRemaining !== null ? ($daysRemaining > 0 ? "$daysRemaining Hari Lagi" : "Pendaftaran Ditutup") : '2 Hari Lagi' }}
                    </span>
                </div>
                
                <p class="text-xs text-on-surface-variant leading-relaxed">
                    Pendaftaran masih dibuka. Segera amankan kursi Anda untuk mendapatkan sertifikat elektronik dan snack box.
                </p>
                
                @if($isRegistered)
                    <button disabled 
                            class="block w-full py-3 text-center bg-outline-variant text-on-surface-variant font-bold rounded-xl text-xs cursor-not-allowed select-none">
                        Sudah Terdaftar ({{ ucfirst($registrationStatus) }})
                    </button>
                @elseif($event->sisa_kuota <= 0)
                    <button disabled 
                            class="block w-full py-3 text-center bg-outline-variant text-on-surface-variant font-bold rounded-xl text-xs cursor-not-allowed select-none">
                        Kuota Penuh
                    </button>
                @else
                    <a href="{{ route('mahasiswa.event-register', $event->slug) }}" wire:navigate 
                       class="block w-full py-3 text-center bg-primary hover:bg-primary-container text-white font-bold rounded-xl text-xs transition duration-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                        Daftar Sekarang
                    </a>
                @endif
            </div>

            {{-- Location Sketch Box / Map --}}
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-primary tracking-wide uppercase">Lokasi Kegiatan</h3>
                
                @if(!$isOnline)
                    <div class="rounded-xl overflow-hidden border border-outline-variant bg-surface-container-low h-48 relative">
                        {{-- Embed Google Maps using the URL from database --}}
                        <iframe 
                            class="w-full h-full border-0" 
                            src="{{ $embedUrl ?? 'https://maps.google.com/maps?q=' . urlencode($event->nama_lokasi) . '&t=&z=15&ie=UTF8&iwloc=&output=embed' }}" 
                            allowfullscreen 
                            loading="lazy">
                        </iframe>
                    </div>
                @else
                    <div class="rounded-xl overflow-hidden border border-outline-variant bg-surface-container-low h-48 flex flex-col items-center justify-center text-center p-4">
                        <svg class="w-12 h-12 text-outline mb-2 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span class="text-sm font-bold text-primary">Kegiatan Daring (Online)</span>
                        
                        @if($isRegistered && in_array($registrationStatus, ['approved', 'completed']))
                            <span class="text-xs text-on-surface-variant mt-1">Silakan gabung melalui link di bawah:</span>
                            @if($event->lokasi_url)
                                <a href="{{ $event->lokasi_url }}" target="_blank" class="mt-3.5 px-4 py-2 bg-primary hover:bg-primary-container text-white font-bold rounded-lg text-[10px] inline-flex items-center gap-1.5 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Gabung Zoom / Meet
                                </a>
                            @endif
                        @elseif($isRegistered && $registrationStatus === 'pending')
                            <span class="text-xs text-warning font-semibold mt-2.5 px-3 py-1.5 bg-warning bg-opacity-5 rounded-lg border border-warning border-opacity-20">
                                Link akses akan aktif setelah pendaftaran Anda disetujui.
                            </span>
                        @elseif($isRegistered && $registrationStatus === 'rejected')
                            <span class="text-xs text-error font-semibold mt-2.5 px-3 py-1.5 bg-error bg-opacity-5 rounded-lg border border-error border-opacity-20">
                                Pendaftaran Anda ditolak. Akses link dinonaktifkan.
                            </span>
                        @else
                            <span class="text-xs text-outline font-semibold mt-2.5 px-3 py-1.5 bg-surface-container rounded-lg border border-outline-variant">
                                Daftar kegiatan untuk melihat link akses.
                            </span>
                        @endif
                    </div>
                @endif

                <div class="flex flex-col gap-2">
                    <div class="flex items-start gap-2 text-xs text-on-surface-variant leading-relaxed">
                        <svg class="w-4 h-4 text-outline flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ $event->nama_lokasi ?? 'Aula Gadjah Mada' }}</span>
                    </div>
                    @if($event->lokasi_url)
                        @if($isOnline)
                            @if($isRegistered && in_array($registrationStatus, ['approved', 'completed']))
                                <a href="{{ $event->lokasi_url }}" target="_blank" class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1">
                                    Buka Link Pertemuan 
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            @else
                                <span class="text-xs text-outline italic inline-flex items-center gap-1 select-none">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Link Pertemuan Terkunci
                                </span>
                            @endif
                        @else
                            <a href="{{ $event->lokasi_url }}" target="_blank" class="text-xs font-bold text-primary hover:underline inline-flex items-center gap-1">
                                Buka di Google Maps 
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Verified Badge Box --}}
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl px-6 py-4.5 shadow-sm flex justify-between items-center">
                <span class="text-xs font-bold text-on-surface-variant">STATUS KEGIATAN</span>
                <span class="px-3.5 py-1 bg-success bg-opacity-10 text-success text-[10px] font-bold rounded-full uppercase tracking-wider">
                    Terverifikasi
                </span>
            </div>

        </div>

    </div>
</div>
