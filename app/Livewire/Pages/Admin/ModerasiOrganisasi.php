<?php

namespace App\Livewire\Pages\Admin;

use App\Enums\EventStatus;
use App\Enums\OrganisasiStatus;
use App\Models\AdminDpm;
use App\Models\OrganisasiMahasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class ModerasiOrganisasi extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';
    public string $filterTingkat = '';
    public string $filterPeriode = '';

    // State Modal
    public bool $showApproveModal = false;
    public bool $showRejectModal = false;
    public ?int $selectedOrgId = null;
    public string $selectedOrgName = '';
    public string $pesanPenolakan = '';

    public function updatingSearch()   { $this->resetPage(); }

    public function applyFilter(): void
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterTingkat = '';
        $this->filterPeriode = '';
        $this->resetPage();
    }

    protected function getAdminDpm(): ?AdminDpm
    {
        return AdminDpm::query()->where('user_id', Auth::id())->first();
    }

    protected function baseQuery()
    {
        $adminDpm = $this->getAdminDpm();

        return OrganisasiMahasiswa::query()
            ->when($adminDpm && $adminDpm->fakultas_id !== null, function ($q) use ($adminDpm) {
                $q->where('fakultas_id', $adminDpm->fakultas_id);
            }, function ($q) {
                $q->where('tingkat_organisasi', 'universitas');
            });
    }

    protected function getStats(): array
    {
        $counts = $this->baseQuery()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $counts = array_change_key_case($counts, CASE_LOWER);

        $pending   = $counts[strtolower(OrganisasiStatus::PENDING->value)] ?? 0;
        $approved  = $counts[strtolower(OrganisasiStatus::APPROVED->value)] ?? 0;
        $rejected  = $counts[strtolower(OrganisasiStatus::REJECTED->value)] ?? 0;
        $total     = $pending + $approved + $rejected;

        return [
            'total'    => $total,
            'pending'  => $pending,
            'approved' => $approved,
            'rejected' => $rejected,
        ];
    }

    public function confirmApprove(int $id, string $name): void
    {
        $this->selectedOrgId   = $id;
        $this->selectedOrgName = $name;
        $this->showApproveModal = true;
    }

    public function approve(int $id): void
    {
        $org = $this->baseQuery()
            ->where('id', $id)
            ->where('status', EventStatus::PENDING_APPROVAL->value)
            ->first();

        if (!$org) {
            $this->dispatch('show-toast', message: 'Aksi tidak sah atau organisasi sudah diproses.', type: 'error');
            $this->closeModal();
            return;
        }

        $org->update(['status' => OrganisasiStatus::APPROVED->value]);

        $this->closeModal();
        $this->dispatch('show-toast', message: "Organisasi '{$org->nama_organisasi}' berhasil disetujui.", type: 'success');
    }

    public function confirmReject(int $id, string $name): void
    {
        $this->selectedOrgId   = $id;
        $this->selectedOrgName = $name;
        $this->pesanPenolakan  = '';
        $this->showRejectModal = true;
    }

    public function reject(int $id): void
    {
        $this->validate([
            'pesanPenolakan' => 'required|string|min:5|max:500',
        ], [
            'pesanPenolakan.required' => 'Alasan penolakan wajib diisi.',
            'pesanPenolakan.min'      => 'Alasan terlalu singkat, minimal 5 karakter.',
            'pesanPenolakan.max'      => 'Alasan terlalu panjang, maksimal 500 karakter.',
        ]);

        $pesanBersih = strip_tags(trim($this->pesanPenolakan));

        if (strlen($pesanBersih) < 5) {
            $this->addError('pesanPenolakan', 'Alasan tidak boleh hanya berisi spasi atau karakter kosong.');
            return;
        }

        $org = $this->baseQuery()
            ->where('id', $id)
            ->where('status', EventStatus::PENDING_APPROVAL->value)
            ->first();

        if (!$org) {
            $this->dispatch('show-toast', message: 'Aksi tidak sah atau organisasi sudah diproses.', type: 'error');
            $this->closeModal();
            return;
        }

        $org->update([
            'status'          => OrganisasiStatus::REJECTED->value,
            'pesan_penolakan' => $pesanBersih,
        ]);

        $this->closeModal();
        $this->dispatch('show-toast', message: "Pengajuan '{$org->nama_organisasi}' berhasil ditolak.", type: 'success');
    }

    public function closeModal(): void
    {
        $this->showApproveModal = false;
        $this->showRejectModal  = false;
        $this->selectedOrgId   = null;
        $this->selectedOrgName = '';
        $this->pesanPenolakan  = '';
        $this->resetErrorBag();
        $this->dispatch('modal-closed');
    }

    #[ \Livewire\Attributes\On('trigger-global-refresh') ]
    public function refreshComponent(): void
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->filterTingkat = '';
        $this->filterPeriode = '';

        $this->resetErrorBag();
        $this->resetPage();
    }

    #[Layout('layouts.admin', ['active' => 'moderasi-organisasi'])]
    public function render()
    {
        $adminDpm = $this->getAdminDpm();

        $query = $this->baseQuery()
            ->with(['user', 'fakultas']) // Eager loading aman
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama_organisasi', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', fn($u) => $u->where('email', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterTingkat, fn($q) => $q->where('tingkat_organisasi', $this->filterTingkat));

        if ($this->filterPeriode) {
            if ($this->filterPeriode === 'today') {
                $query->whereDate('created_at', Carbon::today());
            } elseif ($this->filterPeriode === 'this_week') {
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($this->filterPeriode === 'this_month') {
                $query->whereMonth('created_at', Carbon::now()->month)
                        ->whereYear('created_at', Carbon::now()->year);
            }
        }

        // Pending muncul paling atas
        $query->orderByRaw("FIELD(status, 'pending', 'rejected', 'approved') ASC")->latest();

        $paginator = $query->paginate(6)->onEachSide(1);

        $paginationData = [
            'currentPage' => $paginator->currentPage(),
            'totalPages'  => $paginator->lastPage(),
            'from'        => $paginator->firstItem() ?? 0,
            'to'          => $paginator->lastItem() ?? 0,
            'total'       => $paginator->total(),
        ];

        return view('livewire.pages.admin.moderasi-organisasi', [
            'stats'             => $this->getStats(),
            'daftar_organisasi' => $paginator->items(),
            'paginationData'    => $paginationData,
            'isFakultasAdmin'   => $adminDpm && $adminDpm->fakultas_id !== null,
        ]);
    }
}