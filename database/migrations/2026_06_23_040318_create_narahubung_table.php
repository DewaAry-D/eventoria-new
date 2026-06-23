<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('narahubung', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel event (cascadeOnDelete memastikan data terhapus jika event dihapus)
            $table->foreignId('event_id')->constrained('event')->cascadeOnDelete();
            
            $table->string('nama', 255);
            $table->string('nomor', 50);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('narahubung');
    }
};