<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tujuan_transfer', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel event
            $table->foreignId('event_id')->constrained('event')->cascadeOnDelete();
            
            $table->string('atas_nama', 255);
            $table->string('nama_bank', 25);
            $table->string('no_rekening', 200);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tujuan_transfer');
    }
};