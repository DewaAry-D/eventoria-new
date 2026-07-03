<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    public function adminName(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('adminDpm') && $this->adminDpm) {
                    return $this->adminDpm->nama_admin;
                }

                return $this->adminDpm?->nama_admin 
                    ?? $this->name 
                    ?? 'Administrator';
            }
        );
    }

    public function roleLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $roleRaw = $this->attributes['role'] ?? null; 

                return match ($roleRaw) {
                    'admin_dpm'  => 'Admin DPM',
                    'organisasi' => 'Admin Ormawa',
                    'mahasiswa'  => 'Mahasiswa',
                    default      => 'Operator Sistem',
                };
            }
        );
    }
}