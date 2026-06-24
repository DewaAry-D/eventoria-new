<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Event;
use App\Models\AdminDpm;
use App\Enums\EventStatus;
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
    public string $alasanPenolakan = '';

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
        // Ambil data profil Admin DPM yang sedang login
        $adminDpm = AdminDpm::where('user_id', Auth::id())->first();

        return Event::whereHas('organisasi', function ($q) use ($adminDpm) {
            if ($adminDpm && $adminDpm->fakultas_id !== null) {
                $q->where('fakultas_id', $adminDpm->fakultas_id);
            } 
            else {
                $q->where('tingkat_organisasi', 'universitas');
            }
        });
    }

    public function approveEvent(int $eventId)
    {
        $adminDpm = AdminDpm::where('user_id', Auth::id())->first();
        $event = $this->baseEventQuery()->find($eventId);

        if ($event) {
            $event->update([
                'status'       => EventStatus::PUBLISHED->value,
                'admin_acc_id' => $adminDpm?->id,
            ]);

            session()->flash('success', "Event '{$event->nama_event}' berhasil disetujui!");
        } else {
            session()->flash('error', "Aksi ilegal terdeteksi. Data tidak ditemukan di wilayah Anda.");
        }
    }

    public function rejectEvent(int $eventId, string $alasan)
    {
        $event = $this->baseEventQuery()->find($eventId);

        if ($event) {
            $event->update([
                'status'         => EventStatus::REVISION->value,
                'catatan_revisi' => $alasan,
            ]);

            session()->flash('success', "Event '{$event->nama_event}' dikembalikan untuk direvisi.");
        } else {
            session()->flash('error', "Aksi ilegal terdeteksi. Data tidak ditemukan di wilayah Anda.");
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
            $paginator = $query->latest()->paginate(5);
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