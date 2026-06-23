<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\RegistrationStatus;

class EventRegistration extends Model
{
    protected $table = 'event_registrations';
    
    protected $fillable = [
        'mahasiswa_id', 'event_id', 'waktu_daftar', 
        'status_pendaftaran', 'nama_cetak_sertifikat', 'catatan_penolakan'
    ];

    protected function casts(): array
    {
        return [
            'waktu_daftar' => 'datetime',
            'status_pendaftaran' => RegistrationStatus::class,
        ];
    }

    public function mahasiswa() { return $this->belongsTo(Mahasiswa::class); }
    public function event() { return $this->belongsTo(Event::class); }
    public function responses() { return $this->hasMany(EventFormResponse::class, 'registration_id'); }
}