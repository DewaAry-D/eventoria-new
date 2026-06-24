@props([
    'role' => false,
])

<header class="w-full bg-surface-container-lowest border-b border-outline-variant/30 px-md py-sm sm:py-md flex items-center justify-end sticky top-0 z-30">

    <div class="flex items-center gap-xs sm:gap-sm">
        
        <a href="#" class="p-xs sm:p-sm text-secondary hover:bg-surface-container hover:text-on-surface rounded-full transition-colors relative focus:outline-none shrink-0 flex items-center justify-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-error rounded-full ring-2 ring-surface-container-lowest"></span>
        </a>

        <a href="#" class="p-xs sm:p-sm text-secondary hover:bg-surface-container hover:text-on-surface rounded-full transition-colors focus:outline-none shrink-0 flex items-center justify-center">
            <svg width="20" height="20" class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.95 16C10.3 16 10.5958 15.8792 10.8375 15.6375C11.0792 15.3958 11.2 15.1 11.2 14.75C11.2 14.4 11.0792 14.1042 10.8375 13.8625C10.5958 13.6208 10.3 13.5 9.95 13.5C9.6 13.5 9.30417 13.6208 9.0625 13.8625C8.82083 14.1042 8.7 14.4 8.7 14.75C8.7 15.1 8.82083 15.3958 9.0625 15.6375C9.30417 15.8792 9.6 16 9.95 16ZM9.05 12.15H10.9C10.9 11.6 10.9625 11.1667 11.0875 10.85C11.2125 10.5333 11.5667 10.1 12.15 9.55C12.5833 9.11667 12.925 8.70417 13.175 8.3125C13.425 7.92083 13.55 7.45 13.55 6.9C13.55 5.96667 13.2083 5.25 12.525 4.75C11.8417 4.25 11.0333 4 10.1 4C9.15 4 8.37917 4.25 7.7875 4.75C7.19583 5.25 6.78333 5.85 6.55 6.55L8.2 7.2C8.28333 6.9 8.47083 6.575 8.7625 6.225C9.05417 5.875 9.5 5.7 10.1 5.7C10.6333 5.7 11.0333 5.84583 11.3 6.1375C11.5667 6.42917 11.7 6.75 11.7 7.1C11.7 7.43333 11.6 7.74583 11.4 8.0375C11.2 8.32917 10.95 8.6 10.65 8.85C9.91667 9.5 9.46667 9.99167 9.3 10.325C9.13333 10.6583 9.05 11.2667 9.05 12.15ZM10 20C8.61667 20 7.31667 19.7375 6.1 19.2125C4.88333 18.6875 3.825 17.975 2.925 17.075C2.025 16.175 1.3125 15.1167 0.7875 13.9C0.2625 12.6833 0 11.3833 0 10C0 8.61667 0.2625 7.31667 0.7875 6.1C1.3125 4.88333 2.025 3.825 2.925 2.925C3.825 2.025 4.88333 1.3125 6.1 0.7875C7.31667 0.2625 8.61667 0 10 0C11.3833 0 12.6833 0.2625 13.9 0.7875C15.1167 1.3125 16.175 2.025 17.075 2.925C17.975 3.825 18.6875 4.88333 19.2125 6.1C19.7375 7.31667 20 8.61667 20 10C20 11.3833 19.7375 12.6833 19.2125 13.9C18.6875 15.1167 17.975 16.175 17.075 17.075C16.175 17.975 15.1167 18.6875 13.9 19.2125C12.6833 19.7375 11.3833 20 10 20ZM10 18C12.2333 18 14.125 17.225 15.675 15.675C17.225 14.125 18 12.2333 18 10C18 7.76667 17.225 5.875 15.675 4.325C14.125 2.775 12.2333 2 10 2C7.76667 2 5.875 2.775 4.325 4.325C2.775 5.875 2 7.76667 2 10C2 12.2333 2.775 14.125 4.325 15.675C5.875 17.225 7.76667 18 10 18Z" fill="currentColor"/>
            </svg>
        </a>

        <div class="h-5 w-[1px] bg-outline-variant/60 mx-xs shrink-0"></div>

        <div x-data="{ dropdownOpen: false }" class="relative">
            
            <button @click="dropdownOpen = !dropdownOpen" 
                    @click.away="dropdownOpen = false"
                    class="flex items-center gap-xs sm:gap-sm hover:opacity-85 transition-opacity group ml-xs shrink-0 focus:outline-none">
                
                <div class="text-right hidden md:block select-none">
                    <p class="text-body-md font-bold text-on-surface leading-tight group-hover:text-primary transition-colors">
                        {{ Auth::user()->adminKampus?->nama_admin ?? (Auth::user()->name ?? 'Dr. Aris Setiawan') }}
                    </p>
                    @if ($role)
                        <p class="text-caption text-on-surface-variant font-medium mt-0.5">
                            {{ $role }}
                        </p>
                    @endif
                </div>
                
                <img class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover ring-2 ring-surface-container group-hover:ring-primary/30 transition-all shrink-0" 
                        src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->adminKampus?->nama_admin ?? (Auth::user()->name ?? 'Dr Aris Setiawan')) }}&background=000666&color=fff" 
                        alt="Profile">
            </button>

            <div x-show="dropdownOpen" 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                    class="absolute right-0 mt-2 w-48 bg-surface-container-lowest rounded-xl shadow-xl border border-outline-variant/20 py-1.5 z-50 select-none" 
                    style="display: none;">
                
                <a href="{{ route('profile') }}" wire:navigate 
                    class="block px-4 py-2 text-body-md font-medium text-secondary hover:bg-surface-container hover:text-primary transition-colors">
                    Profil Saya
                </a>
                
                <div class="border-t border-outline-variant/10 my-1"></div>
                
                <button type="button" @click="showLogoutModal = true; dropdownOpen = false" 
                        class="block w-full text-left px-4 py-2 text-body-md font-bold text-error hover:bg-error-container/20 transition-colors">
                    Logout
                </button>
            </div>

        </div>

    </div>
</header>