<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\EventStatus;
use App\Enums\TingkatEvent;

class Event extends Model
{
    protected $table = 'event';
    
    protected $fillable = [
        'kategori_id', 'admin_acc_id', 'organisasi_id', 'nama_event',
        'slug', 'penyelenggara', 'status', 'deskripsi', 'nama_lokasi',
        'lokasi_url', 'kuota', 'sisa_kuota', 'narasumber', 'link_event',
        'catatan_revisi', 'proposal_url', 'flyer_url', 'tingkat_event'
    ];

    protected function casts(): array
    {
        return [
            'status' => EventStatus::class,
            'tingkat_event' => TingkatEvent::class,
        ];
    }

    // Relasi
    public function kategori() { return $this->belongsTo(Kategori::class); }
    public function organisasi() { return $this->belongsTo(OrganisasiMahasiswa::class); }
    public function adminAcc() { return $this->belongsTo(AdminDpm::class, 'admin_acc_id'); }
    
    public function timeLines() { return $this->hasMany(TimeLine::class); }
    public function biayaEvents() { return $this->hasMany(BiayaEvent::class); }
    
    // Relasi untuk Fase 3 nanti
    public function templateSertifikat() { return $this->hasOne(TemplateSertifikat::class); }
    public function formFields() { return $this->hasMany(EventFormField::class); }
    public function registrations() { return $this->hasMany(EventRegistration::class); }

    public function narahubung()
    {
        return $this->hasMany(Narahubung::class, 'event_id');
    }

    public function tujuanTransfer()
    {
        return $this->hasMany(TujuanTransfer::class, 'event_id');
    }
}