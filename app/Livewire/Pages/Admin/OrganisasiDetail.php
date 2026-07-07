<?php

namespace App\Livewire\Pages\Admin;

use App\Enums\OrganisasiStatus;
use App\Models\AdminDpm;
use App\Models\OrganisasiMahasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // 🟢 Tambahkan import Storage
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

class OrganisasiDetail extends Component
{
    public int $orgId;
    public string $pesanPenolakan = '';
    public bool $showApproveModal = false;
    public bool $showRejectModal = false;

    public function mount(int $id): void
    {
        $this->orgId = $id;
        $org = OrganisasiMahasiswa::findOrFail($this->orgId);
        $this->authorizeScope($org);
    }

    protected function getAdminDpm(): ?AdminDpm
    {
        return AdminDpm::query()->where('user_id', Auth::id())->first();
    }

    protected function authorizeScope(OrganisasiMahasiswa $org): void
    {
        $adminDpm = $this->getAdminDpm();
        $allowed = false;

        if ($adminDpm && $adminDpm->fakultas_id !== null) {
            $allowed = ($org->fakultas_id === $adminDpm->fakultas_id);
        } else {
            $allowed = ($org->tingkat_organisasi === 'universitas');
        }

        if (!$allowed) {
            abort(403, 'Anda tidak memiliki otoritas atas organisasi ini.');
        }
    }

    protected function getFreshOrgQuery()
    {
        $adminDpm = $this->getAdminDpm();
        $query = OrganisasiMahasiswa::query()->where('id', $this->orgId);

        if ($adminDpm && $adminDpm->fakultas_id !== null) {
            $query->where('fakultas_id', $adminDpm->fakultas_id);
        } else {
            $query->where('tingkat_organisasi', 'universitas');
        }

        return $query;
    }
    
    private function calculateRingkasan(OrganisasiMahasiswa $org): array
    {
        $fieldsToCheck = [
            $org->nama_organisasi, $org->deskripsi, $org->visi, 
            $org->misi, $org->logo_url, $org->linkedin_url, $org->ig_url, $org->sk, $org->ad_art
        ];
        
        $totalFields = count($fieldsToCheck);
        $filledFields = 0;
        
        foreach ($fieldsToCheck as $field) {
            if (!empty($field)) {
                $filledFields++;
            }
        }
        $kelengkapan = round(($filledFields / $totalFields) * 100);

        $isValid = !empty($org->sk) && !empty($org->ad_art);
        $validasiSistem = $isValid ? 'Terverifikasi' : 'Butuh Review';

        $currentStatus = is_object($org->status) ? $org->status->value : $org->status;

        $urgensi = 'Normal';
        if (strtolower($currentStatus) === 'pending') {
            $daysOld = $org->created_at ? $org->created_at->diffInDays(now()) : 0;
            
            if ($daysOld >= 30) {
                $urgensi = 'Tinggi';
            } elseif ($daysOld >= 7) {
                $urgensi = 'Medium';
            }
        }

        return [
            'kelengkapan' => $kelengkapan . '%',
            'validasi' => $validasiSistem,
            'urgensi' => $urgensi
        ];
    }

    private function generateRiwayatTimeline(OrganisasiMahasiswa $org): array
    {
        $currentStatus = is_object($org->status) ? $org->status->value : $org->status;
        
        $step1Time = $org->created_at ? $org->created_at->translatedFormat('d M Y, H:i') . ' WITA' : '-';
        $step2Time = ($currentStatus !== 'pending' && $org->updated_at) 
            ? $org->updated_at->translatedFormat('d M Y, H:i') . ' WITA' 
            : 'Sedang Berlangsung';

        if ($currentStatus === 'approved') {
            $step2Desc = 'Pemeriksaan Selesai. Berkas dinyatakan sah dan disetujui oleh Admin DPM.';
        } elseif ($currentStatus === 'rejected') {
            $step2Desc = 'Pengajuan Ditolak. Berkas tidak memenuhi syarat. Catatan: ' . $org->pesan_penolakan;
        } else {
            $step2Desc = 'Berkas sedang dalam antrean review dan pemeriksaan validitas oleh Admin DPM.';
        }

        return [
            'step_1' => [
                'title' => 'Registrasi Diajukan',
                'time' => $step1Time,
                'desc' => 'Admin ' . $org->nama_organisasi . ' mengunggah berkas registrasi awal.'
            ],
            'step_2' => [
                'title' => $currentStatus === 'pending' ? 'Pemeriksaan Berkas' : ($currentStatus === 'approved' ? 'Disetujui' : 'Ditolak'),
                'time' => $step2Time,
                'desc' => $step2Desc
            ]
        ];
    }

