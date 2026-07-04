<header class="w-full bg-surface-container-lowest border-b border-outline-variant/30 px-md py-sm sm:py-md flex items-center justify-end sticky top-0 z-30">
    
    <div x-data="{ dropdownOpen: false }" class="relative">
        
        <button @click="dropdownOpen = !dropdownOpen" 
                @click.away="dropdownOpen = false"
                class="flex items-center gap-xs sm:gap-sm hover:opacity-85 transition-opacity group ml-xs shrink-0 focus:outline-none">
            
            <div class="text-right hidden md:block select-none">
                <p class="text-body-md font-bold text-on-surface leading-tight group-hover:text-primary transition-colors">
                    {{ Auth::user()->admin_name }}
                </p>
                <p class="text-caption text-on-surface-variant font-medium mt-0.5">
                    {{ Auth::user()->role_label }}
                </p>
            </div>

            <img class="w-8 h-8 sm:w-10 sm:h-10 rounded-full object-cover ring-2 ring-surface-container group-hover:ring-primary/30 transition-all shrink-0"
                    src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->admin_name) }}&background=000666&color=fff"
                    alt="Profile Avatar">
        </button>

        <div x-show="dropdownOpen" 
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                class="absolute right-0 mt-2 w-48 bg-surface-container-lowest rounded-lg shadow-xl border border-outline-variant/20 py-1.5 z-50 select-none" 
                style="display: none;"
                x-cloak>
            
            <a href="{{ route('admin.profil') }}" wire:navigate 
                class="block px-4 py-2 text-body-md font-medium text-secondary hover:bg-surface-container hover:text-primary transition-colors">
                Profil Admin
            </a>
            
            <div class="border-t border-outline-variant/10 my-1"></div>
            
            <button type="button" @click="showLogoutModal = true; dropdownOpen = false" 
                    class="block w-full text-left px-4 py-2 text-body-md font-bold text-error hover:bg-error-container/20 transition-colors cursor-pointer">
                Logout
            </button>
        </div>

    </div>

</header>