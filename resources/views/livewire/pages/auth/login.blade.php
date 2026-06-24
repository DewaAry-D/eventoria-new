<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $user = Auth::user();

        // Logika Redirect Berdasarkan Role
        if ($user->hasRole('mahasiswa')) {
            $this->redirectIntended(default: route('mahasiswa.dashboard', absolute: false), navigate: true);
        } elseif ($user->hasRole('organisasi')) {
            $this->redirectIntended(default: route('organisasi.dashboard', absolute: false), navigate: true);
        } elseif ($user->hasRole('admin_dpm')) {
            $this->redirectIntended(default: route('admin.dashboard', absolute: false), navigate: true);
        } else {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        }
    }
}; ?>

<div class="flex min-h-screen w-full bg-surface-container-lowest">
    
    <div class="relative hidden w-1/2 overflow-hidden bg-primary lg:flex">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&q=80')] bg-cover bg-center bg-no-repeat opacity-100 mix-blend-overlay"></div>
        
        <div class="absolute inset-0 bg-gradient-to-b from-primary/80 to-primary/95"></div>

        <div class="relative z-10 flex h-full w-full flex-col justify-center px-16 xl:px-24">
            <div class="mb-12 text-lg font-bold tracking-widest text-on-primary">EVENTORIA</div>
            
            <h1 class="text-display-lg mb-6 leading-[1.1] text-on-primary">
                Kelola Event<br>
                Kampus Lebih<br>
                Efisien.
            </h1>
            
            <p class="text-body-lg text-inverse-primary mb-14 max-w-md">
                Satu platform terintegrasi untuk mahasiswa, organisasi, dan administrasi kampus dalam mengatur jadwal dan kegiatan akademik.
            </p>

            <div class="flex gap-4 max-w-lg">
                <div class="flex-1 rounded-xl border border-white/20 bg-white/10 p-5 backdrop-blur-md">
                    <i class="fa-regular fa-calendar-check mb-4 text-2xl text-on-primary"></i>
                    <h3 class="text-sm font-semibold text-on-primary">Penjadwalan Otomatis</h3>
                </div>
                <div class="flex-1 rounded-xl border border-white/20 bg-white/10 p-5 backdrop-blur-md">
                    <i class="fa-solid fa-chart-pie mb-4 text-2xl text-on-primary"></i>
                    <h3 class="text-sm font-semibold text-on-primary">Laporan Real-time</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="flex w-full flex-col items-center justify-center p-8 lg:w-1/2">
        <div class="w-full max-w-[400px]">
            
            <div class="mb-8">
                <h2 class="text-headline-md font-bold tracking-tight text-primary">Selamat Datang Kembali</h2>
                <p class="text-body-md mt-1.5 text-on-surface-variant">Masuk ke akun akademik Anda untuk melanjutkan.</p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form wire:submit="login" class="flex flex-col gap-5">
                
                <div>
                    <label for="email" class="text-label-md mb-1.5 block text-on-surface">Alamat Email Kampus</label>
                    <input wire:model="form.email" id="email" type="email" placeholder="nama@mahasiswa.ac.id" required autofocus autocomplete="username" 
                           class="text-body-md block w-full rounded-md border-outline-variant bg-transparent px-3.5 py-2.5 text-on-surface transition-colors focus:border-primary focus:ring focus:ring-primary-fixed focus:ring-opacity-50" />
                    <x-input-error :messages="$errors->get('form.email')" class="text-error mt-2 text-sm font-medium" />
                </div>

                <div x-data="{ show: false }">
                    <div class="mb-1.5 flex items-center justify-between">
                        <label for="password" class="text-label-md block text-on-surface">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a wire:navigate class="text-[12px] font-bold text-primary transition-colors hover:text-primary-container focus:outline-none" href="{{ route('password.request') }}">
                                Lupa kata sandi?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <input wire:model="form.password" 
                            id="password" 
                            :type="show ? 'text' : 'password'" 
                            placeholder="••••••••" 
                            required 
                            autocomplete="current-password"
                            class="text-body-md block w-full rounded-md border-outline-variant bg-transparent px-3.5 py-2.5 pr-10 text-on-surface transition-colors focus:border-primary focus:ring focus:ring-primary-fixed focus:ring-opacity-50" />
        
                            <i @click="show = !show" 
                            :class="show ? 'fa-eye-slash' : 'fa-eye'"
                            class="fa-regular absolute right-3.5 top-1/2 -translate-y-1/2 cursor-pointer text-outline hover:text-primary transition-colors"></i>
                    </div>
                    <x-input-error :messages="$errors->get('form.password')" class="text-error mt-2 text-sm font-medium" />
                </div>

                <div class="mt-1">
                    <label for="remember" class="group inline-flex cursor-pointer items-center">
                        <input wire:model="form.remember" id="remember" type="checkbox" name="remember" 
                               class="h-4 w-4 cursor-pointer rounded border-outline-variant text-primary shadow-sm transition-colors focus:border-primary focus:ring focus:ring-primary-fixed focus:ring-opacity-50">
                        <span class="text-body-md ms-2 text-on-surface-variant transition-colors group-hover:text-primary">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <div class="mt-2">
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-3 text-sm font-bold text-on-primary shadow-sm transition hover:bg-primary-container focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        <span wire:loading.remove wire:target="login" class="flex items-center gap-2">
                            Masuk Sekarang <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        </span>
                        <span wire:loading wire:target="login">
                            Memproses...
                        </span>
                    </button>
                </div>
            </form>
            
            <div class="mt-8 border-t border-outline-variant pt-6 text-center">
                <p class="text-body-md text-on-surface-variant">
                    Belum punya akun? 
                    <a wire:navigate href="{{ route('register') }}" class="font-bold text-primary transition-colors hover:text-primary-container">Daftar di sini</a>
                </p>
            </div>

            <div class="mt-10 flex items-center justify-center gap-6 text-[12px] text-outline font-medium">
                <a href="#" class="flex items-center gap-1.5 transition-colors hover:text-primary">
                    <i class="fa-regular fa-circle-question"></i> Pusat Bantuan
                </a>
                <a href="#" class="flex items-center gap-1.5 transition-colors hover:text-primary">
                    <i class="fa-solid fa-globe"></i> Bahasa Indonesia
                </a>
            </div>

        </div>
    </div>
</div>