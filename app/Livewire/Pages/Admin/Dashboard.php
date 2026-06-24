<?php

namespace App\Livewire\Pages\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
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

        $this->cardsData = [
            'orgAktif'         => $this->baseOrgQuery()->where('status', OrganisasiStatus::APPROVED->value)->count(),
            'eventBerlangsung' => $this->baseEventQuery()->where('status', EventStatus::PUBLISHED->value)->count(),
            'pendingOrg'       => $this->baseOrgQuery()->where('status', OrganisasiStatus::PENDING->value)->count(),
            'pendingEvent'     => $this->baseEventQuery()->where('status', EventStatus::PENDING_APPROVAL->value)->count(),
        ];

        $this->statPengajuan = $this->getStatPengajuan();
    }

    // Untuk menyaring query dasar event
    protected function baseEventQuery()
    {
        return Event::whereHas('organisasi', function ($q) {
            $this->fakultasId
                ? $q->where('fakultas_id', $this->fakultasId)
                : $q->where('tingkat_organisasi', TingkatOrganisasi::UNIVERSITAS->value);
        });
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

        return [
            'disetujui' => $statusCounts[strtolower(EventStatus::PUBLISHED->value)] ?? 0,
            'menunggu'  => $statusCounts[strtolower(EventStatus::PENDING_APPROVAL->value)] ?? 0,
            'ditolak'   => $statusCounts[strtolower(EventStatus::REVISION->value)] ?? 0,
        ];
    }

    // Fitur Export CSV
    public function exportReport()
    {
        $events = $this->baseEventQuery()->with('organisasi')->latest()->get();

        // Judul Kolom CSV
        $csv = "ID Event,Nama Event,Penyelenggara,Status,Tanggal Pengajuan,Tingkat\n";
        
        foreach ($events as $event) {
            $csv .= implode(',', [
                $event->id,
                str_replace(',', ' ', $event->nama_event),
                $event->organisasi->nama_organisasi ?? '-',
                strtoupper(is_object($event->status) ? $event->status->value : $event->status),
                $event->created_at->format('Y-m-d'),
                strtoupper(is_object($event->tingkat_event) ? $event->tingkat_event->value : $event->tingkat_event),
            ]) . "\n";
        }

        $suffix = $this->fakultasId ? "Fakultas_{$this->fakultasId}" : 'Universitas';
        $fileName = "Laporan_Aktivitas_{$suffix}_" . date('Y_m_d') . '.csv';

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