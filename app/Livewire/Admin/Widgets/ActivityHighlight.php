<?php

namespace App\Livewire\Admin\Widgets;

use Livewire\Component;
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

    public function render()
    {
        // Ambil data profil Admin DPM yang sedang login
        $adminDpm = AdminDpm::where('user_id', Auth::id())->first();

        $baseEventQuery = Event::whereHas('organisasi', function ($q) use ($adminDpm) {
            if ($adminDpm && $adminDpm->fakultas_id !== null) {
                $q->where('fakultas_id', $adminDpm->fakultas_id);
            }
            else {
                $q->where('tingkat_organisasi', 'universitas');
            }
        });

        // Cari Kategori Terpopuler Bulan Ini (Status Published)
        $popularCategory = (clone $baseEventQuery)
            ->where('status', EventStatus::PUBLISHED->value)
            ->whereMonth('created_at', Carbon::now()->month)
            ->select('kategori_id', DB::raw('count(*) as total'))
            ->groupBy('kategori_id')
            ->with('kategori')
            ->orderByDesc('total')
            ->first();

        // Cari Organisasi Teraktif (Punya Event Published Terbanyak)
        $activeOrg = (clone $baseEventQuery)
            ->where('status', EventStatus::PUBLISHED->value)
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