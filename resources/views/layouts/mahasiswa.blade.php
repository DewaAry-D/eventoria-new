<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eventoria - Mahasiswa</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-background text-on-background">
    <div class="min-h-screen bg-background">
        {{-- Top Navigation Bar --}}
        <nav x-data="{ mobileMenuOpen: false }" class="bg-surface-container-lowest border-b border-outline-variant sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 relative">
                    {{-- Brand --}}
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center gap-2 font-bold text-xl text-primary">
                            <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72l5 2.73 5-2.73v3.72z"/></svg>
                            Eventoria
                        </div>
                    </div>
                    
                    {{-- Desktop Nav Links --}}
                    <div class="hidden md:flex space-x-8 items-center h-full absolute left-1/2 transform -translate-x-1/2">
                        <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center h-full px-1 pt-1 border-b-2 {{ request()->routeIs('mahasiswa.dashboard') ? 'border-primary text-primary font-bold' : 'border-transparent text-on-surface-variant hover:text-primary hover:border-outline' }} text-sm font-medium transition duration-150">
                            Dashboard
                        </a>
                        <a href="#" class="inline-flex items-center h-full px-1 pt-1 border-b-2 border-transparent text-on-surface-variant hover:text-primary hover:border-outline text-sm font-medium transition duration-150">
                            Sertifikat
                        </a>
                        <a href="{{ route('mahasiswa.schedule') }}" wire:navigate class="inline-flex items-center h-full px-1 pt-1 border-b-2 {{ request()->routeIs('mahasiswa.schedule') ? 'border-primary text-primary font-bold' : 'border-transparent text-on-surface-variant hover:text-primary hover:border-outline' }} text-sm font-medium transition duration-150">
                            Schedule
                        </a>
                        <a href="{{ route('mahasiswa.my-events') }}" wire:navigate class="inline-flex items-center h-full px-1 pt-1 border-b-2 {{ request()->routeIs('mahasiswa.my-events') ? 'border-primary text-primary font-bold' : 'border-transparent text-on-surface-variant hover:text-primary hover:border-outline' }} text-sm font-medium transition duration-150">
                            My Event
                        </a>
                    </div>

                    {{-- Right Actions --}}
                    <div class="hidden md:flex items-center gap-4">
                        {{-- Profile Dropdown --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2 focus:outline-none py-1">
                                <img class="w-9 h-9 rounded-full bg-surface-container-high object-cover border border-outline-variant" src="https://ui-avatars.com/api/?name={{ Auth::user()->mahasiswa->nama ?? 'User' }}&background=e0e0ff&color=000666" alt="Profile">
                                <svg class="w-4 h-4 text-on-surface-variant" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-52 bg-surface-container-lowest rounded-lg shadow-lg border border-outline-variant py-1 z-50">
                                <div class="px-4 py-2 border-b border-outline-variant">
                                    <p class="text-sm font-bold text-on-surface">{{ Auth::user()->mahasiswa->nama ?? 'Mahasiswa' }}</p>
                                    <p class="text-[10px] text-on-surface-variant">{{ Auth::user()->mahasiswa->prodi->nama_prodi ?? 'Prodi' }}</p>
                                </div>
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-on-surface hover:bg-surface-container-low transition duration-150">Profil Saya</a>
                                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar?');">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-error hover:bg-error-container hover:bg-opacity-20 transition duration-150">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Hamburger Button --}}
                    <div class="flex items-center md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-on-surface-variant hover:text-primary focus:outline-none">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Mobile Nav Links --}}
            <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-outline-variant bg-surface-container-lowest">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-primary-fixed text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-primary' }}">
                        Dashboard
                    </a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-on-surface-variant hover:bg-surface-container-low hover:text-primary">
                        Sertifikat
                    </a>
                    <a href="{{ route('mahasiswa.schedule') }}" wire:navigate class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('mahasiswa.schedule') ? 'bg-primary-fixed text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-primary' }}">
                        Schedule
                    </a>
                    <a href="{{ route('mahasiswa.my-events') }}" wire:navigate class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('mahasiswa.my-events') ? 'bg-primary-fixed text-primary font-bold' : 'text-on-surface-variant hover:bg-surface-container-low hover:text-primary' }}">
                        My Event
                    </a>
                    <a href="{{ route('profile') }}" class="block px-3 py-2 rounded-md text-base font-medium text-on-surface-variant hover:bg-surface-container-low hover:text-primary">
                        Profil Saya
                    </a>
                    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar?');" class="block w-full">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-error hover:bg-error-container hover:bg-opacity-20">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        {{-- Main content slot --}}
        <main class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>