<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\OrganisasiStatus;
use App\Enums\TingkatOrganisasi;

class OrganisasiMahasiswa extends Model
{
    protected $table = 'organisasi_mahasiswa';
    protected $fillable = [
        'user_id', 'nama_organisasi', 'no_organisasi', 'ig_url', 
        'linkedin_url', 'logo_url', 'status', 'tingkat_organisasi', 
        'fakultas_id', 'prodi_id', 'pesan_penolakan', 'deskripsi', 'visi', 'misi','ad_art', 'sk'
    ];

    protected function casts(): array
    {
        return [
            'status' => OrganisasiStatus::class,
            'tingkat_organisasi' => TingkatOrganisasi::class,
        ];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function fakultas() { return $this->belongsTo(Fakultas::class); }
    public function prodi() { return $this->belongsTo(Prodi::class); }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'organisasi_id');
    }
}