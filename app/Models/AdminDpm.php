<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class AdminDpm extends Model
{
    protected $table = 'admin_dpm';
    protected $fillable = ['user_id', 'fakultas_id', 'nama_admin'];

    public function user() { return $this->belongsTo(User::class); }
    public function fakultas() { return $this->belongsTo(Fakultas::class); }
}