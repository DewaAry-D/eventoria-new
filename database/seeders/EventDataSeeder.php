<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\OrganisasiMahasiswa;
use App\Models\TemplateSertifikat;
use App\Models\TimeLine;
use App\Models\BiayaEvent;
use App\Models\TujuanTransfer;
use App\Models\EventFormField;
use App\Models\EventRegistration;
use App\Models\EventFormResponse;
use App\Models\Mahasiswa;
use App\Models\Kategori;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EventDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Dependensi ID Master
        $sic = OrganisasiMahasiswa::query()->where('nama_organisasi', 'Student Innovation Centre')->first()->id;
        $himatika = OrganisasiMahasiswa::query()->where('nama_organisasi', 'Himpunan Mahasiswa Matematika')->first()->id;
        $himaif = OrganisasiMahasiswa::query()->where('nama_organisasi', 'Himpunan Mahasiswa Informatika')->first()->id;
        $ukmSeni = OrganisasiMahasiswa::query()->where('nama_organisasi', 'UKM Seni Budaya Universitas')->first()->id;

        $mhs1 = Mahasiswa::query()->where('nim', '2408561000')->first()->id;
        $mhs2 = Mahasiswa::query()->where('nim', '2408561088')->first()->id;

        $bootcamp = Kategori::query()->where('nama_kategori', 'Bootcamp')->first()->id;
        $seminar = Kategori::query()->where('nama_kategori', 'Seminar')->first()->id;
        $workshop = Kategori::query()->where('nama_kategori', 'Workshop')->first()->id;

        $now = Carbon::create(2026, 7, 6, 12, 0, 0);

        // Kumpulan nama narasumber
        $daftarNarasumber = [
            'Dr. Eng. I Putu Gede Suarta, S.Kom., M.T.',
            'Ni Wayan Prasanti, M.Sc. (Senior UX Researcher GoTo)',
            'Gede Rama Wijaya, B.Sc. (DevOps Engineer at CloudKilat)',
            'Made Dwi Putra Sanjaya, S.Kom. (AI Engineering Lead)',
            'Prof. Ketut Darmawan, Ph.D. (Pakar Data Analytics)',
            'Anak Agung Istri Cynthia, M.A. (Digital Marketer Specialist)',
            'I Kadek Satria Wibawa, S.T. (Senior Backend Developer)',
            'Putu Ayu Saraswati, S.Ds. (Product Illustrator Specialist)'
        ];

        // =========================================================================
        // KATEGORI A: 13 EVENT STATUS PUBLISHED (Masa Pendaftaran Aktif)
        // =========================================================================
        for ($i = 1; $i <= 13; $i++) {
            $daysOffset = $i * 10;
            
            $isOnline = ($i % 4 == 0);
            $namaLokasi = $isOnline ? 'Online via Zoom Meeting' : 'Gedung Rektorat Universitas Udayana Kampus Bukit Jimbaran';
            
            $lokasiUrl = null;
            $lokasiUrl = $isOnline ? 'https://zoom.us/j/999888777666' : 'https://www.google.com/maps/place/Bakmi+Akiu+Barito/@-8.681323,115.2342554,15z/data=!4m6!3m5!1s0x2dd24146079224eb:0xe475df55825e59b5!8m2!3d-8.6840893!4d115.2346304!16s%2Fg%2F11vbttzn03?entry=ttu&g_ep=EgoyMDI2MDYyMy4wIKXMDSoASAFQAw%3D%3D';
            

            $e = Event::create([
                'kategori_id' => ($i % 2 == 0) ? $workshop : $seminar,
                'organisasi_id' => ($i % 3 == 0) ? $himatika : $sic,
                'nama_event' => "Eksplorasi Teknologi Inovatif Vol " . $i,
                'slug' => Str::slug("Eksplorasi Teknologi Inovatif Vol " . $i),
                'penyelenggara' => ($i % 3 == 0) ? 'HIMATIKA FMIPA' : 'Student Innovation Centre',
                'status' => 'published',
                'deskripsi' => 'Pengembangan kompetensi digital mahasiswa guna menyambut era transformasi teknologi informasi global secara inklusif.',
                'nama_lokasi' => $namaLokasi,
                'lokasi_url' => $lokasiUrl,
                'kuota' => 120,
                'sisa_kuota' => 90,
                'narasumber' => $daftarNarasumber[$i % count($daftarNarasumber)],
                'flyer_url' => 'flyers/night.webp',
                'tingkat_event' => 'fakultas'
            ]);

            // Pembuatan Siklus Makro Timeline Administrasi
            TimeLine::create(['event_id' => $e->id, 'nama_timeline' => 'Pendaftaran Utama', 'tanggal_mulai' => (clone $now)->addDays($daysOffset), 'tanggal_selesai' => (clone $now)->addDays($daysOffset + 5)]);
            TimeLine::create(['event_id' => $e->id, 'nama_timeline' => 'Extended Pendaftaran', 'tanggal_mulai' => (clone $now)->addDays($daysOffset + 6), 'tanggal_selesai' => (clone $now)->addDays($daysOffset + 9)]);
            TimeLine::create(['event_id' => $e->id, 'nama_timeline' => 'Day 1 Pelaksanaan', 'tanggal_mulai' => (clone $now)->addDays($daysOffset + 10), 'tanggal_selesai' => (clone $now)->addDays($daysOffset + 10)]);
            TimeLine::create(['event_id' => $e->id, 'nama_timeline' => 'Day 2 Evaluasi', 'tanggal_mulai' => (clone $now)->addDays($daysOffset + 11), 'tanggal_selesai' => (clone $now)->addDays($daysOffset + 11)]);

            EventFormField::create(['event_id' => $e->id, 'nama_field' => 'Nomor WhatsApp Aktif', 'tipe_field' => 'number', 'is_required' => 1]);
            EventFormField::create(['event_id' => $e->id, 'nama_field' => 'Instansi / Asal Universitas', 'tipe_field' => 'text', 'is_required' => 1]);
            EventFormField::create(['event_id' => $e->id, 'nama_field' => 'Unggah Bukti Kartu Tanda Mahasiswa (KTM)', 'tipe_field' => 'file_image', 'is_required' => 0]);

            // Pengisian Multi-HTM dan Multi-Transfer
            if ($i % 2 == 0) {
                BiayaEvent::create(['event_id' => $e->id, 'kategori' => 'HTM Reguler Kampus', 'biaya' => 25000]);
                BiayaEvent::create(['event_id' => $e->id, 'kategori' => 'HTM VIP Akses Eksklusif', 'biaya' => 75000]);

                TujuanTransfer::create(['event_id' => $e->id, 'nama_bank' => 'Bank BCA', 'no_rekening' => '8829012234', 'atas_nama' => 'Bendahara Umum Eventoria']);
                TujuanTransfer::create(['event_id' => $e->id, 'nama_bank' => 'Bank Mandiri', 'no_rekening' => '1450012345678', 'atas_nama' => 'Dana Operasional Ormawa']);
            }
        }

        // =========================================================================
        // KATEGORI B: 3 EVENT STATUS REJECTED / REVISION
        // =========================================================================
        for ($j = 1; $j <= 3; $j++) {
            $e = Event::create([
                'kategori_id' => $workshop,
                'organisasi_id' => $himaif,
                'nama_event' => "Evaluasi Sistem Keamanan Siber Part " . $j,
                'slug' => Str::slug("Evaluasi Sistem Keamanan Siber Part " . $j),
                'penyelenggara' => 'HIMAIF',
                'status' => 'revision',
                'deskripsi' => 'Peninjauan berkas simulasi penetrasi keamanan jaringan lokal.',
                'nama_lokasi' => 'Gedung Lab Komputer AB FMIPA Kampus Bukit',
                'lokasi_url' => 'https://www.google.com/maps/place/Bakmi+Akiu+Barito/@-8.681323,115.2342554,15z/data=!4m6!3m5!1s0x2dd24146079224eb:0xe475df55825e59b5!8m2!3d-8.6840893!4d115.2346304!16s%2Fg%2F11vbttzn03?entry=ttu&g_ep=EgoyMDI2MDYyMy4wIKXMDSoASAFQAw%3D%3D',
                'kuota' => 30,
                'sisa_kuota' => 30,
                'narasumber' => $daftarNarasumber[($j + 2) % count($daftarNarasumber)],
                'catatan_revisi' => 'Berkas proposal belum ditandatangani oleh ketua ormawa pelaksana. Mohon unggah ulang lampiran SK kepengurusan terbaru yang sah.',
                'flyer_url' => 'flyers/cyber.webp',
                'tingkat_event' => 'prodi'
            ]);

            TimeLine::create(['event_id' => $e->id, 'nama_timeline' => 'Pendaftaran Internal', 'tanggal_mulai' => (clone $now)->addDays($j * 5), 'tanggal_selesai' => (clone $now)->addDays(($j * 5) + 3)]);
            
            // Minimal 3 Form Field
            EventFormField::create(['event_id' => $e->id, 'nama_field' => 'IP Address Mesin Lokal', 'tipe_field' => 'text', 'is_required' => 1]);
            EventFormField::create(['event_id' => $e->id, 'nama_field' => 'Sistem Operasi yang Digunakan', 'tipe_field' => 'text', 'is_required' => 1]);
            EventFormField::create(['event_id' => $e->id, 'nama_field' => 'Unggah File Portofolio Penetrasi', 'tipe_field' => 'file_pdf', 'is_required' => 0]);
        }

        // =========================================================================
        // KATEGORI C: 3 EVENT STATUS PENDING APPROVAL
        // =========================================================================
        for ($k = 1; $k <= 3; $k++) {
            $e = Event::create([
                'kategori_id' => $bootcamp,
                'organisasi_id' => $himatika,
                'nama_event' => "Pengolahan Big Data Statistika Angkatan " . $k,
                'slug' => Str::slug("Pengolahan Big Data Statistika Angkatan " . $k),
                'penyelenggara' => 'HIMATIKA FMIPA',
                'status' => 'pending_approval',
                'deskripsi' => 'Analisis data spasial dengan performa komputasi tinggi terpadu.',
                'nama_lokasi' => 'Gedung Aula BG FMIPA Kampus Bukit',
                'lokasi_url' => 'https://www.google.com/maps/place/Bakmi+Akiu+Barito/@-8.681323,115.2342554,15z/data=!4m6!3m5!1s0x2dd24146079224eb:0xe475df55825e59b5!8m2!3d-8.6840893!4d115.2346304!16s%2Fg%2F11vbttzn03?entry=ttu&g_ep=EgoyMDI2MDYyMy4wIKXMDSoASAFQAw%3D%3D',
                'kuota' => 50,
                'sisa_kuota' => 50,
                'narasumber' => $daftarNarasumber[($k + 4) % count($daftarNarasumber)],
                'flyer_url' => 'flyers/bootcamp.webp',
                'tingkat_event' => 'universitas'
            ]);

            TimeLine::create(['event_id' => $e->id, 'nama_timeline' => 'Pendaftaran Utama', 'tanggal_mulai' => (clone $now)->addDays($k * 4), 'tanggal_selesai' => (clone $now)->addDays(($k * 4) + 2)]);
            
            EventFormField::create(['event_id' => $k, 'nama_field' => 'Nilai IPK Terakhir', 'tipe_field' => 'number', 'is_required' => 1]);
            EventFormField::create(['event_id' => $k, 'nama_field' => 'Alasan Mengikuti Data Science', 'tipe_field' => 'textarea', 'is_required' => 1]);
            EventFormField::create(['event_id' => $k, 'nama_field' => 'Unggah Bukti Transkrip Nilai (.pdf)', 'tipe_field' => 'file_pdf', 'is_required' => 1]);
        }

        // =========================================================================
        // KATEGORI D: 4 EVENT STATUS COMPLETED
        // =========================================================================
        $completedEvents = [
            'Webinar Digital Marketing Modern 2025' => 'sertifikat-templates/sertif_2025.webp',
            'Data Science Boot Camp Series 2026' => 'sertifikat-templates/sertif_2026.webp',
            'UI/UX Design Fundamental Mastery' => 'sertifikat-templates/PESERTA.png',
            'DevOps Deployment Automation Systems' => 'sertifikat-templates/ut16fMJjr0ux0jSoSuYBZRfEUWKO1zqkB1AnB9Kr.png',
        ];

        $idx = 1;
        foreach ($completedEvents as $name => $template) {
            $e = Event::create([
                'kategori_id' => $bootcamp,
                'organisasi_id' => $sic,
                'nama_event' => $name,
                'slug' => Str::slug($name),
                'penyelenggara' => 'Student Innovation Centre (SIC)',
                'status' => 'completed',
                'deskripsi' => 'Event pelatihan yang telah selesai dilaksanakan secara sukses dan kredibel bagi seluruh mahasiswa.',
                'nama_lokasi' => 'Gedung Aula AB FMIPA Kampus Bukit Jimbaran',
                'lokasi_url' => 'https://www.google.com/maps/place/Bakmi+Akiu+Barito/@-8.681323,115.2342554,15z/data=!4m6!3m5!1s0x2dd24146079224eb:0xe475df55825e59b5!8m2!3d-8.6840893!4d115.2346304!16s%2Fg%2F11vbttzn03?entry=ttu&g_ep=EgoyMDI2MDYyMy4wIKXMDSoASAFQAw%3D%3D',
                'kuota' => 50,
                'sisa_kuota' => 0,
                'narasumber' => $daftarNarasumber[($idx + 1) % count($daftarNarasumber)],
                'flyer_url' => 'flyers/flyer_2025.webp',
                'tingkat_event' => 'fakultas'
            ]);

            TimeLine::create(['event_id' => $e->id, 'nama_timeline' => 'Pendaftaran Resmi', 'tanggal_mulai' => (clone $now)->subMonths($idx + 1), 'tanggal_selesai' => (clone $now)->subMonths($idx)->subDays(15)]);
            TimeLine::create(['event_id' => $e->id, 'nama_timeline' => 'Day 1 Pelaksanaan', 'tanggal_mulai' => (clone $now)->subMonths($idx)->subDays(5), 'tanggal_selesai' => (clone $now)->subMonths($idx)->subDays(5)]);

            $f1 = EventFormField::create(['event_id' => $e->id, 'nama_field' => 'Pertanyaan Kuesioner Evaluasi', 'tipe_field' => 'textarea', 'is_required' => 1]);
            $f2 = EventFormField::create(['event_id' => $e->id, 'nama_field' => 'Ekspektasi Karir', 'tipe_field' => 'text', 'is_required' => 1]);
            $f3 = EventFormField::create(['event_id' => $e->id, 'nama_field' => 'Portofolio Akhir Pelatihan', 'tipe_field' => 'url', 'is_required' => 0]);

            // Setup Lembar Sertifikat Builder
            TemplateSertifikat::create([
                'event_id' => $e->id,
                'file_template' => $template,
                'posisi_x' => 50,
                'posisi_y' => 45,
                'jenis_font' => 'Poppins',
                'ukuran_font' => 32,
                'warna_font' => '#ffffff'
            ]);

            // Hubungkan data kelulusan mahasiswa ke basis data
            $reg = EventRegistration::create([
                'mahasiswa_id' => ($idx % 2 == 0) ? $mhs2 : $mhs1,
                'event_id' => $e->id,
                'waktu_daftar' => (clone $now)->subMonths($idx + 1),
                'status_pendaftaran' => 'completed',
                'nama_cetak_sertifikat' => 'I Kadek Yogiarta Adi Winata'
            ]);

            // Form Responses Dinamis Wajib Data Terisi untuk Record Completed
            EventFormResponse::create(['registration_id' => $reg->id, 'field_id' => $f1->id, 'jawaban' => 'Materi pemateri sangat luar biasa padat dan mendalam.']);
            EventFormResponse::create(['registration_id' => $reg->id, 'field_id' => $f2->id, 'jawaban' => 'Menjadi Junior Developer.']);

            $idx++;
        }

        $this->command->info('Sukses! Seluruh 23 Event berhasil terintegrasi ke dalam basis data.');
    }
}