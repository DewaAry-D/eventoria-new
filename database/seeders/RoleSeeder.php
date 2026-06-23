<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Enums\UserRole;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat role menggunakan enum yang sudah kita definisikan
        Role::firstOrCreate(['name' => UserRole::MAHASISWA->value]);
        Role::firstOrCreate(['name' => UserRole::ORGANISASI->value]);
        Role::firstOrCreate(['name' => UserRole::ADMIN_DPM->value]);
    }
}
