<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaEvent extends Model
{
    protected $table = 'biaya_event';
    protected $fillable = ['event_id', 'kategori', 'biaya'];

    public function event() { return $this->belongsTo(Event::class); }
}