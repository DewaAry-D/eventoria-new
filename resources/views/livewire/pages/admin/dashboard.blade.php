<?php

use App\Models\Event;
use App\Models\OrganisasiMahasiswa;
use App\Models\AdminDpm;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] class extends Component 
{
    public function with(): array
    {
        $user = Auth::user();

        $adminProfile = AdminDpm::where('user_id', $user->id)->first();
        $adminFakultasId = $adminProfile ? $adminProfile->fakultas_id : null;

        // =========================================================
        // 1. BASE QUERY: FILTER WEWENANG ADMIN
        // =========================================================
        
        $baseOrgQuery = OrganisasiMahasiswa::query();
        
        $baseEventQuery = Event::whereHas('organisasi', function ($q) use ($adminFakultasId) {
            // Gunakan variabel $adminFakultasId yang baru
            if ($adminFakultasId) {
                $q->where('fakultas_id', $adminFakultasId);
            } else {
                $q->where('tingkat_organisasi', 'universitas');
            }
        });

        if ($adminFakultasId) {
            $baseOrgQuery->where('fakultas_id', $adminFakultasId);
        } else {
            $baseOrgQuery->where('tingkat_organisasi', 'universitas');
        }

        // dd([
        //     '1_ID_Fakultas_Admin' => $admin->fakultas_id,
        //     '2_Total_Event_Asli_Di_DB' => Event::count(),
        //     '3_Total_Event_Setelah_Difilter' => (clone $baseEventQuery)->count(),
        //     '4_Total_Org_Asli_Di_DB' => OrganisasiMahasiswa::count(),
        //     '5_Total_Org_Setelah_Difilter' => (clone $baseOrgQuery)->count(),
        // ]);

        // =========================================================
        // 2. MENGHITUNG STATISTIK UTAMA (Gunakan clone agar query tidak tertumpuk)
        // =========================================================

        $totalEvents = (clone $baseEventQuery)->count() > 0 ? (clone $baseEventQuery)->count() : 1; 

        $approvedEvents = (clone $baseEventQuery)->whereIn('status', ['published', 'completed'])->count();
        $pendingEvents = (clone $baseEventQuery)->where('status', 'pending_approval')->count();
        $rejectedEvents = (clone $baseEventQuery)->where('status', 'revision')->count();

        // DATA CHART: Jumlah Event Per Bulan
        $chartData = [];
        $maxEventCount = 1; 
        $monthlyCounts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subMonths($i);
            $count = (clone $baseEventQuery)
              ->whereMonth('created_at', $date->month)
              ->whereYear('created_at', $date->year)
              ->count();
            
            $monthlyCounts[] = [
                'bulan' => $date->translatedFormat('M'),
                'count' => $count,
                'is_current' => $i === 0
            ];
            if ($count > $maxEventCount) $maxEventCount = $count;
        }

        // foreach ($monthlyCounts as $data) {
        //     $percentage = round(($data['count'] / $maxEventCount) * 100);
        //     $height = $percentage > 0 ? $percentage : 5; 
        //     $chartData[] = [
        //         'bulan' => $data['bulan'],
        //         'tinggi' => "h-[{$height}%]",
        //         'is_current' => $data['is_current'],
        //         'jumlah' => $data['count']
        //     ];
        // }
        foreach ($monthlyCounts as $data) {
            $percentage = round(($data['count'] / $maxEventCount) * 100);
            $height = $percentage > 0 ? $percentage : 5; // Minimal 5% agar bar terlihat sedikit
            $chartData[] = [
                'bulan' => $data['bulan'],
                'tinggi' => $height, // Hanya kirimkan angka, bukan string class Tailwind
                'is_current' => $data['is_current'],
                'jumlah' => $data['count']
            ];
        }

        // DATA SOROTAN: Bulan Ini
        $currentMonth = \Carbon\Carbon::now()->month;
        $currentYear = \Carbon\Carbon::now()->year;

        $kategoriPopuler = (clone $baseEventQuery)
            ->select('kategori_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('kategori_id')
            ->orderByDesc('total')
            ->first();

        $orgTeraktif = (clone $baseEventQuery)
            ->select('organisasi_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('organisasi_id')
            ->orderByDesc('total')
            ->first();

        // DATA TREN: Organisasi Aktif (Mingguan)
        $trendPoints = [];
        $maxOrgTrend = 1;
        $xCoords = [0, 33.3, 66.6, 100]; 
        
        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = \Carbon\Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = \Carbon\Carbon::now()->subWeeks($i)->endOfWeek();
            
            $count = (clone $baseOrgQuery)
                        ->where('status', 'approved')
                        ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                        ->count();
                        
            if ($count > $maxOrgTrend) $maxOrgTrend = $count;
            $trendPoints[] = $count;
        }

        $svgPoints = [];
        foreach($trendPoints as $index => $count) {
            $y = 35 - (($count / $maxOrgTrend) * 30);
            $svgPoints[] = ['x' => $xCoords[$index], 'y' => $y];
        }

        $svgLine = "M {$svgPoints[0]['x']} {$svgPoints[0]['y']} L {$svgPoints[1]['x']} {$svgPoints[1]['y']} L {$svgPoints[2]['x']} {$svgPoints[2]['y']} L {$svgPoints[3]['x']} {$svgPoints[3]['y']}";
        $svgArea = "{$svgLine} L 100 40 L 0 40 Z";

        return [
            // Return dengan base query yang sudah ter-filter
            'org_aktif' => (clone $baseOrgQuery)->where('status', 'approved')->count(),
            'event_berlangsung' => $approvedEvents,
            'pengajuan_org' => (clone $baseOrgQuery)->where('status', 'pending')->count(),
            'pengajuan_event' => $pendingEvents,
            
            'recent_events' => (clone $baseEventQuery)->with('organisasi')->where('status', 'pending_approval')->latest()->take(5)->get(),
            'new_orgs' => (clone $baseOrgQuery)->where('status', 'pending')->latest()->take(3)->get(),
            
            'stats' => [
                'disetujui' => round(($approvedEvents / $totalEvents) * 100),
                'menunggu' => round(($pendingEvents / $totalEvents) * 100),
                'ditolak' => round(($rejectedEvents / $totalEvents) * 100),
            ],
            
            'chartData' => $chartData,
            
            'sorotan' => [
                'kategori' => $kategoriPopuler && $kategoriPopuler->kategori ? $kategoriPopuler->kategori->nama_kategori : 'Belum Ada',
                'org' => $orgTeraktif && $orgTeraktif->organisasi ? $orgTeraktif->organisasi->nama_organisasi : 'Belum Ada',
            ],
            
            'trenSvgLine' => $svgLine,
            'trenSvgArea' => $svgArea,
            'trenSvgPoints' => $svgPoints
        ];
    }

    public function unduhLaporan()
    {
        $user = Auth::user();
        $adminProfile = AdminDpm::where('user_id', $user->id)->first();
        $adminFakultasId = $adminProfile ? $adminProfile->fakultas_id : null;
        
        $events = Event::with('organisasi')->whereHas('organisasi', function ($q) use ($adminFakultasId) {
            if ($adminFakultasId) {
                $q->where('fakultas_id', $adminFakultasId);
            } else {
                $q->where('tingkat_organisasi', 'universitas');
            }
        })->latest()->get();
        
        $csvData = "ID Event,Nama Event,Penyelenggara,Status,Tanggal Pengajuan,Tingkat\n";
        
        foreach($events as $event) {
            $orgName = $event->organisasi->nama_organisasi ?? 'Tidak Diketahui';
            $namaEvent = str_replace(',', ' ', $event->nama_event); 
            $status = strtoupper($event->status->value);
            $tanggal = $event->created_at->format('Y-m-d');
            $tingkat = strtoupper($event->tingkat_event->value);
            
            $csvData .= "{$event->id},{$namaEvent},{$orgName},{$status},{$tanggal},{$tingkat}\n";
        }

        $tingkatAdmin = $adminFakultasId ? 'Fakultas_' . $adminFakultasId : 'Universitas';
        $fileName = 'Laporan_Aktivitas_' . $tingkatAdmin . '_' . date('Y_m_d') . '.csv';

        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, $fileName);
    }

    public $showApproveModal = false;
    public $showRejectModal = false;
    public $selectedEventId = null;
    public $selectedEventName = '';
    public $alasanPenolakan = '';

    // Membuka Modal Setuju
    public function confirmApprove($id, $name)
    {
        $this->selectedEventId = $id;
        $this->selectedEventName = $name;
        $this->showApproveModal = true;
    }

    // Membuka Modal Tolak
    public function confirmReject($id, $name)
    {
        $this->selectedEventId = $id;
        $this->selectedEventName = $name;
        $this->alasanPenolakan = ''; // Reset form alasan
        $this->showRejectModal = true;
    }

    // Menutup Semua Modal
    public function closeModal()
    {
        $this->showApproveModal = false;
        $this->showRejectModal = false;
        $this->selectedEventId = null;
        $this->selectedEventName = '';
        $this->alasanPenolakan = '';
        $this->resetErrorBag();
    }

    // Eksekusi Persetujuan Event
    public function approveEvent()
    {
        if ($this->selectedEventId) {
            $event = Event::find($this->selectedEventId);
            if ($event) {
                $user = Auth::user();

                $adminProfile = AdminDpm::where('user_id', $user->id)->first();

                $event->update([
                    'status' => 'published',
                    'admin_acc_id' => $adminProfile->id
                ]); 
                session()->flash('success', "Event '{$this->selectedEventName}' berhasil disetujui!");
            }
        }
        $this->closeModal();
    }

    // Eksekusi Penolakan Event
    public function rejectEvent()
    {
        $this->validate([
            'alasanPenolakan' => 'required|string|min:5'
        ], [
            'alasanPenolakan.required' => 'Alasan penolakan wajib diisi untuk perbaikan.',
            'alasanPenolakan.min' => 'Alasan penolakan terlalu singkat.'
        ]);

        if ($this->selectedEventId) {
            $event = Event::find($this->selectedEventId);
            if ($event) {
                // Ubah status menjadi revision (ditolak/revisi) dan simpan alasannya
                $event->update([
                    'status' => 'revision', 
                    'catatan_revisi' => $this->alasanPenolakan
                ]);
                session()->flash('success', "Event '{$this->selectedEventName}' dikembalikan untuk direvisi.");
            }
        }
        $this->closeModal();
    }

    public $showDetailModal = false;
    public $detailEvent = null;

    // Fungsi untuk memuat data dan membuka modal detail
    public function showEventDetail($id)
    {
        // Pastikan Model Event Anda memiliki relasi bernama 'timelines' dan 'biayas'
        $this->detailEvent = Event::with([
            'organisasi', 
            'kategori', 
            'timelines' => function($query) {
                $query->orderBy('tanggal_mulai', 'asc'); // Urutkan jadwal dari awal ke akhir
            }, 
            'biayaevents'
        ])->find($id);
        
        if ($this->detailEvent) {
            $this->showDetailModal = true;
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->detailEvent = null;
    }
}; ?>

