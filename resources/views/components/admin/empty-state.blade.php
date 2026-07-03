@props([
    'title' => 'Belum Ada Data',
    'description' => 'Data yang kamu cari atau ajukan saat ini belum tersedia di sistem.',
    'inTable' => false, // Set TRUE jika ditaruh di dalam tag <tbody> tabel
    'colspan' => 1      // Jumlah kolom tabel (wajib diisi jika inTable="true")
])

@if($inTable)
    <tr>
        <td colspan="{{ $colspan }}" class="p-xl text-center bg-transparent border-none">
            <div class="flex flex-col items-center justify-center text-center select-none animate-fade-in min-h-[16rem]">
                <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center text-secondary/40 mb-md transition-transform duration-500 hover:scale-105 hover:rotate-6 shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.008 1.24l.885 1.77a2.25 2.25 0 002.007 1.24h1.98a2.25 2.25 0 002.007-1.24l.885-1.77a2.25 2.25 0 012.007-1.24h3.86m-18 0h18a2.25 2.25 0 012.25 2.25v4.5A2.25 2.25 0 0118.75 21H5.25A2.25 2.25 0 013 18.75v-4.5a2.25 2.25 0 012.25-2.25zm1.39-4.878L10.27 3.3a2.25 2.25 0 013.46 0l3.63 4.322m-7.63 3.078h4.001M12 7.5v3"/>
                    </svg>
                </div>

                <h3 class="text-title-md font-bold text-on-surface tracking-tight mb-sm">
                    {{ $title }}
                </h3>
                <p class="text-body-md text-on-surface-variant max-w-sm leading-relaxed mb-md">
                    {{ $description }}
                </p>

                @if($slot->isNotEmpty())
                    <div class="flex justify-center items-center">
                        {{ $slot }}
                    </div>
                @endif

            </div>
        </td>
    </tr>
@else
    <div class="flex flex-col items-center justify-center text-center p-lg select-none min-h-[11rem] bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-card animate-fade-in w-full">
        
        <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-secondary/40 mb-sm transition-transform duration-500 hover:scale-105 hover:rotate-6 shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.008 1.24l.885 1.77a2.25 2.25 0 002.007 1.24h1.98a2.25 2.25 0 002.007-1.24l.885-1.77a2.25 2.25 0 012.007-1.24h3.86m-18 0h18a2.25 2.25 0 012.25 2.25v4.5A2.25 2.25 0 0118.75 21H5.25A2.25 2.25 0 013 18.75v-4.5a2.25 2.25 0 012.25-2.25zm1.39-4.878L10.27 3.3a2.25 2.25 0 013.46 0l3.63 4.322m-7.63 3.078h4.001M12 7.5v3"/>
            </svg>
        </div>

        <h3 class="text-title-sm font-bold text-on-surface tracking-tight mb-xs">
            {{ $title }}
        </h3>
        
        <p class="text-body-sm text-on-surface-variant max-w-xs leading-relaxed mb-sm">
            {{ $description }}
        </p>

        @if($slot->isNotEmpty())
            <div class="flex justify-center items-center mt-xs">
                {{ $slot }}
            </div>
        @endif

    </div>
@endif