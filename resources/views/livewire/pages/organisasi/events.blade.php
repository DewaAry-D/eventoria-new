<?php

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new #[Layout('layouts.organisasi')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'semua';

    

    // Reset paginasi saat melakukan pencarian atau filter
    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }

    // Fungsi Pengajuan Event ke DPM
    public function ajukanEvent($eventId)
    {
        $orgId = Auth::user()->load('organisasi')->organisasi->id;
        $event = Event::with('formFields')->where('organisasi_id', $orgId)->find($eventId);

        if (!$event) {
            session()->flash('error', 'Event tidak ditemukan.');
            return;
        }

        // Validasi: Pastikan event sudah memiliki minimal 1 form field
        if ($event->formFields->count() === 0) {
            session()->flash('error', 'Gagal mengajukan! Anda harus menyusun Form Pendaftaran terlebih dahulu sebelum mengajukan event ke DPM.');
            return;
        }

        // Ubah status menjadi pending_approval
        $event->update([
            'status' => 'pending_approval'
        ]);

        session()->flash('success', 'Event "' . $event->nama_event . '" berhasil diajukan ke DPM untuk direview.');
    }

    public function with(): array
    {
        // $orgId = Auth::user()->load('organisasi')->organisasi->id;
        $organisasi = Auth::user()->load('organisasi')->organisasi;

        $events = Event::with(['kategori'])
            ->withCount(['registrations', 'formFields']) // Ambil jumlah pendaftar dan jumlah kolom form
            ->where('organisasi_id', $organisasi->id)
            ->when($this->search, fn($q) => $q->where('nama_event', 'like', '%' . $this->search . '%'))
            ->when($this->statusFilter !== 'semua', fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(10);

        return [
            'events' => $events,
            'organisasi'=> $organisasi
        ];
    }
}; ?>

<div>
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Event</h1>
            <p class="text-gray-500 text-sm">Kelola seluruh kegiatan, pantau status birokrasi, dan atur pendaftar.</p>
        </div>
        @if($organisasi->status->value === 'approved')
                <a href="{{ route('organisasi.events.create') }}" wire:navigate class="px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition shadow-sm text-sm inline-flex items-center gap-2">
                    + Buat Event Baru
                </a>
        @else
                <button disabled class="px-4 py-2 bg-primary text-white font-medium rounded-lg shadow-sm text-sm opacity-50 cursor-not-allowed inline-flex items-center gap-2">
                    + Buat Event Baru
                </button>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="mb-4 p-4 text-sm text-green-800 rounded-lg bg-green-50 flex items-center shadow-sm">
            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 flex items-center shadow-sm">
            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row gap-4 justify-between items-center mb-6">
        <div class="relative w-full md:w-96">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input wire:model.live.debounce.500ms="search" type="search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5" placeholder="Cari nama event...">
        </div>
        
        <select wire:model.live="statusFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full md:w-48 p-2.5">
            <option value="semua">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="pending_approval">Menunggu ACC</option>
            <option value="revision">Butuh Revisi</option>
            <option value="published">Publikasi (Aktif)</option>
            <option value="completed">Selesai</option>
        </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4">Nama Event</th>
                        <th scope="col" class="px-6 py-4 text-center">Status</th>
                        <th scope="col" class="px-6 py-4 text-center">Tingkat</th>
                        <th scope="col" class="px-6 py-4 text-center">Pendaftar / Kuota</th>
                        <th scope="col" class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-900">{{ $event->nama_event }}</div>
                                <div class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded">{{ $event->kategori->nama_kategori ?? 'Umum' }}</span>
                                    <span>•</span>
                                    <span>Form: {{ $event->form_fields_count }} Pertanyaan</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($event->status->value === 'draft')
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-1 rounded-full border border-gray-300">Draft</span>
                                @elseif($event->status->value === 'pending_approval')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-1 rounded-full border border-yellow-300">Review DPM</span>
                                @elseif($event->status->value === 'revision')
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-1 rounded-full border border-red-300">Revisi</span>
                                        <a class="text-xs text-red-600 cursor-pointer hover:underline" title="{{ $event->catatan_revisi }}" href="#">Lihat Catatan</a>
                                    </div>
                                @elseif($event->status->value === 'published')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full border border-green-300">Dipublikasi</span>
                                @else
                                    <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-1 rounded-full border border-indigo-300">Selesai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="uppercase text-xs font-bold tracking-wider text-gray-500">{{ $event->tingkat_event }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-medium text-gray-900">{{ $event->registrations_count }}</span>
                                <span class="text-gray-400">/ {{ $event->kuota ?? '∞' }}</span>
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    
                                    @if(in_array($event->status->value, ['draft', 'revision']))
                                        <button 
                                            wire:click="ajukanEvent({{ $event->id }})"
                                            wire:confirm="Yakin ingin mengajukan event ini ke DPM sekarang? Pastikan proposal dan form sudah sesuai SOP."
                                            title="Ajukan Event ke DPM"
                                            class="p-1.5 hover:bg-blue-50 hover:text-blue-800 rounded-md transition focus:outline-none focus:ring-2 focus:ring-blue-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        </button>
                                    @endif

                                    @if(in_array($event->status->value, ['draft', 'revision']))
                                        <a href="{{ route('organisasi.events.edit', $event->id) }}" wire:navigate title="Edit Detail Event" class="p-1.5 hover:bg-blue-50 hover:text-blue-800 rounded-md transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.147l-3.141 1.047 1.047-3.141a4.5 4.5 0 011.147-1.89L16.862 4.487zM16.862 4.487L19.5 7.125"/></svg>
                                        </a>
                                    @else
                                        <button disabled title="Event tidak bisa diedit" class="p-1.5 text-gray-300 cursor-not-allowed rounded-md">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.89 1.147l-3.141 1.047 1.047-3.141a4.5 4.5 0 011.147-1.89L16.862 4.487zM16.862 4.487L19.5 7.125"/></svg>
                                        </button>
                                    @endif

                                    <a href="{{ route('organisasi.events.form-builder', $event->id) }}" wire:navigate title="Setup Form Pendaftaran" class="p-1.5 text-gray-500 hover:bg-indigo-50 hover:text-primary rounded-md transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    </a>

                                    <a href="{{ route('organisasi.events.pendaftar', $event->id) }}" title="Kelola Pendaftar & Rekrutmen" class="p-1.5 text-gray-500 hover:bg-green-50 hover:text-green-600 rounded-md transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                    </a>

                                    <a href="{{ route('organisasi.events.sertifikat-builder', $event->id) }}" wire:navigate title="Desain Template Sertifikat" class="p-1.5 text-gray-500 hover:bg-yellow-50 hover:text-yellow-600 rounded-md transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                    </a>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <p class="text-lg font-medium text-gray-900">Belum ada event</p>
                                <p class="text-sm">Klik "Buat Event Baru" untuk memulai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $events->links() }}
        </div>
    </div>
</div>