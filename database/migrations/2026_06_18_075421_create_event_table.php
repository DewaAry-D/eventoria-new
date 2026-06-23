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
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori')->cascadeOnDelete();
            $table->foreignId('admin_acc_id')->nullable()->constrained('admin_dpm')->nullOnDelete(); 
            $table->foreignId('organisasi_id')->constrained('organisasi_mahasiswa')->cascadeOnDelete();
            
            $table->string('nama_event');
            $table->string('slug')->unique();
            $table->string('penyelenggara');
            $table->enum('status', ['draft', 'pending_approval', 'revision', 'published',])->default('draft');
            $table->text('deskripsi');
            $table->string('nama_lokasi');
            $table->string('lokasi_url')->nullable();
            $table->integer('kuota');
            $table->integer('sisa_kuota');
            $table->text('narasumber')->nullable();
            $table->string('link_event')->nullable();
            $table->text('catatan_revisi')->nullable();
            $table->string('flyer_url');
            $table->enum('tingkat_event', ['prodi', 'fakultas', 'universitas']);
            $table->timestamps();
            // Indexing krusial untuk fitur "Personalized Feed" mahasiswa
            $table->index(['status', 'tingkat_event']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
