<?php

use App\Models\OrganisasiMahasiswa;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.admin')] class extends Component
{
    public OrganisasiMahasiswa $org;

    // State Modal
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $pesanPenolakan = '';

    public function mount($id)
    {
        // Muat data organisasi beserta relasi yang mungkin dibutuhkan
        $this->org = OrganisasiMahasiswa::with(['user', 'fakultas', 'prodi'])->findOrFail($id);
    }

    // --- FUNGSI SETUJUI ---
    public function approve()
    {
        $statusVal = $this->org->status instanceof \UnitEnum ? $this->org->status->value : $this->org->status;

        if ($statusVal === 'pending') {
            $this->org->update(['status' => 'approved']);
            session()->flash('success', "Organisasi '{$this->org->nama_organisasi}' berhasil disetujui.");
            $this->redirect(route('admin.moderasi-organisasi', absolute: false), navigate: true);
        }
    }

    // --- FUNGSI TOLAK ---
    public function reject()
    {
        $this->validate([
            'pesanPenolakan' => 'required|string|min:5'
        ], [
            'pesanPenolakan.required' => 'Alasan penolakan wajib diisi agar organisasi dapat memperbaikinya.',
            'pesanPenolakan.min' => 'Alasan penolakan terlalu singkat.'
        ]);

        $statusVal = $this->org->status instanceof \UnitEnum ? $this->org->status->value : $this->org->status;

        if ($statusVal === 'pending') {
            $this->org->update([
                'status' => 'rejected',
                'pesan_penolakan' => $this->pesanPenolakan
            ]);
            session()->flash('success', "Pengajuan organisasi '{$this->org->nama_organisasi}' berhasil ditolak.");
            $this->redirect(route('admin.moderasi-organisasi', absolute: false), navigate: true);
        }
    }

    public function closeModal()
    {
        $this->showApproveModal = false;
        $this->showRejectModal = false;
        $this->pesanPenolakan = '';
        $this->resetErrorBag();
    }
}; ?>

