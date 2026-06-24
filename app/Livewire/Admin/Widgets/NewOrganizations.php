<?php

namespace App\Livewire\Admin\Widgets;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\OrganisasiMahasiswa;
use App\Models\AdminDpm;
use Illuminate\Support\Facades\Auth;

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
        // Biarkan kosong, Livewire otomatis memicu ulang fungsi render() di bawah
    }

    public function render()
    {
        $adminDpm = AdminDpm::where('user_id', Auth::id())->first();

        $query = OrganisasiMahasiswa::query();
        $query->where('status', 'pending');

        if ($adminDpm && $adminDpm->fakultas_id !== null) {
            $query->where('fakultas_id', $adminDpm->fakultas_id);
        } 
        else {
            $query->where('tingkat_organisasi', 'universitas');
        }

        $newOrgs = $query->with('fakultas')
            ->latest()
            ->take(3)
            ->get();

        return view('livewire.admin.widgets.new-organizations', [
            'organizations' => $newOrgs
        ]);
    }
}