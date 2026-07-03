<?php

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.organisasi')] class extends Component
{
    use WithFileUploads;

    public Event $event;

    public $template_sertifikat;
    public $template_url_lama;

    public $x_pos = 50;
    public $y_pos = 45;
    public $font_family = 'Poppins';
    public $font_size = 32;
    public $font_color = '#000666';

    public function mount(Event $event)
    {
        $organisasiId = Auth::user()->load('organisasi')->organisasi->id;

        if ($event->organisasi_id !== $organisasiId) {
            abort(403, 'Akses ditolak.');
        }

        $this->event = $event;

        $template = \Illuminate\Support\Facades\DB::table('template_sertifikat')
            ->where('event_id', $event->id)
            ->first();

        if ($template) {
            $this->x_pos = $template->posisi_x;
            $this->y_pos = $template->posisi_y;
            $this->font_family = $template->jenis_font;
            $this->font_size = $template->ukuran_font;
            $this->font_color = $template->warna_font;
            $this->template_url_lama = $template->file_template;
        }
    }

    public function simpanTemplate()
    {
        $this->validate([
            'template_sertifikat' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'x_pos' => 'required|numeric|min:0|max:100',
            'y_pos' => 'required|numeric|min:0|max:100',
            'font_family' => 'required|string|max:100',
            'font_size' => 'required|numeric|min:10|max:200',
            'font_color' => 'required|string|max:10',
        ]);

        $path = $this->template_url_lama;

        if ($this->template_sertifikat) {
            if ($this->template_url_lama) {
                Storage::disk('public')->delete($this->template_url_lama);
            }
            $path = $this->template_sertifikat->store('sertifikat-templates', 'public');

            $this->template_url_lama = $path;
        }

        \Illuminate\Support\Facades\DB::table('template_sertifikat')
            ->updateOrInsert(
                ['event_id' => $this->event->id],
                [
                    'file_template' => $path,
                    'posisi_x' => round($this->x_pos),
                    'posisi_y' => round($this->y_pos),
                    'jenis_font' => $this->font_family,
                    'ukuran_font' => round($this->font_size),
                    'warna_font' => $this->font_color,
                ]
            );

        $this->template_sertifikat = null;

        session()->flash('success', 'Desain dan posisi sertifikat berhasil disimpan secara permanen!');
    }
}; ?>

