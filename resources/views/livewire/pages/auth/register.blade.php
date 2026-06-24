<?php

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\OrganisasiMahasiswa;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Enums\UserRole;
use App\Enums\OrganisasiStatus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.guest')] class extends Component
{
    use WithFileUploads; // Wajib ditambahkan untuk memproses upload file

    public string $jenis_akun = 'mahasiswa';

    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Field Mahasiswa
    public string $nama = '';
    public string $nim = '';

    // Field Organisasi Dasar
    public string $nama_organisasi = '';
    public string $no_organisasi = '';
    public string $tingkat_organisasi = '';
    public $fakultas_id = '';
    public $prodi_id = '';

    // Field Organisasi Wajib Baru
    public string $deskripsi = '';
    public string $visi = '';
    public string $misi = '';
    public $ad_art;
    public $sk;

    public function with(): array
    {
        return [
            'daftar_fakultas' => Fakultas::all(),
            'semua_prodi' => Prodi::with('fakultas')->get(),
            'prodi_terfilter' => $this->fakultas_id ? Prodi::where('fakultas_id', $this->fakultas_id)->get() : [],
        ];
    }

    public function register(): void
    {
        $rules = [
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ];

        if ($this->jenis_akun === 'mahasiswa') {
            $rules['nama'] = ['required', 'string', 'max:255'];
            $rules['nim'] = ['required', 'string', 'max:50', 'unique:mahasiswa,nim'];
            $rules['prodi_id'] = ['required', 'exists:prodi,id'];
        } else {
            // Validasi data organisasi
            $rules['nama_organisasi'] = ['required', 'string', 'max:255'];
            $rules['no_organisasi'] = ['required', 'string', 'max:50']; // Sekarang wajib (required) sesuai DB
            $rules['tingkat_organisasi'] = ['required', 'in:prodi,fakultas,universitas'];
            $rules['deskripsi'] = ['required', 'string'];
            $rules['visi'] = ['required', 'string'];
            $rules['misi'] = ['required', 'string'];
            $rules['ad_art'] = ['required', 'file', 'mimes:pdf', 'max:5120']; // Wajib PDF, max 5MB
            $rules['sk'] = ['required', 'file', 'mimes:pdf', 'max:5120']; // Wajib PDF, max 5MB

            if (in_array($this->tingkat_organisasi, ['prodi', 'fakultas'])) {
                $rules['fakultas_id'] = ['required', 'exists:fakultas,id'];
            }
            if ($this->tingkat_organisasi === 'prodi') {
                $rules['prodi_id'] = ['required', 'exists:prodi,id'];
            }
        }

        $validated = $this->validate($rules);

        $user = DB::transaction(function () {
            $user = User::create([
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->jenis_akun === 'mahasiswa' ? UserRole::MAHASISWA : UserRole::ORGANISASI,
            ]);

            $user->assignRole($user->role->value);

            if ($this->jenis_akun === 'mahasiswa') {
                Mahasiswa::create([
                    'user_id' => $user->id,
                    'prodi_id' => $this->prodi_id,
                    'nama' => $this->nama,
                    'nim' => $this->nim,
                ]);
            } else {
                // Simpan file dokumen yang diunggah
                $pathAdArt = $this->ad_art->store('dokumen', 'public');
                $pathSk = $this->sk->store('dokumen', 'public');

                OrganisasiMahasiswa::create([
                    'user_id' => $user->id,
                    'nama_organisasi' => $this->nama_organisasi,
                    'no_organisasi' => $this->no_organisasi,
                    'tingkat_organisasi' => $this->tingkat_organisasi,
                    'fakultas_id' => in_array($this->tingkat_organisasi, ['prodi', 'fakultas']) ? $this->fakultas_id : null,
                    'prodi_id' => $this->tingkat_organisasi === 'prodi' ? $this->prodi_id : null,
                    'status' => OrganisasiStatus::PENDING,
                    'deskripsi' => $this->deskripsi,
                    'visi' => $this->visi,
                    'misi' => $this->misi,
                    'ad_art' => $pathAdArt,
                    'sk' => $pathSk,
                ]);
            }

            event(new Registered($user));
            Auth::login($user);

            return $user;
        });

        if ($user->role === 'mahasiswa') {
            $this->redirect(route('mahasiswa.dashboard', absolute: false), navigate: true);
        } elseif ($user->role === 'organisasi') {
            $this->redirect(route('organisasi.dashboard', absolute: false), navigate: true);
        } else {
            $this->redirect(route('dashboard', absolute: false), navigate: true);
        }
    }
}; ?>

