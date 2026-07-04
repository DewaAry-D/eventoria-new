<?php

namespace App\Livewire\Admin\Widgets;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Event;
use App\Models\AdminDpm;
use App\Enums\EventStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
        $adminDpm = Cache::remember('admin_dpm_' . Auth::id(), 300, fn() =>
            AdminDpm::where('user_id', Auth::id())->first()
        );

        $baseEventQuery = Event::whereHas('organisasi', function ($q) use ($adminDpm) {
            if ($this->fakultasId) {
                $q->where('fakultas_id', $this->fakultasId);
            } elseif ($adminDpm && $adminDpm->fakultas_id !== null) {
                $q->where('fakultas_id', $adminDpm->fakultas_id);
            } else {
                $q->where('tingkat_organisasi', 'universitas');
            }
        })->where('status', '!=', EventStatus::DRAFT->value);

        $popularCategory = (clone $baseEventQuery)
            ->whereIn('status', [EventStatus::PUBLISHED->value, EventStatus::COMPLETED->value])
            ->select('kategori_id', DB::raw('SUM(GREATEST(0, COALESCE(kuota, 0) - COALESCE(sisa_kuota, 0))) as total_pendaftar'))
            ->groupBy('kategori_id')
            ->with('kategori')
            ->orderByDesc('total_pendaftar')
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