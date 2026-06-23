<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Fakultas extends Model
{
    protected $table = 'fakultas';
    protected $fillable = ['nama_fakultas'];

    public function prodi() { return $this->hasMany(Prodi::class); }
    public function organisasi() { return $this->hasMany(OrganisasiMahasiswa::class); }
    public function adminDpm() { return $this->hasMany(AdminDpm::class); }
}
