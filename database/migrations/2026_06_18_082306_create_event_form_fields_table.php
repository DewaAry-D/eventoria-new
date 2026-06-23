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
        Schema::create('event_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('event')->cascadeOnDelete();
            $table->string('nama_field');
            $table->enum('tipe_field', ['text', 'textarea', 'number', 'email', 'url', 'select', 'radio', 'file_pdf', 'file_image']);
            $table->boolean('is_required')->default(false);
            $table->json('meta_options')->nullable(); // Menampung pilihan ganda dalam bentuk JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_form_fields');
    }
};
