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
        $adminFakultasId = Cache::remember('admin_fakultas_id_' . Auth::id(), 300, function() {
            return AdminDpm::query()->where('user_id', Auth::id())->value('fakultas_id');
        });

        $query = OrganisasiMahasiswa::query()->where('status', OrganisasiStatus::APPROVED->value); 

        if ($this->fakultasId) {
            $query->where('fakultas_id', $this->fakultasId);
        } elseif ($adminFakultasId !== null) {
            // Karena yang disimpan hanya nilai ID-nya, tidak akan ada error incomplete object
            $query->where('fakultas_id', $adminFakultasId);
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