<div>
    <form wire:submit="register">
        
        <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
            <x-input-label value="Mendaftar Sebagai:" class="mb-2 text-gray-700" />
            <div class="flex items-center space-x-6">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" wire:model.live="jenis_akun" value="mahasiswa" class="text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700 font-medium">Mahasiswa</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" wire:model.live="jenis_akun" value="organisasi" class="text-indigo-600 border-gray-300 focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700 font-medium">Organisasi Kampus</span>
                </label>
            </div>
        </div>

        @if($jenis_akun === 'mahasiswa')
            <div>
                <x-input-label for="nama" value="Nama Lengkap" />
                <x-text-input wire:model="nama" id="nama" class="block mt-1 w-full" type="text" required autofocus />
                <x-input-error :messages="$errors->get('nama')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="nim" value="NIM" />
                <x-text-input wire:model="nim" id="nim" class="block mt-1 w-full" type="text" required />
                <x-input-error :messages="$errors->get('nim')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="prodi_id" value="Program Studi" />
                <select wire:model="prodi_id" id="prodi_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                    <option value="">-- Pilih Program Studi --</option>
                    @foreach($semua_prodi as $prodi)
                        <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }} ({{ $prodi->fakultas->nama_fakultas }})</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('prodi_id')" class="mt-2" />
            </div>
        @endif

        @if($jenis_akun === 'organisasi')
            <div>
                <x-input-label for="nama_organisasi" value="Nama Organisasi" />
                <x-text-input wire:model="nama_organisasi" id="nama_organisasi" class="block mt-1 w-full" type="text" required autofocus />
                <x-input-error :messages="$errors->get('nama_organisasi')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="no_organisasi" value="Nomor Organisasi / Kontak" />
                <x-text-input wire:model="no_organisasi" id="no_organisasi" class="block mt-1 w-full" type="text" required />
                <x-input-error :messages="$errors->get('no_organisasi')" class="mt-2" />
            </div>

            <div class="mt-4 p-4 border rounded-md bg-white shadow-sm">
                <x-input-label for="tingkat_organisasi" value="Tingkat Organisasi" />
                <select wire:model.live="tingkat_organisasi" id="tingkat_organisasi" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                    <option value="">-- Pilih Tingkatan --</option>
                    <option value="universitas">Tingkat Universitas</option>
                    <option value="fakultas">Tingkat Fakultas</option>
                    <option value="prodi">Tingkat Program Studi (Prodi)</option>
                </select>
                <x-input-error :messages="$errors->get('tingkat_organisasi')" class="mt-2" />

                @if(in_array($tingkat_organisasi, ['fakultas', 'prodi']))
                    <div class="mt-4">
                        <x-input-label for="fakultas_id" value="Pilih Fakultas" />
                        <select wire:model.live="fakultas_id" id="fakultas_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($daftar_fakultas as $fakultas)
                                <option value="{{ $fakultas->id }}">{{ $fakultas->nama_fakultas }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('fakultas_id')" class="mt-2" />
                    </div>
                @endif

                @if($tingkat_organisasi === 'prodi' && $fakultas_id)
                    <div class="mt-4">
                        <x-input-label for="prodi_id" value="Pilih Program Studi" />
                        <select wire:model="prodi_id" id="prodi_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required>
                            <option value="">-- Pilih Prodi --</option>
                            @foreach($prodi_terfilter as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('prodi_id')" class="mt-2" />
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <x-input-label for="deskripsi" value="Deskripsi Organisasi" />
                <textarea wire:model="deskripsi" id="deskripsi" rows="3" class="block w-full mt-1 border-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required></textarea>
                <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="visi" value="Visi Organisasi" />
                <textarea wire:model="visi" id="visi" rows="2" class="block w-full mt-1 border-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required></textarea>
                <x-input-error :messages="$errors->get('visi')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="misi" value="Misi Organisasi" />
                <textarea wire:model="misi" id="misi" rows="3" class="block w-full mt-1 border-gray-300 focus:border-indigo-500 rounded-md shadow-sm" required></textarea>
                <x-input-error :messages="$errors->get('misi')" class="mt-2" />
            </div>

            <div class="mt-6 p-4 border rounded-md bg-gray-50 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 mb-4 border-b pb-2">Dokumen Pendukung (Wajib PDF)</h3>
                
                <div class="mt-4">
                    <x-input-label for="ad_art" value="Dokumen AD/ART (PDF)" />
                    <input type="file" wire:model="ad_art" id="ad_art" accept="application/pdf" class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required />
                    <x-input-error :messages="$errors->get('ad_art')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="sk" value="SK Kepengurusan (PDF)" />
                    <input type="file" wire:model="sk" id="sk" accept="application/pdf" class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required />
                    <x-input-error :messages="$errors->get('sk')" class="mt-2" />
                </div>
            </div>
        @endif

        <hr class="my-6 border-gray-200">

        <div>
            <x-input-label for="email" :value="__('Email (Untuk Login)')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full bg-white" type="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full" type="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full" type="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('Sudah punya akun?') }}
            </a>

            <x-primary-button class="ms-4">
                <span wire:loading.remove wire:target="register">{{ __('Daftar Sekarang') }}</span>
                <span wire:loading wire:target="register">{{ __('Memproses...') }}</span>
            </x-primary-button>
        </div>
    </form>
</div>