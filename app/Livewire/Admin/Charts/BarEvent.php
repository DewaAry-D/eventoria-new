<?php

namespace App\Livewire\Admin\Charts;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Event;
use App\Models\AdminDpm;
use App\Enums\EventStatus;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BarEvent extends Component
{
    public ?int $fakultasId = null;

    public string $title = 'Jumlah Event Per Bulan'; // nilai default
    public ?string $description = null; // nilai default

    public array $chartConfig = [];

    public function mount(?int $fakultasId = null, string $title = null, string $description = null)
    {
        $this->fakultasId = $fakultasId;
        if ($title) $this->title = $title;
        $this->description = $description;
    }

    private function baseEventQuery()
    {
        $adminDpm = AdminDpm::query()->where('user_id', Auth::id())->first();

        return Event::whereHas('organisasi', function ($q) use ($adminDpm) {
            if ($adminDpm && $adminDpm->fakultas_id !== null) {
                $q->where('fakultas_id', $adminDpm->fakultas_id);
            } 
            else {
                $q->where('tingkat_organisasi', 'universitas');
            }
        });
    }
    private function getBarChartConfig()
    {
        $startDate = Carbon::now()->subMonths(5)->startOfMonth();

        $eventsCount = $this->baseEventQuery()
            ->where('status', EventStatus::PUBLISHED->value)
            ->where('created_at', '>=', $startDate)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthsLabels = [];
        $chartData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key = $date->format('Y-m');
            
            $monthsLabels[] = $date->isoFormat('MMM');
            $chartData[] = $eventsCount[$key] ?? 0;
        }

        $maxEvent = max($chartData) > 0 ? max($chartData) : 1;
        $backgroundColors = [];
        $borderColors = [];

        foreach ($chartData as $value) {
            if ($value === 0) {
                $backgroundColors[] = 'rgba(148, 163, 184, 0.1)';
                $borderColors[] = 'rgba(148, 163, 184, 0.2)';
            } else {
                $ratio = 0.2 + ($value / $maxEvent) * 0.8;
                $backgroundColors[] = "rgba(0, 6, 102, {$ratio})";
                $borderColors[] = "rgba(0, 6, 102, 1)";
            }
        }

        return [
            'labels' => $monthsLabels,
            'data'   => $chartData,
            'bg'     => $backgroundColors,
            'border' => $borderColors,
        ];
    }

    #[On('trigger-global-refresh')]
    public function refreshChartData()
    {
        // Biarkan kosong, Livewire otomatis memicu fungsi render() di bawah
    }

    public function render()
    {
        $this->chartConfig = $this->getBarChartConfig();

        return view('livewire.admin.charts.bar-event');
    }
}