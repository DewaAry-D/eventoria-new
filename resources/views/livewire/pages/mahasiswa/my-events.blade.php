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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

new #[Layout('layouts.mahasiswa')] class extends Component {
    use WithFileUploads;

    public ?int $editingRegistrationId = null;
    public ?EventRegistration $editingRegistration = null;
    public array $answers = [];
    public array $files = [];

    public function with()
    {
        $user = Auth::user();
        $registrations = collect();

        if ($user && $user->mahasiswa) {
            $registrations = EventRegistration::with([
                'event' => function($q) {
                    $q->withCount('registrations');
                },
                'event.kategori',
                'event.timeLines',
                'responses.field'
            ])
                ->where('mahasiswa_id', $user->mahasiswa->id)
                ->latest()
                ->get();
        }

        // Add helper properties to each registration for the view
        $registrations->each(function($reg) {
            $pendaftaran = $reg->event->timeLines->filter(function($t) {
                $name = strtolower($t->nama_timeline);
                return str_contains($name, 'daftar') || str_contains($name, 'registrasi');
            })->first();

            $reg->is_closed = $pendaftaran && $pendaftaran->tanggal_selesai && $pendaftaran->tanggal_selesai->isPast();
            $status = $reg->status_pendaftaran->value ?? (string)$reg->status_pendaftaran;
            $reg->can_edit = $status === 'rejected' && !$reg->is_closed;
            $reg->pendaftaran_timeline = $pendaftaran;
        });

        return [
            'registrations' => $registrations,
        ];
    }

    public function editForm($registrationId)
    {
        $registration = EventRegistration::with(['event.formFields', 'responses'])->find($registrationId);
        if (!$registration) return;

        // Security check: belongs to current student
        $user = Auth::user();
        if (!$user || !$user->mahasiswa || $registration->mahasiswa_id !== $user->mahasiswa->id) {
            session()->flash('error', 'Akses ditolak.');
            return;
        }

        $status = $registration->status_pendaftaran->value ?? (string)$registration->status_pendaftaran;
        if ($status !== 'rejected') {
            session()->flash('error', 'Hanya pendaftaran berstatus ditolak (rejected) yang dapat diubah.');
            return;
        }

        // Deadline check
        $pendaftaran = $registration->event->timeLines->filter(function($t) {
            $name = strtolower($t->nama_timeline);
            return str_contains($name, 'daftar') || str_contains($name, 'registrasi');
        })->first();

        if ($pendaftaran && $pendaftaran->tanggal_selesai && $pendaftaran->tanggal_selesai->isPast()) {
            session()->flash('error', 'Masa pendaftaran event ini telah berakhir.');
            return;
        }

        $this->editingRegistrationId = $registrationId;
        $this->editingRegistration = $registration;
        $this->answers = [];
        $this->files = [];

        // Pre-populate answers
        foreach ($registration->event->formFields as $field) {
            $response = $registration->responses->firstWhere('field_id', $field->id);
            $this->answers[$field->id] = $response ? $response->jawaban : '';
        }
    }

    public function closeModal()
    {
        $this->editingRegistrationId = null;
        $this->editingRegistration = null;
        $this->answers = [];
        $this->files = [];
        $this->resetErrorBag();
    }

    public function saveAnswers()
    {
        if (!$this->editingRegistration) return;

        $user = Auth::user();
        if (!$user || !$user->mahasiswa || $this->editingRegistration->mahasiswa_id !== $user->mahasiswa->id) {
            session()->flash('error', 'Akses ditolak.');
            return;
        }

        $status = $this->editingRegistration->status_pendaftaran->value ?? (string)$this->editingRegistration->status_pendaftaran;
        if ($status !== 'rejected') {
            session()->flash('error', 'Hanya pendaftaran berstatus ditolak (rejected) yang dapat diubah.');
            return;
        }

        // Dynamic validation
        $rules = [];
        $messages = [];

        foreach ($this->editingRegistration->event->formFields as $field) {
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
                if (in_array($tipe, ['file_pdf', 'file_image'])) {
                    // Check if they already have an existing file response
                    $existingResponse = $this->editingRegistration->responses->firstWhere('field_id', $field->id);
                    if (!$existingResponse || !$existingResponse->jawaban) {
                        $fieldRules[] = 'required';
                    } else {
                        $fieldRules[] = 'nullable';
                    }
                } else {
                    $fieldRules[] = 'required';
                }
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($tipe === 'email') {
                $fieldRules[] = 'email';
            } elseif ($tipe === 'number') {
                $fieldRules[] = 'numeric';
            } elseif ($tipe === 'url') {
                $fieldRules[] = 'url';
            } elseif ($tipe === 'file_pdf') {
                $fieldRules[] = 'file';
                $fieldRules[] = 'mimes:pdf';
                $fieldRules[] = 'max:2048';
            } elseif ($tipe === 'file_image') {
                $fieldRules[] = 'image';
                $fieldRules[] = 'max:2048';
            }

            if (!empty($fieldRules)) {
                $rules[$ruleKey] = implode('|', $fieldRules);
            }

            $messages["{$ruleKey}.required"] = "Kolom '{$fieldName}' wajib diisi.";
            $messages["{$ruleKey}.email"] = "Format email pada '{$fieldName}' tidak valid.";
            $messages["{$ruleKey}.numeric"] = "Kolom '{$fieldName}' harus berupa angka.";
            $messages["{$ruleKey}.url"] = "Format URL pada '{$fieldName}' tidak valid.";
            $messages["{$ruleKey}.mimes"] = "Berkas '{$fieldName}' harus berupa PDF.";
            $messages["{$ruleKey}.image"] = "Berkas '{$fieldName}' harus berupa gambar.";
            $messages["{$ruleKey}.max"] = "Ukuran berkas '{$fieldName}' maksimal 2MB.";
        }

        $this->validate($rules, $messages);

        try {
            DB::transaction(function() {
                foreach ($this->editingRegistration->event->formFields as $field) {
                    $tipe = $field->tipe_field->value ?? $field->tipe_field;
                    
                    $response = EventFormResponse::firstOrNew([
                        'registration_id' => $this->editingRegistration->id,
                        'field_id' => $field->id,
                    ]);

                    if (in_array($tipe, ['file_pdf', 'file_image'])) {
                        if (isset($this->files[$field->id])) {
                            // Delete old file if exists
                            if ($response->jawaban && Storage::disk('public')->exists($response->jawaban)) {
                                Storage::disk('public')->delete($response->jawaban);
                            }
                            // Store new file
                            $path = $this->files[$field->id]->store('form-responses', 'public');
                            $response->jawaban = $path;
                        }
                    } else {
                        $response->jawaban = $this->answers[$field->id] ?? null;
                    }

                    $response->save();
                }

                // Reset rejected status to pending on edit
                if ($this->editingRegistration->status_pendaftaran === RegistrationStatus::REJECTED) {
                    $this->editingRegistration->status_pendaftaran = RegistrationStatus::PENDING;
                    $this->editingRegistration->catatan_penolakan = null;
                    $this->editingRegistration->save();
                }
            });

            session()->flash('success', 'Jawaban formulir pendaftaran Anda berhasil diperbarui!');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui pendaftaran: ' . $e->getMessage());
        }
    }
}; ?>

