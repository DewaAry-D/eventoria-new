<?php

namespace App\Livewire\Admin\Modals;

use App\Enums\OrganisasiStatus;
use App\Models\AdminDpm;
use App\Models\Kategori;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\OrganisasiMahasiswa;
use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;

class EventFilterModal extends Component
{
    public ?string $periode = '';
    public ?string $status = ''; 
    public ?int $fakultasId = null;
    public ?int $kategoriId = null;
    public ?int $organisasiId = null;
    public ?int $prodiId = null;

    public bool $isFakultasScope = false;

    public function mount(?int $currentFakultasId = null)
    {
        if ($currentFakultasId) {
            $this->fakultasId = $currentFakultasId;
            $this->isFakultasScope = true;
        }
    }

    // Apply Filter
    public function applyFilter()
    {
        $this->dispatch('filterEvents', [
            'periode'      => $this->periode,
            'status'       => $this->status, 
            'fakultasId'   => $this->fakultasId,
            'kategoriId'   => $this->kategoriId,
            'organisasiId' => $this->organisasiId,
            'prodiId'      => $this->prodiId,
        ]);

        $this->dispatch('close-modal', id: 'filter-event-modal');
    }

    // Reset Filter
    public function resetFilter()
    {
        $this->periode = '';
        $this->status = '';
        if (!$this->isFakultasScope) {
            $this->fakultasId = null;
        }
        $this->kategoriId = null;
        $this->organisasiId = null;
        $this->prodiId = null;

        $this->applyFilter();
    }

    #[On('trigger-global-refresh')]
    public function resetModalStateOnGlobalRefresh()
    {
        $this->periode = '';
        $this->status = '';
        
        if (!$this->isFakultasScope) {
            $this->fakultasId = null;
        }
        
        $this->kategoriId = null;
        $this->organisasiId = null;
        $this->prodiId = null;
        
        $this->resetErrorBag();
    }

    public function render()
    {
        // Ambil fakultas_id milik admin yang sedang login
        $adminFakultasId = AdminDpm::where('user_id', Auth::id())->value('fakultas_id');

        // Query list prodi yang berada di bawah fakultas admin tersebut
        $listProdi = $adminFakultasId 
            ? Prodi::where('fakultas_id', $adminFakultasId)->orderBy('nama_prodi', 'asc')->get()
            : collect(); // Kosongkan jika admin universitas

        $organisasiQuery = OrganisasiMahasiswa::query()->where('status', OrganisasiStatus::APPROVED->value);

        if ($adminFakultasId) {
            $organisasiQuery->where('fakultas_id', $adminFakultasId);
            if ($this->prodiId) {
                $organisasiQuery->where('prodi_id', $this->prodiId);
            }
        } else {
            $organisasiQuery->where('tingkat_organisasi', 'universitas');
        }

        return view('livewire.admin.modals.event-filter-modal', [
            'listProdi'      => $listProdi,
            'listKategori'   => Kategori::orderBy('nama_kategori', 'asc')->get(),
            'listOrganisasi' => $organisasiQuery->orderBy('nama_organisasi', 'asc')->get()
        ]);
    }
}