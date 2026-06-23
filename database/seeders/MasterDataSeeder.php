<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\Kategori;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Fakultas
        $fmipa = Fakultas::firstOrCreate(['nama_fakultas' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam']);
        $fteknik = Fakultas::firstOrCreate(['nama_fakultas' => 'Fakultas Teknik']);

        // 2. Data Prodi
        Prodi::firstOrCreate(['fakultas_id' => $fmipa->id, 'nama_prodi' => 'Informatika']);
        Prodi::firstOrCreate(['fakultas_id' => $fmipa->id, 'nama_prodi' => 'Matematika']);
        Prodi::firstOrCreate(['fakultas_id' => $fteknik->id, 'nama_prodi' => 'Teknik Sipil']);

        // 3. Data Kategori Event
        Kategori::firstOrCreate(['nama_kategori' => 'Bootcamp & Pelatihan']);
        Kategori::firstOrCreate(['nama_kategori' => 'Seminar Nasional']);
        Kategori::firstOrCreate(['nama_kategori' => 'Workshop Teknologi']);
    }
}