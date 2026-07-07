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

    <body class="bg-background text-on-background antialiased font-sans" 
        x-data="{ sidebarOpen: false, showLogoutModal: false }">

        <!-- Layout Mobile -->
        <header class="flex md:hidden items-center justify-between bg-surface-container-lowest border-b border-outline-variant/30 px-md py-xs sticky top-0 z-30 h-14 w-full shrink-0 select-none">
            <!-- Humburger menu -->
            <button @click="sidebarOpen = true" class="p-xs text-secondary hover:bg-surface-container rounded-full focus:outline-none flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Title -->
            <div class="font-bold text-primary tracking-tight text-title-md">
                Eventoria Admin
            </div>
            
            <!-- Pofile Admin -->
            <div x-data="{ mobileDropdownOpen: false }" class="relative">
                <button @click="mobileDropdownOpen = !mobileDropdownOpen" 
                        @click.away="mobileDropdownOpen = false"
                        class="flex items-center group focus:outline-none shrink-0">
                    
                    <img class="w-9 h-9 rounded-full object-cover ring-2 ring-surface-container group-hover:ring-primary/30 transition-all shrink-0" 
                            src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->admin_name) }}&background=000666&color=fff" 
                            alt="Profile">
                </button>

                <div x-show="mobileDropdownOpen" 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                        class="absolute right-0 mt-2 w-48 bg-surface-container-lowest rounded-xl shadow-xl border border-outline-variant/20 py-1.5 z-50" 
                        style="display: none;">
                    
                    <div class="px-4 py-2 border-b border-outline-variant/10">
                        <p class="text-body-sm font-bold text-on-surface truncate" title="{{ Auth::user()->admin_name }}">
                            {{ Auth::user()->admin_name }}
                        </p>
                        <p class="text-[10px] text-on-surface-variant font-medium">{{ Auth::user()->role_label }}</p>
                    </div>

                    <a href="{{ route('admin.profil') }}" wire:navigate 
                        class="block px-4 py-2 text-body-md font-medium text-secondary hover:bg-surface-container hover:text-primary transition-colors">
                        Profil Saya
                    </a>
                    
                    <div class="border-t border-outline-variant/10 my-1"></div>
                    
                    <button type="button" @click="showLogoutModal = true; mobileDropdownOpen = false" 
                            class="block w-full text-left px-4 py-2 text-body-md font-bold text-error hover:bg-error-container/20 transition-colors">
                        Logout
                    </button>
                </div>
            </div>
        </header>

        <!-- Layout Utama -->
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

                <footer class="max-w-container mx-auto w-full px-md lg:px-lg py-md border-t border-outline-variant/20 flex flex-col sm:flex-row items-center justify-center gap-xs text-caption font-bold text-secondary/50 select-none">
                    <p>{{ date('Y') }} &copy; Eventoria Management System</p>
                </footer>
            </div>
        </div>

        <!-- Modal untuk Logout -->
        <x-admin.modals.logout-modal target="admin"/>

        @livewireScripts
    </body>
</html>