<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateSertifikat extends Model
{
    protected $table = 'template_sertifikat';
    
    protected $fillable = [
        'event_id', 'file_template', 'posisi_x', 'posisi_y', 
        'jenis_font', 'ukuran_font', 'warna_font'
    ];

    public function event() { return $this->belongsTo(Event::class); }
}