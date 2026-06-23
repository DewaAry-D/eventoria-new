<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('event')->cascadeOnDelete();
            $table->dateTime('waktu_daftar');
            $table->enum('status_pendaftaran', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->string('nama_cetak_sertifikat');
            $table->text('catatan_penolakan')->nullable();
            $table->timestamps();
            
            // Indexing agar query status pendaftaran saat mengecek sertifikat berjalan kilat
            $table->index(['event_id', 'status_pendaftaran']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
