<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eventoria - Organisasi</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-background text-gray-900" x-data="{ sidebarOpen: false, showLogoutModal: false }">
    <div class="flex h-screen overflow-hidden">
        
        <div x-show="sidebarOpen" class="fixed inset-0 z-20 transition-opacity bg-black opacity-50 lg:hidden" @click="sidebarOpen = false"></div>

        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform bg-white border-r border-outline-variant lg:translate-x-0 lg:static lg:inset-auto">
            <div class="flex items-center justify-center h-[73px] border-b border-outline-variant px-6">
                <div class="flex items-center gap-2 font-bold text-xl text-primary">
                    <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72l5 2.73 5-2.73v3.72z"/></svg>
                    Eventoria
                </div>
            </div>
            
            <nav class="p-4 space-y-2">
                <!-- Menu Dashboard -->
                <a href="{{ route('organisasi.dashboard') }}" wire:navigate
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('organisasi.dashboard*') || request()->is('dasbor-organisasi') ? 'text-primary bg-indigo-50' : 'text-gray-600 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>

                <!-- Menu Events -->
                <a href="{{ route('organisasi.events') }}" wire:navigate
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('organisasi.events*') || request()->is('organisasi/events*') ? 'text-primary bg-indigo-50' : 'text-gray-600 hover:bg-gray-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Events
                </a>
            </nav>

            <div class="absolute bottom-0 w-full p-4 border-t border-outline-variant bg-surface-container-lowest">
                <button type="button" @click="showLogoutModal = true"
                    class="flex items-center w-full px-4 py-3 text-sm font-medium text-error rounded-lg hover:bg-error-container/40 transition duration-150">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Keluar
                </button>

<x-admin.modals.logout-modal target="organisasi"/>
            </div>
        </aside>

        <div class="flex flex-col flex-1 overflow-hidden">
            <header class="flex items-center justify-between px-6 py-4 bg-white border-b border-gray-200">
                <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="flex-1 lg:flex-none"></div>
                
                <div class="flex items-center gap-4">
                    
                    <a href="{{ route('organisasi.profil') }}" wire:navigate class="flex items-center gap-3 text-left group">
                        @if(Auth::user()->organisasi && Auth::user()->organisasi->logo)
                            <img class="w-10 h-10 rounded-full object-cover border border-indigo-200 group-hover:ring-2 group-hover:ring-indigo-300 transition" src="{{ asset('storage/' . Auth::user()->organisasi->logo) }}" alt="Logo Organisasi">
                        @else
                            <img class="w-10 h-10 rounded-full bg-indigo-100 border border-indigo-200 group-hover:ring-2 group-hover:ring-indigo-300 transition" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->organisasi->nama_organisasi ?? 'Org') }}&background=E0E7FF&color=4338CA" alt="Profile">
                        @endif
                        
                        <div class="hidden md:block">
                            <p class="text-sm font-bold text-gray-900 truncate w-32 group-hover:text-indigo-700 transition">{{ Auth::user()->organisasi->nama_organisasi ?? 'Organisasi' }}</p>
                            <p class="text-xs text-gray-500 uppercase">{{ Auth::user()->organisasi->tingkat_organisasi ?? 'Tingkat' }}</p>
                        </div>
                    </a>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="container px-6 py-8 mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>
</html>