<?php

namespace App\Livewire\Admin\Charts;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Event;
use App\Models\AdminDpm;
use App\Enums\EventStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PieCategory extends Component
{
    public ?int $fakultasId = null;
    public string $title = 'Distribusi Kategori Event';

    public array $chartConfig = [];

    public function mount(?int $fakultasId = null, string $title = null)
    {
        $this->fakultasId = $fakultasId;
        if ($title) $this->title = $title;
    }

    private function baseEventQuery()
    {
        // Mengambil data profil Admin DPM yang sedang login saat ini
        $adminDpm = AdminDpm::query()->where('user_id', Auth::id())->first();

        $query = Event::whereHas('organisasi', function ($q) use ($adminDpm) {
            if ($adminDpm && $adminDpm->fakultas_id !== null) {
                $q->where('fakultas_id', $adminDpm->fakultas_id);
            } 
            else {
                $q->where('tingkat_organisasi', 'universitas');
            }
        });

        return $query->where('status', '!=', EventStatus::DRAFT->value);
    }

    private function getPieChartConfig()
    {
        // Pengelompokan jumlah event berdasarkan kategori_id
        $categoryData = $this->baseEventQuery()
            ->whereIn('status', [EventStatus::PUBLISHED->value, EventStatus::COMPLETED->value])
            ->select('kategori_id', DB::raw('count(*) as total'))
            ->groupBy('kategori_id')
            ->with('kategori')
            ->get();

        $totalEvent = $categoryData->sum('total');

        // Palet warna
        $colorsPool = [
            '#000666', // Primary Navy
            '#001DDB', // Accent Blue
            '#008A2E', // Success Green
            '#BA1A1A', // Error Red
            '#E3AE00', // Warning Yellow
            '#6750A4', // Purple
        ];

        $labels = [];
        $dataCounts = [];
        $backgroundColors = [];
        $legend = [];

        foreach ($categoryData as $index => $item) {
            $namaKategori = $item->kategori->nama_kategori ?? 'Umum';
            $color = $colorsPool[$index % count($colorsPool)];

            $labels[] = $namaKategori;
            $dataCounts[] = $item->total;
            $backgroundColors[] = $color;

            // Masukkan ke array kustom untuk render legend di bawah chart
            $legend[] = [
                'label' => $namaKategori,
                'count' => $item->total,
                'color' => $color
            ];
        }

        return [
            'labels' => $labels,
            'data'   => $dataCounts,
            'bg'     => $backgroundColors,
            'total'  => $totalEvent,
            'legend' => $legend
        ];
    }

    #[On('trigger-global-refresh')]
    public function refreshPieChartData()
    {
        // Kosongkan saja, Livewire otomatis memicu fungsi render() di bawah
    }

    public function render()
    {
        $adminDpm = AdminDpm::with('fakultas')->where('user_id', Auth::id())->first();
        $scopeName = $adminDpm && $adminDpm->fakultas_id !== null ? $adminDpm->fakultas->nama_fakultas : 'Universitas Udayana';

        $this->chartConfig = $this->getPieChartConfig();

        return view('livewire.admin.charts.pie-category', [
            'scopeName'   => $scopeName
        ]);
    }
}