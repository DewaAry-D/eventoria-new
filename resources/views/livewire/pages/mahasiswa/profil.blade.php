<?php

use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.mahasiswa')] class extends Component
{
    public $mahasiswa;
    public $user;

    // Field yang bisa diedit
    public string $nama = '';
    public string $nim = '';
    public $prodi_id = '';

    // Field password (opsional, hanya diisi kalau mau ganti)
    public string $password = '';
    public string $password_confirmation = '';
    
    // PENAMBAHAN: Field untuk password saat ini
    public string $current_password = '';

    // Untuk dropdown pilihan prodi
    public $daftarProdi = [];

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->mahasiswa = $this->user->load('mahasiswa.prodi.fakultas')->mahasiswa;

        // Isi form dengan data yang sudah ada di database
        $this->nama    = $this->mahasiswa->nama ?? '';
        $this->nim     = $this->mahasiswa->nim ?? '';
        $this->prodi_id = $this->mahasiswa->prodi_id ?? '';

        // Ambil semua prodi untuk dropdown
        $this->daftarProdi = Prodi::with('fakultas')->orderBy('nama_prodi')->get();
    }

    public function simpanProfil(): void
    {
        $this->validate([
            'nama'    => 'required|string|max:255',
            // NIM tidak divalidasi unique karena field sudah di-disabled di UI
            // (mahasiswa tidak bisa mengubah NIM)
            'prodi_id' => 'required|exists:prodi,id',
            'password' => 'nullable|string|min:8|confirmed',
            // PENAMBAHAN: Validasi current password
            'current_password' => $this->password ? 'required' : 'nullable',
        ], [
            'nama.required'    => 'Nama lengkap wajib diisi.',
            'nim.required'     => 'NIM wajib diisi.',
            'nim.unique'       => 'NIM ini sudah digunakan oleh akun lain.',
            'prodi_id.required' => 'Program studi wajib dipilih.',
            'password.min'     => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // PENAMBAHAN: Pengecekan Hash password lama
        if (!empty($this->password)) {
            if (!Hash::check($this->current_password, $this->user->password)) {
                $this->addError('current_password', 'Kata sandi saat ini tidak sesuai.');
                return;
            }
        }

        // Simpan perubahan data mahasiswa
        // NIM tidak diupdate karena sudah disabled dan tidak bisa diubah mahasiswa
        $this->mahasiswa->update([
            'nama'     => $this->nama,
            'prodi_id' => $this->prodi_id,
        ]);

        // Ganti password hanya kalau diisi
        if (!empty($this->password)) {
            $this->user->update([
                'password' => Hash::make($this->password),
            ]);
        }

        // Kosongkan field password setelah simpan
        $this->password = '';
        $this->password_confirmation = '';
        // Kosongkan juga current_password
        $this->current_password = '';

        // Reload data mahasiswa supaya tampilan terupdate
        $this->mahasiswa = $this->user->fresh()->load('mahasiswa.prodi.fakultas')->mahasiswa;

        session()->flash('success', 'Profil Anda telah berhasil diperbarui.');
    }
}; ?>

<div>
    {{-- Header halaman --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola informasi pribadi dan keamanan akun Anda.</p>

        {{-- Notifikasi sukses --}}
        @if (session()->has('success'))
            <div class="mt-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 flex items-center gap-2 shadow-sm border border-green-200">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
    </div>

    <form wire:submit="simpanProfil" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ===================== --}}
            {{-- KOLOM KIRI: Kartu identitas singkat --}}
            {{-- ===================== --}}
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col items-center text-center">

                    {{-- Avatar dari nama --}}
                    <div class="w-28 h-28 rounded-full bg-indigo-100 flex items-center justify-center mb-4 shadow-inner">
                        <img
                            src="https://ui-avatars.com/api/?name={{ urlencode($mahasiswa->nama ?? 'M') }}&background=E0E7FF&color=4338CA&size=112&bold=true"
                            alt="Avatar"
                            class="w-full h-full rounded-full object-cover"
                        >
                    </div>

                    {{-- Nama & NIM --}}
                    <p class="text-lg font-bold text-gray-900">{{ $mahasiswa->nama ?? '-' }}</p>
                    <p class="text-sm text-gray-500 mt-1">NIM: {{ $mahasiswa->nim ?? '-' }}</p>

                    <div class="w-full mt-5 pt-4 border-t border-gray-100 space-y-3 text-left">
                        {{-- Program Studi --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Program Studi</p>
                            <p class="text-sm text-gray-700 font-medium mt-0.5">{{ $mahasiswa->prodi->nama_prodi ?? '-' }}</p>
                        </div>
                        {{-- Fakultas --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Fakultas</p>
                            <p class="text-sm text-gray-700 font-medium mt-0.5">{{ $mahasiswa->prodi->fakultas->nama_fakultas ?? '-' }}</p>
                        </div>
                        {{-- Email --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Email</p>
                            <p class="text-sm text-gray-700 font-medium mt-0.5 break-all">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== --}}
            {{-- KOLOM KANAN: Form edit --}}
            {{-- ===================== --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Card: Informasi Pribadi --}}
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h2 class="text-base font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100">Informasi Pribadi</h2>

                    <div class="space-y-4">
                        {{-- Nama Lengkap & NIM --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="nama" value="Nama Lengkap" />
                                <x-text-input
                                    wire:model="nama"
                                    id="nama"
                                    class="block w-full mt-1"
                                    type="text"
                                    placeholder="Masukkan nama lengkap"
                                />
                                <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="nim" value="NIM" />
                                <input
                                type="text"
                                value="{{ $mahasiswa->nim ?? '-' }}"
                                disabled
                                class="block w-full mt-1 text-sm bg-gray-100 border-gray-200 rounded-md text-gray-500 cursor-not-allowed">
                                <x-input-error :messages="$errors->get('nim')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Program Studi (dropdown) --}}
                        <div>
                            <x-input-label for="prodi_id" value="Program Studi" />
                            <select
                                wire:model="prodi_id"
                                id="prodi_id"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach ($daftarProdi as $prodi)
                                    <option value="{{ $prodi->id }}">
                                        {{ $prodi->nama_prodi }} ({{ $prodi->fakultas->nama_fakultas ?? '' }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('prodi_id')" class="mt-2" />
                        </div>

                        {{-- Email (hanya tampil, tidak bisa diedit) --}}
                        <div>
                            <x-input-label for="email" value="Email Akun (Tidak dapat diubah)" />
                            <input
                                type="text"
                                id="email"
                                value="{{ $user->email }}"
                                disabled
                                class="block w-full mt-1 text-sm bg-gray-100 border-gray-200 rounded-md text-gray-500 cursor-not-allowed"
                            >
                        </div>
                    </div>
                </div>

                {{-- Card: Ganti Password --}}
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <h2 class="text-base font-bold text-gray-900 mb-1 pb-2 border-b border-gray-100">Keamanan Akun</h2>
                    <p class="text-xs text-gray-400 mb-4">Kosongkan jika tidak ingin mengubah kata sandi.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        {{-- PENAMBAHAN: UI Kata Sandi Saat Ini --}}
                        <div class="md:col-span-2 mb-2">
                            <x-input-label for="current_password" value="Kata Sandi Saat Ini" />
                            <x-text-input
                                wire:model="current_password"
                                id="current_password"
                                class="block w-full mt-1"
                                type="password"
                                placeholder="Masukkan kata sandi Anda yang sekarang"
                                autocomplete="current-password"
                            />
                            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" value="Kata Sandi Baru" />
                            <x-text-input
                                wire:model="password"
                                id="password"
                                class="block w-full mt-1"
                                type="password"
                                placeholder="Minimal 8 karakter"
                                autocomplete="new-password"
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" />
                            <x-text-input
                                wire:model="password_confirmation"
                                id="password_confirmation"
                                class="block w-full mt-1"
                                type="password"
                                placeholder="Ulangi kata sandi baru"
                                autocomplete="new-password"
                            />
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex items-center justify-between pt-2">
                    <a
                        href="{{ route('mahasiswa.dashboard') }}"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                    >
                        ← Kembali ke Dashboard
                    </a>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition shadow-sm flex items-center gap-2 disabled:opacity-70"
                    >
                        <span wire:loading.remove wire:target="simpanProfil">Simpan Perubahan</span>
                        <span wire:loading wire:target="simpanProfil">Menyimpan...</span>
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>