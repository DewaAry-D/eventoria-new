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
        Schema::create('template_sertifikat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event')->cascadeOnDelete();
            $table->string('file_template');
            $table->integer('posisi_x');
            $table->integer('posisi_y');
            $table->string('jenis_font', 100)->nullable();
            $table->integer('ukuran_font')->default(12);
            $table->string('warna_font', 50)->default('#000000'); // Hex color default hitam
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_sertifikat');
    }
};
