<div class="w-full p-md sm:p-lg xl:p-xl space-y-lg sm:space-y-xl">

    <x-admin.header-info title="Dashboard Overview">
        <p class="text-xs sm:text-body-md text-on-surface-variant/80 font-medium leading-relaxed mt-1">
            Mari kelola aktivitas kampus di lingkup <strong class="text-primary font-bold">{{ $scopeName }}</strong>
        </p>
    
        <!-- Unduh Laporan -->
        <x-slot name="action">
            <button type="button" wire:click="exportReport" wire:loading.attr="disabled"
                class="inline-flex items-center justify-center gap-sm px-md sm:px-lg py-2.5 sm:py-md bg-[#000666] text-white font-bold sm:font-bold rounded-lg shadow-sm hover:bg-[#000666]/90 disabled:opacity-50 transition-colors text-xs sm:text-body-md group">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                </svg>
                <span wire:loading.remove wire:target="exportReport">Unduh Laporan</span>
                <span wire:loading wire:target="exportReport">Mengekspor...</span>
            </button>
        </x-slot>
    </x-admin.header-info>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-sm sm:gap-md w-full">
        <!-- Card 1 -->
        <x-admin.cards.stat-card-bento :value="$cardsData['orgAktif']" title="Organisasi Aktif" badgeText="Aktif" badgeType="success" iconBg="primary">
            <x-slot name="icon">
                <svg width="24" height="21" viewBox="0 0 24 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 21V0H11.6667V4.66667H23.3333V21H0ZM2.33333 18.6667H9.33333V16.3333H2.33333V18.6667ZM2.33333 14H9.33333V11.6667H2.33333V14ZM2.33333 9.33333H9.33333V7H2.33333V9.33333ZM2.33333 4.66667H9.33333V2.33333H2.33333V4.66667ZM11.6667 18.6667H21V7H11.6667V18.6667ZM14 11.6667V9.33333H18.6667V11.6667H14ZM14 16.3333V14H18.6667V16.3333H14Z" fill="currentColor"/></svg>
            </x-slot>
            <x-slot name="badgeIcon">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.3 7.3L7.825 3.775L7.125 3.075L4.3 5.9L2.875 4.475L2.175 5.175L4.3 7.3Z" fill="currentColor"/></svg>
            </x-slot>
        </x-admin.cards.stat-card-bento>
    
        <!-- Card 2 -->
        <x-admin.cards.stat-card-bento :value="$cardsData['eventBerlangsung']" title="Event Berlangsung" badgeText="Published" badgeType="success" iconBg="primary">
            <x-slot name="icon">
                <svg width="19" height="21" viewBox="0 0 19 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14 21C12.6167 21 11.4375 20.5125 10.4625 19.5375C9.4875 18.5625 9 17.3833 9 16C9 14.6167 9.4875 13.4375 10.4625 12.4625C11.4375 11.4875 12.6167 11 14 11C15.3833 11 16.5625 11.4875 17.5375 12.4625C18.5125 13.4375 19 14.6167 19 16C19 17.3833 18.5125 18.5625 17.5375 19.5375C16.5625 20.5125 15.3833 21 14 21ZM15.675 18.375L16.375 17.675L14.5 15.8V13H13.5V16.2L15.675 18.375ZM2 20C1.45 20 0.979167 19.8042 0.5875 19.4125C0.195833 19.0208 0 18.55 0 18V4C0 3.45 0.195833 2.97917 0.5875 2.5875C0.979167 2.19583 1.45 2 2 2H6.175C6.35833 1.41667 6.71667 0.9375 7.25 0.5625C7.78333 0.1875 8.36667 0 9 0C9.66667 0 10.2625 0.1875 10.7875 0.5625C11.3125 0.9375 11.6667 1.41667 11.85 2H16C16.55 2 17.0208 2.19583 17.4125 2.5875C17.8042 2.97917 18 3.45 18 4V10.25C17.7 10.0333 17.3833 9.85 17.05 9.7C16.7167 9.55 16.3667 9.41667 16 9.3V4H14V7H4V4H2V18H7.3C7.41667 18.3667 7.55 18.7167 7.7 19.05C7.85 19.3833 8.03333 19.7 8.25 20H2ZM9 4C9.28333 4 9.52083 3.90417 9.7125 3.7125C9.90417 3.52083 10 3.28333 10 3C10 2.71667 9.90417 2.47917 9.7125 2.2875C9.52083 2.09583 9.28333 2 9 2C8.71667 2 8.47917 2.09583 8.2875 2.2875C8.09583 2.47917 8 2.71667 8 3C8 3.28333 8.09583 3.52083 8.2875 3.7125C8.47917 3.90417 8.71667 4 9 4Z" fill="currentColor"/></svg>
            </x-slot>
        </x-admin.cards.stat-card-bento>
    
        <!-- Card 3 -->
        <x-admin.cards.stat-card-bento :value="$cardsData['pendingOrg']" title="Pengajuan Organisasi" badgeText="Pending" badgeType="neutral" iconBg="primary">
            <x-slot name="icon">
                <svg width="24" height="20" viewBox="0 0 24 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.8083 19.1333L11.7833 15.1083L13.4167 13.475L15.8083 15.8667L21.7 9.975L23.3333 11.6083L15.8083 19.1333ZM9.33333 9.33333C8.05 9.33333 6.95139 8.87639 6.0375 7.9625C5.12361 7.04861 4.66667 5.95 4.66667 4.66667C4.66667 3.38333 5.12361 2.28472 6.0375 1.37083C6.95139 0.456944 8.05 0 9.33333 0C10.6167 0 11.7153 0.456944 12.6292 1.37083C13.5431 2.28472 14 3.38333 14 4.66667C14 5.95 13.5431 7.04861 12.6292 7.9625C11.7153 8.87639 10.6167 9.33333 9.33333 9.33333ZM12.6583 10.85L8.4 15.1083L11.9583 18.6667H0V15.4C0 14.7583 0.165278 14.1556 0.495833 13.5917C0.826389 13.0278 1.28333 12.6 1.86667 12.3083C2.85833 11.8028 3.97639 11.375 5.22083 11.025C6.46528 10.675 7.83611 10.5 9.33333 10.5C9.91667 10.5 10.4854 10.5292 11.0396 10.5875C11.5938 10.6458 12.1333 10.7333 12.6583 10.85Z" fill="currentColor"/></svg>
            </x-slot>
        </x-admin.cards.stat-card-bento>
    
        <!-- Card 4 -->
        <x-admin.cards.stat-card-bento :value="$cardsData['pendingEvent']" title="Pengajuan Event" badgeText="Butuh Review" badgeType="error" iconBg="error">
            <x-slot name="icon">
                <svg width="23" height="25" viewBox="0 0 23 25" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.3333 24.5C14.7194 24.5 13.3438 23.9313 12.2063 22.7938C11.0688 21.6562 10.5 20.2806 10.5 18.6667C10.5 17.0528 11.0688 15.6771 12.2063 14.5396C13.3438 13.4021 14.7194 12.8333 16.3333 12.8333C17.9472 12.8333 19.3229 13.4021 20.4604 14.5396C21.5979 15.6771 22.1667 17.0528 22.1667 18.6667C22.1667 20.2806 21.5979 21.6562 20.4604 22.7938C19.3229 23.9313 17.9472 24.5 16.3333 24.5ZM18.2875 21.4375L19.1042 20.6208L16.9167 18.4333V15.1667H15.75V18.9L18.2875 21.4375ZM2.33333 23.3333C1.69167 23.3333 1.14236 23.1049 0.685417 22.6479C0.228472 22.191 0 21.6417 0 21V4.66667C0 4.025 0.228472 3.47569 0.685417 3.01875C1.14236 2.56181 1.69167 2.33333 2.33333 2.33333H7.20417C7.41806 1.65278 7.83611 1.09375 8.45833 0.65625C9.08055 0.21875 9.76111 0 10.5 0C11.2778 0 11.9729 0.21875 12.5854 0.65625C13.1979 1.09375 13.6111 1.65278 13.825 2.33333H18.6667C19.3083 2.33333 19.8576 2.56181 20.3146 3.01875C20.7715 3.47569 21 4.025 21 4.66667V11.9583C20.65 11.7056 20.2806 11.4917 19.8917 11.3167C19.5028 11.1417 19.0944 10.9861 18.6667 10.85V4.66667H16.3333V8.16667H4.66667V4.66667H2.33333V21H8.51667C8.65278 21.4278 8.80833 21.8361 8.98333 22.225C9.15833 22.6139 9.37222 22.9833 9.625 23.3333H2.33333ZM10.5 4.66667C10.8306 4.66667 11.1076 4.55486 11.3313 4.33125C11.5549 4.10764 11.6667 3.83056 11.6667 3.5C11.6667 3.16944 11.5549 2.89236 11.3313 2.66875C11.1076 2.44514 10.8306 2.33333 10.5 2.33333C10.1694 2.33333 9.89236 2.44514 9.66875 2.66875C9.44514 2.89236 9.33333 3.16944 9.33333 3.5C9.33333 3.83056 9.44514 4.10764 9.66875 4.33125C9.89236 4.55486 10.1694 4.66667 10.5 4.66667Z" fill="currentColor"/></svg>
            </x-slot>
        </x-admin.cards.stat-card-bento>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-lg items-stretch">

        <!-- Sisi Kiri -->
        <div class="lg:col-span-8 space-y-lg flex flex-col">
            <!-- Bar Chart -->
            <livewire:admin.charts.bar-event 
                :fakultasId="$fakultasId" 
                title="Jumlah Event Per Bulan"
                description="Data pertumbuhan volume aktivitas event 6 bulan terakhir"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-lg items-stretch">

                <!-- Event Progress -->
                <div class="h-full flex flex-col">
                    <x-admin.stats.event-progress 
                        :disetujui="$statPengajuan['disetujui']" 
                        :menunggu="$statPengajuan['menunggu']" 
                        :ditolak="$statPengajuan['ditolak']" 
                    />
                </div>

                <!-- Line Chart -->
                <div class="h-full flex flex-col">
                    <livewire:admin.charts.line-trend 
                        :fakultasId="$fakultasId" 
                        title="Tren Organisasi Aktif"
                    />
                </div>
            </div>

            <!-- Pengajuan Event -->
            <livewire:admin.event-master 
                :fakultasId="$fakultasId" 
                :isDashboard="true" 
                title="Antrean Pengajuan Event Terbaru" 
            />
        </div>
    
        <!-- Sisi Kanan -->
        <div class="lg:col-span-4 space-y-lg flex flex-col">
            <!-- Pie Chart -->
            <livewire:admin.charts.pie-category 
                :fakultasId="$fakultasId" 
                title="Distribusi Kategori Event" 
            />

            <!-- Sorotan Aktivitas -->
            <livewire:admin.widgets.activity-highlight :fakultasId="$fakultasId" />

            <!-- Organisasi Baru -->
            <livewire:admin.widgets.new-organizations :fakultasId="$fakultasId" />
        </div>
    </div>
</div>