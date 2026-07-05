<?php

use App\Models\EventRegistration;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.organisasi')] class extends Component
{
    public EventRegistration $peserta;
    public Event $event;

    public function mount(Event $event, EventRegistration $peserta )
    {
        $organisasiId = Auth::user()->load('organisasi')->organisasi->id;
        if ($event->organisasi_id !== $organisasiId) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak untuk melihat data ini.');
        }

        $this->peserta = $peserta->load(['event', 'mahasiswa', 'responses']);
        $this->event = $event;
    }
}; ?>

<div>
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('organisasi.events') }}" wire:navigate class="p-2 transition border rounded-lg text-on-surface-variant bg-surface-container-lowest border-outline-variant hover:bg-surface-container">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-on-surface">Data Jawaban Form</h1>
                <p class="mt-1 text-sm text-on-surface-variant">
                    Peserta: <span class="font-semibold">{{ $peserta->mahasiswa->name ?? $peserta->user->name ?? 'Anonim' }}</span> | 
                    Event: <span class="font-semibold text-primary">{{ $event->nama_event }}</span>
                </p>
            </div>
        </div>
    </div>

    <div class="p-6 border rounded-xl bg-surface-container-lowest border-outline-variant shadow-card">
        <h3 class="pb-3 mb-6 text-lg font-bold border-b text-on-surface border-outline-variant">
            Rincian Jawaban
        </h3>
        
        <div class="space-y-6">
            @forelse($peserta->responses as $jawab)
                <div class="p-4 border rounded-lg bg-surface-container border-outline-variant/30">
                    <p class="mb-2 text-xs font-bold tracking-wider uppercase text-on-surface-variant">
                        {{ $jawab->field->nama_field ?? 'Pertanyaan Tidak Ditemukan' }}
                    </p>
                    
                    @if($jawab->field && in_array($jawab->field->tipe_field, ['file_pdf', 'file_image']))
                        <a href="{{ Storage::url($jawab->jawaban) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium transition rounded-lg bg-primary/10 text-primary hover:bg-primary/20">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Lihat Berkas Lampiran
                        </a>
                        
                    @elseif($jawab->field && $jawab->field->tipe_field === 'url')
                        <a href="{{ $jawab->jawaban }}" target="_blank" class="font-medium break-all text-primary hover:underline">
                            {{ $jawab->jawaban }}
                        </a>
                        
                    @else
                        <p class="font-medium whitespace-pre-wrap text-on-surface">{{ $jawab->jawaban ?? '-' }}</p>
                    @endif
                </div>
            @empty
                <div class="py-8 text-center border border-dashed rounded-lg text-on-surface-variant bg-surface-container border-outline-variant">
                    <svg class="w-12 h-12 mx-auto mb-3 text-on-surface-variant/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p>Peserta ini tidak mengirimkan jawaban form tambahan.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>