<div class="space-y-8" x-data="{ openModal: @entangle('editingRegistrationId') }">
    
    {{-- Header --}}
    <section class="pb-3 border-b border-outline-variant flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-primary tracking-tight">Daftar Event Saya</h1>
            <p class="text-sm text-on-surface-variant mt-1">Pantau status pendaftaran dan edit jawaban formulir event Anda.</p>
        </div>
        <a href="{{ route('mahasiswa.dashboard') }}" wire:navigate class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-on-primary hover:bg-primary-container font-bold rounded-xl text-xs transition duration-150 shadow-sm hover:shadow-md">
            Eksplorasi Event Lainnya
        </a>
    </section>

    {{-- Alert Messages --}}
    @if(session()->has('success'))
        <div class="p-4 bg-success bg-opacity-10 border border-success border-opacity-20 text-success text-sm font-semibold rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="p-4 bg-error bg-opacity-10 border border-error border-opacity-20 text-error text-sm font-semibold rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- Main Table Container --}}
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-on-surface">
                <thead class="text-xs text-on-surface-variant uppercase font-semibold bg-surface-container-low border-b border-outline-variant tracking-wider">
                    <tr>
                        <th scope="col" class="px-6 py-4">NAMA EVENT</th>
                        <th scope="col" class="px-6 py-4 text-center">STATUS</th>
                        <th scope="col" class="px-6 py-4 text-center">TINGKAT</th>
                        <th scope="col" class="px-6 py-4 text-center">PENDAFTAR / KUOTA</th>
                        <th scope="col" class="px-6 py-4 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody x-data="{ activeReasonId: null }" class="divide-y divide-outline-variant">
                    @forelse($registrations as $reg)
                        @php
                            $status = $reg->status_pendaftaran->value ?? (string)$reg->status_pendaftaran;
                            $timeline = $reg->pendaftaran_timeline;
                        @endphp
                        <tr class="hover:bg-surface-container-low transition duration-150">
                            <!-- NAMA EVENT -->
                            <td class="px-6 py-5">
                                <div class="font-semibold text-on-surface text-sm leading-snug">{{ $reg->event->nama_event }}</div>
                                <div class="text-xs text-on-surface-variant mt-1.5 flex items-center gap-2">
                                    <span class="bg-surface-container text-on-surface-variant px-2 py-0.5 rounded text-[10.5px] font-medium">
                                        {{ $reg->event->kategori->nama_kategori ?? 'Umum' }}
                                    </span>
                                    <span class="text-outline-variant">•</span>
                                    <span class="font-normal text-[11px] text-on-surface-variant">{{ $reg->event->nama_lokasi ?? 'Daring' }}</span>
                                    <span class="text-outline-variant">•</span>
                                    <span class="font-normal text-[11px] text-on-surface-variant">Terdaftar: {{ $reg->waktu_daftar?->format('d M Y') }}</span>
                                </div>
                            </td>
                            
                            <!-- STATUS -->
                            <td class="px-6 py-5 text-center">
                                @if($status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 bg-warning bg-opacity-10 text-warning text-xs font-semibold px-2.5 py-0.5 rounded-full border border-warning border-opacity-20 uppercase tracking-wider">
                                        Pending
                                    </span>
                                @elseif($status === 'approved')
                                    <span class="inline-flex items-center gap-1.5 bg-success bg-opacity-10 text-success text-xs font-semibold px-2.5 py-0.5 rounded-full border border-success border-opacity-20 uppercase tracking-wider">
                                        Terdaftar
                                    </span>
                                @elseif($status === 'rejected')
                                    <span class="inline-flex items-center gap-1.5 bg-error bg-opacity-10 text-error text-xs font-semibold px-2.5 py-0.5 rounded-full border border-error border-opacity-20 uppercase tracking-wider">
                                        Ditolak
                                    </span>
                                @elseif($status === 'completed')
                                    <span class="inline-flex items-center gap-1.5 bg-primary-fixed text-primary text-xs font-semibold px-2.5 py-0.5 rounded-full border border-primary-fixed uppercase tracking-wider">
                                        Selesai
                                    </span>
                                @endif
                            </td>
                            
                            <!-- TINGKAT -->
                            <td class="px-6 py-5 text-center">
                                <span class="uppercase text-xs font-medium tracking-wider text-on-surface-variant">{{ $reg->event->tingkat_event->value ?? $reg->event->tingkat_event }}</span>
                            </td>
                            
                            <!-- PENDAFTAR / KUOTA -->
                            <td class="px-6 py-5 text-center text-sm text-on-surface">
                                <span class="font-bold text-on-surface">{{ $reg->event->registrations_count }}</span>
                                <span class="text-on-surface-variant font-medium">/ {{ $reg->event->kuota ?? '∞' }}</span>
                            </td>
                            
                            <!-- AKSI -->
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- 1. Catatan Penolakan Icon (Chat bubble/Warning) - HANYA ditampilkan jika status reject --}}
                                    @if($status === 'rejected' && $reg->catatan_penolakan)
                                        <button @click="activeReasonId = (activeReasonId === {{ $reg->id }} ? null : {{ $reg->id }})" title="Lihat Catatan Penolakan" class="p-1.5 text-error hover:text-red-700 transition duration-150">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.25" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- 2. Edit Response Icon - Ditampilkan di semua status, abu-abu & disabled kecuali rejected --}}
                                    @if($reg->can_edit)
                                        <button wire:click="editForm({{ $reg->id }})" title="Edit Jawaban" class="p-1.5 text-on-surface-variant hover:text-primary transition duration-150">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.25" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                            </svg>
                                        </button>
                                    @else
                                        <button disabled title="Jawaban tidak dapat diedit" class="p-1.5 text-outline-variant opacity-70 cursor-not-allowed">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.25" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    {{-- 3. Detail Event Icon (Eye) --}}
                                    <a href="{{ route('mahasiswa.event-detail', $reg->event->slug) }}" wire:navigate title="Detail Event" class="p-1.5 text-on-surface-variant hover:text-primary transition duration-150">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.25" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        
                        {{-- Expandable Rejection Note Row --}}
                        @if($status === 'rejected' && $reg->catatan_penolakan)
                            <tr x-show="activeReasonId === {{ $reg->id }}" x-cloak class="bg-error bg-opacity-[0.01]">
                                <td colspan="5" class="px-6 py-4 border-b border-outline-variant">
                                    <div class="flex flex-col items-center text-center p-4 max-w-md mx-auto bg-error bg-opacity-5 rounded-2xl border border-error border-opacity-10 shadow-sm">
                                        <svg class="w-6 h-6 text-error mb-2.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <div class="text-xs">
                                            <span class="font-bold text-error uppercase tracking-wider block mb-1">Catatan Penolakan</span>
                                            <p class="font-medium text-on-surface-variant leading-relaxed italic">"{{ $reg->catatan_penolakan }}"</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-on-surface-variant bg-surface-container-lowest">
                                <svg class="mx-auto w-14 h-14 text-outline mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="text-lg font-bold text-primary">Belum Ada Event Terdaftar</h3>
                                <p class="text-sm text-on-surface-variant mt-1.5 max-w-md mx-auto">Anda belum mendaftar di event apapun. Temukan berbagai event menarik di dashboard kampus!</p>
                                <a href="{{ route('mahasiswa.dashboard') }}" wire:navigate class="mt-5 inline-flex px-5 py-2.5 bg-primary hover:bg-primary-container text-white font-bold rounded-xl text-xs transition duration-150 shadow-sm">
                                    Cari Event Sekarang
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Edit Responses Modal --}}
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 transition duration-300" x-transition.opacity style="display: none;">
        <div @click.away="$wire.closeModal()" class="bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-xl max-w-2xl w-full flex flex-col max-h-[90vh] overflow-hidden transform transition duration-300" x-transition.scale>
            @if($editingRegistration)
                {{-- Modal Header --}}
                <div class="bg-primary-container p-6 text-white flex justify-between items-center relative overflow-hidden">
                    <div class="absolute -right-16 -top-16 w-36 h-36 bg-primary rounded-full opacity-20 z-0"></div>
                    <div class="relative z-10 space-y-1">
                        <h3 class="text-lg font-extrabold tracking-tight">Perbarui Jawaban Formulir</h3>
                        <p class="text-xs text-on-primary-container">{{ $editingRegistration->event->nama_event }}</p>
                    </div>
                    <button @click="$wire.closeModal()" class="text-on-primary-container hover:text-white transition focus:outline-none relative z-10">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                {{-- Modal Form Body --}}
                <div class="p-6 overflow-y-auto space-y-6 flex-1">
                    @if(session()->has('error'))
                        <div class="p-4 bg-error bg-opacity-10 border border-error border-opacity-20 text-error text-xs font-semibold rounded-xl flex items-center gap-3">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="space-y-5">
                        @foreach($editingRegistration->event->formFields as $field)
                            @php
                                $tipe = $field->tipe_field->value ?? $field->tipe_field;
                                $isRequired = (bool) $field->is_required;
                                $options = $field->meta_options;
                                $existingResponse = $editingRegistration->responses->firstWhere('field_id', $field->id);
                                $oldFileUrl = $existingResponse && in_array($tipe, ['file_pdf', 'file_image']) && $existingResponse->jawaban ? Storage::url($existingResponse->jawaban) : null;
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
                                        class="w-full px-4 py-2 text-sm bg-surface-container-lowest border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-on-surface transition shadow-sm placeholder-outline-variant"
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
                                        class="w-full px-4 py-2 text-sm bg-surface-container-lowest border border-outline rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-on-surface transition shadow-sm"
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
                                                        name="modal_radio_{{ $field->id }}" 
                                                        value="{{ $opt }}" 
                                                        wire:model="answers.{{ $field->id }}" 
                                                        class="w-4 h-4 text-primary border-outline focus:ring-primary cursor-pointer"
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
                                        {{-- Existing file link --}}
                                        @if($oldFileUrl)
                                            <div class="flex items-center gap-2 bg-surface-container rounded-lg p-2.5 border border-outline-variant text-xs w-fit">
                                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                <a href="{{ $oldFileUrl }}" target="_blank" class="text-primary hover:underline font-bold">Lihat Berkas PDF Terunggah</a>
                                            </div>
                                        @endif

                                        <input 
                                            type="file" 
                                            wire:model="files.{{ $field->id }}" 
                                            accept=".pdf"
                                            class="block w-full text-xs text-on-surface-variant border border-outline rounded-lg cursor-pointer bg-surface-container-lowest focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary-container transition file:cursor-pointer"
                                        >
                                        <p class="text-[10px] text-on-surface-variant">Unggah berkas baru untuk mengganti berkas saat ini (format PDF, maks. 2MB).</p>
                                        
                                        <div wire:loading wire:target="files.{{ $field->id }}" class="text-[10px] text-primary font-bold animate-pulse">
                                            Mengunggah berkas...
                                        </div>
                                    </div>
                                @endif

                                {{-- File Image --}}
                                @if($tipe === 'file_image')
                                    <div class="flex flex-col gap-2">
                                        {{-- Existing image preview --}}
                                        @if($oldFileUrl && !isset($files[$field->id]))
                                            <div class="relative w-28 h-28 rounded-lg overflow-hidden border border-outline-variant bg-surface-container-low shadow-sm">
                                                <img src="{{ $oldFileUrl }}" class="w-full h-full object-cover">
                                            </div>
                                        @endif

                                        <input 
                                            type="file" 
                                            wire:model="files.{{ $field->id }}" 
                                            accept="image/*"
                                            class="block w-full text-xs text-on-surface-variant border border-outline rounded-lg cursor-pointer bg-surface-container-lowest focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-primary-container transition file:cursor-pointer"
                                        >
                                        
                                        {{-- New Image Preview --}}
                                        @if(isset($files[$field->id]) && !$errors->has("files.{$field->id}"))
                                            <div class="mt-2 relative w-28 h-28 rounded-lg overflow-hidden border border-outline-variant bg-surface-container-low shadow-sm">
                                                <img src="{{ $files[$field->id]->temporaryUrl() }}" class="w-full h-full object-cover">
                                            </div>
                                        @endif

                                        <p class="text-[10px] text-on-surface-variant">Unggah gambar baru untuk mengganti gambar saat ini (maks. 2MB).</p>

                                        <div wire:loading wire:target="files.{{ $field->id }}" class="text-[10px] text-primary font-bold animate-pulse">
                                            Mengunggah gambar...
                                        </div>
                                    </div>
                                @endif

                                @error("answers.{$field->id}")
                                    <span class="block text-[11px] text-error font-semibold mt-1">{{ $message }}</span>
                                @enderror
                                @error("files.{$field->id}")
                                    <span class="block text-[11px] text-error font-semibold mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="bg-surface-container p-4 flex justify-end gap-3.5 border-t border-outline-variant">
                    <button type="button" @click="$wire.closeModal()" class="px-5 py-2.5 border border-outline-variant text-on-surface-variant hover:bg-surface-container-high rounded-xl text-xs font-bold transition">
                        Batal
                    </button>
                    <button type="button" wire:click="saveAnswers()" wire:loading.attr="disabled" class="px-5 py-2.5 bg-primary hover:bg-primary-container disabled:bg-outline-variant text-white font-bold rounded-xl text-xs transition duration-200 shadow-sm inline-flex items-center gap-2">
                        <span wire:loading.remove wire:target="saveAnswers">Simpan Perubahan</span>
                        <span wire:loading wire:target="saveAnswers" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>
                </div>
            @endif
        </div>
    </div>

</div>
