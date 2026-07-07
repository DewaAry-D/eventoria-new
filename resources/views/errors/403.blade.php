<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak | Eventoria</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-background font-sans antialiased overflow-y-hidden">

    <div class="min-h-screen flex flex-col items-center justify-center p-md text-center">

        <div class="w-full max-w-md space-y-xl my-auto">

            <div class="relative flex items-center justify-center h-36">
                <h1 class="text-[11rem] font-black text-error-container/40 tracking-tighter select-none leading-none">
                    403
                </h1>
                <div class="absolute inset-0 flex items-center justify-center animate-bounce duration-1000">
                    <div class="bg-error-container border border-error/20 rounded-lg p-2 shadow-card">
                        <svg class="w-12 h-12 text-error" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="space-y-sm">
                <h2 class="text-headline-lg text-primary font-bold tracking-tight">Anda Tidak Memiliki Izin</h2>
                <p class="text-body-md text-on-surface-variant max-w-sm mx-auto leading-relaxed">
                    Halaman ini memerlukan hak akses khusus yang tidak dimiliki oleh jenis akun Anda saat ini.
                </p>
            </div>

            <div class="w-full bg-surface-container-lowest border border-outline-variant/50 rounded-md p-md text-left shadow-card space-y-sm">
                <p class="text-label-md text-on-surface flex items-center gap-xs">
                    <svg class="w-4 h-4 text-warning shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12v-.008z"/>
                    </svg>
                    Mengapa ini terjadi?
                </p>
                <ul class="space-y-xs">
                    @foreach([
                        'Anda login dengan role yang tidak sesuai untuk halaman ini',
                        'Halaman ini hanya bisa diakses oleh role tertentu',
                        'Sesi autentikasi Anda mungkin sudah tidak valid',
                    ] as $reason)
                        <li class="flex items-start gap-sm text-body-sm text-on-surface-variant leading-snug">
                            <span class="w-1.5 h-1.5 rounded-full bg-outline-variant mt-1.5 shrink-0"></span>
                            <span>{{ $reason }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-sm w-full">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-xs px-lg py-md border border-outline-variant text-on-surface bg-surface-container-lowest text-body-md font-semibold rounded-md hover:bg-surface-container transition duration-200 shadow-card active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    Kembali
                </a>

                @auth
                    @php
                        $userRole = auth()->user()->role->value ?? (string) auth()->user()->role;
                        
                        $dashboardRoute = match($userRole) {
                            'admin_dpm'  => route('admin.dashboard'),
                            'organisasi' => route('organisasi.dashboard'),
                            'mahasiswa'  => route('mahasiswa.dashboard'),
                            default      => '/'
                        };
                    @endphp
                    <a href="{{ $dashboardRoute }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-xs px-lg py-md bg-primary text-on-primary text-body-md font-semibold rounded-md hover:bg-primary-container transition duration-200 shadow-card active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                        </svg>
                        Dashboard Saya
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-xs px-lg py-md bg-primary text-on-primary text-body-md font-semibold rounded-md hover:bg-primary-container transition duration-200 shadow-card active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                        </svg>
                        Login Akun Lain
                    </a>
                @endauth
            </div>
        </div>

        <div class="mt-auto pt-lg text-caption text-outline select-none tracking-wide font-medium">
            &copy; 2026 Eventoria Management System &mdash; Universitas Udayana
        </div>
    </div>

</body>
</html>