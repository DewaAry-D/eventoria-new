<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eventoria - Mahasiswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen bg-gray-50">
        {{-- Top Navigation Bar --}}
        <nav x-data="{ mobileMenuOpen: false }" class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 relative">
                    {{-- Brand --}}
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center gap-2 font-bold text-xl text-indigo-900">
                            <svg class="w-8 h-8 text-indigo-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72l5 2.73 5-2.73v3.72z"/></svg>
                            Eventoria
                        </div>
                    </div>
                    
                    {{-- Desktop Nav Links --}}
                    <div class="hidden md:flex space-x-8 items-center h-full absolute left-1/2 transform -translate-x-1/2">
                        <a href="{{ route('mahasiswa.dashboard') }}" class="inline-flex items-center h-full px-1 pt-1 border-b-2 {{ request()->routeIs('mahasiswa.dashboard') ? 'border-indigo-900 text-indigo-950 font-bold' : 'border-transparent text-gray-500 hover:text-indigo-900 hover:border-gray-300' }} text-sm font-medium transition duration-150">
                            Dashboard
                        </a>
                        <a href="#" class="inline-flex items-center h-full px-1 pt-1 border-b-2 border-transparent text-gray-500 hover:text-indigo-900 hover:border-gray-300 text-sm font-medium transition duration-150">
                            Sertifikat
                        </a>
                        <a href="#" class="inline-flex items-center h-full px-1 pt-1 border-b-2 border-transparent text-gray-500 hover:text-indigo-900 hover:border-gray-300 text-sm font-medium transition duration-150">
                            Schedule
                        </a>
                        <a href="#" class="inline-flex items-center h-full px-1 pt-1 border-b-2 border-transparent text-gray-500 hover:text-indigo-900 hover:border-gray-300 text-sm font-medium transition duration-150">
                            My Event
                        </a>
                    </div>

                    {{-- Right Actions --}}
                    <div class="hidden md:flex items-center gap-4">
                        {{-- Notifications --}}
                        <button class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Notifications</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </button>

                        {{-- Profile Dropdown --}}
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center gap-2 focus:outline-none py-1">
                                <img class="w-9 h-9 rounded-full bg-gray-300 object-cover border border-gray-200" src="https://ui-avatars.com/api/?name={{ Auth::user()->mahasiswa->nama ?? 'User' }}&background=E0E7FF&color=4338CA" alt="Profile">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            
                            <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-52 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-bold text-gray-900">{{ Auth::user()->mahasiswa->nama ?? 'Mahasiswa' }}</p>
                                    <p class="text-[10px] text-gray-500">{{ Auth::user()->mahasiswa->prodi->nama_prodi ?? 'Prodi' }}</p>
                                </div>
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 transition duration-150">Profil Saya</a>
                                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar?');">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition duration-150">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Hamburger Button --}}
                    <div class="flex items-center md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-500 hover:text-indigo-900 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Mobile Nav Links --}}
            <div x-show="mobileMenuOpen" x-transition class="md:hidden border-t border-gray-200 bg-white">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-900' }}">
                        Dashboard
                    </a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-indigo-900">
                        Sertifikat
                    </a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-indigo-900">
                        Schedule
                    </a>
                    <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-indigo-900">
                        My Event
                    </a>
                    <a href="{{ route('profile') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-50 hover:text-indigo-900">
                        Profil Saya
                    </a>
                    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar?');" class="block w-full">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50">
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