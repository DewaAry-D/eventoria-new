<?php

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.organisasi')] class extends Component
{
    public Event $event;
    public array $fields = [];

    public function mount(Event $event)
    {
        $organisasiId = Auth::user()->load('organisasi')->organisasi->id;

        if ($event->organisasi_id !== $organisasiId) {
            abort(403, 'Akses ditolak.');
        }

        $this->event = $event;

        $existingFields = $event->formFields ?? collect([]);

        // Hanya masukkan field dinamis/tambahan ke dalam array builder
        if ($existingFields->count() > 0) {
            foreach ($existingFields as $field) {
                $this->fields[] = [
                    'nama_field' => $field->nama_field,
                    'tipe_field' => $field->tipe_field,
                    'is_required' => (bool) $field->is_required,
                    'opsi' => $field->opsi ?? '',
                ];
            }
        }
        // Kita HAPUS field default "Nama Sertifikat" dari array dinamis ini.
    }

    public function tambahPertanyaan()
    {
        $this->fields[] = [
            'nama_field' => '',
            'tipe_field' => 'text',
            'is_required' => false,
            'opsi' => '',
        ];
    }

    public function hapusPertanyaan($index)
    {
        unset($this->fields[$index]);
        $this->fields = array_values($this->fields); 
    }

    public function simpanForm()
    {
        // Validasi
        $this->validate([
            'fields.*.nama_field' => 'required|string|max:255',
            'fields.*.tipe_field' => 'required|string|in:text,textarea,number,email,select,radio,file_pdf,file_image',
        ], [
            'fields.*.nama_field.required' => 'Label pertanyaan tidak boleh kosong.',
        ]);

        DB::transaction(function () {
            // Hapus form lama
            $this->event->formFields()->delete();

            // Masukkan urutan form baru
            foreach ($this->fields as $field) {
                $this->event->formFields()->create([
                    'nama_field' => $field['nama_field'],
                    'tipe_field' => $field['tipe_field'],
                    'is_required' => $field['is_required'],
                    'opsi' => in_array($field['tipe_field'], ['select', 'radio']) ? $field['opsi'] : null,
                ]);
            }
        });

        session()->flash('success', 'Formulir pendaftaran berhasil dirakit dan disimpan!');
        $this->redirect(route('organisasi.events', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('organisasi.events') }}" wire:navigate class="p-2 text-gray-500 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Setup Form Pendaftaran</h1>
                <p class="text-gray-500 text-sm mt-1">Rakit pertanyaan untuk event <span class="font-semibold text-indigo-700">"{{ $event->nama_event }}"</span>.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            <form wire:submit="simpanForm">
                
                <div class="space-y-4" id="form-builder-container">
                    
                    <div class="relative p-5 bg-gray-50 border border-gray-200 shadow-sm rounded-xl opacity-90">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xs font-bold px-2 py-1 bg-indigo-100 text-indigo-700 rounded flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                                Wajib Sistem
                            </span>
                            <span class="text-sm font-semibold text-gray-700">Nama Lengkap (Cetak Sertifikat)</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Sistem otomatis meminta nama ini dan menyimpannya langsung ke tabel registrasi. Anda tidak perlu membuatnya secara manual.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <input type="text" value="Nama Lengkap (Untuk Sertifikat)" class="block w-full mt-1 text-sm bg-gray-200 border-gray-300 rounded-md cursor-not-allowed text-gray-500" disabled>
                            </div>
                            <div>
                                <input type="text" value="Teks Pendek" class="block w-full mt-1 text-sm bg-gray-200 border-gray-300 rounded-md cursor-not-allowed text-gray-500" disabled>
                            </div>
                        </div>
                    </div>

                    @foreach($fields as $index => $field)
                        <div class="relative p-5 bg-white border border-gray-200 shadow-sm rounded-xl transition hover:shadow-md">
                            
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-2">
                                    <div class="cursor-move text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                                    </div>
                                    <span class="text-xs font-bold px-2 py-1 bg-gray-100 text-gray-600 rounded">Pertanyaan Tambahan {{ $index + 1 }}</span>
                                </div>
                                
                                <button type="button" wire:click="hapusPertanyaan({{ $index }})" class="text-red-400 hover:text-red-600 hover:bg-red-50 p-1.5 rounded-md transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <x-input-label value="Pertanyaan / Label Field" required />
                                    <x-text-input wire:model="fields.{{ $index }}.nama_field" class="block w-full mt-1 text-sm" type="text" placeholder="Cth: Alasan mengikuti kegiatan..." />
                                    <x-input-error :messages="$errors->get('fields.'.$index.'.nama_field')" class="mt-1 text-xs" />
                                </div>
                                
                                <div>
                                    <x-input-label value="Jenis Input" required />
                                    <select wire:model.live="fields.{{ $index }}.tipe_field" class="block w-full mt-1 text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="text">Teks Pendek</option>
                                        <option value="textarea">Paragraf</option>
                                        <option value="number">Angka</option>
                                        <option value="email">Email</option>
                                        <option value="url">URL / Tautan (Cth: Portofolio/Github)</option>
                                        <option value="select">Dropdown</option>
                                        <option value="radio">Pilihan Ganda (Radio)</option>
                                        
                                        <option value="file_pdf">Upload Dokumen (Hanya PDF)</option>
                                        <option value="file_image">Upload Gambar (JPG/PNG)</option>
                                    </select>
                                </div>
                            </div>

                            @if(in_array($fields[$index]['tipe_field'], ['select', 'radio']))
                                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <x-input-label value="Daftar Pilihan (Opsi)" required />
                                    <x-text-input wire:model="fields.{{ $index }}.opsi" class="block w-full mt-1 text-sm" type="text" placeholder="Cth: Ya, Tidak, Mungkin (Pisahkan dengan tanda koma)" />
                                    <p class="text-xs text-yellow-700 mt-1">Gunakan tanda koma ( , ) untuk memisahkan setiap pilihan.</p>
                                </div>
                            @endif

                            <div class="mt-4 flex items-center justify-end border-t border-gray-100 pt-3">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model="fields.{{ $index }}.is_required" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700 font-medium">Wajib Diisi Peserta (Required)</span>
                                </label>
                            </div>

                        </div>
                    @endforeach
                </div>

                <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
                    <button type="button" wire:click="tambahPertanyaan" class="w-full sm:w-auto px-4 py-2 text-sm font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Tambah Pertanyaan
                    </button>

                    <button type="submit" wire:loading.attr="disabled" class="w-full sm:w-auto px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 flex items-center justify-center gap-2 shadow-sm transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        <span wire:loading.remove wire:target="simpanForm">Simpan Form Pendaftaran</span>
                        <span wire:loading wire:target="simpanForm">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-1 hidden lg:block">
            <div class="bg-indigo-900 text-white p-6 rounded-xl shadow-lg sticky top-6">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1h4v1a2 2 0 11-4 0zM12 14c.015-.34.208-.646.477-.859a4 4 0 10-4.954 0c.27.213.462.519.476.859h4.002z" /></svg>
                    Tips Setup Form
                </h3>
                <ul class="space-y-4 text-sm text-indigo-100">
                    <li class="flex items-start gap-2">
                        <div class="mt-1 bg-indigo-700 p-1 rounded"><svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                        <span>Sistem otomatis menanyakan <strong>Nama Cetak Sertifikat</strong> di awal form pendaftaran mahasiswa.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <div class="mt-1 bg-indigo-700 p-1 rounded"><svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                        <span>Gunakan tipe <strong>URL / Tautan</strong> jika Anda meminta peserta melampirkan portofolio, Linkedin, atau GitHub.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <div class="mt-1 bg-indigo-700 p-1 rounded"><svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg></div>
                        <span>Gunakan tipe <strong>Upload File</strong> untuk meminta bukti transfer pembayaran (jika berbayar) atau CV (jika bootcamp rekrutmen).</span>
                    </li>
                </ul>
            </div>
        </div>
        
    </div>
</div>