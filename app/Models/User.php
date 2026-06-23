<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Enums\UserRole;

class User extends Authenticatable
{
    use Notifiable, HasRoles; 

    protected $fillable = ['email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class, 
        ];
    }

    public function mahasiswa() { return $this->hasOne(Mahasiswa::class); }
    public function organisasi() { return $this->hasOne(OrganisasiMahasiswa::class); }
    public function adminDpm() { return $this->hasOne(AdminDpm::class); }
}