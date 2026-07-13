<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="flex min-h-screen w-full bg-surface-container-lowest">
    
    <!-- Bagian Kiri (Sama dengan Halaman Login) -->
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

    <!-- Bagian Kanan (Form Lupa Sandi) -->
    <div class="flex w-full flex-col items-center justify-center p-8 lg:w-1/2">
        <div class="w-full max-w-[400px]">
            
            <div class="mb-8">
                <h2 class="text-headline-md font-bold tracking-tight text-primary">Lupa Kata Sandi?</h2>
                <p class="text-body-md mt-1.5 text-on-surface-variant">Jangan khawatir. Cukup masukkan alamat email kampus Anda dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form wire:submit="sendPasswordResetLink" class="flex flex-col gap-5">
                
                <div>
                    <label for="email" class="text-label-md mb-1.5 block text-on-surface">Alamat Email Kampus</label>
                    <input wire:model="email" id="email" type="email" placeholder="nama@mahasiswa.ac.id" required autofocus 
                           class="text-body-md block w-full rounded-md border-outline-variant bg-transparent px-3.5 py-2.5 text-on-surface transition-colors focus:border-primary focus:ring focus:ring-primary-fixed focus:ring-opacity-50" />
                    <x-input-error :messages="$errors->get('email')" class="text-error mt-2 text-sm font-medium" />
                </div>

                <div class="mt-2">
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-3 text-sm font-bold text-on-primary shadow-sm transition hover:bg-primary-container focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                        <span wire:loading.remove wire:target="sendPasswordResetLink" class="flex items-center gap-2">
                            Kirim Tautan Reset <i class="fa-solid fa-paper-plane"></i>
                        </span>
                        <span wire:loading wire:target="sendPasswordResetLink">
                            Mengirim...
                        </span>
                    </button>
                </div>
            </form>
            
            <div class="mt-8 border-t border-outline-variant pt-6 text-center">
                <p class="text-body-md text-on-surface-variant">
                    Ingat kata sandi Anda? 
                    <a wire:navigate href="{{ route('login') }}" class="font-bold text-primary transition-colors hover:text-primary-container">Kembali untuk Masuk</a>
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