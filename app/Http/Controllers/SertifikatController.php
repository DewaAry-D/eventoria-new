<?php

namespace App\Http\Controllers;

use App\Enums\RegistrationStatus;
use App\Models\EventRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SertifikatController extends Controller
{
    // ---------------------------------------------------------------
    // HELPER PRIVATE: validasi akses sertifikat
    // Dipanggil oleh download() dan downloadJpg() agar tidak duplikasi
    // logika yang sama di dua tempat (prinsip DRY - Don't Repeat Yourself)
    // ---------------------------------------------------------------
    private function getValidatedRegistration(int $registration_id): EventRegistration
    {
        $registration = EventRegistration::with([
            'event.templateSertifikat',
            'mahasiswa',
        ])->findOrFail($registration_id);

        // Guard 1: sertifikat harus milik mahasiswa yang sedang login
        $mahasiswaId = Auth::user()->mahasiswa->id ?? null;
        if ($registration->mahasiswa_id !== $mahasiswaId) {
            abort(403, 'Akses ditolak: sertifikat ini bukan milik Anda.');
        }

        // Guard 2: status harus completed — panitia yang menentukan ini, bukan mahasiswa
        if ($registration->status_pendaftaran !== RegistrationStatus::COMPLETED) {
            abort(403, 'Sertifikat belum tersedia. Acara belum selesai atau belum dikonfirmasi panitia.');
        }

        // Guard 3: panitia harus sudah upload template
        if (! $registration->event->templateSertifikat?->file_template) {
            abort(404, 'Template sertifikat belum disiapkan oleh panitia.');
        }

        return $registration;
    }

    // ---------------------------------------------------------------
    // Download PDF
    // Generate PDF secara real-time di server menggunakan dompdf.
    // Nama dan data sertifikat diambil dari database (server-controlled),
    // mahasiswa tidak bisa memanipulasi isinya.
    // ---------------------------------------------------------------
    public function download(Request $request, int $registration_id)
    {
        $registration = $this->getValidatedRegistration($registration_id);
        $template     = $registration->event->templateSertifikat;

        // Encode gambar template ke base64 agar bisa diembed di HTML
        // dompdf tidak bisa mengakses URL relatif, harus base64 atau path absolut
        $templatePath = public_path('storage/' . $template->file_template);
        $imageBase64  = '';

        if (file_exists($templatePath)) {
            $imageBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($templatePath));
        }

        // Semua data sertifikat berasal dari database, bukan dari input user
        $pdf = Pdf::loadView('pdf.sertifikat', [
            'namaCetak'     => $registration->nama_cetak_sertifikat ?? $registration->mahasiswa->nama,
            'namaEvent'     => $registration->event->nama_event,
            'penyelenggara' => $registration->event->penyelenggara,
            'imageBase64'   => $imageBase64,
            'posisiX'       => $template->posisi_x ?? 500,
            'posisiY'       => $template->posisi_y ?? 420,
            'jenisFont'     => $template->jenis_font ?? 'Arial',
            'ukuranFont'    => $template->ukuran_font ?? 48,
            'warnaFont'     => $template->warna_font ?? '#1e1b4b',
        ])->setPaper('A4', 'landscape');

        $namaFile = 'Sertifikat-'
            . str_replace(' ', '-', $registration->event->nama_event) . '-'
            . str_replace(' ', '-', $registration->nama_cetak_sertifikat ?? $registration->mahasiswa->nama)
            . '.pdf';

        return $pdf->download($namaFile);
    }

    // ---------------------------------------------------------------
    // Download JPG
    // Mengirim file gambar template asli yang sudah diupload panitia.
    // Ini adalah file yang tersimpan di server (public/storage/sertifikat-templates/),
    // bukan file yang bisa dimanipulasi mahasiswa dari browser.
    // ---------------------------------------------------------------
    public function downloadJpg(Request $request, int $registration_id)
    {
        $registration = $this->getValidatedRegistration($registration_id);
        $template     = $registration->event->templateSertifikat;

        $templatePath = public_path('storage/' . $template->file_template);

        if (! file_exists($templatePath)) {
            abort(404, 'File template tidak ditemukan di server.');
        }

        $namaFile = 'Sertifikat-'
            . str_replace(' ', '-', $registration->event->nama_event) . '-'
            . str_replace(' ', '-', $registration->nama_cetak_sertifikat ?? $registration->mahasiswa->nama)
            . '.jpg';

        return response()->download($templatePath, $namaFile, [
            'Content-Type' => 'image/jpeg',
        ]);
    }
}