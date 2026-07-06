<?php

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Enums\RegistrationStatus;

new #[Layout('layouts.organisasi')] class extends Component
{
    use WithFileUploads;

    public Event $event;
    public $pendaftar = [];

    public $showRejectModal = false;
    public $rejectId = null;
    public $catatan_penolakan = '';

    public $showUploadModal = false;
    public $csvFile;

    public function mount(Event $event)
    {
        $organisasiId = Auth::user()->load('organisasi')->organisasi->id;

        if ($event->organisasi_id !== $organisasiId) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak untuk mengelola event ini.');
        }

        $this->event = $event;
        $this->loadPendaftar();
    }

    public function loadPendaftar()
    {
        $this->pendaftar = EventRegistration::with('mahasiswa')
            ->where('event_id', $this->event->id)
            ->latest()
            ->get();
    }

    public function terima($id)
    {
        $reg = EventRegistration::find($id);
        if ($reg && $reg->status_pendaftaran === RegistrationStatus::PENDING) {
            $reg->update(['status_pendaftaran' => RegistrationStatus::APPROVED]);
            $this->loadPendaftar();
            session()->flash('success', 'Pendaftar berhasil disetujui.');
        }
    }

    public function konfirmasiTolak($id)
    {
        $this->rejectId = $id;
        $this->catatan_penolakan = '';
        $this->showRejectModal = true;
    }

    public function tolak()
    {
        $this->validate([
            'catatan_penolakan' => 'required|string|min:5'
        ], [
            'catatan_penolakan.required' => 'Wajib memberikan alasan penolakan.'
        ]);

        $reg = EventRegistration::find($this->rejectId);
        if ($reg && $reg->status_pendaftaran === RegistrationStatus::PENDING) {
            $reg->update([
                'status_pendaftaran' => RegistrationStatus::REJECTED,
                'catatan_penolakan' => $this->catatan_penolakan
            ]);

            $this->showRejectModal = false;
            $this->loadPendaftar();
            session()->flash('success', 'Pendaftar ditolak beserta alasannya.');
        }
    }

    public function selesaikan($id)
    {
        $reg = EventRegistration::find($id);
        if ($reg && $reg->status_pendaftaran === RegistrationStatus::APPROVED) {
            $reg->update(['status_pendaftaran' => RegistrationStatus::COMPLETED]);
            $this->loadPendaftar();
            session()->flash('success', 'Status pendaftar diubah menjadi Selesai (Completed).');
        }
    }

    public function unduhCsv()
    {
        $approvedRegistrations = EventRegistration::with('mahasiswa')
            ->where('event_id', $this->event->id)
            ->where('status_pendaftaran', RegistrationStatus::APPROVED)
            ->get();

        $csvData = "ID_Pendaftaran,NIM,Nama,Ubah_Jadi_Selesai(Isi dengan angka 1)\n";

        foreach ($approvedRegistrations as $reg) {
            $nama = str_replace(',', ' ', $reg->mahasiswa->nama ?? '-');
            $nim = $reg->mahasiswa->nim ?? '-';
            $csvData .= "{$reg->id},{$nim},{$nama},0\n";
        }

        $fileName = 'Template_Update_Selesai_' . Str::slug($this->event->nama_event) . '.csv';

        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, $fileName);
    }

    public function uploadCsv()
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:2048'
        ], [
            'csvFile.required' => 'Silakan pilih file CSV terlebih dahulu.',
            'csvFile.mimes' => 'File harus berformat CSV.'
        ]);

        $filePath = $this->csvFile->getRealPath();
        $file = fopen($filePath, 'r');

        fgetcsv($file);

        $updatedCount = 0;

        DB::transaction(function () use ($file, &$updatedCount) {
            while (($row = fgetcsv($file)) !== FALSE) {
                if (count($row) >= 4) {
                    $id_pendaftaran = $row[0];
                    $is_completed = trim($row[3]);

                    if ($is_completed == '1' || strtolower($is_completed) == 'y' || strtolower($is_completed) == 'yes') {
                        $reg = EventRegistration::find($id_pendaftaran);

                        if ($reg && $reg->event_id == $this->event->id && $reg->status_pendaftaran === RegistrationStatus::APPROVED) {
                            $reg->update(['status_pendaftaran' => RegistrationStatus::COMPLETED]);
                            $updatedCount++;
                        }
                    }
                }
            }
        });

        fclose($file);

        $this->showUploadModal = false;
        $this->csvFile = null;
        $this->loadPendaftar();

        session()->flash('success', "Berhasil memperbarui {$updatedCount} pendaftar menjadi Selesai (Completed).");
    }
}; ?>

