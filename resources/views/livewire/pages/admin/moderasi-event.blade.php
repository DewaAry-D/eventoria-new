<?php

use App\Models\Event;
use App\Models\OrganisasiMahasiswa;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] class extends Component
{
    use WithPagination;

    // State Pencarian & Filter
    public $search = '';
    public $filterOrganisasi = '';
    public $filterTingkat = '';
    public $filterStatus = '';

    // State Modal Konfirmasi
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $selectedEventId = null;
    public $selectedEventTitle = '';
    public $catatanRevisi = '';

    // Reset pagination ketika nilai pencarian/filter berubah
    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterOrganisasi() { $this->resetPage(); }
    public function updatingFilterTingkat() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    public function resetFilter()
    {
        $this->filterOrganisasi = '';
        $this->filterTingkat = '';
        $this->filterStatus = '';
        $this->search = '';
        $this->resetPage();
    }

    public function with(): array
    {
        // Hitung Statistik (Mengabaikan status 'draft' dari total karena belum diajukan)
        $stats = [
            'total' => Event::where('status', '!=', 'draft')->count(),
            'pending' => Event::where('status', 'pending_approval')->count(),
            'approved' => Event::whereIn('status', ['published', 'completed'])->count(),
            'rejected' => Event::where('status', 'revision')->count(), // 'revision' kita anggap ditolak sementara
        ];

        // Ambil Data Event beserta relasi Kategori & Organisasi
        $eventQuery = Event::with(['kategori', 'organisasi'])
            ->where('status', '!=', 'draft') // Jangan tampilkan draft
            ->when($this->search, function ($query) {
                $query->where('nama_event', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterOrganisasi, function ($query) {
                $query->where('organisasi_id', $this->filterOrganisasi);
            })
            ->when($this->filterTingkat, function ($query) {
                $query->where('tingkat_event', $this->filterTingkat);
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->latest();

        return [
            'stats' => $stats,
            'daftar_event' => $eventQuery->paginate(10),
            // Kirim daftar organisasi aktif untuk opsi Dropdown Filter
            'daftar_organisasi' => OrganisasiMahasiswa::where('status', 'approved')->get(),
        ];
    }

    // --- FUNGSI SETUJUI ---
    public function confirmApprove($id, $title)
    {
        $this->selectedEventId = $id;
        $this->selectedEventTitle = $title;
        $this->showApproveModal = true;
    }

    public function approve()
    {
        if ($this->selectedEventId) {
            $event = Event::find($this->selectedEventId);
            $statusVal = $event->status instanceof \UnitEnum ? $event->status->value : $event->status;

            if ($event && $statusVal === 'pending_approval') {
                $event->update([
                    'status' => 'published',
                    'catatan_revisi' => null // Bersihkan catatan lama jika ada
                ]);
                session()->flash('success', "Event '{$this->selectedEventTitle}' berhasil disetujui dan diterbitkan.");
            }
        }
        $this->closeModal();
    }

    // --- FUNGSI TOLAK (REVISI) ---
    public function confirmReject($id, $title)
    {
        $this->selectedEventId = $id;
        $this->selectedEventTitle = $title;
        $this->catatanRevisi = '';
        $this->showRejectModal = true;
    }

    public function reject()
    {
        $this->validate([
            'catatanRevisi' => 'required|string|min:5'
        ], [
            'catatanRevisi.required' => 'Catatan revisi wajib diisi agar panitia mengetahui kekurangannya.',
            'catatanRevisi.min' => 'Catatan revisi terlalu singkat.'
        ]);

        if ($this->selectedEventId) {
            $event = Event::find($this->selectedEventId);
            $statusVal = $event->status instanceof \UnitEnum ? $event->status->value : $event->status;

            if ($event && $statusVal === 'pending_approval') {
                $event->update([
                    'status' => 'revision',
                    'catatan_revisi' => $this->catatanRevisi
                ]);
                session()->flash('success', "Event '{$this->selectedEventTitle}' dikembalikan untuk direvisi.");
            }
        }
        $this->closeModal();
    }

    // --- FUNGSI UTILITAS ---
    public function closeModal()
    {
        $this->showApproveModal = false;
        $this->showRejectModal = false;
        $this->selectedEventId = null;
        $this->selectedEventTitle = '';
        $this->catatanRevisi = '';
        $this->resetErrorBag();
    }
}; ?>

<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#000666]">Manajemen Pengajuan Event</h1>
            <p class="text-gray-500 text-sm mt-1">Tinjau dan verifikasi pengajuan event dari organisasi mahasiswa secara berkala.</p>
        </div>
        <button wire:click="$refresh" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#000666] hover:bg-indigo-900 rounded-xl flex items-center gap-2 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Refresh
        </button>
    </div>

    <!-- Flash Message -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-6 p-4 text-sm text-green-800 rounded-xl bg-green-50 flex items-center border border-green-100 shadow-sm transition-all">
            <svg class="w-5 h-5 inline mr-2 shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Kartu Statistik (4 Kolom) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 bg-indigo-50 text-[#000666] rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-[#000666]">{{ $stats['total'] }}</h3>
                <p class="text-sm font-medium text-gray-500 mt-1">Total Pengajuan Event</p>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 bg-gray-50 text-gray-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-gray-900">{{ $stats['pending'] }}</h3>
                <p class="text-sm font-medium text-gray-500 mt-1">Menunggu Persetujuan</p>
            </div>
        </div>

        <!-- Approved -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-gray-900">{{ $stats['approved'] }}</h3>
                <p class="text-sm font-medium text-gray-500 mt-1">Event Disetujui</p>
            </div>
        </div>

        <!-- Rejected/Revision -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
            <div class="flex items-start justify-between mb-2">
                <div class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div>
                <h3 class="text-3xl font-extrabold text-gray-900">{{ $stats['rejected'] }}</h3>
                <p class="text-sm font-medium text-gray-500 mt-1">Event Ditolak / Revisi</p>
            </div>
        </div>
    </div>

    <!-- Area Tabel dan Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-visible relative">
        
        <!-- Toolbar Atas: Judul, Search & Filter -->
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="text-lg font-bold text-[#000666]">Daftar Pengajuan Terbaru</h2>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <!-- Search Box -->
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" wire:model.live="search" class="block w-full pl-10 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:ring-[#000666] focus:border-[#000666] transition" placeholder="Cari pengajuan event...">
                </div>

                <!-- Alpine.js Dropdown Filter -->
                <div x-data="{ openFilter: false }" class="relative inline-block text-left w-full md:w-auto">
                    <button @click="openFilter = !openFilter" type="button" class="w-full md:w-auto px-4 py-2.5 border rounded-xl text-sm font-bold flex items-center justify-center gap-2 transition {{ ($filterStatus !== '' || $filterTingkat !== '' || $filterOrganisasi !== '') ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                        @if($filterStatus !== '' || $filterTingkat !== '' || $filterOrganisasi !== '')
                            <span class="w-2 h-2 rounded-full bg-red-500 ml-1"></span>
                        @endif
                    </button>

                    <!-- Dropdown Content -->
                    <div x-show="openFilter"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.away="openFilter = false"
                         class="absolute right-0 z-[60] mt-2 w-72 origin-top-right rounded-2xl bg-white shadow-xl border border-gray-100 p-5"
                         style="display: none;">

                        <div class="mb-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-4 border-b pb-2">Filter Event</h3>
                            <div class="space-y-4">
                                <!-- Filter Organisasi -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Organisasi Penyelenggara</label>
                                    <select wire:model.live="filterOrganisasi" class="block w-full text-sm border-gray-200 rounded-lg bg-gray-50 focus:ring-[#000666]">
                                        <option value="">Semua Organisasi</option>
                                        @foreach($daftar_organisasi as $org)
                                            <option value="{{ $org->id }}">{{ $org->nama_organisasi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Filter Status -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Status Pengajuan</label>
                                    <select wire:model.live="filterStatus" class="block w-full text-sm border-gray-200 rounded-lg bg-gray-50 focus:ring-[#000666]">
                                        <option value="">Semua Status</option>
                                        <option value="pending_approval">Pending (Menunggu)</option>
                                        <option value="published">Diterima (Published)</option>
                                        <option value="revision">Ditolak / Revisi</option>
                                    </select>
                                </div>

                                <!-- Filter Tingkat Event -->
                                @if(\App\Models\AdminDpm::where('user_id', auth()->id())->value('fakultas_id'))
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Tingkat Event</label>
                                    <select wire:model.live="filterTingkat" class="block w-full text-sm border-gray-200 rounded-lg bg-gray-50 focus:ring-[#000666]">
                                        <option value="">Semua Tingkat</option>
                                        <option value="fakultas">Fakultas</option>
                                        <option value="prodi">Program Studi</option>
                                    </select>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <button wire:click="resetFilter" type="button" class="text-xs font-bold text-red-600 hover:text-red-800 transition">Bersihkan</button>
                            <button @click="openFilter = false" type="button" class="px-5 py-2 text-xs font-bold text-white bg-[#000666] rounded-lg hover:bg-indigo-900 transition shadow-sm">Terapkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($filterStatus || $filterTingkat || $filterOrganisasi || $search)
            <div class="bg-indigo-50/50 px-6 py-2 border-b border-gray-100 flex items-center justify-between text-xs text-indigo-700">
                <span>Penyaringan aktif sedang diterapkan.</span>
                <button wire:click="resetFilter" class="font-bold hover:underline">Hapus Filter</button>
            </div>
        @endif

        <!-- Tabel Event -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-bold tracking-wider">Nama Event</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Organisasi</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Kategori</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Tgl Pengajuan</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                        <th class="px-6 py-4 font-bold tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($daftar_event as $ev)
                        @php
                            $statusVal = $ev->status instanceof \UnitEnum ? $ev->status->value : $ev->status;
                        @endphp
                        <tr class="bg-white hover:bg-gray-50/50 transition">
                            <!-- Judul & Lokasi -->
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-900">{{ $ev->nama_event }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $ev->nama_lokasi ?? 'Lokasi belum ditentukan' }}</p>
                            </td>
                            <!-- Organisasi -->
                            <td class="px-6 py-4 text-gray-700 font-medium">
                                {{ $ev->organisasi->nama_organisasi ?? '-' }}
                            </td>
                            <!-- Kategori (Badge) -->
                            <td class="px-6 py-4">
                                {{ $ev->kategori->nama_kategori ?? 'Umum' }}    
                            </td>
                            <!-- Tgl Pengajuan -->
                            <td class="px-6 py-4 text-gray-600">
                                {{ $ev->created_at->translatedFormat('d M Y') }}
                            </td>
                            <!-- Status -->
                            <td class="px-6 py-4">
                                @if($statusVal === 'pending_approval')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Pending
                                    </span>
                                @elseif(in_array($statusVal, ['published', 'completed']))
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Diterima
                                    </span>
                                @elseif($statusVal === 'revision')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <!-- Aksi -->
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Aksi Setuju & Tolak hanya jika status pending_approval -->
                                    @if($statusVal === 'pending_approval')
                                        <button wire:click="confirmApprove({{ $ev->id }}, '{{ addslashes($ev->nama_event) }}')" class="p-1.5 text-green-600 hover:bg-green-100 rounded-lg transition border border-transparent hover:border-green-200" title="Setujui Event">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        
                                        <button wire:click="confirmReject({{ $ev->id }}, '{{ addslashes($ev->nama_event) }}')" class="p-1.5 text-red-600 hover:bg-red-100 rounded-lg transition border border-transparent hover:border-red-200" title="Kembalikan / Tolak">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    @endif

                                    <!-- Tombol Mata (Lihat Detail) -->
                                    <a href="#" class="inline-block p-1.5 text-gray-400 hover:text-[#000666] hover:bg-indigo-50 rounded-lg transition" title="Lihat Detail Event">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <p class="text-gray-500 font-medium">Tidak ada event yang ditemukan berdasarkan kriteria Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100 bg-gray-50/30">
            {{ $daftar_event->links() }}
        </div>
    </div>

    <!-- MODAL SETUJUI EVENT -->
    @if($showApproveModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center transform transition-all">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Setujui Event?</h3>
                <p class="text-sm text-gray-500 mb-8 leading-relaxed">
                    Apakah Anda yakin ingin menyetujui event <span class="font-bold text-gray-800">{{ $selectedEventTitle }}</span>? Event ini akan langsung terbit (Published) dan bisa diakses pendaftar.
                </p>
                <div class="flex items-center justify-center gap-3">
                    <button wire:click="closeModal" class="flex-1 py-2.5 px-4 bg-white text-gray-700 font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition">Batal</button>
                    <button wire:click="approve" class="flex-1 py-2.5 px-4 bg-[#16A34A] hover:bg-green-700 text-white font-semibold rounded-xl transition shadow-sm">Ya, Setujui</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL TOLAK / REVISI EVENT -->
    @if($showRejectModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 transform transition-all">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Kembalikan untuk Revisi?</h3>
                    <p class="text-sm text-gray-500 leading-relaxed px-4">Berikan catatan khusus agar panitia <span class="font-bold text-gray-800">{{ $selectedEventTitle }}</span> dapat segera memperbaikinya.</p>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-900 mb-2">Catatan Revisi / Penolakan</label>
                    <textarea wire:model="catatanRevisi" rows="4" class="w-full bg-[#FAFAFA] border border-gray-200 rounded-xl text-sm p-3 focus:ring-red-500 focus:border-red-500 placeholder-gray-400" placeholder="Misal: Mohon lengkapi detail rundown acara..."></textarea>
                    <x-input-error :messages="$errors->get('catatanRevisi')" class="mt-1" />
                </div>
                <div class="flex items-center justify-center gap-3">
                    <button wire:click="closeModal" class="flex-1 py-2.5 px-4 bg-white text-gray-700 font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition">Batal</button>
                    <button wire:click="reject" class="flex-1 py-2.5 px-4 bg-[#DC2626] hover:bg-red-700 text-white font-semibold rounded-xl transition shadow-sm">Kirim Catatan</button>
                </div>
            </div>
        </div>
    @endif
</div>