<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    protected $table = 'prodi';
    protected $fillable = ['fakultas_id', 'nama_prodi'];

    public function fakultas() { return $this->belongsTo(Fakultas::class); }
    public function mahasiswa() { return $this->hasMany(Mahasiswa::class); }
    public function organisasi() { return $this->hasMany(OrganisasiMahasiswa::class); }
}