    protected function parsePoinText(?string $text): array
    {
        if (empty($text)) return [];
        $textBersih = strip_tags(trim($text));
        $pattern = '/(?<=\s|^)(?:\d+[\.\)]+|\d+\s*[\)]+|\-|\*|•)\s+/';
        $split = preg_split($pattern, $textBersih, -1, PREG_SPLIT_NO_EMPTY);

        if (count($split) <= 1) {
            $splitLine = preg_split('/\r\n|\r|\n/', $textBersih, -1, PREG_SPLIT_NO_EMPTY);
            return count($splitLine) > 0 ? array_values(array_filter(array_map('trim', $splitLine))) : [trim($textBersih)];
        }
        return array_values(array_filter(array_map('trim', $split)));
    }
    
    protected function getFriendlyFileSize(?string $filename): string
    {
        if (empty($filename)) {
            return 'Tidak Ada File';
        }

        try {
            if (Storage::disk('public')->exists($filename)) {
                $bytes = Storage::disk('public')->size($filename);
                
                // Konversi bytes ke Megabytes (MB)
                $megabytes = round($bytes / 1024 / 1024, 1);
                
                if ($megabytes == 0) {
                    $kilobytes = round($bytes / 1024);
                    return $kilobytes . ' KB';
                }
                
                return $megabytes . ' MB';
            }
        } catch (\Exception $e) {
            return 'Terlampir';
        }

        return 'Terlampir';
    }


    public function confirmApprove(): void
    {
        $this->showApproveModal = true;
    }

    public function approve()
    {
        $org = $this->getFreshOrgQuery()->where('status', 'pending')->first();

        if (!$org) {
            $this->dispatch('show-toast', message: 'Aksi tidak sah atau organisasi sudah diproses.', type: 'error');
            return;
        }

        $org->update(['status' => OrganisasiStatus::APPROVED->value]);
        
        session()->flash('success', "Berkas organisasi berhasil diverifikasi dan disetujui!");
        return redirect()->route('admin.moderasi.organisasi');
    }

    public function confirmReject(): void
    {
        $this->pesanPenolakan = '';
        $this->showRejectModal = true;
    }

    public function reject()
    {
        $this->validate([
            'pesanPenolakan' => 'required|string|min:5|max:500',
        ], [
            'pesanPenolakan.required' => 'Alasan penolakan wajib diisi agar ormawa bisa memperbaikinya.',
            'pesanPenolakan.min'      => 'Alasan terlalu pendek, berikan ulasan minimal 5 karakter.',
            'pesanPenolakan.max'      => 'Alasan terlalu panjang, batasi maksimal 500 karakter.'
        ]);

        $pesanBersih = strip_tags(trim($this->pesanPenolakan));
        if (strlen($pesanBersih) < 5) {
            $this->addError('pesanPenolakan', 'Alasan tidak boleh berisi spasi kosong.');
            return;
        }

        $org = $this->getFreshOrgQuery()->where('status', 'pending')->first();

        if (!$org) {
            $this->dispatch('show-toast', message: 'Aksi tidak sah atau organisasi sudah diproses.', type: 'error');
            return;
        }

        $org->update([
            'status'          => OrganisasiStatus::REJECTED->value,
            'pesan_penolakan' => $pesanBersih,
        ]);

        session()->flash('success', "Pendaftaran berkas organisasi telah berhasil ditolak");
        return redirect()->route('admin.moderasi.organisasi');
    }

    public function closeModal(): void
    {
        $this->showApproveModal = false;
        $this->showRejectModal = false;
        $this->pesanPenolakan = '';
        $this->resetErrorBag();
    }

    #[Layout('layouts.admin', ['active' => 'moderasi-organisasi'])]
    public function render()
    {
        $orgData = OrganisasiMahasiswa::with(['user', 'fakultas', 'prodi'])->findOrFail($this->orgId);

        // Hitung total jumlah dokumen terlampir
        $jumlahDokumen = 0;
        if (!empty($orgData->ad_art)) $jumlahDokumen++;
        if (!empty($orgData->sk)) $jumlahDokumen++;

        return view('livewire.pages.admin.organisasi-detail', [
            'org' => $orgData,
            'ringkasan' => $this->calculateRingkasan($orgData),
            'timeline' => $this->generateRiwayatTimeline($orgData),
            'listVisi' => $this->parsePoinText($orgData->visi),
            'listMisi' => $this->parsePoinText($orgData->misi),
            
            'jumlah_dokumen' => $jumlahDokumen,
            'ad_art_size' => $this->getFriendlyFileSize($orgData->ad_art),
            'sk_size' => $this->getFriendlyFileSize($orgData->sk)
        ]);
    }
}