<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - Eventoria</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-background font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center p-md text-center">
        
        <div class="max-w-md space-y-xl my-auto">
            
            <div class="relative flex items-center justify-center h-36">
                <h1 class="text-[11rem] font-black text-on-surface-variant/10 tracking-tighter select-none leading-none">
                    404
                </h1>
                <div class="absolute inset-0 flex items-center justify-center animate-bounce duration-1000">
                    <div class="bg-surface-container border border-outline-variant rounded-lg p-2 shadow-card">
                        <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" 
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="space-y-sm">
                <h2 class="text-headline-lg text-primary font-bold tracking-tight">Halaman Tidak Ditemukan</h2>
                <p class="text-body-md text-on-surface-variant max-w-sm mx-auto leading-relaxed">
                    Maaf, tautan atau halaman yang Anda tuju tidak eksis, telah dihapus, atau sedang dalam pembatasan akses sistem.
                </p>
            </div>

            <div class="pt-sm flex flex-col sm:flex-row items-center justify-center gap-sm">
                @auth
                    @php
                        $userRole = auth()->user()->role->value ?? (string) auth()->user()->role;
                    @endphp

                    @if($userRole === 'mahasiswa')
                        <a href="{{ route('mahasiswa.dashboard') }}" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-lg py-md bg-primary text-on-primary text-body-md font-semibold rounded-md hover:bg-primary-container transition duration-200 shadow-card">
                            Kembali ke Dashboard
                        </a>
                    @elseif($userRole === 'organisasi')
                        <a href="{{ route('organisasi.dashboard') }}" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-lg py-md bg-primary text-on-primary text-body-md font-semibold rounded-md hover:bg-primary-container transition duration-200 shadow-card">
                            Kembali ke Dashboard
                        </a>
                    @elseif($userRole === 'admin_dpm')
                        <a href="{{ route('admin.dashboard') }}" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-lg py-md bg-primary text-on-primary text-body-md font-semibold rounded-md hover:bg-primary-container transition duration-200 shadow-card">
                            Kembali ke Panel Admin
                        </a>
                    @else
                        <a href="/" 
                            class="w-full sm:w-auto inline-flex items-center justify-center px-lg py-md bg-primary text-on-primary text-body-md font-semibold rounded-md hover:bg-primary-container transition duration-200 shadow-card">
                            Kembali ke Beranda
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" 
                        class="w-full sm:w-auto inline-flex items-center justify-center px-lg py-md bg-primary text-on-primary text-body-md font-semibold rounded-md hover:bg-primary-container transition duration-200 shadow-card">
                        Masuk ke Akun
                    </a>
                    <a href="/" 
                        class="w-full sm:w-auto inline-flex items-center justify-center px-lg py-md border border-outline-variant text-on-surface bg-surface-container-lowest text-body-md font-semibold rounded-md hover:bg-surface-container transition duration-200">
                        Halaman Utama
                    </a>
                @endauth
            </div>
        </div>

        <div class="mt-auto pt-lg text-caption text-outline select-none tracking-wide">
            &copy; 2026 Eventoria. All rights reserved.
        </div>
    </div>
</body>
</html>