<div>
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('organisasi.events') }}" wire:navigate class="p-2 text-on-surface-variant bg-surface-container-lowest border border-outline-variant rounded-lg hover:bg-surface-container transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-on-surface">Kelola Pendaftaran</h1>
                <p class="text-on-surface-variant text-sm mt-1">Event: <span class="font-bold text-primary">{{ $event->nama_event }}</span></p>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-success rounded-lg bg-success/10 flex items-center shadow-sm">
            <svg class="w-5 h-5 inline mr-2 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-surface-container-lowest rounded-xl shadow-card border border-outline-variant overflow-hidden">
        <div class="p-4 border-b border-outline-variant flex flex-wrap gap-3 justify-between items-center bg-surface-container/50">
            <div class="text-sm font-bold text-on-surface">
                Total Pendaftar: {{ count($pendaftar) }} Orang
            </div>
            <div class="flex gap-2">
                <button wire:click="unduhCsv" class="px-4 py-2 text-xs font-bold text-primary bg-primary/10 border border-primary/20 hover:bg-primary/20 rounded-lg flex items-center gap-2 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Unduh Template (Peserta ACC)
                </button>
                <button @click="$wire.set('showUploadModal', true)" class="px-4 py-2 text-xs font-bold text-on-primary bg-primary hover:bg-primary/90 rounded-lg flex items-center gap-2 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Bulk Update (Selesai)
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-on-surface-variant">
                <thead class="text-center text-xs text-on-surface-variant uppercase bg-surface-container border-b border-outline-variant">
                    <tr>
                        <th class="px-6 py-4">NIM</th>
                        <th class="px-6 py-4">Nama Mahasiswa</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">View</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftar as $peserta)
                        <tr class="text-center bg-surface-container-lowest border-b border-outline-variant/30 hover:bg-surface-container-low transition">
                            <td class="px-6 py-4 font-medium text-on-surface">
                                {{ $peserta->mahasiswa->nim ?? '-' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $peserta->mahasiswa->nama ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                @if($peserta->status_pendaftaran === RegistrationStatus::PENDING)
                                    <span class="px-2.5 py-1 text-[10px] font-bold text-warning bg-warning/10 rounded-full uppercase">Pending</span>
                                @elseif($peserta->status_pendaftaran === RegistrationStatus::APPROVED)
                                    <span class="px-2.5 py-1 text-[10px] font-bold text-success bg-success/10 rounded-full uppercase">Disetujui</span>
                                @elseif($peserta->status_pendaftaran === RegistrationStatus::COMPLETED)
                                    <span class="px-2.5 py-1 text-[10px] font-bold text-primary bg-primary/10 border border-primary/20 rounded-full uppercase">Completed</span>
                                @elseif($peserta->status_pendaftaran === RegistrationStatus::REJECTED)
                                    <span class="px-2.5 py-1 text-[10px] font-bold text-on-error-container bg-error-container rounded-full uppercase">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('organisasi.events.jawaban', ['event' => $event->id, 'peserta' => $peserta->id]) }}" wire:navigate class="px-3 py-1 text-xs font-bold text-on-primary bg-primary hover:bg-primary/90 rounded-lg transition">Lihat Detail</a>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($peserta->status_pendaftaran === RegistrationStatus::PENDING)
                                        <button type="button" wire:click="terima({{ $peserta->id }})" title="Setujui" class="p-1.5 text-success hover:bg-success/10 rounded-lg transition border border-transparent hover:border-success/30">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <button type="button" wire:click="konfirmasiTolak({{ $peserta->id }})" title="Tolak" class="p-1.5 text-error hover:bg-error-container/40 rounded-lg transition border border-transparent hover:border-error/30">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    @elseif($peserta->status_pendaftaran === RegistrationStatus::APPROVED)
                                        <button type="button" wire:click="selesaikan({{ $peserta->id }})" title="Tandai Selesai" class="p-1.5 text-primary hover:bg-primary/10 rounded-lg transition border border-transparent hover:border-primary/20 flex items-center gap-1 text-xs font-bold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                            Selesai
                                        </button>
                                    @elseif($peserta->status_pendaftaran === RegistrationStatus::REJECTED) 
                                        <span class="text-xs text-error font-semibold">Ditolak</span>
                                    @elseif($peserta->status_pendaftaran === RegistrationStatus::COMPLETED) 
                                        <span class="text-xs text-success font-semibold">Selesai</span>
                                    @else
                                        <span class="text-xs text-on-surface-variant/60 italic">Dikunci</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-outline-variant mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    <p class="text-on-surface-variant font-medium">Belum ada pendaftar untuk event ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/60 backdrop-blur-sm px-4">
            <div class="bg-surface-container-lowest rounded-2xl shadow-2xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold text-on-surface mb-2">Tolak Peserta</h3>
                <p class="text-sm text-on-surface-variant mb-4">Alasan penolakan ini akan dicatat dan dapat dilihat oleh peserta.</p>

                <textarea wire:model="catatan_penolakan" rows="3" class="w-full border-outline-variant rounded-lg text-sm focus:ring-error focus:border-error" placeholder="Tulis alasan penolakan... (Maksimal kuota, dll)"></textarea>
                <x-input-error :messages="$errors->get('catatan_penolakan')" class="mt-1" />

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showRejectModal', false)" class="px-4 py-2 text-sm font-medium text-on-surface bg-surface-container rounded-lg hover:bg-surface-container-high">Batal</button>
                    <button wire:click="tolak" class="px-4 py-2 text-sm font-medium text-on-error bg-error rounded-lg hover:bg-error/90">Tolak Pendaftaran</button>
                </div>
            </div>
        </div>
    @endif

    @if($showUploadModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/60 backdrop-blur-sm px-4">
            <div class="bg-surface-container-lowest rounded-2xl shadow-2xl w-full max-w-md p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-primary/10 text-primary rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-on-surface">Upload CSV Update Selesai</h3>
                </div>

                <div class="bg-warning/10 border border-warning/30 p-3 rounded-lg text-xs text-warning mb-4 leading-relaxed">
                    <strong>Cara Penggunaan:</strong><br>
                    1. Unduh template CSV dari tombol sebelumnya.<br>
                    2. Buka di Excel/Spreadsheet.<br>
                    3. Ubah angka <strong>0</strong> menjadi <strong>1</strong> pada kolom terakhir untuk peserta yang lulus/selesai.<br>
                    4. Simpan kembali dalam format <strong>CSV</strong> dan unggah ke sini.
                </div>

                <form wire:submit="uploadCsv">
                    <input type="file" wire:model="csvFile" accept=".csv" class="block w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 border border-outline-variant rounded-lg" required>
                    <x-input-error :messages="$errors->get('csvFile')" class="mt-2" />

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" wire:click="$set('showUploadModal', false)" class="px-4 py-2 text-sm font-medium text-on-surface bg-surface-container rounded-lg hover:bg-surface-container-high">Batal</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-4 py-2 text-sm font-medium text-on-primary bg-primary rounded-lg hover:bg-primary/90 flex items-center">
                            <span wire:loading.remove wire:target="uploadCsv">Proses Bulk Update</span>
                            <span wire:loading wire:target="uploadCsv">Memproses Data...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>