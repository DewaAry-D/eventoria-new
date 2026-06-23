<?php

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.organisasi')] class extends Component
{
    public function with(): array
    {
        $organisasi = Auth::user()->load('organisasi')->organisasi;
        
        // Mengambil semua event milik organisasi ini
        $events = Event::with('kategori')
                    ->withCount('registrations') // Menghitung total pendaftar di setiap event
                    ->where('organisasi_id', $organisasi->id)
                    ->latest()
                    ->get();

        // Metrik
        $totalEvent = $events->count();
        $totalPendaftar = $events->sum('registrations_count');
        $menungguPersetujuan = $events->where('status', 'pending_approval')->count();
        $butuhRevisi = $events->where('status', 'revision')->count();

        return [
            'organisasi' => $organisasi,
            'events' => $events,
            'totalEvent' => $totalEvent,
            'totalPendaftar' => $totalPendaftar,
            'menungguPersetujuan' => $menungguPersetujuan,
            'butuhRevisi' => $butuhRevisi,
        ];
    }
}; ?>

<div>
    @if($organisasi->status->value === 'pending')
        <div class="mb-6 p-4 border-l-4 border-yellow-400 bg-yellow-50 text-yellow-800 rounded-r-lg shadow-sm">
            <div class="flex items-center mb-1">
                <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                <h3 class="font-bold text-lg">Akun Sedang Diverifikasi</h3>
            </div>
            <p class="text-sm ml-7">Akun organisasi Anda saat ini sedang dalam proses peninjauan oleh DPM. Fitur pembuatan event akan diaktifkan setelah akun disetujui.</p>
        </div>
    @elseif($organisasi->status->value === 'rejected')
        <div class="mb-6 p-4 border-l-4 border-red-500 bg-red-50 text-red-800 rounded-r-lg shadow-sm">
            <div class="flex items-center mb-1">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                <h3 class="font-bold text-lg">Pendaftaran Akun Ditolak</h3>
            </div>
            <p class="text-sm ml-7 mb-2">Pendaftaran organisasi Anda ditolak oleh DPM dengan catatan berikut:</p>
            <div class="ml-7 p-3 bg-white border border-red-200 rounded text-gray-700 text-sm font-medium italic">
                "{{ $organisasi->pesan_penolakan ?? 'Dokumen pendirian tidak valid.' }}"
            </div>
        </div>
    @endif

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Manajemen</h1>
            <p class="text-gray-500 text-sm">Kelola kegiatan dan pantau antusiasme pendaftar program kerja Anda.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition shadow-sm text-sm">
                Lihat SOP Pengajuan
            </button>
            @if($organisasi->status->value === 'approved')
                <a href="{{ route('organisasi.events.create') }}" wire:navigate class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm text-sm inline-flex items-center gap-2">
                    + Buat Event Baru
                </a>
            @else
                <button disabled class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg shadow-sm text-sm opacity-50 cursor-not-allowed inline-flex items-center gap-2">
                    + Buat Event Baru
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-4 bg-indigo-50 text-indigo-600 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Event</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalEvent }}</p>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-4 bg-green-50 text-green-600 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Pendaftar</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalPendaftar }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-center gap-4">
            <div class="p-4 bg-yellow-50 text-yellow-600 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Menunggu ACC / Revisi</p>
                <div class="flex items-center gap-2">
                    <p class="text-2xl font-bold text-gray-900">{{ $menungguPersetujuan }}</p>
                    @if($butuhRevisi > 0)
                        <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded-full border border-red-200">{{ $butuhRevisi }} Revisi</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-900">Event Terbaru Anda</h2>
            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama Event</th>
                        <th scope="col" class="px-6 py-3">Status Birokrasi</th>
                        <th scope="col" class="px-6 py-3">Pendaftar / Kuota</th>
                        <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events->take(5) as $event)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900 w-1/2">
                                <div class="line-clamp-1">{{ $event->nama_event }}</div>
                                <div class="text-xs text-gray-400 font-normal mt-1">{{ $event->kategori->nama_kategori ?? 'Umum' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($event->status->value === 'draft')
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded border border-gray-300">Draft</span>
                                @elseif($event->status->value === 'pending_approval')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded border border-yellow-300">Diproses DPM</span>
                                @elseif($event->status->value === 'revision')
                                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded border border-red-300">Revisi Proposal</span>
                                @elseif($event->status->value === 'published')
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded border border-green-300">Dipublikasi</span>
                                @else
                                    <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded border border-indigo-300">Selesai</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 max-w-[100px]">
                                        <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $event->kuota > 0 ? ($event->registrations_count / $event->kuota) * 100 : 0 }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">{{ $event->registrations_count }} / {{ $event->kuota ?? '~' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="font-medium text-indigo-600 hover:text-indigo-900">Kelola</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                Belum ada event yang dibuat. Mulai rencanakan kegiatan pertama Anda!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>