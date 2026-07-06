<?php

namespace App\Livewire\Pages\Admin;

use App\Enums\EventStatus;
use App\Models\AdminDpm;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EventDetail extends Component
{
    public $eventId; 
    public Event $event;
    public string $activeTab = 'detail';
    public string $tanggalPelaksanaan = '-';
    public $riwayatPengajuan;
    public string $biayaTeks = 'Gratis';
    public string $alasanPenolakan = '';

    public function mount(Event $event)
    {
        $this->eventId = $event->id;
    }

    protected function baseEventQuery()
    {
        $adminDpm = AdminDpm::query()->where('user_id', Auth::id())->first();

        return Event::whereHas('organisasi', function ($q) use ($adminDpm) {
            if ($adminDpm && $adminDpm->fakultas_id !== null) {
                $q->where('fakultas_id', $adminDpm->fakultas_id);
            } else {
                $q->where('tingkat_organisasi', 'universitas');
            }
        });
    }

    public function getCleanMapsUrlProperty()
    {
        $url = $this->event->lokasi_url; 
        return $this->getEmbedUrl($url);
    }

    public function getEmbedUrl($url)
    {
        if (!$url) return null;

        $isMapsUrl = str_contains($url, 'google.com/maps') || 
                        str_contains($url, 'maps.google') || 
                        str_contains($url, 'maps.app.goo.gl') || 
                        str_contains($url, 'goo.gl/maps');
        
        if (!$isMapsUrl) {
            return null;
        }

        if (str_contains($url, 'output=embed') || str_contains($url, '/embed')) {
            return $url;
        }

        if (str_contains($url, 'maps.app.goo.gl') || str_contains($url, 'goo.gl/maps')) {
            $url = Cache::remember('map_url_' . md5($url), 86400, function() use ($url) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 3);
                $response = curl_exec($ch);
                curl_close($ch);
                
                if (preg_match('/^Location:\s+(.*)$/mi', $response, $matches)) {
                    return trim($matches[1]);
                }
                return $url;
            });
        }

        if (preg_match('/\/maps\/place\/([^\/@?#]+)/', $url, $matches)) {
            return "https://maps.google.com/maps?q=" . $matches[1] . "&t=&z=15&ie=UTF8&iwloc=&output=embed";
        }

        if (preg_match('/\/maps\/search\/([^\/?#]+)/', $url, $matches)) {
            return "https://maps.google.com/maps?q=" . $matches[1] . "&t=&z=15&ie=UTF8&iwloc=&output=embed";
        }

        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $matches)) {
            return "https://maps.google.com/maps?q={$matches[1]},{$matches[2]}&t=&z=15&ie=UTF8&iwloc=&output=embed";
        }

        $parsed = parse_url($url);
        if (isset($parsed['query'])) {
            parse_str($parsed['query'], $queryParts);
            if (isset($queryParts['q'])) {
                return "https://maps.google.com/maps?q=" . urlencode($queryParts['q']) . "&t=&z=15&ie=UTF8&iwloc=&output=embed";
            }
        }

        return "https://maps.google.com/maps?q=" . urlencode($url) . "&t=&z=15&ie=UTF8&iwloc=&output=embed";
    }
    public function approveEvent(int $eventId)
    {
        $adminDpm = AdminDpm::query()->where('user_id', Auth::id())->first();
        $event = $this->baseEventQuery()->find($eventId);

        if ($event) {
            $event->update([
                'status'       => EventStatus::PUBLISHED->value,
                'admin_acc_id' => $adminDpm?->id,
                'catatan_revisi' => null,
            ]);

            session()->flash('success', "Event '{$event->nama_event}' berhasil disetujui!");
            
            return redirect()->route('admin.moderasi.event');
        } else {
            session()->flash('error', "Aksi ilegal terdeteksi. Data tidak ditemukan di wilayah Anda.");
        }
    }

    public function rejectEvent(int $eventId)
    {
        // 1. Validasi Input sesuai aturan string
        $this->validate([
            'alasanPenolakan' => 'required|string|min:5|max:500'
        ], [
            'alasanPenolakan.required' => 'Alasan wajib diisi agar panitia mengetahui kekurangannya.',
            'alasanPenolakan.string'   => 'Format ulasan alasan penolakan tidak valid.',
            'alasanPenolakan.min'      => 'Alasan terlalu singkat, berikan ulasan yang jelas minimal 5 karakter.',
            'alasanPenolakan.max'      => 'Alasan terlalu panjang, batasi ulasan maksimal 500 karakter saja.'
        ]);

        $event = $this->baseEventQuery()->find($eventId);

        if ($event) {
            $alasanBersih = strip_tags($this->alasanPenolakan);

            if (strlen(trim($alasanBersih)) < 5) {
                $this->addError('alasanPenolakan', 'Alasan tidak boleh hanya berisi spasi atau karakter kosong.');
                return;
            }

            $event->update([
                'status'         => EventStatus::REVISION->value,
                'catatan_revisi' => trim($alasanBersih),
            ]);

            $this->reset('alasanPenolakan');

            session()->flash('success', "Event '{$event->nama_event}' telah dikembalikan ke ormawa untuk direvisi.");
            
            return redirect()->route('admin.moderasi.event');
        } else {
            session()->flash('error', "Aksi ilegal terdeteksi. Data tidak ditemukan di wilayah otoritas Anda.");
        }
    }

    public function closeModal()
    {
        $this->alasanPenolakan = '';
        $this->resetErrorBag();
        $this->dispatch('modal-closed');
    }

    #[ \Livewire\Attributes\On('trigger-global-refresh') ]
    public function refreshComponent()
    {
        // Ini akan memaksa Livewire memanggil ulang fungsi render() dan mengambil data terbaru
        $this->resetErrorBag();
        $this->alasanPenolakan = '';
    }
    
    #[Layout('layouts.admin', ['active' => 'moderasi-event'])]
    public function render()
    {
        // Mengunci render data hanya jika event berada di wilayahnya
        $eventData = $this->baseEventQuery()->with([
            'kategori', 
            'organisasi.fakultas',
            'timeLines' => function($query) {
                $query->orderBy('tanggal_mulai', 'asc');
            },
            'biayaEvents',
            'tujuanTransfer',
            'narahubung',
            'formFields',
            'templateSertifikat'
        ])->findOrFail($this->eventId);

        $totalEventSelesai = Event::query()
            ->where('organisasi_id', $eventData->organisasi_id)
            ->where('status', EventStatus::COMPLETED->value)
            ->count();

        // Ambil Riwayat Pengajuan Event dari Ormawa Bersangkutan
        $this->riwayatPengajuan = Event::query()->where('organisasi_id', $eventData->organisasi_id)
            ->where('id', '!=', $eventData->id)
            ->where('status', '!=', EventStatus::DRAFT->value)
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung Tanggal Pelaksanaan Utama
        $agendaAwal = $eventData->timeLines?->min('tanggal_mulai');
        $this->tanggalPelaksanaan = $agendaAwal
            ? Carbon::parse($agendaAwal)->translatedFormat('d F Y')
            : '-';

        // Hitung Rentang Harga Secara Dinamis (Min - Max)
        $totalTiket = $eventData->biayaEvents?->count() ?? 0;
        $hargaMin = $eventData->biayaEvents?->min('biaya') ?? 0;
        $hargaMax = $eventData->biayaEvents?->max('biaya') ?? 0;

        if ($totalTiket === 0 || ($hargaMin === 0 && $hargaMax === 0)) {
            $this->biayaTeks = 'Gratis';
        } elseif ($totalTiket === 1 || $hargaMin === $hargaMax) {
            $this->biayaTeks = 'Rp ' . number_format($hargaMin, 0, ',', '.');
        } else {
            $this->biayaTeks = 'Rp ' . number_format($hargaMin, 0, ',', '.') . ' - Rp ' . number_format($hargaMax, 0, ',', '.');
        }

        // Ambil URL sematan Google Maps melalui properti kustom
        $cleanMapsUrl = $this->getEmbedUrl($eventData->lokasi_url);

        $currentStatus = is_object($eventData->status) ? $eventData->status->value : $eventData->status;
        
        // Deteksi status event
        $isLiveMode = in_array($currentStatus, ['published', 'completed']);
        
        // Deteksi Kondisi Media Pelaksanaan Online vs Offline
        $isOnline = Str::contains(
            strtolower($eventData->nama_lokasi), 
            ['online', 'zoom', 'meet', 'daring', 'youtube']
        );

        $kuotaTotal = $eventData->kuota ?? 0;
        $sisaKuota = $eventData->sisa_kuota ?? 0;
        $pendaftarAktif = max(0, $kuotaTotal - $sisaKuota);
        $persenTerisi = $kuotaTotal > 0 ? round(($pendaftarAktif / $kuotaTotal) * 100) : 0;

        $cpUtama = $eventData->narahubung?->first();
        $waNumber = '';

        if ($cpUtama && !empty($cpUtama->nomor)) {
            // Bersihkan dari spasi, strip (-), plus (+), dan karakter non-angka lainnya
            $nomorAngka = preg_replace('/[^0-9]/', '', $cpUtama->nomor);

            // Jika input berawalan '0812...' -> ubah jadi '62812...'
            if (str_starts_with($nomorAngka, '0')) {
                $nomorAngka = '62' . substr($nomorAngka, 1);
            }
            // Jika input langsung angka '812...' -> tambahkan '62812...'
            elseif (str_starts_with($nomorAngka, '8')) {
                $nomorAngka = '62' . $nomorAngka;
            }

            $waNumber = $nomorAngka;
        }

        $listBankJson = collect($eventData->tujuanTransfer)->map(function($b) {
            return [
                'nama'      => $b->nama_bank,
                'nomor'     => $b->no_rekening,
                'atas_nama' => $b->atas_nama
            ];
        })->toJson();

        return view('livewire.pages.admin.event-detail', [
            'event'              => $eventData,
            'biayaTeks'          => $this->biayaTeks,
            'tanggalPelaksanaan' => $this->tanggalPelaksanaan,
            'cleanMapsUrl'       => $cleanMapsUrl,
            'totalEventSelesai'  => $totalEventSelesai,
            'isLiveMode'         => $isLiveMode,
            'isOnline'           => $isOnline,
            'kuotaTotal'         => $kuotaTotal,
            'sisaKuota'          => $sisaKuota,
            'pendaftarAktif'     => $pendaftarAktif,
            'persenTerisi'       => $persenTerisi,
            'listBankJson'       => $listBankJson,
            'waNumber'           => $waNumber
        ]);
    }
}