<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventFormResponse extends Model
{
    protected $table = 'event_form_responses';
    
    protected $fillable = ['registration_id', 'field_id', 'jawaban'];

    public function registration() { return $this->belongsTo(EventRegistration::class, 'registration_id'); }
    public function field() { return $this->belongsTo(EventFormField::class, 'field_id'); }
}