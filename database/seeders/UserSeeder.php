<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $idFmipa = Fakultas::query()->where('nama_fakultas', 'Fakultas Matematika dan Ilmu Pengetahuan Alam')->first()->id;
        $idTeknik = Fakultas::query()->where('nama_fakultas', 'Fakultas Teknik')->first()->id;
        
        $idInformatika = Prodi::query()->where('nama_prodi', 'Informatika')->first()->id;
        $idMatematika = Prodi::query()->where('nama_prodi', 'Matematika')->first()->id;
        $idSipil = Prodi::query()->where('nama_prodi', 'Teknik Sipil')->first()->id;

        // ACTOR 1: ADMIN DPM
        $dpms = [
            ['email' => 'dpm.fmipa@univ.ac.id', 'nama' => 'DPM FMIPA', 'fakultas' => $idFmipa],
            ['email' => 'dpm.teknik@univ.ac.id', 'nama' => 'Admin DPM Fakultas Teknik', 'fakultas' => $idTeknik],
            ['email' => 'admin.univ@univ.ac.id', 'nama' => 'Admin Universitas', 'fakultas' => null],
        ];
        foreach ($dpms as $d) {
            $u = User::firstOrCreate(['email' => $d['email']], ['password' => Hash::make('password123'), 'role' => 'admin_dpm']);
            $u->assignRole('admin_dpm');
            $u->adminDpm()->firstOrCreate(['nama_admin' => $d['nama'], 'fakultas_id' => $d['fakultas']]);
        }

        // ACTOR 2: ORGANISASI MAHASISWA
        $ormawas = [
            ['email' => 'sic@univ.ac.id', 'nama' => 'Student Innovation Centre', 'tingkat' => 'fakultas', 'fakultas' => $idFmipa, 'prodi' => $idInformatika, 'ig_url' => 'https://www.instagram.com/sic.unud/', 'linkedin_url' => 'https://www.linkedin.com/company/student-innovation-centre/', 'logo' => 'logo/sic.webp', 'ad' => 'dokumen/ad-art/ad_art_sic_2026.pdf', 'sk' => 'dokumen/sk/sk_kepengurusan_sic_2026.pdf'],
            ['email' => 'himatika@student.univ.ac.id', 'nama' => 'Himpunan Mahasiswa Matematika', 'tingkat' => 'prodi', 'fakultas' => $idFmipa, 'prodi' => $idMatematika, 'ig_url' => 'https://www.instagram.com/sic.unud/', 'linkedin_url' => 'https://www.linkedin.com/company/student-innovation-centre/', 'logo' => 'logo/himatika.webp', 'ad' => 'dokumen/ad-art/ad_art_himatika.pdf', 'sk' => 'dokumen/sk/sk_himatika.pdf'],
            ['email' => 'himaif@student.univ.ac.id', 'nama' => 'Himpunan Mahasiswa Informatika', 'tingkat' => 'prodi', 'fakultas' => $idFmipa, 'prodi' => $idInformatika, 'ig_url' => 'https://www.instagram.com/sic.unud/', 'linkedin_url' => 'https://www.linkedin.com/company/student-innovation-centre/', 'logo' => 'logo/himaif.webp', 'ad' => 'dokumen/ad-art/ad_art_himaif.pdf', 'sk' => 'dokumen/sk/sk_himaif.pdf'],
            ['email' => 'hmsi@student.univ.ac.id', 'nama' => 'Himpunan Mahasiswa Sistem Informasi', 'tingkat' => 'prodi', 'fakultas' => $idFmipa, 'prodi' => $idInformatika, 'ig_url' => 'https://www.instagram.com/sic.unud/', 'linkedin_url' => 'https://www.linkedin.com/company/student-innovation-centre/', 'logo' => 'logo/hmsi.webp', 'ad' => 'dokumen/ad-art/ad_art_hmsi.pdf', 'sk' => 'dokumen/sk/sk_hmsi.pdf'],
            ['email' => 'ukm.seni@univ.ac.id', 'nama' => 'UKM Seni Budaya Universitas', 'tingkat' => 'universitas', 'fakultas' => null, 'prodi' => null, 'ig_url' => 'https://www.instagram.com/sic.unud/', 'linkedin_url' => 'https://www.linkedin.com/company/student-innovation-centre/', 'logo' => 'logo/ukmseni.webp', 'ad' => 'dokumen/ad-art/ad_art_seni.pdf', 'sk' => 'dokumen/sk/sk_seni.pdf'],
        ];
        foreach ($ormawas as $o) {
            $u = User::firstOrCreate(['email' => $o['email']], ['password' => Hash::make('password123'), 'role' => 'organisasi']);
            $u->assignRole('organisasi');
            $u->organisasi()->firstOrCreate(['nama_organisasi' => $o['nama']], [
                'no_organisasi' => '081234567890',
                'status' => 'approved',
                'tingkat_organisasi' => $o['tingkat'],
                'fakultas_id' => $o['fakultas'],
                'prodi_id' => $o['prodi'],
                'ig_url' => $o['ig_url'],
                'linkedin_url' => $o['linkedin_url'],
                'logo_url' => $o['logo'],
                'deskripsi' => 'Deskripsi profil organisasi kerja mahasiswa intra kampus ' . $o['nama'],
                'visi' => 'Visi organisasi utama ' . $o['nama'],
                'misi' => 'Misi organisasi utama ' . $o['nama'],
                'ad_art' => $o['ad'],
                'sk' => $o['sk']
            ]);
        }

        // ACTOR 3: MAHASISWA
        $students = [
            ['email' => 'mahasiswa@student.univ.ac.id', 'nama' => 'User Mahasiswa', 'nim' => '2408561000', 'prodi' => $idInformatika],
            ['email' => 'test@gmail.com', 'nama' => 'Untuk Testing', 'nim' => '2408561088', 'prodi' => $idMatematika],
            ['email' => 'mangde@gmail.com', 'nama' => 'i dewa mangde', 'nim' => '2408561090', 'prodi' => $idInformatika],
            ['email' => 'mahasiswa.teknik@student.univ.ac.id', 'nama' => 'Mighty', 'nim' => '2405551099', 'prodi' => $idSipil],
            ['email' => 'madetest@student.ac.id', 'nama' => 'Made Test Skenario', 'nim' => '2323843394', 'prodi' => $idInformatika],
        ];
        foreach ($students as $s) {
            $u = User::firstOrCreate(['email' => $s['email']], ['password' => Hash::make('password123'), 'role' => 'mahasiswa']);
            $u->assignRole('mahasiswa');
            $u->mahasiswa()->firstOrCreate(['nim' => $s['nim']], ['prodi_id' => $s['prodi'], 'nama' => $s['nama']]);
        }
    }
}