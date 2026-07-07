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
        // Data Fakultas
        $fmipa = Fakultas::firstOrCreate(['nama_fakultas' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam']);
        $fteknik = Fakultas::firstOrCreate(['nama_fakultas' => 'Fakultas Teknik']);
        $fdokter = Fakultas::firstOrCreate(['nama_fakultas' => 'Fakultas Kedokteran']);

        // Data Program Studi
        Prodi::firstOrCreate(['fakultas_id' => $fmipa->id, 'nama_prodi' => 'Informatika']);
        Prodi::firstOrCreate(['fakultas_id' => $fmipa->id, 'nama_prodi' => 'Matematika']);
        Prodi::firstOrCreate(['fakultas_id' => $fteknik->id, 'nama_prodi' => 'Teknik Sipil']);
        Prodi::firstOrCreate(['fakultas_id' => $fteknik->id, 'nama_prodi' => 'Teknik Elektro']);
        Prodi::firstOrCreate(['fakultas_id' => $fdokter->id, 'nama_prodi' => 'Psikologi']);

        // Data Kategori Event
        Kategori::firstOrCreate(['nama_kategori' => 'Bootcamp']);
        Kategori::firstOrCreate(['nama_kategori' => 'Seminar']);
        Kategori::firstOrCreate(['nama_kategori' => 'Workshop']);
    }
}