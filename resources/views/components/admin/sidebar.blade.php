@props(['active' => 'dashboard'])

<div class="w-sidebar h-full bg-surface-container-lowest border-r border-outline-variant/30 flex flex-col justify-between p-md shrink-0 select-none">
    
    <div>
        <div class="flex items-center gap-sm mb-xl px-xs h-14 shrink-0 border-b border-outline-variant/20 pb-md space-y-xs">
            <div class="w-10 h-10 shrink-0 flex items-center shadow-sm rounded-md overflow-hidden">
                <svg width="100%" height="100%" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="40" rx="8" fill="#000666"/>
                    <path d="M20 28.75L13.1944 25.0556V19.2222L9.30554 17.0833L20 11.25L30.6944 17.0833V24.8611H28.75V18.1528L26.8055 19.2222V25.0556L20 28.75ZM20 20.6806L26.6597 17.0833L20 13.4861L13.3403 17.0833L20 20.6806ZM20 26.5382L24.8611 23.9132V20.2431L20 22.9167L15.1389 20.2431V23.9132L20 26.5382Z" fill="white"/>
                </svg>
            </div>
            <div>
                <h1 class="text-title-lg font-bold text-primary tracking-tight leading-tight">Eventoria</h1>
                <p class="text-[11px] text-secondary/70 font-semibold tracking-wide">Academic Management</p>
            </div>
        </div>

        <nav class="space-y-xs">
            <!-- Halaman Dashboard -->
            <a href="{{ route('admin.dashboard') }}" wire:navigate
                class="flex items-center gap-md px-md py-sm rounded-xl transition-all group
                {{ $active === 'dashboard' ? 'bg-secondary-container text-primary shadow-sm' : 'text-secondary hover:bg-surface-container hover:text-on-surface' }}">
                <svg width="17" height="17" class="{{ $active === 'dashboard' ? 'text-primary' : 'text-secondary/70 group-hover:text-on-surface' }}" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9.16667 5.5V0H16.5V5.5H9.16667ZM0 9.16667V0H7.33333V9.16667H0ZM9.16667 16.5V7.33333H16.5V16.5H9.16667ZM0 16.5V11H7.33333V16.5H0ZM1.83333 7.33333H5.5V1.83333H1.83333V7.33333ZM11 14.6667H14.6667V9.16667H11V14.6667ZM11 3.66667H14.6667V1.83333H11V3.66667ZM1.83333 14.6667H5.5V12.8333H1.83333V14.6667Z" fill="currentColor"/>
                </svg>
                <span class="text-body-md {{ $active === 'dashboard' ? 'font-bold' : 'font-medium' }}">Dashboard</span>
            </a>

            <!-- Halaman Moderasi Organisasi -->
            <a href="#" 
                class="flex items-center gap-md px-md py-sm rounded-xl transition-all group
                {{ $active === 'moderasi-organisasi' ? 'bg-secondary-container text-primary shadow-sm' : 'text-secondary hover:bg-surface-container hover:text-on-surface' }}">
                <svg width="15" height="19" class="{{ $active === 'moderasi-organisasi' ? 'text-primary' : 'text-secondary/70 group-hover:text-on-surface' }}" viewBox="0 0 15 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.37083 12.4208L11.55 7.24167L10.2437 5.93542L6.37083 9.80833L4.44583 7.88333L3.13958 9.18958L6.37083 12.4208ZM7.33333 18.3333C5.20972 17.7986 3.4566 16.5802 2.07396 14.6781C0.691319 12.776 0 10.6639 0 8.34167V2.75L7.33333 0L14.6667 2.75V8.34167C14.6667 10.6639 13.9753 12.776 12.5927 14.6781C11.2101 16.5802 9.45694 17.7986 7.33333 18.3333ZM7.33333 16.4083C8.92222 15.9042 10.2361 14.8958 11.275 13.3833C12.3139 11.8708 12.8333 10.1903 12.8333 8.34167V4.01042L7.33333 1.94792L1.83333 4.01042V8.34167C1.83333 10.1903 2.35278 11.8708 3.39167 13.3833C4.43056 14.8958 5.74444 15.9042 7.33333 16.4083Z" fill="currentColor"/>
                </svg>
                <span class="text-body-md {{ $active === 'moderasi-organisasi' ? 'font-bold' : 'font-medium' }}">Moderasi Organisasi</span>
            </a>

            <!-- Halaman Moderasi Event -->
            <a href="{{ route('admin.event.master') }}" wire:navigate
                class="flex items-center gap-md px-md py-sm rounded-xl transition-all group
                {{ $active === 'moderasi-event' ? 'bg-secondary-container text-primary shadow-sm' : 'text-secondary hover:bg-surface-container hover:text-on-surface' }}">
                <svg width="17" height="19" class="{{ $active === 'moderasi-event' ? 'text-primary' : 'text-secondary/70 group-hover:text-on-surface' }}" viewBox="0 0 17 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1.83333 18.3333C1.32917 18.3333 0.897569 18.1538 0.538542 17.7948C0.179514 17.4358 0 17.0042 0 16.5V3.66667C0 3.1625 0.179514 2.7309 0.538542 2.37188C0.897569 2.01285 1.32917 1.83333 1.83333 1.83333H2.75V0H4.58333V1.83333H11.9167V0H13.75V1.83333H14.6667C15.1708 1.83333 15.6024 2.01285 15.9615 2.37188C16.3205 2.7309 16.5 3.1625 16.5 3.66667V16.5C16.5 17.0042 16.3205 17.4358 15.9615 17.7948C15.6024 18.1538 15.1708 18.3333 14.6667 18.3333H1.83333ZM1.83333 16.5H14.6667V7.33333H1.83333V16.5ZM1.83333 5.5H14.6667V3.66667H1.83333V5.5ZM1.83333 5.5V3.66667V5.5ZM3.66667 11V9.16667H12.8333V11H3.66667ZM3.66667 14.6667V12.8333H10.0833V14.6667H3.66667Z" fill="currentColor"/>
                </svg>
                <span class="text-body-md {{ $active === 'moderasi-event' ? 'font-bold' : 'font-medium' }}">Moderasi Event</span>
            </a>

            <!-- Master Kategori -->
            <a href="#" 
                class="flex items-center gap-md px-md py-sm rounded-xl transition-all group
                {{ $active === 'master-kategori' ? 'bg-secondary-container text-primary shadow-sm' : 'text-secondary hover:bg-surface-container hover:text-on-surface' }}">
                <svg width="19" height="20" class="{{ $active === 'master-kategori' ? 'text-primary' : 'text-secondary/70 group-hover:text-on-surface' }}" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3.5 9L9 0L14.5 9H3.5ZM14.5 20C13.25 20 12.1875 19.5625 11.3125 18.6875C10.4375 17.8125 10 16.75 10 15.5C10 14.25 10.4375 13.1875 11.3125 12.3125C12.1875 11.4375 13.25 11 14.5 11C15.75 11 16.8125 11.4375 17.6875 12.3125C18.5625 13.1875 19 14.25 19 15.5C19 16.75 18.5625 17.8125 17.6875 18.6875C16.8125 19.5625 15.75 20 14.5 20ZM0 19.5V11.5H8V19.5H0ZM14.5 18C15.2 18 15.7917 17.7583 16.275 17.275C16.7583 16.7917 17 16.2 17 15.5C17 14.8 16.7583 14.2083 16.275 13.725C15.7917 13.2417 15.2 13 14.5 13C13.8 13 13.2083 13.2417 12.725 13.725C12.2417 14.2083 12 14.8 12 15.5C12 16.2 12.2417 16.7917 12.725 17.275C13.2083 17.7583 13.8 18 14.5 18ZM2 17.5H6V13.5H2V17.5ZM7.05 7H10.95L9 3.85L7.05 7Z" fill="currentColor"/>
                </svg>
                <span class="text-body-md {{ $active === 'master-kategori' ? 'font-bold' : 'font-medium' }}">Master Data Kategori</span>
            </a>

            <!-- Organisasi Aktif -->
            <a href="#" 
                class="flex items-center gap-md px-md py-sm rounded-xl transition-all group
                {{ $active === 'organisasi-aktif' ? 'bg-secondary-container text-primary shadow-sm' : 'text-secondary hover:bg-surface-container hover:text-on-surface' }}">
                <svg width="18" height="13" class="{{ $active === 'organisasi-aktif' ? 'text-primary' : 'text-secondary/70 group-hover:text-on-surface' }}" viewBox="0 0 17 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5.83333 7.29167C3.88333 7.29167 0 8.26667 0 10.2083V11.6667H11.6667V10.2083C11.6667 8.26667 7.78333 7.29167 5.83333 7.29167ZM1.95 10C2.65 9.51667 4.34167 8.95833 5.83333 8.95833C7.325 8.95833 9.01667 9.51667 9.71667 10H1.95ZM5.83333 5.83333C7.44167 5.83333 8.75 4.525 8.75 2.91667C8.75 1.30833 7.44167 0 5.83333 0C4.225 0 2.91667 1.30833 2.91667 2.91667C2.91667 4.525 4.225 5.83333 5.83333 5.83333ZM5.83333 1.66667C6.525 1.66667 7.08333 2.225 7.08333 2.91667C7.08333 3.60833 6.525 4.16667 5.83333 4.16667C5.14167 4.16667 4.58333 3.60833 4.58333 2.91667C4.58333 2.225 5.14167 1.66667 5.83333 1.66667ZM11.7 7.34167C12.6667 8.04167 13.3333 8.975 13.3333 10.2083V11.6667H16.6667V10.2083C16.6667 8.525 13.75 7.56667 11.7 7.34167ZM10.8333 5.83333C12.4417 5.83333 13.75 4.525 13.75 2.91667C13.75 1.30833 12.4417 0 10.8333 0C10.3833 0 9.96667 0.108333 9.58333 0.291667C10.1083 1.03333 10.4167 1.94167 10.4167 2.91667C10.4167 3.89167 10.1083 4.8 9.58333 5.54167C9.96667 5.725 10.3833 5.83333 10.8333 5.83333Z" fill="currentColor"/>
                </svg>
                <span class="text-body-md {{ $active === 'organisasi-aktif' ? 'font-bold' : 'font-medium' }}">Data Organisasi Aktif</span>
            </a>
        </nav>
    </div>
    
    <div class="border-t border-outline-variant/20 pt-md space-y-xs">
        <!-- Halaman Pengaturan -->
        <a href="#" wire:navigate
            class="flex items-center gap-md px-md py-sm rounded-xl transition-all group
            {{ $active === 'pengaturan' ? 'bg-secondary-container text-primary shadow-sm' : 'text-secondary hover:bg-surface-container hover:text-on-surface' }}">
            <svg width="21" height="20" class="{{ $active === 'pengaturan' ? 'text-primary' : 'text-secondary/70 group-hover:text-on-surface' }}" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.3 20L6.9 16.8C6.68333 16.8 6.47917 16.6167 6.2875 16.5C6.09583 16.3833 5.90833 16.2583 5.725 16.125L2.75 17.375L0 12.625L2.575 10.675C2.55833 10.5583 2.55 10.4458 2.55 10.3375C2.55 10.2292 2.55 10.1167 2.55 10C2.55 9.88333 2.55 9.77083 2.55 9.6625C2.55 9.55417 2.55833 9.44167 2.575 9.325L0 7.375L2.75 2.625L5.725 3.875C5.90833 3.74167 6.1 3.61667 6.3 3.5C6.5 3.38333 6.7 3.28333 6.9 3.2L7.3 0H12.8L13.2 3.2C13.4167 3.28333 13.6208 3.38333 13.8125 3.5C14.0042 3.61667 14.1917 3.74167 14.375 3.875L17.35 2.625L20.1 7.375L17.525 9.325C17.5417 9.44167 17.55 9.55417 17.55 9.6625C17.55 9.77083 17.55 9.88333 17.55 10C17.55 10.1167 17.55 10.2292 17.55 10.3375C17.55 10.4458 17.5333 10.5583 17.5 10.675L20.075 12.625L17.325 17.375L14.375 16.125C14.1917 16.2583 14 16.3833 13.8 16.5C13.6 16.6167 13.4 16.7167 13.2 16.8L12.8 20H7.3ZM9.05 18H11.025L11.375 15.35C11.8917 15.2167 12.3708 15.0208 12.8125 14.7625C13.2542 14.5042 13.6583 14.1917 14.025 13.825L16.5 14.85L17.475 13.15L15.325 11.525C15.4083 11.2917 15.4667 11.0458 15.5 10.7875C15.5333 10.5292 15.55 10.2667 15.55 10C15.55 9.73333 15.5333 9.47083 15.5 9.2125C15.4667 8.95417 15.4083 8.70833 15.325 8.475L17.475 6.85L16.5 5.15L14.025 6.2C13.6583 5.81667 13.2542 5.49583 12.8125 5.2375C12.3708 4.97917 11.8917 4.78333 11.375 4.65L11.05 2H9.075L8.725 4.65C8.20833 4.78333 7.72917 4.97917 7.2875 5.2375C6.84583 5.49583 6.44167 5.80833 6.075 6.175L3.6 5.15L2.625 6.85L4.775 8.45C4.69167 8.7 4.63333 8.95 4.6 9.2C4.56667 9.45 4.55 9.71667 4.55 10C4.55 10.2667 4.56667 10.525 4.6 10.775C4.63333 11.025 4.69167 11.275 4.775 11.525L2.625 13.15L3.6 14.85L6.075 13.8C6.44167 14.1833 6.84583 14.5042 7.2875 14.7625C7.72917 15.0208 8.20833 15.2167 8.725 15.35L9.05 18ZM10.1 13.5C11.0667 13.5 11.8917 13.1583 12.575 12.475C13.2583 11.7917 13.6 10.9667 13.6 10C13.6 9.03333 13.2583 8.20833 12.575 7.525C11.8917 6.84167 11.0667 6.5 10.1 6.5C9.11667 6.5 8.2875 6.84167 7.6125 7.525C6.9375 8.20833 6.6 9.03333 6.6 10C6.6 10.9667 6.9375 11.7917 7.6125 12.475C8.2875 13.1583 9.11667 13.5 10.1 13.5Z" fill="currentColor"/>
            </svg>
            <span class="text-body-md {{ $active === 'pengaturan' ? 'font-bold' : 'font-medium' }}">Pengaturan</span>
        </a>
        
        <!-- Button Logout -->
        <button type="button" @click="showLogoutModal = true" 
            class="w-full flex items-center gap-md px-md py-sm text-error hover:bg-error-container/40 rounded-xl transition-all text-left group active:scale-95">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="text-error transition-transform group-hover:translate-x-0.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span class="text-body-md font-bold">Logout</span>
        </button>
    </div>
</div>