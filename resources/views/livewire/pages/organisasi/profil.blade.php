<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.organisasi')] class extends Component
{
    use WithFileUploads;

    public $organisasi;
    public $user;
    
    // Editable Fields (Informasi Publik)
    public $nama_organisasi;
    public $no_organisasi;
    public $ig_url;
    public $linkedin_url;

    // Editable Fields (Detail Organisasi - Wajib)
    public $deskripsi;
    public $visi;
    public $misi;
    
    // File handling
    public $logo;
    public $logo_url_lama;
    
    public $ad_art;
    public $ad_art_lama;
    
    public $sk;
    public $sk_lama;

    public function mount()
    {
        $this->user = Auth::user();
        $this->organisasi = $this->user->load('organisasi.fakultas', 'organisasi.prodi')->organisasi;

        // Populate default value dari database
        $this->nama_organisasi = $this->organisasi->nama_organisasi ?? '';
        $this->no_organisasi = $this->organisasi->no_organisasi ?? '';
        $this->ig_url = $this->organisasi->ig_url ?? '';
        $this->linkedin_url = $this->organisasi->linkedin_url ?? '';
        
        $this->deskripsi = $this->organisasi->deskripsi ?? '';
        $this->visi = $this->organisasi->visi ?? '';
        $this->misi = $this->organisasi->misi ?? '';

        $this->logo_url_lama = $this->organisasi->logo_url ?? null;
        $this->ad_art_lama = $this->organisasi->ad_art ?? null;
        $this->sk_lama = $this->organisasi->sk ?? null;
    }

    public function simpanProfil()
    {
        $this->validate([
            'nama_organisasi' => 'required|string|max:255',
            'no_organisasi' => 'required|string|max:50',
            'ig_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'deskripsi' => 'required|string',
            'visi' => 'required|string',
            'misi' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maks 2MB
            'ad_art' => 'nullable|file|mimes:pdf|max:5120', // Maks 5MB PDF
            'sk' => 'nullable|file|mimes:pdf|max:5120', // Maks 5MB PDF
        ], [
            'ig_url.url' => 'Format URL Instagram tidak valid.',
            'linkedin_url.url' => 'Format URL LinkedIn tidak valid.',
            'ad_art.mimes' => 'AD/ART harus berupa file PDF.',
            'sk.mimes' => 'SK Kepengurusan harus berupa file PDF.',
        ]);

        $pathLogo = $this->logo_url_lama;
        $pathAdArt = $this->ad_art_lama;
        $pathSk = $this->sk_lama;
        
        // 1. Proses Logo
        if ($this->logo) {
            if ($this->logo_url_lama) Storage::disk('public')->delete($this->logo_url_lama);
            $pathLogo = $this->logo->store('logos', 'public');
            $this->logo_url_lama = $pathLogo;
        }

        // 2. Proses AD/ART
        if ($this->ad_art) {
            if ($this->ad_art_lama) Storage::disk('public')->delete($this->ad_art_lama);
            $pathAdArt = $this->ad_art->store('dokumen', 'public');
            $this->ad_art_lama = $pathAdArt;
        }

        // 3. Proses SK
        if ($this->sk) {
            if ($this->sk_lama) Storage::disk('public')->delete($this->sk_lama);
            $pathSk = $this->sk->store('dokumen', 'public');
            $this->sk_lama = $pathSk;
        }

        // Simpan ke database
        $this->organisasi->update([
            'nama_organisasi' => $this->nama_organisasi,
            'no_organisasi' => $this->no_organisasi,
            'ig_url' => $this->ig_url,
            'linkedin_url' => $this->linkedin_url,
            'deskripsi' => $this->deskripsi,
            'visi' => $this->visi,
            'misi' => $this->misi,
            'logo_url' => $pathLogo,
            'ad_art' => $pathAdArt,
            'sk' => $pathSk,
        ]);

        // Bersihkan input file sementara
        $this->logo = null;
        $this->ad_art = null;
        $this->sk = null;

        session()->flash('success', 'Profil organisasi berhasil diperbarui!');
    }
}; ?>

