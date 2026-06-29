<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use App\Models\AdminDpm;
use App\Models\Event;
use App\Models\OrganisasiMahasiswa;
use App\Enums\EventStatus;
use App\Enums\OrganisasiStatus;
use App\Enums\TingkatOrganisasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public ?int $fakultasId = null;
    public array $cardsData = [];
    public array $statPengajuan = [];
    public string $scopeName = '';

    public function mount()
    {
        $adminDpm = AdminDpm::with('fakultas')->where('user_id', Auth::id())->first();

        // Ambil id fakultas
        $this->fakultasId = $adminDpm?->fakultas_id;

        if ($adminDpm && $adminDpm->fakultas_id !== null) {
            $this->scopeName = $adminDpm->fakultas->nama_fakultas;
        } else {
            $this->scopeName = 'Universitas Udayana';
        }

        // Fungsi pengambil data
        $this->loadDashboardData();
    }

    #[On('trigger-global-refresh')]
    public function loadDashboardData()
    {
        $this->cardsData = [
            'orgAktif'         => $this->baseOrgQuery()->where('status', OrganisasiStatus::APPROVED->value)->count(),
            'eventBerlangsung' => $this->baseEventQuery()->whereIn('status', [EventStatus::PUBLISHED->value, EventStatus::COMPLETED->value])->count(),
            'pendingOrg'       => $this->baseOrgQuery()->where('status', OrganisasiStatus::PENDING->value)->count(),
            'pendingEvent'     => $this->baseEventQuery()->where('status', EventStatus::PENDING_APPROVAL->value)->count(),
        ];

        $this->statPengajuan = $this->getStatPengajuan();
    }

    // Untuk menyaring query dasar event
    protected function baseEventQuery()
    {
        $query = Event::whereHas('organisasi', function ($q) {
            $this->fakultasId
                ? $q->where('fakultas_id', $this->fakultasId)
                : $q->where('tingkat_organisasi', TingkatOrganisasi::UNIVERSITAS->value);
        });

        return $query->where('status', '!=', EventStatus::DRAFT->value);
    }

    // Untuk menyaring query dasar organisasi mahasiswa
    protected function baseOrgQuery()
    {
        return OrganisasiMahasiswa::when(
            $this->fakultasId,
            fn($q) => $q->where('fakultas_id', $this->fakultasId),
            fn($q) => $q->where('tingkat_organisasi', TingkatOrganisasi::UNIVERSITAS->value)
        );
    }

    private function getStatPengajuan()
    {
        $statusCounts = $this->baseEventQuery()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusCounts = array_change_key_case($statusCounts, CASE_LOWER);

        $published = $statusCounts[strtolower(EventStatus::PUBLISHED->value)] ?? 0;
        $completed = $statusCounts[strtolower(EventStatus::COMPLETED->value)] ?? 0;

        return [
            'disetujui' => $published + $completed, 
            'menunggu'  => $statusCounts[strtolower(EventStatus::PENDING_APPROVAL->value)] ?? 0,
            'revisi'    => $statusCounts[strtolower(EventStatus::REVISION->value)] ?? 0,
        ];
    }

    // Fitur Export CSV
    public function exportReport()
    {
        $events = $this->baseEventQuery()
            ->with(['organisasi', 'kategori']) 
            ->latest()
            ->get();

        $headers = [
            'ID Event', 'Nama Event', 'Kategori', 'Penyelenggara', 
            'Tingkat', 'Status', 'Kuota Total', 'Sisa Kuota', 
            'Narasumber', 'Nama Lokasi', 'URL Lokasi', 'Link Pendaftaran', 
            'Tanggal Diajukan'
        ];
        
        $csv = implode(',', $headers) . "\n";
        
        foreach ($events as $event) {
            $status = is_object($event->status) ? ($event->status?->value ?? '-') : $event->status;
            $tingkat = is_object($event->tingkat_event) ? ($event->tingkat_event?->value ?? '-') : $event->tingkat_event;

            $row = [
                $event->id,
                '"' . str_replace('"', '""', $event->nama_event) . '"',
                '"' . ($event->kategori?->nama_kategori ?? '-') . '"',
                '"' . ($event->organisasi->nama_organisasi ?? '-') . '"',
                strtoupper($tingkat ?? '-'),
                strtoupper($status ?? '-'),
                $event->kuota ?? 0,
                $event->sisa_kuota ?? 0,
                '"' . str_replace('"', '""', $event->narasumber ?? '-') . '"',
                '"' . str_replace('"', '""', $event->nama_lokasi ?? '-') . '"',
                $event->lokasi_url ?? '-',
                $event->link_event ?? '-',
                $event->created_at->format('Y-m-d H:i')
            ];

            $csv .= implode(',', $row) . "\n";
        }

        $suffix = $this->fakultasId ? "Fakultas_{$this->fakultasId}" : 'Universitas';
        $fileName = "Laporan_Aktivitas_Event_{$suffix}_" . date('Y_m_d_His') . '.csv';

        return response()->streamDownload(fn() => print($csv), $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    // Memuat Layout Utama
    #[Layout('layouts.admin', ['active' => 'dashboard'])]
    public function render()
    {
        return view('livewire.pages.admin.dashboard');
    }
}