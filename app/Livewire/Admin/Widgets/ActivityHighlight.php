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
        $adminFakultasId = Cache::remember('admin_fakultas_id_' . Auth::id(), 300, function() {
            return AdminDpm::query()->where('user_id', Auth::id())->value('fakultas_id');
        });

        // Ikat properti scope wilaya
        $targetFakultasId = $this->fakultasId;

        $baseEventQuery = Event::whereHas('organisasi', function ($q) use ($adminFakultasId, $targetFakultasId) {
            if ($this->fakultasId) {
                $q->where('fakultas_id', $targetFakultasId);
            } elseif ($adminFakultasId !== null) {
                $q->where('fakultas_id', $adminFakultasId);
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