<div x-data="certificateBuilder()"
     @mousemove.window="drag"
     @mouseup.window="stopDrag">

    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('organisasi.events') }}" wire:navigate class="p-2 text-on-surface-variant bg-surface-container-lowest border border-outline-variant rounded-lg hover:bg-surface-container transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-on-surface">Desain Template Sertifikat</h1>
                <p class="text-on-surface-variant text-sm mt-1">Atur posisi nama peserta untuk event <span class="font-semibold text-primary">"{{ $event->nama_event }}"</span>.</p>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="mt-4 p-4 text-sm text-success rounded-lg bg-success/10 flex items-center shadow-sm">
                <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                {{ session('success') }}
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <div class="lg:col-span-4 space-y-6">
            <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/50 shadow-card">
                <h2 class="text-lg font-semibold text-primary mb-4">Upload Template</h2>
                <div class="border-2 border-dashed border-outline-variant rounded-lg p-8 text-center bg-surface-container hover:bg-surface-container-high transition cursor-pointer relative">
                    <input type="file" wire:model="template_sertifikat" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/jpeg, image/png">
                    <svg class="mx-auto h-10 w-10 text-on-surface-variant mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <div wire:loading wire:target="template_sertifikat" class="text-sm text-primary font-medium mb-1">Mengunggah...</div>
                    <div wire:loading.remove wire:target="template_sertifikat">
                        <p class="text-sm text-on-surface-variant mb-2">Klik atau seret file JPG/PNG sertifikat di sini</p>
                        <p class="text-[10px] font-bold text-on-surface-variant/60 tracking-wider">MAKSIMUM 5MB</p>
                    </div>
                </div>
                <x-input-error :messages="$errors->get('template_sertifikat')" class="mt-2" />
            </div>

            <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/50 shadow-card">
                <h2 class="text-lg font-semibold text-primary mb-6">Pengaturan Posisi Nama</h2>
                <div class="space-y-5">

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-xs font-semibold text-on-surface-variant">Posisi Horizontal (X)</label>
                            <input type="number" x-model="xPos" class="w-16 px-2 py-1 text-center text-sm border border-outline-variant rounded bg-surface-container focus:ring-primary focus:border-primary">
                        </div>
                        <input type="range" x-model="xPos" min="0" max="100" class="w-full h-2 bg-surface-container-high rounded-lg appearance-none cursor-pointer accent-primary">
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-xs font-semibold text-on-surface-variant">Posisi Vertikal (Y)</label>
                            <input type="number" x-model="yPos" class="w-16 px-2 py-1 text-center text-sm border border-outline-variant rounded bg-surface-container focus:ring-primary focus:border-primary">
                        </div>
                        <input type="range" x-model="yPos" min="0" max="100" class="w-full h-2 bg-surface-container-high rounded-lg appearance-none cursor-pointer accent-primary">
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-on-surface-variant block mb-2">Jenis Font</label>
                        <select x-model="fontFamily" class="block w-full text-sm border-outline-variant rounded-md bg-surface-container focus:ring-primary focus:border-primary">
                            <option value="'Poppins', sans-serif">Poppins</option>
                            <option value="'Montserrat', sans-serif">Montserrat</option>
                            <option value="'Playfair Display', serif">Playfair Display</option>
                            <option value="'Great Vibes', cursive">Great Vibes (Latin)</option>
                            <option value="Arial, sans-serif">Arial</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-on-surface-variant block mb-2">Ukuran Font</label>
                            <select x-model="fontSize" class="block w-full text-sm border-outline-variant rounded-md bg-surface-container focus:ring-primary focus:border-primary">
                                <option value="8">8px</option>
                                <option value="12">12px</option>
                                <option value="16">16px</option>
                                <option value="20">20px</option>
                                <option value="24">24px</option>
                                <option value="32">32px</option>
                                <option value="48">48px</option>
                                <option value="64">64px</option>
                                <option value="72">72px</option>
                                <option value="96">96px</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-on-surface-variant block mb-2">Warna Teks</label>
                            <div class="flex items-center gap-2 border border-outline-variant rounded-md bg-surface-container px-2 py-1.5 focus-within:ring-1 focus-within:ring-primary">
                                <input type="color" x-model="fontColor" class="w-6 h-6 rounded cursor-pointer border-0 p-0 bg-transparent">
                                <span class="text-sm font-medium text-on-surface-variant uppercase" x-text="fontColor"></span>
                            </div>
                        </div>
                    </div>

                    <button wire:click="simpanTemplate" class="w-full mt-4 flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-semibold text-on-primary bg-primary rounded-lg hover:bg-primary/90 transition shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Simpan Template
                    </button>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8">
            <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/50 shadow-card h-[800px] flex flex-col overflow-hidden">

                <div class="px-6 py-4 flex items-center justify-between border-b border-outline-variant bg-surface-container-lowest z-10">
                    <h2 class="text-lg font-semibold text-primary">Preview Sertifikat</h2>
                    <div class="flex items-center gap-3 bg-surface-container border border-outline-variant rounded-lg p-1">
                        <button @click="zoomOut" class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container-lowest rounded transition" title="Perkecil">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/></svg>
                        </button>
                        <span class="text-xs font-bold text-on-surface-variant w-12 text-center" x-text="Math.round(zoomLevel * 100) + '%'">100%</span>
                        <button @click="zoomIn" class="p-1.5 text-on-surface-variant hover:text-primary hover:bg-surface-container-lowest rounded transition" title="Perbesar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                        </button>
                    </div>
                </div>

                <div class="bg-surface flex-1 overflow-auto relative p-8 flex justify-center items-start">

                    <div class="relative inline-block shadow-2xl bg-surface-container-lowest select-none transition-transform duration-200 ease-out origin-top"
                         x-ref="certContainer"
                         :style="`transform: scale(${zoomLevel});`">

                        @if ($template_sertifikat)
                            <img src="{{ $template_sertifikat->temporaryUrl() }}" class="block max-w-[800px] h-auto pointer-events-none border border-outline-variant">
                        @elseif($template_url_lama)
                            <img src="{{ asset('storage/' . $template_url_lama) }}" class="block max-w-[800px] h-auto pointer-events-none border border-outline-variant">
                        @else
                            <div class="w-[800px] aspect-[1.414/1] bg-surface-container-lowest border border-outline-variant flex flex-col items-center justify-center">
                                <svg class="w-24 h-24 text-outline-variant mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                <p class="text-xl font-bold text-outline-variant">CERTIFICATE PLACEHOLDER</p>
                            </div>
                        @endif

                        <div class="absolute cursor-move inline-block whitespace-nowrap"
                             :class="isDragging ? 'ring-2 ring-dashed ring-primary bg-primary/10' : 'hover:ring-1 hover:ring-dashed hover:ring-primary/60'"
                             :style="`left: ${xPos}%; top: ${yPos}%; transform: translate(-50%, -50%); font-family: ${fontFamily}; font-size: ${fontSize}px; color: ${fontColor};`"
                             @mousedown="startDrag">

                            Name Surname

                            <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-inverse-surface text-inverse-on-surface text-[10px] font-bold px-3 py-1 rounded-full tracking-wider opacity-0 transition-opacity whitespace-nowrap"
                                 :class="!isDragging ? 'group-hover:opacity-100 hover:opacity-100' : ''"
                                 style="pointer-events: none;">
                                SERET POSISI TEKS
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function certificateBuilder() {
        return {
            xPos: @entangle('x_pos'),
            yPos: @entangle('y_pos'),
            fontFamily: @entangle('font_family'),
            fontSize: @entangle('font_size'),
            fontColor: @entangle('font_color'),

            zoomLevel: 1,
            isDragging: false,

            zoomIn() {
                if (this.zoomLevel < 2) this.zoomLevel += 0.1;
            },
            zoomOut() {
                if (this.zoomLevel > 0.5) this.zoomLevel -= 0.1;
            },

            startDrag(e) {
                this.isDragging = true;
                e.preventDefault();
            },
            stopDrag() {
                this.isDragging = false;
            },
            drag(e) {
                if (!this.isDragging) return;

                const container = this.$refs.certContainer.getBoundingClientRect();

                let x = ((e.clientX - container.left) / container.width) * 100;
                let y = ((e.clientY - container.top) / container.height) * 100;

                this.xPos = Math.max(0, Math.min(100, Math.round(x)));
                this.yPos = Math.max(0, Math.min(100, Math.round(y)));
            }
        }
    }
</script>