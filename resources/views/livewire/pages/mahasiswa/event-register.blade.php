<?php

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventFormResponse;
use App\Enums\RegistrationStatus;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

new #[Layout('layouts.mahasiswa')] class extends Component {
    use WithFileUploads;

    public Event $event;
    public array $answers = [];
    public array $files = [];

    public string $namaCetakSertifikat = '';

    public function mount(Event $event)
    {
        // Load the event with formFields
        $this->event = $event->load(['formFields']);

        // Check if student has already registered for this event
        $user = Auth::user();
        if ($user && $user->mahasiswa) {
            $existing = EventRegistration::where('event_id', $this->event->id)
                ->where('mahasiswa_id', $user->mahasiswa->id)
                ->first();

            if ($existing) {
                session()->flash('error', 'Anda sudah terdaftar untuk event ini.');
                return $this->redirectRoute('mahasiswa.event-detail', $this->event->slug, navigate: true);
            }

            $this->namaCetakSertifikat = $user->mahasiswa->nama;
        }

        // If no slot is left
        if ($this->event->sisa_kuota <= 0) {
            session()->flash('error', 'Pendaftaran gagal karena kuota event sudah penuh.');
            return $this->redirectRoute('mahasiswa.event-detail', $this->event->slug, navigate: true);
        }

        // Initialize answers array for all fields
        foreach ($this->event->formFields as $field) {
            $this->answers[$field->id] = '';
        }
    }

    public function submit()
    {
        $user = Auth::user();
        if (!$user || !$user->mahasiswa) {
            session()->flash('error', 'Anda harus masuk sebagai mahasiswa untuk mendaftar.');
            return;
        }

        // Dynamic validation rules & custom error messages
        $rules = [];
        $messages = [];

        $rules['namaCetakSertifikat'] = 'required|string|min:3|max:255';
        $messages['namaCetakSertifikat.required'] = "Kolom 'Nama Lengkap (Cetak Sertifikat)' wajib diisi.";
        $messages['namaCetakSertifikat.min'] = "Nama lengkap untuk sertifikat terlalu pendek.";
        
        foreach ($this->event->formFields as $field) {
            $fieldName = $field->nama_field;
            $isFieldRequired = (bool) $field->is_required;
            $tipe = $field->tipe_field->value ?? $field->tipe_field;
            
            $ruleKey = '';
            if (in_array($tipe, ['file_pdf', 'file_image'])) {
                $ruleKey = "files.{$field->id}";
            } else {
                $ruleKey = "answers.{$field->id}";
            }

            $fieldRules = [];
            if ($isFieldRequired) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Type-specific rules
            if ($tipe === 'email') {
                $fieldRules[] = 'email';
            } elseif ($tipe === 'number') {
                $fieldRules[] = 'numeric';
            } elseif ($tipe === 'url') {
                $fieldRules[] = 'url';
            } elseif ($tipe === 'file_pdf') {
                $fieldRules[] = 'file';
                $fieldRules[] = 'mimes:pdf';
                $fieldRules[] = 'max:2048'; // 2MB
            } elseif ($tipe === 'file_image') {
                $fieldRules[] = 'image';
                $fieldRules[] = 'max:2048'; // 2MB
            }

            if (!empty($fieldRules)) {
                $rules[$ruleKey] = implode('|', $fieldRules);
            }

            // Custom error messages
            $messages["{$ruleKey}.required"] = "Kolom '{$fieldName}' wajib diisi.";
            $messages["{$ruleKey}.email"] = "Format email pada kolom '{$fieldName}' tidak valid.";
            $messages["{$ruleKey}.numeric"] = "Kolom '{$fieldName}' harus berupa angka.";
            $messages["{$ruleKey}.url"] = "Format tautan/URL pada kolom '{$fieldName}' tidak valid.";
            $messages["{$ruleKey}.mimes"] = "Berkas pada kolom '{$fieldName}' harus berupa file PDF.";
            $messages["{$ruleKey}.image"] = "Berkas pada kolom '{$fieldName}' harus berupa gambar (JPEG, PNG, dll.).";
            $messages["{$ruleKey}.max"] = "Ukuran berkas pada kolom '{$fieldName}' tidak boleh lebih dari 2MB.";
        }

        $this->validate($rules, $messages);

        try {
            DB::transaction(function () use ($user) {
                // Reload event to lock row and avoid race conditions on quota
                $eventLock = Event::where('id', $this->event->id)->lockForUpdate()->first();

                if ($eventLock->sisa_kuota <= 0) {
                    throw new \Exception('Kuota event ini sudah penuh.');
                }

                // Create EventRegistration
                $registration = EventRegistration::create([
                    'mahasiswa_id' => $user->mahasiswa->id,
                    'event_id' => $this->event->id,
                    'waktu_daftar' => now(),
                    'status_pendaftaran' => RegistrationStatus::PENDING,
                    'nama_cetak_sertifikat' => trim($this->namaCetakSertifikat),
                ]);

                // Save form responses
                foreach ($this->event->formFields as $field) {
                    $tipe = $field->tipe_field->value ?? $field->tipe_field;
                    $jawaban = null;

                    if (in_array($tipe, ['file_pdf', 'file_image'])) {
                        if (isset($this->files[$field->id])) {
                            // Store upload and get filename/path
                            $path = $this->files[$field->id]->store('form-responses', 'public');
                            $jawaban = $path;
                        }
                    } else {
                        $jawaban = $this->answers[$field->id] ?? null;
                    }

                    EventFormResponse::create([
                        'registration_id' => $registration->id,
                        'field_id' => $field->id,
                        'jawaban' => $jawaban,
                    ]);
                }

                // Decrement sisa_kuota
                $eventLock->decrement('sisa_kuota');
            });

            session()->flash('success', 'Pendaftaran Anda berhasil dikirim! Silakan tunggu konfirmasi dari penyelenggara.');
            return $this->redirectRoute('mahasiswa.event-detail', $this->event->slug, navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }
}; ?>

<div class="max-w-3xl mx-auto py-4 px-2">
    {{-- Back Link --}}
    <div class="mb-6">
        <a href="{{ route('mahasiswa.event-detail', $event->slug) }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:text-primary-container transition group">
            <svg class="w-5 h-5 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Detail Event
        </a>
    </div>

    {{-- Alert Messages --}}
    @if(session()->has('error'))
        <div class="mb-6 p-4 bg-error bg-opacity-10 border border-error border-opacity-20 text-error text-sm font-semibold rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Main Registration Card --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm overflow-hidden">
        {{-- Card Header --}}
        <div class="bg-primary-container p-6 md:p-8 text-white relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-48 h-48 bg-primary rounded-full opacity-25 z-0"></div>
            
            <div class="relative z-10 space-y-2">
                <span class="px-3 py-1 bg-surface bg-opacity-25 text-white text-[10px] font-bold rounded-full uppercase tracking-wider">
                    {{ $event->kategori->nama_kategori ?? 'Pendaftaran' }}
                </span>
                <h1 class="text-xl md:text-2xl font-extrabold tracking-tight leading-tight">Formulir Pendaftaran</h1>
                <p class="text-xs text-on-primary-container font-medium">{{ $event->nama_event }}</p>
                <div class="flex items-center gap-2 text-[11px] text-on-primary-container pt-1">
                    <span>Diselenggarakan oleh: <strong>{{ $event->penyelenggara }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Form Body --}}
        <form wire:submit.prevent="submit" class="p-6 md:p-8 space-y-6">
            @csrf

            <div class="space-y-5">
                <div class="space-y-2 p-4 bg-indigo-50 bg-opacity-40 border border-indigo-100 rounded-xl">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-indigo-900 uppercase tracking-wide">
                            Nama Lengkap (Cetak Sertifikat)
                            <span class="text-error font-extrabold">*</span>
                        </label>
                        <span class="text-[9px] font-bold text-indigo-700 bg-white border border-indigo-200 px-2 py-0.5 rounded uppercase tracking-wider">
                            Wajib Sistem
                        </span>
                    </div>
                    
                    <input 
                        type="text" 
                        wire:model="namaCetakSertifikat" 
                        placeholder="Masukkan nama lengkap beserta gelar (jika ada) untuk lembar sertifikat..." 
                        class="w-full px-4 py-2.5 text-sm bg-white border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-on-surface transition shadow-sm placeholder-outline-variant"
                    >
                    <p class="text-[10px] text-indigo-950 text-opacity-60 font-medium">
                        Periksa kembali ejaan nama Anda. Nama ini akan langsung digunakan secara permanen oleh sistem untuk merender teks sertifikat.
                    </p>
                    
                    @error('namaCetakSertifikat')
                        <span class="block text-[11px] text-error font-semibold mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- @if($event->formFields->isEmpty())
                    <div class="border-t border-dashed border-outline-variant/60 my-4"></div>

                    <div class="text-center p-8 border border-dashed border-outline-variant rounded-xl bg-surface-container-low">
                        <svg class="mx-auto w-12 h-12 text-outline mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm font-bold text-primary">Formulir Standar</p>
                        <p class="text-xs text-on-surface-variant mt-1">Event ini tidak memerlukan informasi tambahan khusus. Anda dapat langsung mengirim pendaftaran.</p>
                    </div>
                @endif --}}

                @if(!$event->formFields->isEmpty())
                    <div class="border-t border-dashed border-outline-variant/60 my-4"></div>
                @endif
                
                @foreach($event->formFields as $field)
                    @php
                        $tipe = $field->tipe_field->value ?? $field->tipe_field;
                        $isRequired = (bool) $field->is_required;
                        $options = $field->meta_options;
                    @endphp
                    
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-primary uppercase tracking-wide">
                            {{ $field->nama_field }}
                            @if($isRequired)
                                <span class="text-error font-extrabold">*</span>
                            @endif
                        </label>

                        {{-- Text, Number, Email, URL inputs --}}
                        @if(in_array($tipe, ['text', 'number', 'email', 'url']))
                            <input 
                                type="{{ $tipe }}" 
                                wire:model="answers.{{ $field->id }}" 
                                placeholder="Masukkan {{ strtolower($field->nama_field) }}..." 
                                class="w-full px-4 py-2.5 text-sm bg-surface-container-lowest border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-on-surface transition shadow-sm placeholder-outline-variant"
                            >
                        @endif

                        {{-- Textarea inputs --}}
                        @if($tipe === 'textarea')
                            <textarea 
                                wire:model="answers.{{ $field->id }}" 
                                rows="4" 
                                placeholder="Masukkan {{ strtolower($field->nama_field) }}..." 
                                class="w-full px-4 py-2.5 text-sm bg-surface-container-lowest border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-on-surface transition shadow-sm placeholder-outline-variant"
                            ></textarea>
                        @endif

                        {{-- Select dropdown --}}
                        @if($tipe === 'select')
                            <select 
                                wire:model="answers.{{ $field->id }}" 
                                class="w-full px-4 py-2.5 text-sm bg-surface-container-lowest border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-on-surface transition shadow-sm"
                            >
                                <option value="">Pilih opsi...</option>
                                @if(!empty($options))
                                    @foreach($options as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                @endif
                            </select>
                        @endif

                        {{-- Radio options --}}
                        @if($tipe === 'radio')
                            <div class="flex flex-col gap-2.5 pt-1">
                                @if(!empty($options))
                                    @foreach($options as $opt)
                                        <label class="inline-flex items-center gap-2 text-sm text-on-surface cursor-pointer select-none">
                                            <input 
                                                type="radio" 
                                                name="radio_{{ $field->id }}" 
                                                value="{{ $opt }}" 
                                                wire:model="answers.{{ $field->id }}" 
                                                class="w-4.5 h-4.5 text-primary border-outline focus:ring-primary focus:ring-offset-0 cursor-pointer animate-none"
                                            >
                                            <span>{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                @endif
                            </div>
                        @endif

                        {{-- File PDF --}}
                        @if($tipe === 'file_pdf')
                            <div class="flex flex-col gap-2">
                                <input 
                                    type="file" 
                                    wire:model="files.{{ $field->id }}" 
                                    accept=".pdf"
                                    class="block w-full text-xs text-on-surface-variant border border-outline rounded-lg cursor-pointer bg-surface-container-lowest focus:outline-none file:mr-4 file:py-2.5 file:px-4 file:rounded-l-lg file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary-container transition file:cursor-pointer"
                                >
                                <p class="text-[10px] text-on-surface-variant">Format berkas wajib PDF, ukuran maksimal 2MB.</p>
                                
                                {{-- Uploading State --}}
                                <div wire:loading wire:target="files.{{ $field->id }}" class="text-[10px] text-primary font-bold animate-pulse">
                                    Mengunggah berkas...
                                </div>
                            </div>
                        @endif

                        {{-- File Image --}}
                        @if($tipe === 'file_image')
                            <div class="flex flex-col gap-2">
                                <input 
                                    type="file" 
                                    wire:model="files.{{ $field->id }}" 
                                    accept="image/*"
                                    class="block w-full text-xs text-on-surface-variant border border-outline rounded-lg cursor-pointer bg-surface-container-lowest focus:outline-none file:mr-4 file:py-2.5 file:px-4 file:rounded-l-lg file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary-container transition file:cursor-pointer"
                                >
                                <p class="text-[10px] text-on-surface-variant">Format gambar (JPEG, PNG, JPG), ukuran maksimal 2MB.</p>
                                
                                {{-- Image Preview --}}
                                @if(isset($files[$field->id]) && !$errors->has("files.{$field->id}"))
                                    <div class="mt-2 relative w-28 h-28 rounded-lg overflow-hidden border border-outline-variant bg-surface-container-low shadow-sm">
                                        <img src="{{ $files[$field->id]->temporaryUrl() }}" class="w-full h-full object-cover">
                                    </div>
                                @endif

                                {{-- Uploading State --}}
                                <div wire:loading wire:target="files.{{ $field->id }}" class="text-[10px] text-primary font-bold animate-pulse">
                                    Mengunggah gambar...
                                </div>
                            </div>
                        @endif

                        {{-- Validation error --}}
                        @error("answers.{$field->id}")
                            <span class="block text-[11px] text-error font-semibold mt-1">{{ $message }}</span>
                        @enderror
                        @error("files.{$field->id}")
                            <span class="block text-[11px] text-error font-semibold mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                @endforeach
            </div>

            <hr class="border-outline-variant">

            {{-- Submit button --}}
            <div class="flex gap-4 items-center justify-end">
                <a href="{{ route('mahasiswa.event-detail', $event->slug) }}" wire:navigate class="px-6 py-2.5 border border-outline-variant text-on-surface-variant hover:bg-surface-container hover:text-primary rounded-xl text-xs font-bold transition">
                    Batalkan
                </a>
                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    class="px-6 py-2.5 bg-primary hover:bg-primary-container disabled:bg-outline-variant text-white font-bold rounded-xl text-xs transition duration-200 shadow-sm hover:shadow-md inline-flex items-center gap-2"
                >
                    <span wire:loading.remove wire:target="submit">Kirim Pendaftaran</span>
                    <span wire:loading wire:target="submit" class="inline-flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
