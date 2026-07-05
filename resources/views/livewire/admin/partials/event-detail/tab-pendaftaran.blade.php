<div class="w-full bg-surface-container-lowest p-md sm:p-lg rounded-3xl border border-outline-variant/30 shadow-card flex flex-col gap-lg animate-fade-in select-none">
    
    <div class="flex items-center justify-between border-b border-surface-container/60 pb-sm select-none">
        <div class="flex items-center gap-sm text-primary">
            <svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <h4 class="text-title-sm font-bold tracking-tight">Preview Form Formulir Pendaftaran</h4>
        </div>
        <span class="text-caption font-extrabold text-primary bg-primary/5 px-3 py-1 rounded-xl font-sans border border-primary/10">
            {{ $event->formFields?->count() ?? 0 }} Kolom Kustom
        </span>
    </div>

    <div class="w-full bg-surface-container-low p-md sm:p-lg rounded-2xl border border-outline-variant/10 flex flex-col gap-md max-h-[500px] overflow-y-auto [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-transparent [&::-webkit-scrollbar-thumb]:bg-outline-variant/40 [&::-webkit-scrollbar-thumb]:rounded-full hover:[&::-webkit-scrollbar-thumb]:bg-outline-variant/70">
        
        <!-- Form untuk nama sertif -->
        <div class="w-full flex flex-col gap-sm pr-xs border-b border-outline-variant/20 pb-md mb-xs">
            <div class="flex items-center justify-between">
                <label class="text-body-sm font-extrabold text-on-surface flex items-center gap-xs">
                    <span>Nama Lengkap (Cetak Sertifikat)</span>
                    <span class="text-error text-title-xs" title="Wajib Diisi">*</span>
                </label>
                <!-- Badge Penanda Wajib Sistem -->
                <span class="text-[10px] font-extrabold text-indigo-700 bg-indigo-50 border border-indigo-200/50 px-2.5 py-0.5 rounded-md uppercase tracking-wider select-none">
                    Wajib Sistem
                </span>
            </div>

            <div class="w-full text-on-surface">
                <input type="text" 
                        disabled 
                        placeholder="Nama lengkap yang akan dicetak pada lembar sertifikat..." 
                        class="w-full text-body-md px-md py-2.5 bg-surface-container-lowest border border-outline-variant/30 rounded-xl text-on-surface/40 placeholder-secondary/30 font-medium cursor-not-allowed">
            </div>
            <p class="text-[11px] text-secondary/50 font-semibold mt-0.5">
                Sistem otomatis meminta nama ini dan menyimpannya langsung ke kolom registrasi database.
            </p>
        </div>

        @forelse($event->formFields as $field)
            @php
                // Decode opsi meta (pilihan select/radio/checkbox)
                $options = [];
                if (!empty($field->meta_options)) {
                    $options = is_array($field->meta_options) ? $field->meta_options : json_decode($field->meta_options, true);
                }
                $type = is_object($field->tipe_field) ? $field->tipe_field->value : $field->tipe_field;
                $isRequired = (bool) $field->is_required;
            @endphp

            <div class="w-full flex flex-col gap-sm pr-xs">
                <label class="text-body-sm font-extrabold text-on-surface flex items-center gap-xs">
                    <span>{{ $field->nama_field }}</span>
                    @if($isRequired)
                        <span class="text-error text-title-xs" title="Wajib Diisi">*</span>
                    @endif
                </label>

                <div class="w-full text-on-surface">
                    
                    {{-- Kondisi 1: TEXT / NUMBER / EMAIL / URL --}}
                    @if(in_array($type, ['text', 'number', 'email', 'url']))
                        <input type="{{ $type }}" 
                                disabled 
                                placeholder="Contoh input data {{ strtolower($field->nama_field) }}..." 
                                class="w-full text-body-md px-md py-2.5 bg-surface-container-lowest border border-outline-variant/30 rounded-xl text-on-surface/40 placeholder-secondary/30 font-medium cursor-not-allowed">

                    {{-- Kondisi 2: TEXTAREA --}}
                    @elseif($type === 'textarea')
                        <textarea disabled 
                                    rows="3" 
                                    placeholder="Contoh input deskripsi narasi panjang..." 
                                    class="w-full text-body-md px-md py-2.5 bg-surface-container-lowest border border-outline-variant/30 rounded-xl text-on-surface/40 placeholder-secondary/30 font-medium resize-none cursor-not-allowed"></textarea>

                    {{-- Kondisi 3: SELECT DROPDOWN --}}
                    @elseif($type === 'select')
                        <div class="relative w-full">
                            <select disabled class="w-full text-body-md pl-md pr-xl py-2.5 bg-surface-container-lowest border border-outline-variant/30 rounded-xl text-on-surface/40 font-medium cursor-not-allowed appearance-none">
                                <option value="">-- Pilih salah satu opsi --</option>
                                @foreach(($options ?? []) as $opt)
                                    <option value="">{{ $opt }}</option>
                                @endforeach
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-md text-secondary/40 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </span>
                        </div>

                    {{-- Kondisi 4: RADIO BUTTONS --}}
                    @elseif($type === 'radio')
                        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-md pt-xs">
                            @if(!empty($options))
                                @foreach($options as $index => $opt)
                                    <label class="flex items-center gap-xs cursor-not-allowed text-body-sm font-semibold text-secondary/60">
                                        <input type="radio" disabled name="radio-preview-{{ $field->id }}" class="w-4 h-4 text-primary/40 bg-surface-container-lowest border-outline-variant/30 cursor-not-allowed">
                                        <span>{{ $opt }}</span>
                                    </label>
                                @endforeach
                            @else
                                <span class="text-caption text-secondary/40 font-medium italic">Opsi radio pilihan belum dikonfigurasi.</span>
                            @endif
                        </div>

                    {{-- Kondisi 5: CHECKBOXES --}}
                    @elseif($type === 'checkbox')
                        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-md pt-xs">
                            @if(!empty($options))
                                @foreach($options as $opt)
                                    <label class="flex items-center gap-xs cursor-not-allowed text-body-sm font-semibold text-secondary/60">
                                        <input type="checkbox" disabled class="w-4 h-4 rounded text-primary/40 bg-surface-container-lowest border-outline-variant/30 cursor-not-allowed">
                                        <span>{{ $opt }}</span>
                                    </label>
                                @endforeach
                            @else
                                <span class="text-caption text-secondary/40 font-medium italic">Opsi kotak centang belum dikonfigurasi.</span>
                            @endif
                        </div>

                    {{-- Kondisi 6: FILE UPLOAD (PDF ATAU IMAGE) --}}
                    @elseif(in_array($type, ['file_pdf', 'file_image']))
                        <div class="w-full p-md bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant/50 flex flex-col items-center justify-center text-center gap-xs group transition-colors select-none cursor-not-allowed">
                            <div class="w-9 h-9 rounded-xl bg-secondary/5 text-secondary/40 flex items-center justify-center">
                                @if($type === 'file_pdf')
                                    <svg class="w-5 h-5 stroke-[2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                @else
                                    <svg class="w-5 h-5 stroke-[2]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                                @endif
                            </div>
                            <span class="text-caption font-extrabold text-secondary/50">
                                {{ $type === 'file_pdf' ? 'Unggah Berkas Lampiran PDF' : 'Unggah Bukti Gambar Digital' }}
                            </span>
                            <span class="text-[10px] text-secondary/30 font-medium">Maksimal ukuran file dokumen berkas 2MB</span>
                        </div>
                    @endif

                </div>
            </div>
        @empty
            <div class="w-full py-md">
                <x-admin.empty-state 
                    title="Form Kustom Kosong" 
                    description="Pihak panitia pelaksana ormawa tidak melampirkan kolom formulir kustom tambahan selain input data default." 
                />
            </div>
        @endforelse

    </div>
</div>