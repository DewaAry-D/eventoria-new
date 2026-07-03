<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\Event;
use App\Models\AdminDpm;
use App\Enums\EventStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EventMaster extends Component
{
    use WithPagination;

    public bool $isDashboard = false;
    public string $title = 'Daftar Pengajuan Terbaru';
    public ?int $fakultasId = null;
    public string $search = '';

    // State untuk Keperluan Modal Pop-up
    public bool $showApproveModal = false;
    public bool $showRejectModal = false;
    public ?int $selectedEventId = null;
    public string $selectedEventName = '';
    public ?string $alasanPenolakan = '';

    // State untuk Filter
    public ?string $filterPeriode = '';
    public ?int $filterKategoriId = null;
    public ?string $filterStatus = '';
    public ?int $filterOrganisasiId = null;

    // Daftarkan listener event
    #[ \Livewire\Attributes\On('filterEvents') ]
    public function handleFilters($filters)
    {
        $adminDpm = AdminDpm::query()->where('user_id', Auth::id())->first();
        
        $this->filterPeriode = $filters['periode'];
        $this->filterStatus = $filters['status'];
        $this->filterKategoriId = $filters['kategoriId'];
        $this->filterOrganisasiId = $filters['organisasiId'];
        
        // Hanya admin Universitas (fakultas_id == null) yang boleh mengganti filter fakultas
        if ($adminDpm && $adminDpm->fakultas_id === null) {
            $this->fakultasId = $filters['fakultasId'];
        } else {
            $this->fakultasId = $adminDpm?->fakultas_id;
        }
        
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount(?int $fakultasId = null, bool $isDashboard = false, string $title = null)
    {
        $this->fakultasId = $fakultasId;
        $this->isDashboard = $isDashboard;
        if ($title) $this->title = $title;
    }

    protected function baseEventQuery()
    {
        $adminDpm = AdminDpm::query()->where('user_id', Auth::id())->first();

        // Query awal berdasarkan birokrasi DPM
        $query = Event::whereHas('organisasi', function ($q) use ($adminDpm) {
            $q->where('status', 'approved'); // Mencegah ormawa pending/rejected memunculkan event

            if ($this->fakultasId) {
                $q->where('fakultas_id', $this->fakultasId);
            } elseif ($adminDpm && $adminDpm->fakultas_id !== null) {
                $q->where('fakultas_id', $adminDpm->fakultas_id);
            } else {
                $q->where('tingkat_organisasi', 'universitas');
            }
        });

        // buang status draft
        $query->where('status', '!=', EventStatus::DRAFT->value);

        // logika Filter Status jika dipilih
        if (!$this->isDashboard && $this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        // logika Filter Kategori jika dipilih
        if ($this->filterKategoriId) {
            $query->where('kategori_id', $this->filterKategoriId);
        }

        // logika Filter Organisasi jika dipilih
        if ($this->filterOrganisasiId) {
            $query->where('organisasi_id', $this->filterOrganisasiId);
        }

        // logika Filter Periode Waktu jika dipilih
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

        return $query;
    }

    public function approveEvent(int $eventId)
    {
        $adminDpm = AdminDpm::query()->where('user_id', Auth::id())->first();
        $event = $this->baseEventQuery()->find($eventId);

        if ($event) {
            $event->update([
                'status'         => EventStatus::PUBLISHED->value,
                'admin_acc_id'   => $adminDpm?->id,
                'catatan_revisi' => null,
            ]);

            $this->closeModal();

            $this->dispatch('show-toast',
                message: "Event '{$event->nama_event}' berhasil disetujui!",
                type: 'success'
            );
            $this->dispatch('refresh-after-toast');

        } else {
            $this->dispatch('show-toast',
                message: 'Aksi ilegal terdeteksi. Data tidak ditemukan di wilayah Anda.',
                type: 'error'
            );
        }
    }

    public function rejectEvent(int $eventId)
    {
        $this->validate([
            'alasanPenolakan' => 'required|string|min:5|max:500'
        ], [
            'alasanPenolakan.required' => 'Alasan wajib diisi agar panitia mengetahui kekurangannya.',
            'alasanPenolakan.string'   => 'Format ulasan alasan penolakan tidak valid.',
            'alasanPenolakan.min'      => 'Alasan terlalu singkat, berikan ulasan yang jelas minimal 5 karakter.',
            'alasanPenolakan.max'      => 'Alasan terlalu panjang, batasi ulasan maksimal 500 karakter saja.',
        ]);

        $event = $this->baseEventQuery()->find($eventId);

        if ($event) {
            $alasanBersih = strip_tags($this->alasanPenolakan);

            if (strlen(trim($alasanBersih)) < 5) {
                $this->addError('alasanPenolakan', 'Alasan tidak boleh hanya berisi spasi atau karakter kosong.');
                return;
            }

            $event->update([
                'status'         => EventStatus::REVISION->value,
                'catatan_revisi' => trim($alasanBersih),
            ]);

            $this->reset('alasanPenolakan');
            $this->closeModal();

            $this->dispatch('show-toast',
                message: "Event '{$event->nama_event}' dikembalikan untuk direvisi.",
                type: 'success'
            );
            $this->dispatch('refresh-after-toast');

        } else {
            $this->dispatch('show-toast',
                message: 'Aksi ilegal terdeteksi. Data tidak ditemukan di wilayah Anda.',
                type: 'error'
            );
        }
    }

    // Reset & Tutup Semua Modal
    public function closeModal()
    {
        $this->showApproveModal = false;
        $this->showRejectModal = false;
        $this->selectedEventId = null;
        $this->selectedEventName = '';
        $this->alasanPenolakan = '';
        $this->resetErrorBag();

        $this->dispatch('modal-closed');
    }

    #[On('trigger-global-refresh')]
    public function resetAndRefreshTable()
    {
        $this->search = '';
        $this->filterPeriode = '';
        $this->filterStatus = '';
        $this->filterKategoriId = null;
        $this->filterOrganisasiId = null;

        $this->resetErrorBag(); 
        $this->alasanPenolakan = '';
        
        $this->resetPage(); // Kembalikan paginasi ke halaman 1
    }

    public function render()
    {
        $query = $this->baseEventQuery()->with('organisasi');

        if ($this->isDashboard) {
            // Dashboard: Hanya status pending_approval
            $events = $query->where('status', EventStatus::PENDING_APPROVAL->value)
                            ->latest()
                            ->take(2)
                            ->get();
            $paginationData = null;
        } else {
            // Halaman Moderasi Lengkap
            if (!empty($this->search)) {
                $query->where(function($q) {
                    $q->where('nama_event', 'like', '%' . $this->search . '%')
                        ->orWhereHas('organisasi', function($org) {
                            $org->where('nama_organisasi', 'like', '%' . $this->search . '%');
                        });
                });
            }

            // Paginasi 5 data per halaman
            $paginator = $query->orderByRaw("FIELD(status, '" . 
                                EventStatus::PENDING_APPROVAL->value . "', '" . 
                                EventStatus::REVISION->value . "', '" .
                                EventStatus::PUBLISHED->value . "', '" . 
                                EventStatus::COMPLETED->value . "') ASC")
                            ->orderBy('created_at', 'desc')
                            ->paginate(6)
                            ->onEachSide(1);
            
            $events = $paginator->items();
            
            $paginationData = [
                'currentPage' => $paginator->currentPage(),
                'totalPages'  => $paginator->lastPage(),
                'from'        => $paginator->firstItem() ?? 0,
                'to'          => $paginator->lastItem() ?? 0,
                'total'       => $paginator->total(),
            ];
        }

        return view('livewire.admin.event-master', [
            'events' => $events,
            'paginationData' => $paginationData
        ]);
    }
}