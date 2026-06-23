<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Narahubung extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara eksplisit karena bukan bahasa Inggris
    protected $table = 'narahubung';

    protected $fillable = [
        'event_id',
        'nama',
        'nomor',
    ];

    // Relasi balik (Belongs To) ke Event
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}