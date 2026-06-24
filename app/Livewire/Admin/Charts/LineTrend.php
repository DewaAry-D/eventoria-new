<?php

namespace App\Livewire\Admin\Charts;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Event;
use App\Models\AdminDpm;
use App\Enums\EventStatus;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LineTrend extends Component
{
    public ?int $fakultasId = null;
    
    public string $title = 'Tren Organisasi Aktif'; // Nilai default
    public ?string $description = null; // Nilai default

    public array $config = [];

    public function mount(?int $fakultasId = null, string $title = null, string $description = null)
    {
        $this->fakultasId = $fakultasId;
        if ($title) $this->title = $title;
        if ($description) $this->description = $description;
    }

    private function baseEventQuery()
    {
        // Ambil data profil Admin DPM yang sedang login
        $adminDpm = AdminDpm::where('user_id', Auth::id())->first();

        return Event::whereHas('organisasi', function ($q) use ($adminDpm) {
            if ($adminDpm && $adminDpm->fakultas_id !== null) {
                $q->where('fakultas_id', $adminDpm->fakultas_id);
            } 
            else {
                $q->where('tingkat_organisasi', 'universitas');
            }
        });
    }

    private function getLineChartConfig()
    {
        $startDate = Carbon::now()->subMonths(5)->startOfMonth();

        // Mengambil tren dari data event berstatus PUBLISHED 6 bulan terakhir
        $activeOrgsCount = $this->baseEventQuery()
            ->where('status', EventStatus::PUBLISHED->value)
            ->where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(distinct organisasi_id) as total")
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthsLabels = [];
        $lineData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key = $date->format('Y-m');
            
            $monthsLabels[] = $date->isoFormat('MMM'); 
            $lineData[] = $activeOrgsCount[$key] ?? 0;
        }

        return [
            'labels' => $monthsLabels,
            'data'   => $lineData
        ];
    }

    #[On('trigger-global-refresh')]
    public function refreshLineChartData()
    {
        // Kosongkan saja, Livewire otomatis memicu fungsi render() di bawah
    }

    public function render()
    {
        $this->config = $this->getLineChartConfig();

        return view('livewire.admin.charts.line-trend');
    }
}