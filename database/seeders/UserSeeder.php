<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Enums\UserRole;
use App\Enums\OrganisasiStatus;
use App\Enums\TingkatOrganisasi;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID relasi yang dibutuhkan
        $idFmipa = Fakultas::where('nama_fakultas', 'Fakultas Matematika dan Ilmu Pengetahuan Alam')->first()->id;
        $idInformatika = Prodi::where('nama_prodi', 'Informatika')->first()->id;

        // 1. Akun Admin DPM (DPM FMIPA)
        $admin = User::firstOrCreate(
            ['email' => 'dpm.fmipa@univ.ac.id'],
            ['password' => Hash::make('password123'), 'role' => UserRole::ADMIN_DPM]
        );
        $admin->assignRole(UserRole::ADMIN_DPM->value);
        $admin->adminDpm()->firstOrCreate([
            'fakultas_id' => $idFmipa,
            'nama_admin' => 'Admin DPM FMIPA'
        ]);

        // 2. Akun Organisasi (Student Innovation Centre)
        $organisasi = User::firstOrCreate(
            ['email' => 'sic@univ.ac.id'],
            ['password' => Hash::make('password123'), 'role' => UserRole::ORGANISASI]
        );
        $organisasi->assignRole(UserRole::ORGANISASI->value);
        $organisasi->organisasi()->firstOrCreate(
            ['nama_organisasi' => 'Student Innovation Centre (SIC)'],
            [
                'no_organisasi' => '081234567890',
                'ig_url' => 'https://instagram.com/sic_udayana',
                'status' => OrganisasiStatus::APPROVED, // Langsung di-ACC agar bisa test buat event
                'tingkat_organisasi' => TingkatOrganisasi::PRODI,
                'fakultas_id' => $idFmipa,
                'prodi_id' => $idInformatika,
                
                // Kolom Teks Wajib (Tidak Nullable)
                'deskripsi' => 'Student Innovation Centre (SIC) adalah organisasi mahasiswa yang berfokus pada inovasi, pengembangan teknologi, dan peningkatan kompetensi teknis.',
                'visi' => 'Menjadi pusat inovasi yang adaptif dan mendorong anggotanya untuk selalu mau belajar serta berkembang di bidang teknologi.',
                'misi' => '1. Menyelenggarakan bootcamp intensif seperti SIC Guild. 2. Mengadakan STACK (SIC Tech Talk & Practice) sebagai wadah persiapan jalur karier dan rekrutmen.',
                
                // Kolom File Wajib (Tidak Nullable)
                'ad_art' => 'dokumen/ad_art_sic_2026.pdf',
                'sk' => 'dokumen/sk_kepengurusan_sic_2026.pdf',
            ]
        );

        // 3. Akun Mahasiswa
        $mahasiswa = User::firstOrCreate(
            ['email' => 'mahasiswa@student.univ.ac.id'],
            ['password' => Hash::make('password123'), 'role' => UserRole::MAHASISWA]
        );
        $mahasiswa->assignRole(UserRole::MAHASISWA->value);
        $mahasiswa->mahasiswa()->firstOrCreate([
            'prodi_id' => $idInformatika,
            'nama' => 'User Mahasiswa',
            'nim' => '2408561000'
        ]);
    }
}