<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Profil Organisasi</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola informasi publik dan identitas visual organisasi Anda.</p>
        
        @if (session()->has('success'))
            <div class="mt-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 flex items-center shadow-sm">
                <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                {{ session('success') }}
            </div>
        @endif
    </div>

    <form wire:submit="simpanProfil" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col items-center text-center">
                    <h2 class="text-sm font-bold text-gray-900 mb-4 w-full text-left border-b pb-2">Logo Organisasi</h2>
                    
                    <div class="relative w-32 h-32 mb-4 rounded-full border-4 border-gray-50 shadow-inner overflow-hidden bg-gray-100 flex items-center justify-center">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif ($logo_url_lama)
                            <img src="{{ asset('storage/' . $logo_url_lama) }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-12 h-12 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        @endif
                    </div>

                    <div class="w-full">
                        <label class="block w-full cursor-pointer bg-indigo-50 text-indigo-700 text-sm font-semibold py-2 px-4 rounded-lg hover:bg-indigo-100 transition text-center border border-indigo-200">
                            <span>Ganti Logo</span>
                            <input type="file" wire:model="logo" class="hidden" accept="image/jpeg, image/png">
                        </label>
                        <p class="text-xs text-gray-400 mt-2">JPG atau PNG. Maks 2MB.</p>
                        <x-input-error :messages="$errors->get('logo')" class="mt-2 text-left" />
                    </div>
                </div>
                
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h2 class="text-sm font-bold text-gray-900 mb-4 border-b border-gray-200 pb-2">Data Sistem (Terkunci)</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500">Email Akun</label>
                            <input type="text" value="{{ $user->email }}" disabled class="block w-full mt-1 text-sm bg-gray-200 border-transparent rounded-md text-gray-600 cursor-not-allowed">
                        </div>
                        
                        <div>
                            <label class="block text-xs font-semibold text-gray-500">Tingkat Organisasi</label>
                            <input type="text" value="{{ strtoupper($organisasi->tingkat_organisasi->value) }}" disabled class="block w-full mt-1 text-sm bg-gray-200 border-transparent rounded-md text-gray-600 cursor-not-allowed">
                        </div>

                        @if($organisasi->fakultas_id)
                        <div>
                            <label class="block text-xs font-semibold text-gray-500">Fakultas</label>
                            <input type="text" value="{{ $organisasi->fakultas->nama_fakultas ?? 'Fakultas ID: ' . $organisasi->fakultas_id }}" disabled class="block w-full mt-1 text-sm bg-gray-200 border-transparent rounded-md text-gray-600 cursor-not-allowed">
                        </div>
                        @endif

                        @if($organisasi->prodi_id)
                        <div>
                            <label class="block text-xs font-semibold text-gray-500">Program Studi</label>
                            <input type="text" value="{{ $organisasi->prodi->nama_prodi ?? 'Prodi ID: ' . $organisasi->prodi_id }}" disabled class="block w-full mt-1 text-sm bg-gray-200 border-transparent rounded-md text-gray-600 cursor-not-allowed">
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Informasi Publik</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <x-input-label value="Nama Organisasi" required />
                            <x-text-input wire:model="nama_organisasi" class="block w-full mt-1" type="text" placeholder="Cth: Badan Eksekutif Mahasiswa" />
                            <x-input-error :messages="$errors->get('nama_organisasi')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="Nomor Organisasi / Kontak" required />
                            <x-text-input wire:model="no_organisasi" class="block w-full mt-1" type="text" placeholder="Cth: 081234567890" />
                            <x-input-error :messages="$errors->get('no_organisasi')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label value="Link Instagram" />
                                <x-text-input wire:model="ig_url" class="block w-full mt-1" type="url" placeholder="https://instagram.com/..." />
                                <x-input-error :messages="$errors->get('ig_url')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label value="Link LinkedIn" />
                                <x-text-input wire:model="linkedin_url" class="block w-full mt-1" type="url" placeholder="https://linkedin.com/company/..." />
                                <x-input-error :messages="$errors->get('linkedin_url')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Detail Organisasi</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <x-input-label value="Deskripsi Singkat" required />
                            <textarea wire:model="deskripsi" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Ceritakan tentang organisasi Anda..."></textarea>
                            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                        </div>
                        
                        <div>
                            <x-input-label value="Visi Organisasi" required />
                            <textarea wire:model="visi" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Visi yang ingin dicapai..."></textarea>
                            <x-input-error :messages="$errors->get('visi')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="Misi Organisasi" required />
                            <textarea wire:model="misi" rows="4" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="1. Misi pertama&#10;2. Misi kedua"></textarea>
                            <x-input-error :messages="$errors->get('misi')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Dokumen Legal</h2>
                    <p class="text-xs text-gray-500 mb-4">Unggah dokumen baru hanya jika Anda ingin memperbarui file yang sudah ada.</p>

                    <div class="space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 border p-4 rounded-lg bg-gray-50">
                            <div class="flex-1">
                                <label class="block text-sm font-bold text-gray-700">Dokumen AD/ART</label>
                                @if($ad_art_lama)
                                    <a href="{{ asset('storage/' . $ad_art_lama) }}" target="_blank" class="text-xs text-indigo-600 hover:underline flex items-center gap-1 mt-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        Lihat File Saat Ini
                                    </a>
                                @endif
                            </div>
                            <div class="flex-shrink-0 w-full sm:w-auto">
                                <input type="file" wire:model="ad_art" accept="application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                                <x-input-error :messages="$errors->get('ad_art')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center gap-4 border p-4 rounded-lg bg-gray-50">
                            <div class="flex-1">
                                <label class="block text-sm font-bold text-gray-700">SK Kepengurusan</label>
                                @if($sk_lama)
                                    <a href="{{ asset('storage/' . $sk_lama) }}" target="_blank" class="text-xs text-indigo-600 hover:underline flex items-center gap-1 mt-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        Lihat File Saat Ini
                                    </a>
                                @endif
                            </div>
                            <div class="flex-shrink-0 w-full sm:w-auto">
                                <input type="file" wire:model="sk" accept="application/pdf" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition">
                                <x-input-error :messages="$errors->get('sk')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 flex items-center transition shadow-sm">
                        <span wire:loading.remove wire:target="simpanProfil">Simpan Perubahan Profil</span>
                        <span wire:loading wire:target="simpanProfil">Menyimpan...</span>
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>