<?php

namespace App\Livewire\Pages\Admin;

use App\Models\User;
use App\Models\OrganisasiMahasiswa;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

class AdminProfil extends Component
{
    // State Informasi Profil
    public string $nama_admin = '';
    public string $email = '';
    public string $nama_fakultas = 'Tingkat Universitas'; // Untuk komponen read-only di UI
    public int $totalPendingOrganisasi = 0;
    public int $totalPendingEvent = 0;

    // State Ubah Kata Sandi
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $isChangingPassword = false;

    public function mount(): void
    {
        // Muat user beserta relasi profil admin dpm dan fakultasnya
        $user = User::with(['adminDpm.fakultas'])->findOrFail(Auth::id());
        
        $adminDpm = $user->adminDpm;

        if ($adminDpm) {
            $this->nama_admin = $adminDpm->nama_admin;
            $this->nama_fakultas = $adminDpm->fakultas ? $adminDpm->fakultas->nama_fakultas : 'Dewan Perwakilan Mahasiswa Universitas';

            $queryOrg = OrganisasiMahasiswa::query()->where('status', 'pending');
            if ($adminDpm->fakultas_id !== null) {
                $queryOrg->where('fakultas_id', $adminDpm->fakultas_id);
            } else {
                $queryOrg->where('tingkat_organisasi', 'universitas');
            }
            $this->totalPendingOrganisasi = $queryOrg->count();
            
            $queryEvent = Event::query()->where('status', 'pending_approval')
                ->whereHas('organisasi', function ($q) use ($adminDpm) {
                    if ($adminDpm->fakultas_id !== null) {
                        $q->where('fakultas_id', $adminDpm->fakultas_id);
                    } else {
                        $q->where('tingkat_organisasi', 'universitas');
                    }
                });
            $this->totalPendingEvent = $queryEvent->count();
        }

        $this->email = $user->email;
    }

    public function updateProfil(): void
    {
        $user = Auth::user();

        // Validasi ganda: email untuk tabel users, nama_admin untuk tabel admin_dpm
        $validated = $this->validate([
            'nama_admin' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'lowercase', 
                'email', 
                'max:255', 
                Rule::unique(User::class)->ignore($user->id)
            ],
        ], [
            'nama_admin.required' => 'Nama lengkap instansi admin wajib diisi.',
            'email.unique' => 'Alamat email tersebut sudah digunakan oleh akun lain.'
        ]);

        // Perbarui email di tabel users
        $user->update([
            'email' => $validated['email']
        ]);

        // Perbarui nama di tabel relasi admin_dpm
        $user->adminDpm()->update([
            'nama_admin' => $validated['nama_admin']
        ]);

        session()->flash('success', 'Informasi profil Admin DPM berhasil diperbarui.');
        $this->redirect(route('admin.profil'), navigate: true);
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini yang Anda masukkan salah.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.'
        ]);

        User::findOrFail(Auth::id())->update([
            'password' => Hash::make($validated['password']),
        ]);

        session()->flash('success', 'Kata sandi akun admin berhasil diubah.');
        $this->redirect(route('admin.profil'), navigate: true);
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName, [
            'nama_admin' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);
    }

    #[Layout('layouts.admin', ['active' => 'pengaturan'])]
    public function render()
    {
        return view('livewire.pages.admin.admin-profil');
    }
}