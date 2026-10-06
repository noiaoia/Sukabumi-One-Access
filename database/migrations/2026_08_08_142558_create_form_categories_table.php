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
        Schema::create('form_categories', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel layanans
            $table->foreignId('layanan_id')
                ->constrained('layanans')
                ->cascadeOnDelete();

            // Informasi Kategori
            $table->string('nama');
            $table->string('slug');
            $table->text('deskripsi')->nullable();

            // Konfigurasi Form
            $table->string('form_title')->nullable();
            $table->text('form_description')->nullable();
            $table->integer('edit_limit')->default(-1);

            // Struktur Form Builder dalam JSON
            $table->longText('structure')->nullable();

            // Visual
            $table->string('icon')->default('bi-geo-fill');
            $table->string('primary_color', 20)->default('#E8A33D');

            // Pengaturan
            $table->integer('urutan')->default(0);
            $table->enum('status', ['aktif', 'draft'])->default('aktif');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_categories');
    }
};