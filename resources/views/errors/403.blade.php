<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak | Eventoria</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-surface min-h-screen flex items-center justify-center p-md font-sans antialiased">

    <!-- Kontainer Utama Terkunci Rapi -->
    <div class="w-full max-w-[540px] flex flex-col items-center text-center">

        {{-- Icon Gembok/Perisai Bulat Presisi Simetris --}}
        <div class="relative w-24 h-24 mb-lg flex items-center justify-center">
            <div class="absolute inset-0 rounded-full border border-error/30"></div>
            <div class="absolute inset-2.5 rounded-full bg-error-container/40"></div>
            <div class="relative w-24 h-24 bg-surface-container-lowest rounded-full border border-error/20 flex items-center justify-center shadow-card">
                <svg class="w-9 h-9 text-error/80" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                </svg>
            </div>
        </div>

        {{-- Label Sub-Header --}}
        <p class="text-[11px] font-bold text-error/70 uppercase tracking-[0.15em] mb-xs select-none">
            Error 403 &mdash; Akses Ditolak
        </p>

        {{-- Judul Utama --}}
        <h1 class="text-headline-md font-bold text-primary tracking-tight mb-xs">
            Anda Tidak Memiliki Izin
        </h1>

        {{-- Deskripsi Ringkas --}}
        <p class="text-body-md text-secondary/70 leading-relaxed mb-xl max-w-[380px] mx-auto">
            Halaman ini memerlukan hak akses khusus yang tidak dimiliki akun Anda saat ini.
        </p>

        {{-- 🟢 FIX SAKTI: Info Box Alasan dengan Restriksi Max-Width di Desktop --}}
        <div class="w-full max-w-[420px] bg-surface-container-lowest border border-outline-variant/30 rounded-2xl p-md mb-xl text-left shadow-card">
            <p class="text-label-md font-bold text-primary mb-sm flex items-center gap-xs">
                <svg class="w-4 h-4 text-warning shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12v-.008z"/>
                </svg>
                Mengapa ini terjadi?
            </p>
            <ul class="space-y-xs">
                @foreach([
                    'Anda login dengan role yang tidak sesuai untuk halaman ini',
                    'Halaman ini hanya bisa diakses oleh role tertentu',
                    'Sesi Anda mungkin sudah tidak valid',
                ] as $reason)
                    <li class="flex items-start gap-xs text-body-sm text-secondary/70 leading-snug">
                        <span class="w-1 h-1 rounded-full bg-outline-variant mt-2 shrink-0"></span>
                        <span>{{ $reason }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Tombol Navigasi --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-sm w-full mb-lg sm:w-auto">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}"
                class="inline-flex items-center gap-xs px-md h-10 bg-surface-container-lowest border border-outline-variant/30 text-primary font-bold text-body-md rounded-xl hover:bg-surface-container transition-all shadow-card w-full sm:w-auto justify-center cursor-pointer active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Kembali
            </a>

            @auth
                @php
                    $dashboardRoute = match(true) {
                        Auth::user()->hasRole('admin_dpm')  => route('admin.dashboard'),
                        Auth::user()->hasRole('organisasi') => route('organisasi.dashboard'),
                        Auth::user()->hasRole('mahasiswa')  => route('mahasiswa.dashboard'),
                        default => '/'
                    };
                @endphp
                <a href="{{ $dashboardRoute }}"
                    class="inline-flex items-center gap-xs px-md h-10 bg-primary text-on-primary font-bold text-body-md rounded-xl hover:bg-primary/90 transition-all shadow-card w-full sm:w-auto justify-center cursor-pointer active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                    </svg>
                    Dashboard saya
                </a>
            @else
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-xs px-md h-10 bg-primary text-on-primary font-bold text-body-md rounded-xl hover:bg-primary/90 transition-all shadow-card w-full sm:w-auto justify-center cursor-pointer active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                    </svg>
                    Login dengan akun lain
                </a>
            @endauth
        </div>

        {{-- Footer --}}
        <p class="mt-xl text-caption text-secondary/40 font-bold select-none">
            &copy; 2026 Eventoria Management System &mdash; Universitas Udayana
        </p>

    </div>

</body>
</html>