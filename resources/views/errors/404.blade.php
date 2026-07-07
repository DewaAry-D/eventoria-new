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
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center p-8 text-center">
        
        <div class="max-w-md space-y-10 my-auto">
            
            <div class="relative flex items-center justify-center h-36">
                <h1 class="text-[11rem] font-black text-gray-200/70 tracking-tighter select-none leading-none">
                    404
                </h1>
                <div class="absolute inset-0 flex items-center justify-center animate-bounce duration-1000">
                    <div class="bg-indigo-50 border border-indigo-100/80 rounded-2xl p-4.5 shadow-md">
                        <svg class="w-12 h-12 text-indigo-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" 
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Halaman Tidak Ditemukan</h2>
                <p class="text-sm text-gray-500 max-w-sm mx-auto leading-relaxed">
                    Maaf, tautan atau halaman yang Anda tuju tidak eksis, telah dihapus, atau sedang dalam pembatasan akses sistem.
                </p>
            </div>

            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                @auth
                    @php
                        $userRole = auth()->user()->role->value ?? (string) auth()->user()->role;
                    @endphp

                    @if($userRole === 'mahasiswa')
                        <a href="{{ route('mahasiswa.dashboard') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-900 text-white text-sm font-semibold rounded-xl hover:bg-indigo-800 transition duration-200 shadow-sm">
                            Kembali ke Dashboard
                        </a>
                    @elseif($userRole === 'organisasi')
                        <a href="{{ route('organisasi.dashboard') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-900 text-white text-sm font-semibold rounded-xl hover:bg-indigo-800 transition duration-200 shadow-sm">
                            Kembali ke Dashboard
                        </a>
                    @elseif($userRole === 'admin_dpm')
                        <a href="{{ route('admin.dashboard') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-900 text-white text-sm font-semibold rounded-xl hover:bg-indigo-800 transition duration-200 shadow-sm">
                            Kembali ke Panel Admin
                        </a>
                    @else
                        <a href="/"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-900 text-white text-sm font-semibold rounded-xl hover:bg-indigo-800 transition duration-200 shadow-sm">
                            Kembali ke Beranda
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" 
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-indigo-900 text-white text-sm font-semibold rounded-xl hover:bg-indigo-800 transition duration-200 shadow-sm">
                        Masuk ke Akun
                    </a>
                    <a href="/"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 bg-white text-sm font-semibold rounded-xl hover:bg-gray-50 transition duration-200">
                        Halaman Utama
                    </a>
                @endauth
            </div>
        </div>

        <div class="mt-auto pt-12 text-xs text-gray-400 select-none tracking-wide">
            &copy; 2026 Eventoria. All rights reserved.
        </div>
    </div>
</body>
</html>