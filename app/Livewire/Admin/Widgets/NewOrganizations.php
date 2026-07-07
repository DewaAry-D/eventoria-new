<?php

namespace App\Livewire\Admin\Widgets;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\OrganisasiMahasiswa;
use App\Models\AdminDpm;
use App\Enums\OrganisasiStatus; // 🟢 Tambahkan enum status jika ada
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class NewOrganizations extends Component
{
    public ?int $fakultasId = null;

    public function mount(?int $fakultasId = null)
    {
        $this->fakultasId = $fakultasId;
    }

    #[On('trigger-global-refresh')]
    public function refreshNewOrganizations()
    {
        // Biarkan kosong, Livewire otomatis memicu ulang fungsi render()
    }

    public function render()
    {
        $adminDpm = Cache::remember('admin_dpm_' . Auth::id(), 300, fn() =>
            AdminDpm::query()->where('user_id', Auth::id())->first()
        );

        $query = OrganisasiMahasiswa::query()->where('status', OrganisasiStatus::APPROVED->value); 

        if ($this->fakultasId) {
            $query->where('fakultas_id', $this->fakultasId);
        } elseif ($adminDpm && $adminDpm->fakultas_id !== null) {
            $query->where('fakultas_id', $adminDpm->fakultas_id);
        } else {
            $query->where('tingkat_organisasi', 'universitas');
        }

        $newOrgs = $query->with(['user', 'fakultas'])
            ->latest()
            ->take(3)
            ->get();
            
        return view('livewire.admin.widgets.new-organizations', [
            'organizations' => $newOrgs
        ]);
    }
}