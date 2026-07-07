<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'mahasiswa', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'organisasi', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin_dpm', 'guard_name' => 'web']);
    }
}