<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Eventoria') }} - Admin DPM</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-[#F8F9FE]">
    
    <div x-data="{ sidebarOpen: false, showLogoutModal: false }" class="flex h-screen overflow-hidden">
        
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col">
            
            <div class="flex items-center justify-center h-20 border-b border-gray-100 px-6 shrink-0">
                <a href="#" class="flex items-center gap-3 w-full">
                    <div class="w-8 h-8 bg-[#000666] rounded-lg flex items-center justify-center text-white font-bold text-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-[#000666] leading-tight">Eventoria</h2>
                        <p class="text-[10px] text-gray-500 font-semibold tracking-wider">ACADEMIC MANAGEMENT</p>
                    </div>
                </a>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                
                <!-- Menu Dashboard -->
                <a href="{{ route('admin.dashboard') }}" wire:navigate 
                class="flex items-center gap-3 px-4 py-3 text-sm font-bold transition rounded-xl 
                {{ request()->routeIs('admin.dashboard') ? 'text-[#000666] bg-blue-50/80' : 'text-gray-600 hover:text-[#000666] hover:bg-gray-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>

                <!-- Menu Moderasi Organisasi -->
                <a href="{{ route('admin.moderasi-organisasi') }}" wire:navigate 
                class="flex items-center gap-3 px-4 py-3 text-sm font-bold transition rounded-xl 
                {{ request()->routeIs('admin.moderasi-organisasi*') ? 'text-[#000666] bg-blue-50/80' : 'text-gray-600 hover:text-[#000666] hover:bg-gray-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Moderasi Organisasi
                </a>

                <!-- Menu Moderasi Event -->
                <a href="{{ route('admin.moderasi-event') }}" wire:navigate 
                class="flex items-center gap-3 px-4 py-3 text-sm font-bold transition rounded-xl 
                {{ request()->routeIs('admin.moderasi-event*') ? 'text-[#000666] bg-blue-50/80' : 'text-gray-600 hover:text-[#000666] hover:bg-gray-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Moderasi Event
                </a>
            </nav>

            <div class="p-4 border-t border-gray-100 space-y-1 shrink-0">
                <a href="{{ route('profile') }}" wire:navigate class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-[#000666] hover:bg-gray-50 rounded-lg transition">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Pengaturan
                </a>
                
                <button @click="showLogoutModal = true" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-red-600 hover:bg-red-50 rounded-lg transition">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
            
            <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-6 lg:px-10 z-40 shrink-0">
                
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    
                    <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-lg border border-gray-100 text-sm font-medium text-gray-600">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </div>
                </div>

                <div class="flex items-center gap-5">
                    
                    <div class="flex items-center gap-3 pr-5 border-r border-gray-200">
                        <button class="p-2 text-gray-400 hover:text-[#000666] transition relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                        </button>
                        <button class="p-2 text-gray-400 hover:text-[#000666] transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                    </div>

                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-3 text-left focus:outline-none">
                            <div class="hidden md:block text-right">
                                <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name ?? 'Admin DPM' }}</p>
                                <p class="text-xs text-gray-500">Super Admin</p>
                            </div>
                            <img class="w-10 h-10 rounded-full object-cover border border-gray-200" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=000666&color=fff" alt="Admin Profile">
                        </button>

                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50" style="display: none;">
                            <a href="{{ route('profile') }}" wire:navigate class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#000666]">Profil Saya</a>
                            <button @click="showLogoutModal = true; dropdownOpen = false" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">Logout</button>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-[#F8F9FE] p-6 lg:p-10 relative">
                
                {{ $slot }}
                
                <footer class="mt-12 pt-6 border-t border-gray-200/60 flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-medium text-gray-500">
                    <p>{{ date('Y') }} &copy; Eventoria Management System</p>
                    <div class="flex gap-4">
                        <a href="#" class="hover:text-[#000666] transition">Syarat & Ketentuan</a>
                        <a href="#" class="hover:text-[#000666] transition">Kebijakan Privasi</a>
                    </div>
                </footer>
            </main>

            <div x-show="showLogoutModal" style="display: none;" class="relative z-[150]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div x-show="showLogoutModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

                <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        
                        <div x-show="showLogoutModal" @click.away="showLogoutModal = false"
                             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                             class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-sm mx-4 p-6">
                            
                            <div class="text-center mb-6">
                                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Keluar dari Sistem?</h3>
                                <p class="text-sm text-gray-500 leading-relaxed px-2">
                                    Apakah Anda yakin ingin mengakhiri sesi ini? Anda harus login kembali untuk masuk ke Dasbor.
                                </p>
                            </div>
                            
                            <form method="POST" action="{{ route('logout') }}" class="hidden" id="form-logout">
                                @csrf
                            </form>
                            
                            <div class="flex items-center justify-center gap-3 mt-2">
                                <button @click="showLogoutModal = false" type="button" class="flex-1 py-2.5 px-4 bg-white text-gray-700 font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 transition">
                                    Batal
                                </button>
                                <button @click="document.getElementById('form-logout').submit();" type="button" class="flex-1 py-2.5 px-4 bg-[#DC2626] hover:bg-red-700 text-white font-semibold rounded-xl transition shadow-sm">
                                    Ya, Keluar
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>