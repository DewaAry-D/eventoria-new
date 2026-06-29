<?php

namespace App\Livewire\Admin\Widgets;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Event;
use App\Models\AdminDpm;
use App\Enums\EventStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityHighlight extends Component
{
    public ?int $fakultasId = null;

    public function mount(?int $fakultasId = null)
    {
        $this->fakultasId = $fakultasId;
    }

    #[On('trigger-global-refresh')]
    public function refreshHighlight()
    {
        // Biarkan kosong, Livewire otomatis memicu ulang fungsi render()
    }

    public function render()
    {
        $adminDpm = AdminDpm::query()->where('user_id', Auth::id())->first();

        $baseEventQuery = Event::whereHas('organisasi', function ($q) use ($adminDpm) {
            if ($adminDpm && $adminDpm->fakultas_id !== null) {
                $q->where('fakultas_id', $adminDpm->fakultas_id);
            }
            else {
                $q->where('tingkat_organisasi', 'universitas');
            }
        });

        $baseEventQuery->where('status', '!=', EventStatus::DRAFT->value);

        // Kategori Terpopuler
        $popularCategory = (clone $baseEventQuery)
            ->whereIn('status', [EventStatus::PUBLISHED->value, EventStatus::COMPLETED->value])
            ->whereMonth('created_at', Carbon::now()->month)
            ->select('kategori_id', DB::raw('count(*) as total'))
            ->groupBy('kategori_id')
            ->with('kategori')
            ->orderByDesc('total')
            ->first();

        // Organisasi Teraktif
        $activeOrg = (clone $baseEventQuery)
            ->whereIn('status', [EventStatus::PUBLISHED->value, EventStatus::COMPLETED->value])
            ->select('organisasi_id', DB::raw('count(*) as total'))
            ->groupBy('organisasi_id')
            ->with('organisasi')
            ->orderByDesc('total')
            ->first();

        return view('livewire.admin.widgets.activity-highlight', [
            'kategoriNama' => $popularCategory?->kategori?->nama_kategori ?? 'Belum Ada',
            'organisasiNama' => $activeOrg?->organisasi?->nama_organisasi ?? 'Belum Ada'
        ]);
    }
}