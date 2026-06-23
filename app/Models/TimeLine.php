<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeLine extends Model
{
    protected $table = 'time_line';
    protected $fillable = [
        'event_id', 'nama_timeline', 'deskripsi_timeline', 
        'tanggal_mulai', 'tanggal_selesai'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
        ];
    }

    public function event() { return $this->belongsTo(Event::class); }
}