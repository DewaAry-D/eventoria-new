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

            <div class="flex items-center gap-xs sm:gap-sm">
                <a href="#" class="p-xs sm:p-sm text-secondary hover:bg-surface-container hover:text-on-surface rounded-full transition-colors relative focus:outline-none shrink-0 flex items-center justify-center">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-[4px] right-[5px] sm:top-2.5 sm:right-2.5 w-2 h-2 bg-error rounded-full ring-2 ring-surface-container-lowest"></span>
                </a>
        
                <a href="#" class="flex items-center gap-xs sm:gap-sm hover:opacity-85 transition-opacity group ml-xs shrink-0">
                    <img class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover ring-2 ring-surface-container group-hover:ring-primary/30 transition-all shrink-0" 
                        src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->adminKampus?->nama_admin ?? (Auth::user()->name ?? 'Dr Aris Setiawan')) }}&background=000666&color=fff" 
                        alt="Profile">
                </a>
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