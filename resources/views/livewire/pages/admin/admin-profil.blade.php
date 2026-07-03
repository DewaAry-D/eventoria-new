<div class="w-full p-md sm:p-lg xl:p-xl space-y-lg sm:space-y-xl select-none animate-fade-in">
    
    <x-admin.header-info title="Pengaturan Akun Admin">
        <p class="text-xs sm:text-body-md text-on-surface-variant/80 font-medium leading-relaxed mt-1">
            Kelola data kredensial instansi, amankan kata sandi, serta tinjau ruang lingkup yurisdiksi pengawasan secara berkala.
        </p>
    </x-admin.header-info>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-md lg:gap-lg w-full items-start">
        
        <div class="lg:col-span-2 flex flex-col gap-md sm:gap-lg">
            
            <!-- Informasi Kredensial Profil -->
            <div class="bg-surface-container-lowest p-md sm:p-lg rounded-2xl border border-outline-variant/30 shadow-2xs flex flex-col gap-md">
                <div class="flex items-center gap-xs text-body-md font-bold text-primary border-b border-outline-variant/10 pb-sm">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    <span>Profil Informasi Instansi</span>
                </div>
                
                <form x-data @submit.prevent="$dispatch('open-modal-confirm-profile')" class="space-y-md">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
                        <div class="flex flex-col gap-xs">
                            <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide">Nama Lengkap Admin</label>
                            <input type="text" wire:model="nama_admin" class="w-full text-body-md px-sm py-2 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary font-bold transition-all">
                            @error('nama_admin') <span class="text-error font-bold text-[10px] pl-xs mt-1 font-sans">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col gap-xs">
                            <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide">Alamat Email Kredensial</label>
                            <input type="email" wire:model="email" class="w-full text-body-md px-sm py-2 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary font-bold transition-all">
                            @error('email') <span class="text-error font-bold text-[10px] pl-xs mt-1 font-sans">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-xs">
                        <button type="submit" class="px-md py-2 bg-primary hover:bg-primary/90 text-white font-bold text-xs rounded-xl shadow-2xs transition-all active:scale-95 cursor-pointer">
                            Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>

            <!-- Form Perbarui Keamanan Password -->
            <div class="bg-surface-container-lowest p-md sm:p-lg rounded-2xl border border-outline-variant/30 shadow-2xs flex flex-col gap-md">
                <div class="flex items-center justify-between border-b border-outline-variant/10 pb-sm">
                    <div class="flex items-center gap-xs text-body-md font-bold text-primary">
                        <svg class="w-4 h-4 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>Keamanan Kata Sandi Akun</span>
                    </div>
                    
                    <button type="button" wire:click="$toggle('isChangingPassword')" class="text-xs font-bold text-primary hover:underline cursor-pointer">
                        {{ $isChangingPassword ? 'Batalkan Ubah Password' : 'Ubah Kata Sandi Akun' }}
                    </button>
                </div>
                
                @if($isChangingPassword)
                    <form x-data @submit.prevent="$dispatch('open-modal-confirm-password')" class="space-y-md animate-fade-in">
            
                        <!-- Kata Sandi Saat Ini -->
                        <div class="flex flex-col gap-xs">
                            <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide">Kata Sandi Saat Ini</label>
                            <div x-data="{ show: false }" class="relative w-full flex items-center">
                                <input :type="show ? 'text' : 'password'" 
                                    wire:model="current_password" 
                                    class="w-full text-body-md pl-sm pr-[40px] py-2 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary font-medium transition-all">
                                
                                {{-- Tombol Ikon Mata --}}
                                <button type="button" @click="show = !show" class="absolute right-sm text-secondary/50 hover:text-primary transition-colors cursor-pointer select-none">
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                    </svg>
                                </button>
                            </div>
                            @error('current_password') <span class="text-error font-bold text-[10px] pl-xs mt-1 font-sans">{{ $message }}</span> @enderror
                        </div>
                
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
                            
                            <!-- Kata Sandi Baru -->
                            <div class="flex flex-col gap-xs">
                                <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide">Kata Sandi Baru</label>
                                <div x-data="{ show: false }" class="relative w-full flex items-center">
                                    <input :type="show ? 'text' : 'password'" 
                                        wire:model="password" 
                                        class="w-full text-body-md pl-sm pr-[40px] py-2 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary font-medium transition-all">
                                    
                                    <button type="button" @click="show = !show" class="absolute right-sm text-secondary/50 hover:text-primary transition-colors cursor-pointer select-none">
                                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                        </svg>
                                    </button>
                                </div>
                                @error('password') <span class="text-error font-bold text-[10px] pl-xs mt-1 font-sans">{{ $message }}</span> @enderror
                            </div>
                
                            <!-- Konfirmasi Kata Sandi Baru -->
                            <div class="flex flex-col gap-xs">
                                <label class="text-xs font-bold text-secondary/60 uppercase tracking-wide">Konfirmasi Kata Sandi Baru</label>
                                <div x-data="{ show: false }" class="relative w-full flex items-center">
                                    <input :type="show ? 'text' : 'password'" 
                                        wire:model="password_confirmation" 
                                        class="w-full text-body-md pl-sm pr-[40px] py-2 bg-surface-container/40 border border-outline-variant/30 rounded-xl focus:outline-none focus:border-primary/30 text-primary font-medium transition-all">
                                    
                                    <button type="button" @click="show = !show" class="absolute right-sm text-secondary/50 hover:text-primary transition-colors cursor-pointer select-none">
                                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" x-cloak>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                
                        <div class="flex justify-end pt-xs">
                            <button type="submit" class="px-md py-2 bg-primary hover:bg-primary/90 text-white font-bold text-xs rounded-xl shadow-2xs transition-all active:scale-95 cursor-pointer">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                @else
                <div class="flex gap-sm p-sm bg-primary/[0.03] border-l-4 border-primary rounded-r-xl text-caption text-secondary/80 font-medium leading-relaxed">
                    <svg class="w-4 h-4 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.286z"/>
                    </svg>
                    <p>
                        <span class="font-extrabold text-primary">Perlindungan Akun Aktif:</span> 
                        Kata sandi Anda disimpan dengan aman menggunakan enkripsi standar industri. Demi menjaga privasi data, disarankan untuk memperbarui kata sandi secara berkala.
                    </p>
                </div>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="flex flex-col gap-md">
            <!-- Detail Ruang Lingkup Otoritas Akademik -->
            <div class="bg-surface-container-lowest p-md rounded-2xl border border-outline-variant/30 shadow-sm flex flex-col gap-sm select-none">
                <span class="text-caption font-bold text-secondary/60 uppercase tracking-wide">Yurisdiksi Otoritas</span>
                <div class="p-md rounded-xl border border-primary/10 bg-primary/[0.02] flex flex-col items-center justify-center text-center gap-sm shadow-2xs py-lg">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary border border-primary/20 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18"/></svg>
                    </div>
                    <div class="flex flex-col gap-1 w-full">
                        <span class="text-[10px] text-secondary/40 font-bold uppercase tracking-widest">Ruang Lingkup Kerja</span>
                        <h4 class="text-body-md font-bold text-primary tracking-tight px-xs leading-snug">{{ $nama_fakultas }}</h4>
                    </div>
                </div>

                <div class="p-sm bg-surface-container/30 rounded-xl text-caption font-sans text-secondary/60 leading-relaxed font-medium flex flex-col gap-xs">
                    <div class="flex items-center gap-xs font-bold text-primary">
                        <svg width="18" height="18" viewBox="0 0 20 20" class="w-4 h-4 text-primary shrink-0" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 15H11V9H9V15ZM10 7C10.2833 7 10.5208 6.90417 10.7125 6.7125C10.9042 6.52083 11 6.28333 11 6C11 5.71667 10.9042 5.47917 10.7125 5.2875C10.5208 5.09583 10.2833 5 10 5C9.71667 5 9.47917 5.09583 9.2875 5.2875C9.09583 5.47917 9 5.71667 9 6C9 6.28333 9.09583 6.52083 9.2875 6.7125C9.47917 6.90417 9.71667 7 10 7ZM10 20C8.61667 20 7.31667 19.7375 6.1 19.2125C4.88333 18.6875 3.825 17.975 2.925 17.075C2.025 16.175 1.3125 15.1167 0.7875 13.9C0.2625 12.6833 0 11.3833 0 10C0 8.61667 0.2625 7.31667 0.7875 6.1C1.3125 4.88333 2.025 3.825 2.925 2.925C3.825 2.025 4.88333 1.3125 6.1 0.7875C7.31667 0.2625 8.61667 0 10 0C11.3833 0 12.6833 0.2625 13.9 0.7875C15.1167 1.3125 16.175 2.025 17.075 2.925C17.975 3.825 18.6875 4.88333 19.2125 6.1C19.7375 7.31667 20 8.61667 20 10C20 11.3833 19.7375 12.6833 19.2125 13.9C18.6875 15.1167 17.975 16.175 17.075 17.075C16.175 17.975 15.1167 18.6875 13.9 19.2125C12.6833 19.7375 11.3833 20 10 20ZM10 18C12.2333 18 14.125 17.225 15.675 15.675C17.225 14.125 18 12.2333 18 10C18 7.76667 17.225 5.875 15.675 4.325C14.125 2.775 12.2333 2 10 2C7.76667 2 5.875 2.775 4.325 4.325C2.775 5.875 2 7.76667 2 10C2 12.2333 2.775 14.125 4.325 15.675C5.875 17.225 7.76667 18 10 18Z" fill="currentColor"/></svg>
                        <span>Sistem Otoritas Terisolasi:</span>
                    </div>
                    <p class="mt-smtext-justify">Akun Anda terikat pada struktur birokrasi institusi resmi. Anda hanya dapat melihat, melakukan moderasi, serta menyetujui ajuan berkas yang berada di bawah lingkup perlindungan wilayah akademik ini.</p>
                </div>
            </div>

            <!-- Panel Status Tugas & Produktivitas Kerja -->
            <div class="bg-surface-container-lowest p-md rounded-2xl border border-outline-variant/30 shadow-sm flex flex-col gap-sm">
                <span class="text-caption font-bold text-secondary/60 uppercase tracking-wide">Ringkasan Beban Kerja</span>
                <div class="flex flex-col gap-sm text-caption text-secondary/70 font-medium font-sans">
                    <div class="flex justify-between items-center border-b border-outline-variant/10 pb-2">
                        <span>Antrean Moderasi Ormawa</span>
                        @if($totalPendingOrganisasi > 0)
                            <span class="px-2 py-0.5 bg-error/10 text-error font-bold rounded-full text-[10px] animate-pulse">{{ $totalPendingOrganisasi }} Berkas Menunggu</span>
                        @else
                            <span class="px-2 py-0.5 bg-success/10 text-success font-bold rounded-full text-[10px]">Selesai</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center py-0.5">
                        <span>Antrean Pengajuan Event</span>
                        @if($totalPendingEvent > 0)
                            <span class="px-2 py-0.5 bg-warning/20 text-amber-700 font-bold rounded-full text-[10px] animate-pulse">{{ $totalPendingEvent }} Proposal Menunggu</span>
                        @else
                            <span class="px-2 py-0.5 bg-success/10 text-success font-bold rounded-full text-[10px]">Selesai</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <!-- Global Component -->
    
    <!-- Modal Konfirmasi Perbarui Profil -->
    <x-admin.modals.confirm-modal 
        id="confirm-profile" 
        title="Konfirmasi Perbarui Profil" 
        wireAction="updateProfil">
        Apakah Anda yakin ingin menyimpan perubahan informasi nama admin instansi dan alamat email kredensial yang baru ini?
    </x-admin.modals.confirm-modal>

    <!-- Modal Konfirmasi Perbarui Password -->
    <x-admin.modals.confirm-modal 
        id="confirm-password" 
        title="Amankan Kata Sandi Baru?" 
        wireAction="updatePassword">
        Tindakan ini akan mengganti kredensial enkripsi kunci masuk akun Anda. Pastikan Anda mengingat kata sandi baru yang telah dimasukkan.
    </x-admin.modals.confirm-modal>

    <!-- Toast Alert Global -->
    <x-admin.modals.toast-alert />

</div>