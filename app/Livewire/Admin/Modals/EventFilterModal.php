<?php

namespace App\Livewire\Admin\Modals;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Fakultas;
use App\Models\Kategori;
use App\Models\OrganisasiMahasiswa;

class EventFilterModal extends Component
{
    public ?string $periode = '';
    public ?string $status = ''; 
    public ?int $fakultasId = null;
    public ?int $kategoriId = null;
    public ?int $organisasiId = null;

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
        
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.modals.event-filter-modal', [
            'listFakultas'   => Fakultas::orderBy('nama_fakultas', 'asc')->get(),
            'listKategori'   => Kategori::orderBy('nama_kategori', 'asc')->get(),
            'listOrganisasi' => OrganisasiMahasiswa::query()->when($this->fakultasId, function($q) {
                                    $q->where('fakultas_id', $this->fakultasId);
                                    })->orderBy('nama_organisasi', 'asc')->get()
        ]);
    }
}