<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TujuanTransfer extends Model
{
    use HasFactory;

    protected $table = 'tujuan_transfer';

    protected $fillable = [
        'event_id',
        'atas_nama',
        'nama_bank',
        'no_rekening',
    ];

    // Relasi balik (Belongs To) ke Event
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}