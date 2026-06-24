<?php

use App\Models\OrganisasiMahasiswa;
use App\Models\AdminDpm;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] class extends Component
{
    use WithPagination;

    // State untuk Pencarian & Filter
    public $search = '';
    public $filterStatus = '';
    public $filterTingkat = '';

    // State Modal Konfirmasi Aksi
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $selectedOrgId = null;
    public $selectedOrgName = '';
    public $pesanPenolakan = '';

    // Reset pagination ketika nilai pencarian/filter berubah
    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterTingkat() { $this->resetPage(); }

    public function resetFilter()
    {
        $this->filterStatus = '';
        $this->filterTingkat = '';
        $this->search = '';
        $this->resetPage();
    }

    public function with(): array
    {
        // Hitung Statistik (Tetap hitung semua data terlepas dari filter)
        $stats = [
            'pending' => OrganisasiMahasiswa::where('status', 'pending')->count(),
            'approved' => OrganisasiMahasiswa::where('status', 'approved')->count(),
            'rejected' => OrganisasiMahasiswa::where('status', 'rejected')->count(),
        ];

        // Ambil Data Organisasi beserta Filter
        $organisasiQuery = OrganisasiMahasiswa::with('user')
            ->when($this->search, function ($query) {
                // Bungkus pencarian dalam closure agar tidak bertabrakan dengan filter status
                $query->where(function($subQuery) {
                    $subQuery->where('nama_organisasi', 'like', '%' . $this->search . '%')
                             ->orWhereHas('user', function ($q) {
                                 $q->where('email', 'like', '%' . $this->search . '%');
                             });
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->when($this->filterTingkat, function ($query) {
                $query->where('tingkat_organisasi', $this->filterTingkat);
            })
            ->latest();

        return [
            'stats' => $stats,
            'daftar_organisasi' => $organisasiQuery->paginate(10),
        ];
    }

    // --- FUNGSI SETUJUI ---
    public function confirmApprove($id, $name)
    {
        $this->selectedOrgId = $id;
        $this->selectedOrgName = $name;
        $this->showApproveModal = true;
    }

    public function approve()
    {
        if ($this->selectedOrgId) {
            $org = OrganisasiMahasiswa::find($this->selectedOrgId);
            $statusVal = $org->status instanceof \UnitEnum ? $org->status->value : $org->status;

            if ($org && $statusVal === 'pending') {
                $org->update(['status' => 'approved']);
                session()->flash('success', "Organisasi '{$this->selectedOrgName}' berhasil disetujui.");
            }
        }
        $this->closeModal();
    }

    // --- FUNGSI TOLAK ---
    public function confirmReject($id, $name)
    {
        $this->selectedOrgId = $id;
        $this->selectedOrgName = $name;
        $this->pesanPenolakan = '';
        $this->showRejectModal = true;
    }

    public function reject()
    {
        $this->validate([
            'pesanPenolakan' => 'required|string|min:5'
        ], [
            'pesanPenolakan.required' => 'Alasan penolakan wajib diisi agar organisasi dapat memperbaikinya.',
            'pesanPenolakan.min' => 'Alasan penolakan terlalu singkat.'
        ]);

        if ($this->selectedOrgId) {
            $org = OrganisasiMahasiswa::find($this->selectedOrgId);
            $statusVal = $org->status instanceof \UnitEnum ? $org->status->value : $org->status;

            if ($org && $statusVal === 'pending') {
                $org->update([
                    'status' => 'rejected',
                    'pesan_penolakan' => $this->pesanPenolakan
                ]);
                session()->flash('success', "Pengajuan organisasi '{$this->selectedOrgName}' berhasil ditolak.");
            }
        }
        $this->closeModal();
    }

    // --- FUNGSI UTILITAS ---
    public function closeModal()
    {
        $this->showApproveModal = false;
        $this->showRejectModal = false;
        $this->selectedOrgId = null;
        $this->selectedOrgName = '';
        $this->pesanPenolakan = '';
        $this->resetErrorBag();
    }
}; ?>

<div>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-[#000666]">Manajemen Pengajuan Organisasi</h1>
            <p class="text-gray-500 text-sm mt-1">Tinjau dan verifikasi pengajuan organisasi mahasiswa secara berkala.</p>
        </div>
        <button wire:click="$refresh" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#000666] hover:bg-indigo-900 rounded-xl flex items-center gap-2 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Refresh
        </button>
    </div>

    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-6 p-4 text-sm text-green-800 rounded-xl bg-green-50 flex items-center border border-green-100 shadow-sm transition-all">
            <svg class="w-5 h-5 inline mr-2 shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute top-4 right-4 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Menunggu Persetujuan</p>
            <div class="flex items-baseline gap-2 mb-4">
                <span class="text-4xl font-extrabold text-gray-900">{{ $stats['pending'] }}</span>
                <span class="text-sm font-medium text-gray-500">Organisasi</span>
            </div>
            <span class="inline-block px-3 py-1 bg-[#000666] text-white text-[10px] font-bold uppercase tracking-wider rounded-full">
                Perlu Verifikasi
            </span>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute top-4 right-4 w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Organisasi Aktif</p>
            <div class="flex items-baseline gap-2 mb-4">
                <span class="text-4xl font-extrabold text-gray-900">{{ $stats['approved'] }}</span>
                <span class="text-sm font-medium text-gray-500">Terdaftar</span>
            </div>
            <span class="inline-block px-3 py-1 bg-green-50 text-green-700 text-[10px] font-bold uppercase tracking-wider rounded-full border border-green-100">
                Beroperasi Normal
            </span>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute top-4 right-4 w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <p class="text-sm font-semibold text-gray-500 mb-1">Organisasi Ditolak</p>
            <div class="flex items-baseline gap-2 mb-4">
                <span class="text-4xl font-extrabold text-gray-900">{{ $stats['rejected'] }}</span>
                <span class="text-sm font-medium text-gray-500">Total</span>
            </div>
            <span class="inline-block px-3 py-1 bg-red-50 text-red-700 text-[10px] font-bold uppercase tracking-wider rounded-full border border-red-100">
                Gagal Verifikasi
            </span>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-visible relative">
        
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="text-lg font-bold text-[#000666]">Daftar Pengajuan & Organisasi</h2>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative w-full md:w-72">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" wire:model.live="search" class="block w-full pl-10 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:ring-[#000666] focus:border-[#000666] focus:bg-white transition" placeholder="Cari organisasi atau email...">
                </div>

                <div x-data="{ openFilter: false }" class="relative inline-block text-left w-full md:w-auto">
                    <button @click="openFilter = !openFilter" type="button" class="w-full md:w-auto px-4 py-2.5 border rounded-xl text-sm font-bold flex items-center justify-center gap-2 transition {{ ($filterStatus !== '' || $filterTingkat !== '') ? 'bg-indigo-50 border-indigo-200 text-indigo-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                        @if($filterStatus !== '' || $filterTingkat !== '')
                            <span class="w-2 h-2 rounded-full bg-red-500 ml-1"></span>
                        @endif
                    </button>

                    <div x-show="openFilter"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.away="openFilter = false"
                         class="absolute right-0 z-[60] mt-2 w-72 origin-top-right rounded-2xl bg-white shadow-xl border border-gray-100 ring-1 ring-black ring-opacity-5 p-5"
                         style="display: none;">

                        <div class="mb-4">
                            <h3 class="text-sm font-bold text-gray-900 mb-4 border-b pb-2">Opsi Penyaringan Data</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Status Pengajuan</label>
                                    <select wire:model.live="filterStatus" class="block w-full text-sm border-gray-200 rounded-lg bg-gray-50 focus:ring-[#000666] focus:border-[#000666]">
                                        <option value="">Semua Status</option>
                                        <option value="pending">Menunggu (Pending)</option>
                                        <option value="approved">Aktif (Approved)</option>
                                        <option value="rejected">Ditolak (Rejected)</option>
                                    </select>
                                </div>

                                @if(AdminDpm::where('user_id', auth()->id())->value('fakultas_id'))
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Tingkat Organisasi</label>
                                        <select wire:model.live="filterTingkat" class="block w-full text-sm border-gray-200 rounded-lg bg-gray-50 focus:ring-[#000666] focus:border-[#000666]">
                                            <option value="">Semua Tingkat</option>
                                            <option value="fakultas">Tingkat Fakultas</option>
                                            <option value="prodi">Tingkat Program Studi</option>
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <button wire:click="resetFilter" type="button" class="text-xs font-bold text-red-600 hover:text-red-800 transition">
                                Bersihkan
                            </button>
                            <button @click="openFilter = false" type="button" class="px-5 py-2 text-xs font-bold text-white bg-[#000666] rounded-lg hover:bg-indigo-900 transition shadow-sm">
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        @if($filterStatus || $filterTingkat || $search)
            <div class="bg-indigo-50/50 px-6 py-2 border-b border-gray-100 flex items-center justify-between text-xs text-indigo-700">
                <span>Data ditampilkan berdasarkan penyaringan khusus.</span>
                <button wire:click="resetFilter" class="font-bold hover:underline">Hapus Semua Filter</button>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-500 uppercase bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 font-bold tracking-wider">Logo</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Nama Organisasi</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Tingkat</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Pendaftaran</th>
                        <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                        <th class="px-6 py-4 font-bold tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($daftar_organisasi as $org)
                        @php
                            $statusVal = $org->status instanceof \UnitEnum ? $org->status->value : $org->status;
                            $tingkatVal = $org->tingkat_organisasi instanceof \UnitEnum ? $org->tingkat_organisasi->value : $org->tingkat_organisasi;
                        @endphp
                        <tr class="bg-white hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                                    @if($org->logo_url)
                                        <img src="{{ asset('storage/' . $org->logo_url) }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-[#000666] font-bold text-sm">{{ substr($org->nama_organisasi, 0, 1) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-[#000666]">{{ $org->nama_organisasi }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $org->user->email ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold text-gray-600 bg-gray-100 border border-gray-200 rounded-md uppercase">
                                    {{ $tingkatVal ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-medium">
                                {{ $org->created_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($statusVal === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Menunggu
                                    </span>
                                @elseif($statusVal === 'approved')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Aktif
                                    </span>
                                @elseif($statusVal === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($statusVal === 'pending')
                                        <button wire:click="confirmApprove({{ $org->id }}, '{{ addslashes($org->nama_organisasi) }}')" class="p-1.5 text-green-600 hover:bg-green-100 rounded-lg transition border border-transparent hover:border-green-200" title="Setujui">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        
                                        <button wire:click="confirmReject({{ $org->id }}, '{{ addslashes($org->nama_organisasi) }}')" class="p-1.5 text-red-600 hover:bg-red-100 rounded-lg transition border border-transparent hover:border-red-200" title="Tolak">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    @endif

                                    <a href="{{ route('admin.moderasi-organisasi.detail', $org->id) }}" wire:navigate class="inline-block p-1.5 text-gray-400 hover:text-[#000666] hover:bg-indigo-50 rounded-lg transition" title="Lihat Detail">
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
                                    <p class="text-gray-500 font-medium">Tidak ada data organisasi yang sesuai kriteria pencarian/filter.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100 bg-gray-50/30">
            {{ $daftar_organisasi->links() }}
        </div>
    </div>

    @if($showApproveModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center transform transition-all">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Setujui Pengajuan?</h3>
                <p class="text-sm text-gray-500 mb-8 leading-relaxed">
                    Apakah Anda yakin ingin menyetujui pendaftaran <span class="font-bold text-gray-800">{{ $selectedOrgName }}</span>? Organisasi ini akan segera aktif di platform.
                </p>
                <div class="flex items-center justify-center gap-3">
                    <button wire:click="closeModal" class="flex-1 py-2.5 px-4 bg-white text-gray-700 font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition">Batal</button>
                    <button wire:click="approve" class="flex-1 py-2.5 px-4 bg-[#16A34A] hover:bg-green-700 text-white font-semibold rounded-xl transition shadow-sm">Ya, Setujui</button>
                </div>
            </div>
        </div>
    @endif

    @if($showRejectModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 transform transition-all">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tolak Pengajuan?</h3>
                    <p class="text-sm text-gray-500 leading-relaxed px-4">Berikan alasan penolakan agar organisasi <span class="font-bold text-gray-800">{{ $selectedOrgName }}</span> dapat melakukan perbaikan.</p>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-900 mb-2">Alasan Penolakan</label>
                    <textarea wire:model="pesanPenolakan" rows="4" class="w-full bg-[#FAFAFA] border border-gray-200 rounded-xl text-sm p-3 focus:ring-red-500 focus:border-red-500 placeholder-gray-400" placeholder="Tulis alasan di sini..."></textarea>
                    <x-input-error :messages="$errors->get('pesanPenolakan')" class="mt-1" />
                </div>
                <div class="flex items-center justify-center gap-3">
                    <button wire:click="closeModal" class="flex-1 py-2.5 px-4 bg-white text-gray-700 font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition">Batal</button>
                    <button wire:click="reject" class="flex-1 py-2.5 px-4 bg-[#DC2626] hover:bg-red-700 text-white font-semibold rounded-xl transition shadow-sm">Kirim Penolakan</button>
                </div>
            </div>
        </div>
    @endif
</div>