<div class="pb-20 relative"> @php
        $statusVal = $org->status instanceof \UnitEnum ? $org->status->value : $org->status;
        $tingkatVal = $org->tingkat_organisasi instanceof \UnitEnum ? $org->tingkat_organisasi->value : $org->tingkat_organisasi;
    @endphp

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.moderasi-organisasi') }}" wire:navigate class="p-2 text-gray-500 bg-gray-50 hover:bg-gray-100 rounded-xl transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-[#000666]">Detail Pengajuan Organisasi</h1>
                <p class="text-xs text-gray-500 mt-1 uppercase tracking-wider">ID Pengajuan: #ORG-{{ $org->created_at->format('Y') }}-{{ str_pad($org->id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>
        
        <div>
            @if($statusVal === 'pending')
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-100 uppercase">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Menunggu Review
                </span>
            @elseif($statusVal === 'approved')
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-100 uppercase">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Aktif (Disetujui)
                </span>
            @elseif($statusVal === 'rejected')
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-100 uppercase">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Ditolak
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- KOLOM KIRI (Info & Sosmed) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Box Identitas Utama -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
                <div class="h-24 bg-[#000666] w-full"></div> <!-- Header Biru -->
                
                <div class="px-6 pb-6 pt-12 relative">
                    <!-- Logo dengan posisi absolut agar tumpang tindih dengan header -->
                    <div class="absolute -top-12 left-6 w-24 h-24 rounded-2xl bg-white p-1.5 shadow-md border border-gray-100">
                        <div class="w-full h-full rounded-xl bg-gray-50 flex items-center justify-center overflow-hidden">
                            @if($org->logo_url)
                                <img src="{{ asset('storage/' . $org->logo_url) }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-[#000666] font-bold text-3xl">{{ substr($org->nama_organisasi, 0, 1) }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Nama & Tingkat -->
                    <div class="mt-4">
                        <h2 class="text-lg font-bold text-gray-900 leading-tight">{{ $org->nama_organisasi }}</h2>
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mt-1">Tingkat: {{ $tingkatVal }}</p>
                    </div>

                    <!-- List Kontak -->
                    <div class="mt-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-gray-50 rounded-lg text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
                            <div class="overflow-hidden">
                                <p class="text-xs font-bold text-gray-900 truncate">{{ $org->user->email ?? '-' }}</p>
                                <p class="text-[9px] text-gray-400 uppercase">Email Akun</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-gray-50 rounded-lg text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></div>
                            <div>
                                <p class="text-xs font-bold text-gray-900">{{ $org->no_organisasi }}</p>
                                <p class="text-[9px] text-gray-400 uppercase">Nomor Kontak / SK</p>
                            </div>
                        </div>

                        @if(in_array($tingkatVal, ['prodi', 'fakultas']))
                        <div class="flex items-center gap-3 border-t pt-4">
                            <div class="p-2 bg-gray-50 rounded-lg text-gray-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                            <div class="overflow-hidden">
                                <p class="text-xs font-bold text-gray-900 truncate">{{ $org->fakultas->nama_fakultas ?? '-' }}</p>
                                <p class="text-[9px] text-gray-400 uppercase">Afiliasi Akademik</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Box Sosial Media -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 mb-4 uppercase tracking-wider">Sosial Media</p>
                    <div class="flex flex-col gap-2">
                        @if($org->ig_url)
                            <a href="{{ $org->ig_url }}" target="_blank" class="flex items-center gap-3 text-xs font-bold text-gray-700 hover:text-indigo-600 truncate transition">
                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center">IG</div>
                                {{ $org->ig_url }}
                            </a>
                        @endif
                        @if($org->linkedin_url)
                            <a href="{{ $org->linkedin_url }}" target="_blank" class="flex items-center gap-3 text-xs font-bold text-gray-700 hover:text-indigo-600 truncate transition">
                                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center">IN</div>
                                {{ $org->linkedin_url }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 space-y-6">
            
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2 border-b border-gray-100 pb-4">
                    <svg class="w-5 h-5 text-[#000666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Profil & Tujuan Organisasi
                </h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Deskripsi Organisasi</h3>
                        <p class="text-sm text-gray-700 leading-relaxed text-justify whitespace-pre-line">{{ $org->deskripsi }}</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-50">
                        <div class="bg-[#F8F9FE] p-5 rounded-xl border border-indigo-50">
                            <h3 class="text-xs font-bold text-[#000666] uppercase tracking-wider mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Visi
                            </h3>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $org->visi }}</p>
                        </div>
                        <div class="bg-[#F8F9FE] p-5 rounded-xl border border-indigo-50">
                            <h3 class="text-xs font-bold text-[#000666] uppercase tracking-wider mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                Misi
                            </h3>
                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $org->misi }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2 border-b border-gray-100 pb-4">
                    <svg class="w-5 h-5 text-[#000666]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Dokumen Legalitas
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-indigo-300 hover:shadow-sm transition bg-gray-50/50 gap-4">
                        <div class="flex items-center gap-4 w-full">
                            <div class="w-12 h-12 bg-red-100 text-red-600 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">AD / ART Organisasi</p>
                                <p class="text-[10px] text-gray-500 uppercase mt-0.5">Format: PDF</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 w-full sm:w-auto shrink-0 justify-end">
                            <a href="{{ asset('storage/' . $org->ad_art) }}" target="_blank" class="px-3 py-1.5 text-xs font-semibold text-[#000666] bg-indigo-50 border border-indigo-100 hover:bg-indigo-100 rounded-lg transition">Lihat</a>
                            <a href="{{ asset('storage/' . $org->ad_art) }}" download class="px-3 py-1.5 text-xs font-semibold text-white bg-[#000666] hover:bg-indigo-900 rounded-lg shadow-sm transition">Unduh</a>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-indigo-300 hover:shadow-sm transition bg-gray-50/50 gap-4">
                        <div class="flex items-center gap-4 w-full">
                            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">SK Kepengurusan</p>
                                <p class="text-[10px] text-gray-500 uppercase mt-0.5">Format: PDF</p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 w-full sm:w-auto shrink-0 justify-end">
                            <a href="{{ asset('storage/' . $org->sk) }}" target="_blank" class="px-3 py-1.5 text-xs font-semibold text-[#000666] bg-indigo-50 border border-indigo-100 hover:bg-indigo-100 rounded-lg transition">Lihat</a>
                            <a href="{{ asset('storage/' . $org->sk) }}" download class="px-3 py-1.5 text-xs font-semibold text-white bg-[#000666] hover:bg-indigo-900 rounded-lg shadow-sm transition">Unduh</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @if($statusVal === 'pending')
        <div class="fixed bottom-0 left-0 lg:left-64 right-0 bg-white border-t border-gray-200 p-4 px-6 md:px-10 z-40 flex items-center justify-between shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="hidden md:inline">Periksa seluruh dokumen sebelum memberikan persetujuan final.</span>
            </div>
            
            <div class="flex gap-3">
                <button wire:click="$set('showRejectModal', true)" class="px-6 py-2.5 text-sm font-bold text-red-600 bg-white border-2 border-red-600 rounded-xl hover:bg-red-50 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Tolak Pengajuan
                </button>
                <button wire:click="$set('showApproveModal', true)" class="px-6 py-2.5 text-sm font-bold text-white bg-[#000666] rounded-xl hover:bg-indigo-900 transition shadow-md flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Setujui Organisasi
                </button>
            </div>
        </div>
    @endif

    @if($showApproveModal)
        <div class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center transform transition-all">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Setujui Pengajuan?</h3>
                <p class="text-sm text-gray-500 mb-8 leading-relaxed">
                    Apakah Anda yakin ingin menyetujui pendaftaran organisasi <span class="font-bold text-gray-800">{{ $org->nama_organisasi }}</span>?
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
                    <p class="text-sm text-gray-500 leading-relaxed px-4">Berikan alasan penolakan agar organisasi <span class="font-bold text-gray-800">{{ $org->nama_organisasi }}</span> dapat melakukan perbaikan.</p>
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