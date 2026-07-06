<?php

use App\Models\EventRegistration;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Enums\RegistrationStatus;

new #[Layout('layouts.organisasi')] class extends Component
{
    public EventRegistration $peserta;
    public Event $event;

    public $showRejectModal = false;
    public $catatan_penolakan = '';

    public function mount(Event $event, EventRegistration $peserta)
    {
        $organisasiId = Auth::user()->load('organisasi')->organisasi->id;
        if ($event->organisasi_id !== $organisasiId) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak untuk melihat data ini.');
        }

        $this->peserta = $peserta->load(['event', 'mahasiswa', 'responses.field']);
        $this->event = $event;
    }

    // Menyetujui Pendaftaran
    public function terima()
    {
        if ($this->peserta->status_pendaftaran === RegistrationStatus::PENDING) {
            $this->peserta->update(['status_pendaftaran' => RegistrationStatus::APPROVED]);
            session()->flash('success', 'Pendaftar berhasil disetujui.');
        }
    }

    // Membuka Modal Penolakan
    public function konfirmasiTolak()
    {
        $this->catatan_penolakan = '';
        $this->showRejectModal = true;
    }

    // Proses Penolakan Pendaftaran
    public function tolak()
    {
        $this->validate([
            'catatan_penolakan' => 'required|string|min:5'
        ], [
            'catatan_penolakan.required' => 'Wajib memberikan alasan penolakan.'
        ]);

        if ($this->peserta->status_pendaftaran === RegistrationStatus::PENDING) {
            $this->peserta->update([
                'status_pendaftaran' => RegistrationStatus::REJECTED,
                'catatan_penolakan' => $this->catatan_penolakan
            ]);

            $this->showRejectModal = false;
            session()->flash('success', 'Pendaftar ditolak beserta alasannya.');
        }
    }

    // Mengubah Status Menjadi Selesai (Completed)
    public function selesaikan()
    {
        if ($this->peserta->status_pendaftaran === RegistrationStatus::APPROVED) {
            $this->peserta->update(['status_pendaftaran' => RegistrationStatus::COMPLETED]);
            session()->flash('success', 'Status pendaftar diubah menjadi Selesai (Completed).');
        }
    }
}; ?>

