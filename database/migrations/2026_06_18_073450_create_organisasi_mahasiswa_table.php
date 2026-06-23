<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organisasi_mahasiswa', function (Blueprint $table) {
            $table->id();
            
            // Foreign Keys
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Informasi Dasar
            $table->string('nama_organisasi', 255);
            $table->string('no_organisasi', 50);
            
            // URL & Sosmed (Nullable sesuai tanda 'N' di gambar)
            $table->string('ig_url', 255)->nullable();
            $table->string('linkedin_url', 255)->nullable();
            
            // Status & Tingkat
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('tingkat_organisasi', ['prodi', 'fakultas', 'universitas']);
            
            // Relasi Akademik
            // fakultas_id (int, Nullable)
            $table->unsignedBigInteger('fakultas_id')->nullable();
            // prodi_id (var(200), Nullable)
            $table->string('prodi_id', 200)->nullable();
            
            // File & Teks (Nullable)
            $table->string('logo_url', 255)->nullable();
            $table->string('pesan_penolakan')->nullable();
            
            // Detail Organisasi (Wajib Diisi / Tidak Nullable)
            $table->text('deskripsi');
            $table->text('visi');
            $table->text('misi');
            
            // File Dokumen URL/Path ke PDF (Wajib Diisi / Tidak Nullable)
            $table->string('ad_art', 255);
            $table->string('sk', 255);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organisasi_mahasiswa');
    }
};