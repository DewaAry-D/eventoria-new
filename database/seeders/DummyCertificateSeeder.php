<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\TemplateSertifikat;
use App\Models\TimeLine;
use App\Enums\EventStatus;
use App\Enums\TingkatEvent;
use App\Enums\RegistrationStatus;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DummyCertificateSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Mahasiswa dan Organisasi dari seeder sebelumnya
        $userMahasiswa = User::where('email', 'mahasiswa@student.univ.ac.id')->first();
        $userOrganisasi = User::where('email', 'sic@univ.ac.id')->first();

        if (!$userMahasiswa || !$userOrganisasi) {
            $this->command->error('Silakan jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $mahasiswaId = $userMahasiswa->mahasiswa->id;
        $organisasiId = $userOrganisasi->organisasi->id;
        $kategoriId = 1; // Asumsi ada kategori ID 1 (misal Seminar/Workshop)

        // 2. Buat Event untuk Tahun 2025
        $event2025 = Event::create([
            'kategori_id' => $kategoriId,
            'organisasi_id' => $organisasiId,
            'nama_event' => 'Webinar Digital Marketing 2025',
            'slug' => Str::slug('Webinar Digital Marketing 2025'),
            'penyelenggara' => 'Student Innovation Centre (SIC)',
            'status' => EventStatus::PUBLISHED, // DB enum belum punya 'completed', pakai 'published'
            'deskripsi' => 'Webinar strategi digital marketing terkini.',
            'nama_lokasi' => 'Zoom Meeting',
            'kuota' => 100,
            'sisa_kuota' => 50,
            'flyer_url' => 'dummy/flyer_2025.jpg',
            'tingkat_event' => TingkatEvent::PRODI,
            'created_at' => Carbon::create(2025, 10, 12, 10, 0, 0),
            'updated_at' => Carbon::create(2025, 10, 12, 10, 0, 0),
        ]);

        TimeLine::create([
            'event_id' => $event2025->id,
            'nama_timeline' => 'Pelaksanaan Acara',
            'tanggal_mulai' => Carbon::create(2025, 10, 12, 8, 0, 0),
            'tanggal_selesai' => Carbon::create(2025, 10, 12, 12, 0, 0),
        ]);

        // 3. Buat Event untuk Tahun 2026
        $event2026 = Event::create([
            'kategori_id' => $kategoriId,
            'organisasi_id' => $organisasiId,
            'nama_event' => 'Data Science Boot Camp Series',
            'slug' => Str::slug('Data Science Boot Camp Series'),
            'penyelenggara' => 'Student Innovation Centre (SIC)',
            'status' => EventStatus::PUBLISHED, // Gunakan PUBLISHED karena tabel DB belum punya nilai 'completed'
            'deskripsi' => 'Bootcamp intensif untuk belajar Data Science dari nol.',
            'nama_lokasi' => 'Gedung Fakultas MIPA',
            'kuota' => 50,
            'sisa_kuota' => 10,
            'flyer_url' => 'dummy/flyer_2026.jpg',
            'tingkat_event' => TingkatEvent::FAKULTAS,
            'created_at' => Carbon::create(2026, 2, 20, 9, 0, 0),
            'updated_at' => Carbon::create(2026, 2, 20, 9, 0, 0),
        ]);

        TimeLine::create([
            'event_id' => $event2026->id,
            'nama_timeline' => 'Pelaksanaan Acara',
            'tanggal_mulai' => Carbon::create(2026, 2, 20, 9, 0, 0),
            'tanggal_selesai' => Carbon::create(2026, 2, 22, 15, 0, 0),
        ]);

        // 4. Daftarkan Mahasiswa ke Event 2025 dan jadikan status COMPLETED
        EventRegistration::create([
            'mahasiswa_id' => $mahasiswaId,
            'event_id' => $event2025->id,
            'waktu_daftar' => Carbon::create(2025, 9, 10, 14, 0, 0),
            'status_pendaftaran' => RegistrationStatus::COMPLETED,
            'nama_cetak_sertifikat' => $userMahasiswa->mahasiswa->nama,
            'created_at' => Carbon::create(2025, 10, 12, 12, 30, 0), // Tanggal sertifikat
            'updated_at' => Carbon::create(2025, 10, 12, 12, 30, 0),
        ]);

        // 5. Daftarkan Mahasiswa ke Event 2026 dan jadikan status COMPLETED
        EventRegistration::create([
            'mahasiswa_id' => $mahasiswaId,
            'event_id' => $event2026->id,
            'waktu_daftar' => Carbon::create(2026, 1, 15, 9, 0, 0),
            'status_pendaftaran' => RegistrationStatus::COMPLETED,
            'nama_cetak_sertifikat' => $userMahasiswa->mahasiswa->nama,
            'created_at' => Carbon::create(2026, 2, 20, 15, 30, 0), // Tanggal sertifikat
            'updated_at' => Carbon::create(2026, 2, 20, 15, 30, 0),
        ]);

        $this->command->info('Dummy sertifikat tahun 2025 dan 2026 berhasil dibuat!');
    }
}