<div>
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('organisasi.events.pendaftar', $event->id) }}" wire:navigate class="p-2 transition border rounded-lg text-on-surface-variant bg-surface-container-lowest border-outline-variant hover:bg-surface-container">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-on-surface">Data Jawaban Form</h1>
                <p class="mt-1 text-sm text-on-surface-variant">
                    Peserta: <span class="font-semibold">{{ $peserta->mahasiswa->nama ?? 'Anonim' }}</span> | 
                    Event: <span class="font-semibold text-primary">{{ $event->nama_event }}</span>
                </p>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-success rounded-lg bg-success/10 flex items-center shadow-sm">
            <svg class="w-5 h-5 inline mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="p-6 border rounded-xl bg-surface-container-lowest border-outline-variant shadow-card">
                <h3 class="pb-3 mb-6 text-lg font-bold border-b text-on-surface border-outline-variant">
                    Rincian Jawaban Kustom
                </h3>
                
                <div class="space-y-6">
                    @forelse($peserta->responses as $jawab)
                        <div class="p-4 border rounded-lg bg-surface-container border-outline-variant/30">
                            <p class="mb-2 text-xs font-bold tracking-wider uppercase text-on-surface-variant">
                                {{ $jawab->field->nama_field ?? 'Pertanyaan Tidak Ditemukan' }}
                            </p>
                            
                            @if($jawab->field && in_array($jawab->field->tipe_field->value ?? $jawab->field->tipe_field, ['file_pdf', 'file_image']))
                                <a href="{{ Storage::url($jawab->jawaban) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium transition rounded-lg bg-primary/10 text-primary hover:bg-primary/20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Lihat Berkas Lampiran
                                </a>
                            @elseif($jawab->field && ($jawab->field->tipe_field->value ?? $jawab->field->tipe_field) === 'url')
                                <a href="{{ $jawab->jawaban }}" target="_blank" class="font-medium break-all text-primary hover:underline">
                                    {{ $jawab->jawaban }}
                                </a>
                            @else
                                <p class="font-medium whitespace-pre-wrap text-on-surface">{{ $jawab->jawaban ?? '-' }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="py-8 text-center border border-dashed rounded-lg text-on-surface-variant bg-surface-container border-outline-variant">
                            <p class="text-sm font-medium select-none">Peserta ini tidak mengirimkan jawaban form tambahan.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-5 border border-outline-variant/60 rounded-xl bg-surface-container-low flex flex-wrap items-center justify-between gap-4">
                <div class="text-left">
                    <span class="text-xs font-bold text-secondary/70 uppercase tracking-wider block">Status Otoritas Pendaftaran</span>
                    <p class="text-sm font-semibold mt-1">
                        Status Saat Ini: 
                        @if($peserta->status_pendaftaran === RegistrationStatus::PENDING)
                            <span class="text-warning font-extrabold uppercase text-xs pl-1">Pending</span>
                        @elseif($peserta->status_pendaftaran === RegistrationStatus::APPROVED)
                            <span class="text-success font-extrabold uppercase text-xs pl-1">Disetujui</span>
                        @elseif($peserta->status_pendaftaran === RegistrationStatus::COMPLETED)
                            <span class="text-primary font-extrabold uppercase text-xs pl-1">Completed</span>
                        @elseif($peserta->status_pendaftaran === RegistrationStatus::REJECTED)
                            <span class="text-error font-extrabold uppercase text-xs pl-1">Ditolak</span>
                        @endif
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    @if($peserta->status_pendaftaran === RegistrationStatus::PENDING)
                        <button type="button" wire:click="konfirmasiTolak" class="px-4 py-2 text-xs font-bold border border-error/30 text-error bg-white hover:bg-error/5 rounded-lg flex items-center gap-1 transition shadow-2xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Tolak Pendaftaran
                        </button>
                        <button type="button" wire:click="terima" class="px-5 py-2 text-xs font-bold text-white bg-success hover:bg-success/90 rounded-lg flex items-center gap-1 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Setujui Peserta
                        </button>
                    @elseif($peserta->status_pendaftaran === RegistrationStatus::APPROVED)
                        <button type="button" wire:click="selesaikan" class="px-5 py-2 text-xs font-bold text-white bg-primary hover:bg-primary/90 rounded-lg flex items-center gap-1 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                            Tandai Acara Selesai (Lulus)
                        </button>
                    @else
                        <span class="text-xs font-bold text-secondary/40 italic">Otoritas Pendaftaran Telah Dikunci</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="p-6 border rounded-xl bg-surface-container-lowest border-outline-variant shadow-card">
                <h3 class="pb-3 mb-4 text-base font-bold border-b text-indigo-950 border-outline-variant">
                    Identitas Resmi Berkas
                </h3>
                <div class="space-y-4">
                    <div class="bg-indigo-50/60 border border-indigo-100 p-4 rounded-xl">
                        <span class="text-[10px] font-bold text-indigo-700 bg-white border border-indigo-200 px-2 py-0.5 rounded uppercase tracking-wider block w-max mb-1.5">
                            Wajib Sistem
                        </span>
                        <p class="text-xs font-semibold text-secondary">Nama Cetak Sertifikat:</p>
                        <p class="text-sm font-bold text-indigo-900 mt-1 break-words">
                            {{ $peserta->nama_cetak_sertifikat ?? $peserta->mahasiswa->nama ?? 'Belum Diisi' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-secondary">NIM Asli Mahasiswa:</p>
                        <p class="text-sm font-bold text-on-surface mt-0.5">{{ $peserta->mahasiswa->nim ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-secondary">Nama Profil Master:</p>
                        <p class="text-sm font-semibold text-on-surface mt-0.5">{{ $peserta->mahasiswa->nama ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold text-secondary">Waktu Pendaftaran:</p>
                        <p class="text-xs font-medium text-on-surface-variant mt-0.5">
                            {{ \Carbon\Carbon::parse($peserta->waktu_daftar)->translatedFormat('d F Y, H:i') }} WITA
                        </p>
                    </div>

                    @if($peserta->status_pendaftaran === RegistrationStatus::REJECTED && $peserta->catatan_penolakan)
                        <div class="bg-error/5 border border-error/20 p-3 rounded-lg mt-2">
                            <p class="text-xs font-bold text-error uppercase tracking-wider">Alasan Penolakan:</p>
                            <p class="text-xs text-on-error-container font-medium mt-1">{{ $peserta->catatan_penolakan }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    @if($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/60 backdrop-blur-sm px-4">
            <div class="bg-surface-container-lowest rounded-2xl shadow-2xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold text-on-surface mb-2">Tolak Peserta</h3>
                <p class="text-sm text-on-surface-variant mb-4">Alasan penolakan ini akan dicatat dan dapat dilihat oleh peserta.</p>

                <textarea wire:model="catatan_penolakan" rows="3" class="w-full border-outline-variant rounded-lg text-sm focus:ring-error focus:border-error" placeholder="Tulis alasan penolakan... (Maksimal kuota, berkas tidak valid, dll)"></textarea>
                @error('catatan_penolakan')
                    <span class="text-xs text-error font-semibold mt-1 block">{{ $message }}</span>
                @enderror

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showRejectModal', false)" class="px-4 py-2 text-sm font-medium text-on-surface bg-surface-container rounded-lg hover:bg-surface-container-high">Batal</button>
                    <button wire:click="tolak" class="px-4 py-2 text-sm font-medium text-on-error bg-error rounded-lg hover:bg-error/90">Tolak Pendaftaran</button>
                </div>
            </div>
        </div>
    @endif
</div>