<div class="max-w-7xl mx-auto space-y-6">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#000666]">Dashboard Overview</h1>
            <p class="text-gray-500 text-sm mt-1">Selamat datang kembali, mari kelola aktivitas kampus hari ini.</p>
        </div>
        <button wire:click="unduhLaporan" class="px-5 py-2.5 bg-[#000666] text-white font-medium rounded-lg hover:bg-blue-900 transition flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Unduh Laporan
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-gray-100 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-blue-50 text-[#000666] rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg> +12%
                </span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-[#000666]">{{ $org_aktif }}</h3>
                <p class="text-sm text-gray-500 font-medium">Organisasi Aktif</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-gray-100 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-blue-50 text-[#000666] rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-[#000666]">{{ $event_berlangsung }}</h3>
                <p class="text-sm text-gray-500 font-medium">Event Berlangsung</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-gray-100 flex flex-col justify-between relative overflow-hidden">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-gray-50 text-gray-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-full uppercase">Pending</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-gray-800">{{ $pengajuan_org }}</h3>
                <p class="text-sm text-gray-500 font-medium">Pengajuan Organisasi</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-gray-100 flex flex-col justify-between">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2.5 bg-red-50 text-red-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <span class="text-[10px] font-bold text-red-600 bg-red-100 px-2 py-1 rounded-full uppercase tracking-wider">High Priority</span>
            </div>
            <div>
                <h3 class="text-3xl font-bold text-red-600">{{ $pengajuan_event }}</h3>
                <p class="text-sm text-gray-500 font-medium">Pengajuan Event</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-gray-100 h-[350px] flex flex-col">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-[#000666]">Jumlah Event Per Bulan</h2>
                        <p class="text-xs text-gray-500 mt-1">Data pertumbuhan event selama 6 bulan terakhir</p>
                    </div>
                    <button class="p-2 bg-gray-50 rounded text-gray-500 hover:text-[#000666]"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg></button>
                </div>
                
                {{-- <div class="flex-1 flex items-end gap-2 md:gap-4 justify-between mt-4">
                    @foreach($chartData as $data)
                        <div class="flex flex-col items-center w-full h-full justify-end group">
                            
                            <span class="text-[10px] font-bold text-gray-400 group-hover:text-[#000666] mb-1.5 transition duration-300">
                                {{ $data['jumlah'] }}
                            </span>
                            
                            <div class="w-full rounded-t-sm transition-all duration-300 {{ $data['is_current'] ? 'bg-[#000666]' : 'bg-[#8E9BCE] group-hover:bg-[#000666]/70' }} {{ $data['tinggi'] }}"></div>
                            
                            <span class="text-xs text-gray-500 mt-3 font-medium">{{ $data['bulan'] }}</span>
                        </div>
                    @endforeach
                </div> --}}
                <div class="h-48 w-full flex items-end gap-2 md:gap-4 justify-between mt-6">
                    @foreach($chartData as $data)
                        <div class="flex flex-col items-center w-full h-full justify-end group">
                            
                            <span class="text-[10px] font-bold text-gray-400 group-hover:text-[#000666] mb-1.5 transition duration-300">
                                {{ $data['jumlah'] }}
                            </span>
                            
                            <div class="w-full rounded-t-sm transition-all duration-300 {{ $data['is_current'] ? 'bg-[#000666]' : 'bg-[#8E9BCE] group-hover:bg-[#000666]/70' }}" 
                                style="height: {{ $data['tinggi'] }}%;">
                            </div>
                            
                            <span class="text-xs text-gray-500 mt-3 font-medium">{{ $data['bulan'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-gray-100">
                    <h2 class="text-sm font-bold text-[#000666] mb-4">Statistik Pengajuan Event</h2>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4">
                            <span class="text-xs text-gray-500 w-16">Disetujui</span>
                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#000666] rounded-full" style="width: {{ $stats['disetujui'] }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-[#000666] w-8 text-right">{{ $stats['disetujui'] }}%</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-xs text-gray-500 w-16">Menunggu</span>
                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#8E9BCE] rounded-full" style="width: {{ $stats['menunggu'] }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-700 w-8 text-right">{{ $stats['menunggu'] }}%</span>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-xs text-gray-500 w-16">Ditolak</span>
                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-red-400 rounded-full" style="width: {{ $stats['ditolak'] }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-700 w-8 text-right">{{ $stats['ditolak'] }}%</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-gray-100 flex flex-col">
                    <h2 class="text-sm font-bold text-[#000666] mb-2">Tren Organisasi Aktif</h2>
                    <div class="flex-1 flex items-center justify-center relative">
                        <svg class="w-full h-24 text-[#000666]" preserveAspectRatio="none" viewBox="0 0 100 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="{{ $trenSvgLine }}" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            
                            @foreach($trenSvgPoints as $point)
                                <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="2" fill="currentColor"/>
                            @endforeach
                            
                            <path d="{{ $trenSvgArea }}" fill="url(#paint0_linear)" opacity="0.1"/>
                            
                            <defs>
                                <linearGradient id="paint0_linear" x1="50" y1="5" x2="50" y2="40" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#000666"/>
                                    <stop offset="1" stop-color="#000666" stop-opacity="0"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <div class="flex justify-between text-[10px] text-gray-400 mt-2">
                        <span>Minggu 1</span>
                        <span>Minggu 2</span>
                        <span>Minggu 3</span>
                        <span>Minggu 4</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-[#F8F9FE] p-6 rounded-2xl border border-indigo-50">
                <h2 class="text-sm font-bold text-[#000666] mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                    Sorotan Aktivitas
                </h2>
                <div class="space-y-3">
                    <div class="bg-white p-4 rounded-xl shadow-sm flex items-center justify-between group cursor-pointer hover:border-indigo-200 border border-transparent transition">
                        <div class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-red-500 mt-2"></div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">Kategori Event Terpopuler</p>
                                <p class="text-xs text-gray-500 mt-0.5"><span class="font-semibold text-indigo-700">{{ $sorotan['kategori'] }}</span> mendominasi bulan ini</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                    
                    <div class="bg-white p-4 rounded-xl shadow-sm flex items-center justify-between group cursor-pointer hover:border-indigo-200 border border-transparent transition">
                        <div class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-[#000666] mt-2"></div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">Organisasi Teraktif</p>
                                <p class="text-xs text-gray-500 mt-0.5"><span class="font-semibold text-indigo-700">{{ $sorotan['org'] }}</span> paling banyak upload event</p>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-gray-100">
                <h2 class="text-base font-bold text-gray-900 mb-5">Organisasi Baru</h2>
                
                <div class="space-y-4">
                    @forelse($new_orgs as $org)
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200 overflow-hidden">
                            @if($org->logo_url)
                                <img src="{{ asset('storage/' . $org->logo_url) }}" class="w-full h-full object-cover">
                            @else
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">{{ $org->nama_organisasi }}</h4>
                            <p class="text-xs text-gray-500 mt-0.5">{{ Str::title($org->tingkat_organisasi->value ?? 'Organisasi') }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 italic">Belum ada pendaftaran baru.</p>
                    @endforelse
                </div>

                <button class="w-full mt-6 py-2.5 bg-gray-50 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-100 transition border border-gray-200">
                    Lihat Semua Antrean
                </button>
            </div>

        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07)] border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-gray-900">Pengajuan Event Terbaru</h2>
            <a href="#" class="text-sm font-bold text-[#000666] hover:underline">Lihat Semua</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="pb-3 font-medium">Nama Event</th>
                        <th class="pb-3 font-medium">Organisasi</th>
                        <th class="pb-3 font-medium">Tanggal Pengajuan</th>
                        <th class="pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-50">
                    @forelse($recent_events as $ev)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-4 font-bold text-gray-900 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            {{ $ev->nama_event }}
                        </td>
                        <td class="py-4 text-gray-600">{{ $ev->organisasi->nama_organisasi ?? '-' }}</td>
                        <td class="py-4 text-gray-600">{{ $ev->created_at->format('d M Y') }}</td>
                        <td class="py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Menunggu
                            </span>
                        </td>
                        <td class="py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="confirmApprove({{ $ev->id }}, '{{ addslashes($ev->nama_event) }}')" title="Setujui" class="p-1.5 text-green-600 hover:bg-green-50 rounded-full transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </button>
                                
                                <button wire:click="confirmReject({{ $ev->id }}, '{{ addslashes($ev->nama_event) }}')" title="Tolak" class="p-1.5 text-red-600 hover:bg-red-50 rounded-full transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </button>
                                
                                {{-- <button title="Lihat Detail" class="p-1.5 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-full transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button> --}}
                                <button wire:click="showEventDetail({{ $ev->id }})" title="Lihat Detail" class="p-1.5 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-full transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500 text-sm">Tidak ada antrean pengajuan event saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-5 right-5 bg-gray-900 text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3 z-50">
            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if($showApproveModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center mx-4 transform transition-all">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 mb-2">Setujui Event?</h3>
                <p class="text-sm text-gray-500 mb-8 leading-relaxed">
                    Apakah Anda yakin ingin menyetujui pendaftaran event <span class="font-bold text-gray-800">{{ $selectedEventName }}</span>? Event ini akan segera aktif di platform.
                </p>
                
                <div class="flex items-center justify-center gap-3">
                    <button wire:click="closeModal" wire:loading.attr="disabled" class="flex-1 py-2.5 px-4 bg-white text-gray-700 font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button wire:click="approveEvent" wire:loading.attr="disabled" class="flex-1 py-2.5 px-4 bg-[#16A34A] hover:bg-green-700 text-white font-semibold rounded-xl transition shadow-sm flex justify-center items-center">
                        <span wire:loading.remove wire:target="approveEvent">Ya, Setujui</span>
                        <span wire:loading wire:target="approveEvent">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showRejectModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm transition-opacity">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 mx-4 transform transition-all">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tolak Event?</h3>
                    <p class="text-sm text-gray-500 leading-relaxed px-4">
                        Berikan alasan penolakan agar panitia <span class="font-bold text-gray-800">{{ $selectedEventName }}</span> dapat melakukan perbaikan.
                    </p>
                </div>
                
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-900 mb-2">Alasan Penolakan</label>
                    <textarea wire:model="alasanPenolakan" rows="4" class="w-full bg-[#FAFAFA] border border-gray-200 rounded-xl text-sm p-3 focus:ring-red-500 focus:border-red-500 placeholder-gray-400" placeholder="Tulis alasan di sini..."></textarea>
                    <x-input-error :messages="$errors->get('alasanPenolakan')" class="mt-1" />
                    
                    <div class="flex items-start gap-1.5 mt-3">
                        <svg class="w-3.5 h-3.5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-[10px] text-gray-400">Alasan ini akan dikirimkan ke email pendaftar sebagai feedback.</p>
                    </div>
                </div>
                
                <div class="flex items-center justify-center gap-3">
                    <button wire:click="closeModal" wire:loading.attr="disabled" class="flex-1 py-2.5 px-4 bg-white text-gray-700 font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                        Batal
                    </button>
                    <button wire:click="rejectEvent" wire:loading.attr="disabled" class="flex-1 py-2.5 px-4 bg-[#DC2626] hover:bg-red-700 text-white font-semibold rounded-xl transition shadow-sm flex justify-center items-center">
                        <span wire:loading.remove wire:target="rejectEvent">Kirim Penolakan</span>
                        <span wire:loading wire:target="rejectEvent">Mengirim...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($showDetailModal && $detailEvent)
        <div class="fixed inset-0 z-[110] flex items-center justify-center bg-gray-900/70 backdrop-blur-sm transition-opacity p-4 sm:p-6">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[95vh] flex flex-col overflow-hidden relative">
                
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-white z-10">
                    <div class="flex items-center gap-4">
                        <button wire:click="closeDetailModal" class="p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-full transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 leading-none">Pratinjau Detail Event</h2>
                            <p class="text-xs text-gray-500 mt-1">ID Pengajuan: EVT-{{ $detailEvent->created_at->format('Y') }}-{{ str_pad($detailEvent->id, 3, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                    
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-bold tracking-wide uppercase bg-gray-100 text-gray-600 border border-gray-200">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ str_replace('_', ' ', $detailEvent->status?->value ?? 'Menunggu') }}
                    </span>
                </div>

                <div class="flex-1 overflow-y-auto p-6 bg-white">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        
                        <div class="lg:col-span-5 space-y-6">
                            <div class="relative w-full aspect-[4/5] bg-gray-900 rounded-2xl overflow-hidden group shadow-lg border border-gray-100">
                                @if($detailEvent->flyer_url)
                                    <img src="{{ asset('storage/' . $detailEvent->flyer_url) }}" alt="Flyer Event" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-900 to-[#000666]">
                                        <span class="text-white/50 font-medium">Tidak Ada Flyer</span>
                                    </div>
                                @endif
                                
                                <div class="absolute inset-x-0 bottom-4 px-4 flex justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    @if($detailEvent->flyer_url)
                                        <a href="{{ asset('storage/' . $detailEvent->flyer_url) }}" target="_blank" class="bg-white/20 backdrop-blur-md text-white border border-white/30 text-sm font-medium py-2.5 px-6 rounded-xl hover:bg-white/30 transition flex items-center gap-2 w-full justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                            Perbesar Flyer
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-[#F8F9FE] p-4 rounded-xl border border-gray-100">
                                    <p class="text-xs text-gray-500 mb-1">Kuota Peserta</p>
                                    <p class="text-lg font-bold text-[#000666] flex items-center gap-2">
                                        <svg class="w-5 h-5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                                        {{ $detailEvent->kuota ?? 'Tidak Terbatas' }}
                                    </p>
                                </div>
                                
                                @php
                                    // Logika mencari harga termurah dari tabel biaya_event
                                    $isGratis = true;
                                    $hargaTampil = 'Gratis';
                                    if ($detailEvent->biayas && $detailEvent->biayas->count() > 0) {
                                        $hargaTermurah = $detailEvent->biayas->min('biaya');
                                        if ($hargaTermurah > 0) {
                                            $isGratis = false;
                                            $hargaTampil = 'Rp ' . number_format($hargaTermurah, 0, ',', '.');
                                        }
                                    }
                                @endphp

                                <div class="bg-[#F8F9FE] p-4 rounded-xl border border-gray-100">
                                    <p class="text-xs text-gray-500 mb-1">Mulai Dari (Biaya)</p>
                                    <p class="text-lg font-bold {{ !$isGratis ? 'text-gray-900' : 'text-green-600' }} flex items-center gap-2">
                                        <svg class="w-5 h-5 {{ !$isGratis ? 'text-gray-400' : 'text-green-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        {{ $hargaTampil }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-7 space-y-8">
                            
                            <div>
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-lg">
                                        {{ $detailEvent->kategori->nama_kategori ?? 'Kategori Umum' }}
                                    </span>
                                    <span class="inline-block px-3 py-1 bg-gray-50 text-gray-600 border border-gray-200 text-xs font-bold rounded-lg uppercase">
                                        Tingkat: {{ $detailEvent->tingkat_event ?? '-' }}
                                    </span>
                                </div>
                                
                                <h1 class="text-3xl font-extrabold text-gray-900 leading-tight mb-4">{{ $detailEvent->nama_event }}</h1>
                                
                                <div class="flex items-center gap-4 bg-[#F8F9FE] p-4 rounded-xl border border-indigo-50/50">
                                    <div class="w-12 h-12 bg-white rounded-lg border border-gray-200 flex items-center justify-center overflow-hidden shadow-sm shrink-0">
                                        @if($detailEvent->organisasi && $detailEvent->organisasi->logo_url)
                                            <img src="{{ asset('storage/' . $detailEvent->organisasi->logo_url) }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-6 h-6 text-[#000666]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $detailEvent->organisasi->nama_organisasi ?? 'Organisasi Tidak Diketahui' }}</p>
                                        <p class="text-xs text-gray-500">
                                            Penyelenggara: {{ $detailEvent->penyelenggara ?? 'Sesuai Organisasi' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Deskripsi Kegiatan
                                </h3>
                                <p class="text-sm text-gray-600 leading-relaxed text-justify mb-4">
                                    {{ $detailEvent->deskripsi ?? 'Tidak ada deskripsi yang diberikan.' }}
                                </p>

                                @if($detailEvent->narasumber)
                                    <div class="bg-yellow-50/50 border border-yellow-100 p-3 rounded-lg">
                                        <p class="text-xs font-bold text-yellow-800 mb-1">Narasumber / Bintang Tamu:</p>
                                        <p class="text-sm text-yellow-900">{{ $detailEvent->narasumber }}</p>
                                    </div>
                                @endif
                            </div>

                            <div>
                                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Lokasi & Tautan
                                </h3>
                                <div class="bg-[#F8F9FE] p-4 rounded-xl border border-gray-100 flex items-start gap-4">
                                    <div class="p-2.5 bg-indigo-100 text-indigo-600 rounded-lg shrink-0 mt-0.5">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <div class="w-full">
                                        <p class="text-sm font-bold text-gray-900">{{ $detailEvent->nama_lokasi ?? 'Lokasi Belum Ditentukan' }}</p>
                                        
                                        @if($detailEvent->lokasi_url)
                                            <a href="{{ $detailEvent->lokasi_url }}" target="_blank" class="text-xs text-indigo-600 hover:underline flex items-center gap-1 mt-1 truncate max-w-[200px] md:max-w-md">
                                                <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                {{ $detailEvent->lokasi_url }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Agenda Acara (Timeline)
                                </h3>
                                
                                <div class="pl-2">
                                    @if($detailEvent->timelines && $detailEvent->timelines->count() > 0)
                                        <div class="relative border-l-2 border-gray-200 ml-3 space-y-6 pb-4">
                                            @foreach($detailEvent->timelines as $timeline)
                                                <div class="relative pl-6">
                                                    <span class="absolute -left-[7px] top-1.5 w-3 h-3 rounded-full {{ $loop->first ? 'bg-[#000666]' : 'bg-gray-300' }} ring-4 ring-white"></span>
                                                    
                                                    <p class="text-xs {{ $loop->first ? 'text-[#000666] font-semibold' : 'text-gray-500' }} mb-0.5">
                                                        {{ \Carbon\Carbon::parse($timeline->tanggal_mulai)->translatedFormat('d M Y') }}  
                                                        <span class="mx-1 text-gray-400">s.d</span> 
                                                        {{ \Carbon\Carbon::parse($timeline->tanggal_selesai)->translatedFormat('d M Y') }}
                                                    </p>
                                                    
                                                    <p class="text-sm font-bold text-gray-900">{{ $timeline->nama_timeline }}</p>
                                                    
                                                    @if($timeline->deskripsi_timeline)
                                                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">{{ $timeline->deskripsi_timeline }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 italic border border-dashed border-gray-300 p-4 rounded-lg text-center">Panitia belum menambahkan timeline acara.</p>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>