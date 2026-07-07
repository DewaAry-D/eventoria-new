<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use App\Enums\FieldType;

class EventFormField extends Model
{
    protected $table = 'event_form_fields';
    
    protected $fillable = [
        'event_id', 'nama_field', 'tipe_field', 
        'is_required', 'meta_options'
    ];

    protected function casts(): array
    {
        return [
            'tipe_field' => FieldType::class,
            'is_required' => 'boolean',
            // AsArrayObject memungkinkan Anda berinteraksi dengan JSON seolah itu adalah array PHP murni
            'meta_options' => AsArrayObject::class, 
        ];
    }

    public function event() { return $this->belongsTo(Event::class); }
    public function responses() { return $this->hasMany(EventFormResponse::class, 'field_id'); }
}