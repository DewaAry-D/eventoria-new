<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Eventoria') }} - Admin Dashboard</title>
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>

    <body class="bg-surface text-on-surface antialiased font-sans" 
            x-data="{ sidebarOpen: false, showLogoutModal: false }">

        <header class="flex md:hidden items-center justify-between bg-surface-container-lowest border-b border-outline-variant/30 px-md py-xs sticky top-0 z-30 h-14 w-full shrink-0 select-none">
            <button @click="sidebarOpen = true" class="p-xs text-secondary hover:bg-surface-container rounded-full focus:outline-none flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="font-bold text-primary tracking-tight text-title-sm">
                Eventoria Admin
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
        </header>

        <div class="flex min-h-screen">
            
            <aside 
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
                class="fixed inset-y-0 left-0 z-40 w-sidebar bg-surface-container-lowest border-r border-outline-variant/30 transform ease-in-out md:translate-x-0 shrink-0 h-screen transition-all duration-300 md:sticky top-0"
            >
                <x-admin.sidebar :active="$active ?? 'dashboard'" />
            </aside>

            <div @click="sidebarOpen = false" x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black/40 z-30 md:hidden" style="display: none;"></div>

            <div class="flex-1 flex flex-col min-w-0">
                
                <div class="hidden md:block">
                    <x-admin.topbar :role="'Super Admin'"/>
                </div>

                <main class="flex-1 max-w-container mx-auto w-full">
                    {{ $slot }}
                </main>

                <footer class="max-w-container mx-auto w-full px-md lg:px-lg py-md border-t border-outline-variant/20 flex flex-col sm:flex-row items-center justify-between gap-xs text-caption font-bold text-secondary/50 select-none">
                    <p>{{ date('Y') }} &copy; Eventoria Management System</p>
                    <div class="flex gap-md">
                        <a href="#" class="hover:text-primary transition-colors">Syarat & Ketentuan</a>
                        <a href="#" class="hover:text-primary transition-colors">Kebijakan Privasi</a>
                    </div>
                </footer>
            </div>

        </div>

        <!-- Modal untuk Logout -->
        <x-admin.modals.logout-modal />

        @livewireScripts
    </body>
</html>