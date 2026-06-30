<?php

use App\Models\Event;
use App\Models\Kategori;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.organisasi')] class extends Component
{
    use WithFileUploads;

    public Event $event;

    public $nama_event;
    public $kategori_id;
    public $penyelenggara;
    public $tingkat_event;
    public $deskripsi;
    public $nama_lokasi;
    public $lokasi_url;
    public $kuota;
    public $narasumber;

    public $flyer;
    public $flyer_lama;

    public array $timelines = [];
    public array $biayas = [];
    public array $narahubungs = [];
    public array $tujuan_transfers = [];

    public function mount(Event $event)
    {
        $organisasiId = Auth::user()->load('organisasi')->organisasi->id;

        if ($event->organisasi_id !== $organisasiId) {
            abort(403, 'Akses ditolak.');
        }

        if (!in_array($event->status->value, ['draft', 'revision'])) {
            session()->flash('error', 'Event ini sudah tidak bisa diedit karena sedang diproses atau sudah tayang.');
            $this->redirect(route('organisasi.events', absolute: false), navigate: true);
            return;
        }

        $this->event = $event;

        $this->nama_event = $event->nama_event;
        $this->kategori_id = $event->kategori_id;
        $this->penyelenggara = $event->penyelenggara;
        $this->tingkat_event = $event->tingkat_event;
        $this->deskripsi = $event->deskripsi;
        $this->nama_lokasi = $event->nama_lokasi;
        $this->lokasi_url = $event->lokasi_url;
        $this->kuota = $event->kuota;
        $this->narasumber = $event->narasumber;
        $this->flyer_lama = $event->flyer_url;

        foreach ($event->timeLines as $index => $tl) {
            $this->timelines[] = [
                'nama_timeline' => $tl->nama_timeline,
                'deskripsi_timeline' => $tl->deskripsi_timeline,
                'tanggal_mulai' => Carbon::parse($tl->tanggal_mulai)->format('Y-m-d\TH:i'),
                'tanggal_selesai' => Carbon::parse($tl->tanggal_selesai)->format('Y-m-d\TH:i'),
                'is_default' => $tl->nama_timeline === 'Pendaftaran' || $index === 0
            ];
        }

        foreach ($event->biayaEvents as $by) {
            $this->biayas[] = [
                'kategori' => $by->kategori,
                'biaya' => $by->biaya
            ];
        }

        foreach ($event->narahubung as $nh) {
            $this->narahubungs[] = [
                'nama' => $nh->nama,
                'nomor' => $nh->nomor
            ];
        }

        foreach ($event->tujuanTransfer as $tt) {
            $this->tujuan_transfers[] = [
                'nama_bank' => $tt->nama_bank,
                'no_rekening' => $tt->no_rekening,
                'atas_nama' => $tt->atas_nama
            ];
        }
    }

    public function tambahTimeline()
    {
        $this->timelines[] = [
            'nama_timeline' => '',
            'deskripsi_timeline' => '',
            'tanggal_mulai' => '',
            'tanggal_selesai' => '',
            'is_default' => false
        ];
    }

    public function hapusTimeline($index)
    {
        unset($this->timelines[$index]);
        $this->timelines = array_values($this->timelines);
    }

    public function tambahBiaya()
    {
        $this->biayas[] = [
            'kategori' => '',
            'biaya' => 0
        ];
    }

    public function hapusBiaya($index)
    {
        unset($this->biayas[$index]);
        $this->biayas = array_values($this->biayas);
    }

    public function tambahNarahubung()
    {
        $this->narahubungs[] = [
            'nama' => '',
            'nomor' => ''
        ];
    }

    public function hapusNarahubung($index)
    {
        unset($this->narahubungs[$index]);
        $this->narahubungs = array_values($this->narahubungs);
    }

    public function tambahTujuanTransfer()
    {
        $this->tujuan_transfers[] = [
            'nama_bank' => '',
            'no_rekening' => '',
            'atas_nama' => ''
        ];
    }

    public function hapusTujuanTransfer($index)
    {
        unset($this->tujuan_transfers[$index]);
        $this->tujuan_transfers = array_values($this->tujuan_transfers);
    }

    public function with(): array
    {
        return [
            'daftar_kategori' => Kategori::all(),
        ];
    }

    public function updateEvent()
    {
        $this->validate([
            'nama_event' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'penyelenggara' => 'required|string|max:255',
            'tingkat_event' => 'required|in:prodi,fakultas,universitas',
            'deskripsi' => 'required|string',
            'kuota' => 'required|integer|min:1',
            'flyer' => 'nullable|image|max:2048',

            'timelines.*.nama_timeline' => 'required|string|max:255',
            'timelines.*.tanggal_mulai' => 'required|date',
            'timelines.*.tanggal_selesai' => 'required|date|after:timelines.*.tanggal_mulai',
            'biayas.*.kategori' => 'required|string|max:50',
            'biayas.*.biaya' => 'required|numeric|min:0',

            'narahubungs.*.nama' => 'required|string|max:255',
            'narahubungs.*.nomor' => 'required|string|max:50',
            'tujuan_transfers.*.nama_bank' => 'required|string|max:25',
            'tujuan_transfers.*.no_rekening' => 'required|string|max:200',
            'tujuan_transfers.*.atas_nama' => 'required|string|max:255',
        ], [
            'timelines.*.nama_timeline.required' => 'Nama timeline harus diisi.',
            'timelines.*.tanggal_mulai.required' => 'Tanggal mulai harus diisi.',
            'timelines.*.tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'biayas.*.kategori.required' => 'Nama tiket/biaya wajib diisi.',
            'narahubungs.*.nama.required' => 'Nama narahubung wajib diisi.',
            'narahubungs.*.nomor.required' => 'Nomor narahubung wajib diisi.',
            'tujuan_transfers.*.nama_bank.required' => 'Nama bank wajib diisi.',
            'tujuan_transfers.*.no_rekening.required' => 'Nomor rekening wajib diisi.',
            'tujuan_transfers.*.atas_nama.required' => 'Atas nama rekening wajib diisi.',
        ]);

        DB::transaction(function () {
            $flyerPath = $this->flyer_lama;
            if ($this->flyer) {
                if ($this->flyer_lama) {
                    Storage::disk('public')->delete($this->flyer_lama);
                }
                $flyerPath = $this->flyer->store('flyers', 'public');
            }

            $this->event->update([
                'kategori_id' => $this->kategori_id,
                'nama_event' => $this->nama_event,
                'slug' => Str::slug($this->nama_event . '-' . Str::random(5)),
                'penyelenggara' => $this->penyelenggara,
                'deskripsi' => $this->deskripsi,
                'nama_lokasi' => $this->nama_lokasi,
                'lokasi_url' => $this->lokasi_url,
                'kuota' => $this->kuota,
                'narasumber' => $this->narasumber,
                'flyer_url' => $flyerPath,
                'tingkat_event' => $this->tingkat_event,
                'status' => 'draft',
            ]);

            $this->event->timeLines()->delete();
            foreach ($this->timelines as $tl) {
                $this->event->timeLines()->create([
                    'nama_timeline' => $tl['nama_timeline'],
                    'deskripsi_timeline' => $tl['deskripsi_timeline'] ?? 'Masa pendaftaran peserta',
                    'tanggal_mulai' => $tl['tanggal_mulai'],
                    'tanggal_selesai' => $tl['tanggal_selesai'],
                ]);
            }

            $this->event->biayaEvents()->delete();
            foreach ($this->biayas as $by) {
                $this->event->biayaEvents()->create([
                    'kategori' => $by['kategori'],
                    'biaya' => $by['biaya'],
                ]);
            }

            $this->event->narahubung()->delete();
            foreach ($this->narahubungs as $nh) {
                $this->event->narahubung()->create([
                    'nama' => $nh['nama'],
                    'nomor' => $nh['nomor'],
                ]);
            }

            $this->event->tujuanTransfer()->delete();
            foreach ($this->tujuan_transfers as $tt) {
                $this->event->tujuanTransfer()->create([
                    'nama_bank' => $tt['nama_bank'],
                    'no_rekening' => $tt['no_rekening'],
                    'atas_nama' => $tt['atas_nama'],
                ]);
            }
        });

        session()->flash('success', 'Perubahan detail event berhasil disimpan!');
        $this->redirect(route('organisasi.events', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('organisasi.events') }}" wire:navigate class="p-2 text-on-surface-variant bg-surface-container-lowest border border-outline-variant rounded-lg hover:bg-surface-container">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-2xl font-bold text-on-surface">Edit Detail Event</h1>
        </div>

        @if($event->status->value === 'revision' && $event->catatan_revisi)
            <div class="mt-4 p-4 bg-error-container border-l-4 border-error rounded-r-lg">
                <p class="text-sm font-bold text-on-error-container">Catatan Revisi dari DPM:</p>
                <p class="text-sm text-on-error-container italic mt-1">"{{ $event->catatan_revisi }}"</p>
            </div>
        @endif
    </div>

    <form wire:submit="updateEvent" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
                <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-card">
                    <h2 class="text-lg font-bold text-on-surface mb-4 border-b border-outline-variant pb-2">Informasi Utama</h2>

                    <div class="space-y-4">
                        <div>
                            <x-input-label value="Nama Event" required />
                            <x-text-input wire:model="nama_event" class="block w-full mt-1" type="text" />
                            <x-input-error :messages="$errors->get('nama_event')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label value="Kategori Event" required />
                                <select wire:model="kategori_id" class="block w-full mt-1 border-outline-variant rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($daftar_kategori as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('kategori_id')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label value="Tingkat Akses Event" required />
                                <select wire:model="tingkat_event" class="block w-full mt-1 border-outline-variant rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm">
                                    <option value="universitas">Tingkat Universitas</option>
                                    <option value="fakultas">Tingkat Fakultas</option>
                                    <option value="prodi">Tingkat Program Studi</option>
                                </select>
                                <x-input-error :messages="$errors->get('tingkat_event')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label value="Penyelenggara / Panitia" required />
                            <x-text-input wire:model="penyelenggara" class="block w-full mt-1" type="text" />
                            <x-input-error :messages="$errors->get('penyelenggara')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="Deskripsi Lengkap Event" required />
                            <textarea wire:model="deskripsi" rows="5" class="block w-full mt-1 border-outline-variant rounded-md shadow-sm focus:ring-primary focus:border-primary text-sm"></textarea>
                            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label value="Narasumber / Pembicara" />
                            <x-text-input wire:model="narasumber" class="block w-full mt-1" type="text" />
                        </div>
                    </div>
                </div>

                <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-card">
                    <h2 class="text-lg font-bold text-on-surface mb-4 border-b border-outline-variant pb-2">Lokasi & Tautan</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label value="Nama Lokasi / Gedung" />
                            <x-text-input wire:model="nama_lokasi" class="block w-full mt-1" type="text" />
                        </div>
                        <div>
                            <x-input-label value="Link Google Maps (Opsional)" />
                            <x-text-input wire:model="lokasi_url" class="block w-full mt-1" type="url" />
                        </div>
                    </div>
                </div>

                <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-card">
                    <div class="flex items-center justify-between mb-4 border-b border-outline-variant pb-2">
                        <h2 class="text-lg font-bold text-on-surface">Narahubung (Opsional)</h2>
                        <button type="button" wire:click="tambahNarahubung" class="text-xs font-bold text-primary hover:text-primary/80 bg-primary/10 px-2 py-1 rounded">
                            + Tambah Kontak
                        </button>
                    </div>

                    <div class="space-y-3">
                        @forelse($narahubungs as $index => $nh)
                            <div class="flex gap-2 items-start relative bg-surface-container p-3 border border-outline-variant/50 rounded-lg">
                                <div class="w-1/2">
                                    <x-input-label value="Nama Narahubung" required />
                                    <x-text-input wire:model="narahubungs.{{ $index }}.nama" class="block w-full mt-1 text-sm" type="text" placeholder="Cth: Budi" />
                                    <x-input-error :messages="$errors->get('narahubungs.'.$index.'.nama')" class="mt-1 text-xs" />
                                </div>
                                <div class="w-1/2">
                                    <x-input-label value="Nomor WhatsApp/HP" required />
                                    <x-text-input wire:model="narahubungs.{{ $index }}.nomor" class="block w-full mt-1 text-sm" type="text" placeholder="Cth: 08123456789" />
                                    <x-input-error :messages="$errors->get('narahubungs.'.$index.'.nomor')" class="mt-1 text-xs" />
                                </div>
                                <button type="button" wire:click="hapusNarahubung({{ $index }})" class="mt-7 text-error hover:text-error/70 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        @empty
                            <p class="text-sm text-on-surface-variant italic text-center py-2">Belum ada narahubung yang ditambahkan.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-card">
                    <div class="flex items-center justify-between mb-4 border-b border-outline-variant pb-2">
                        <h2 class="text-lg font-bold text-on-surface">Rekening Pembayaran (Opsional)</h2>
                        <button type="button" wire:click="tambahTujuanTransfer" class="text-xs font-bold text-primary hover:text-primary/80 bg-primary/10 px-2 py-1 rounded">
                            + Tambah Rekening
                        </button>
                    </div>

                    <div class="space-y-3">
                        @forelse($tujuan_transfers as $index => $tt)
                            <div class="grid grid-cols-12 gap-2 items-start relative bg-surface-container p-3 border border-outline-variant/50 rounded-lg">
                                <div class="col-span-3">
                                    <x-input-label value="Bank/E-Wallet" required />
                                    <x-text-input wire:model="tujuan_transfers.{{ $index }}.nama_bank" class="block w-full mt-1 text-sm" type="text" placeholder="BCA / DANA" />
                                    <x-input-error :messages="$errors->get('tujuan_transfers.'.$index.'.nama_bank')" class="mt-1 text-xs" />
                                </div>
                                <div class="col-span-4">
                                    <x-input-label value="No. Rekening" required />
                                    <x-text-input wire:model="tujuan_transfers.{{ $index }}.no_rekening" class="block w-full mt-1 text-sm" type="text" placeholder="12345678" />
                                    <x-input-error :messages="$errors->get('tujuan_transfers.'.$index.'.no_rekening')" class="mt-1 text-xs" />
                                </div>
                                <div class="col-span-4">
                                    <x-input-label value="Atas Nama" required />
                                    <x-text-input wire:model="tujuan_transfers.{{ $index }}.atas_nama" class="block w-full mt-1 text-sm" type="text" placeholder="Budi Santoso" />
                                    <x-input-error :messages="$errors->get('tujuan_transfers.'.$index.'.atas_nama')" class="mt-1 text-xs" />
                                </div>
                                <div class="col-span-1 text-right mt-6">
                                    <button type="button" wire:click="hapusTujuanTransfer({{ $index }})" class="text-error hover:text-error/70 p-1">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-on-surface-variant italic text-center py-2">Belum ada rekening pembayaran yang ditambahkan.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-card">
                    <div class="flex items-center justify-between mb-4 border-b border-outline-variant pb-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <h2 class="text-lg font-bold text-on-surface">Timeline Event</h2>
                        </div>
                        <button type="button" wire:click="tambahTimeline" class="text-xs font-bold text-primary hover:text-primary/80 bg-primary/10 px-2 py-1 rounded">
                            + Tambah Jadwal
                        </button>
                    </div>

                    <div class="space-y-6">
                        @foreach($timelines as $index => $tl)
                            <div class="relative p-4 border border-outline-variant/50 bg-surface-container rounded-lg shadow-inner">
                                @if(!isset($tl['is_default']) || !$tl['is_default'])
                                    <button type="button" wire:click="hapusTimeline({{ $index }})" class="absolute top-2 right-2 text-error hover:text-error/70">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                @endif

                                <div class="space-y-3">
                                    <div>
                                        <x-input-label value="Nama Jadwal" required />
                                        @if(isset($tl['is_default']) && $tl['is_default'])
                                            <x-text-input wire:model="timelines.{{ $index }}.nama_timeline" class="block w-full mt-1 text-sm bg-surface-container-high cursor-not-allowed" type="text" readonly />
                                        @else
                                            <x-text-input wire:model="timelines.{{ $index }}.nama_timeline" class="block w-full mt-1 text-sm" type="text" />
                                        @endif
                                        <x-input-error :messages="$errors->get('timelines.'.$index.'.nama_timeline')" class="mt-1 text-xs" />
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <x-input-label value="Mulai" required />
                                            <x-text-input wire:model="timelines.{{ $index }}.tanggal_mulai" class="block w-full mt-1 text-sm px-2" type="datetime-local" />
                                            <x-input-error :messages="$errors->get('timelines.'.$index.'.tanggal_mulai')" class="mt-1 text-xs" />
                                        </div>
                                        <div>
                                            <x-input-label value="Selesai" required />
                                            <x-text-input wire:model="timelines.{{ $index }}.tanggal_selesai" class="block w-full mt-1 text-sm px-2" type="datetime-local" />
                                            <x-input-error :messages="$errors->get('timelines.'.$index.'.tanggal_selesai')" class="mt-1 text-xs" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-card">
                    <div class="flex items-center justify-between mb-4 border-b border-outline-variant pb-2">
                        <h2 class="text-lg font-bold text-on-surface">Kuota & Biaya</h2>
                        <button type="button" wire:click="tambahBiaya" class="text-xs font-bold text-success hover:text-success/80 bg-success/10 px-2 py-1 rounded">
                            + Tambah Tiket
                        </button>
                    </div>

                    <div class="mb-4">
                        <x-input-label value="Total Kuota Keseluruhan" required />
                        <x-text-input wire:model="kuota" class="block w-full mt-1" type="number" min="1" />
                        <x-input-error :messages="$errors->get('kuota')" class="mt-2" />
                    </div>

                    <div class="space-y-3 border-t border-outline-variant pt-4">
                        @foreach($biayas as $index => $by)
                            <div class="flex gap-2 items-start relative">
                                <div class="w-1/2">
                                    <x-input-label value="Kategori/Tiket" />
                                    <x-text-input wire:model="biayas.{{ $index }}.kategori" class="block w-full mt-1 text-sm" type="text" />
                                    <x-input-error :messages="$errors->get('biayas.'.$index.'.kategori')" class="mt-1 text-xs" />
                                </div>
                                <div class="w-1/2">
                                    <x-input-label value="Biaya (Rp)" />
                                    <x-text-input wire:model="biayas.{{ $index }}.biaya" class="block w-full mt-1 text-sm" type="number" min="0" />
                                    <x-input-error :messages="$errors->get('biayas.'.$index.'.biaya')" class="mt-1 text-xs" />
                                </div>

                                @if(count($biayas) > 1)
                                    <button type="button" wire:click="hapusBiaya({{ $index }})" class="mt-7 text-error hover:text-error/70 p-1">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-card">
                    <h2 class="text-lg font-bold text-on-surface mb-4 border-b border-outline-variant pb-2">Poster Event</h2>

                    @if ($flyer_lama && !$flyer)
                        <div class="mb-4">
                            <p class="text-xs text-on-surface-variant mb-2">Gambar Saat Ini:</p>
                            <img src="{{ asset('storage/' . $flyer_lama) }}" class="w-full h-auto rounded-lg border border-outline-variant opacity-80">
                        </div>
                    @endif

                    <input wire:model="flyer" class="block w-full text-sm text-on-surface border border-outline-variant rounded-lg cursor-pointer bg-surface-container focus:outline-none" type="file" accept="image/*">
                    <p class="text-xs text-on-surface-variant mt-1">Biarkan kosong jika tidak ingin mengubah gambar.</p>
                    <x-input-error :messages="$errors->get('flyer')" class="mt-2" />

                    @if ($flyer)
                        <div class="mt-4">
                            <p class="text-xs text-success font-bold mb-2">Pratinjau Gambar Baru:</p>
                            <img src="{{ $flyer->temporaryUrl() }}" class="w-full h-auto rounded-lg border border-success/30 shadow-sm">
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <div class="flex items-center justify-end gap-4 py-4 border-t border-outline-variant">
            <a href="{{ route('organisasi.events') }}" wire:navigate class="px-6 py-2.5 text-sm font-medium text-on-surface-variant bg-surface-container-lowest border border-outline-variant rounded-lg hover:bg-surface-container">Batal</a>
            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 text-sm font-medium text-on-primary bg-primary rounded-lg hover:bg-primary/90 flex items-center">
                <span wire:loading.remove wire:target="updateEvent">Simpan Perubahan</span>
                <span wire:loading wire:target="updateEvent">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>