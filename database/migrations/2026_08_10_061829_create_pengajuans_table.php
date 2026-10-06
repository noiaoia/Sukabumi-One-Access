<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();

            // 1. TAMBAHKAN INI (Wajib tanpa ->after() karena ini Schema::create)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Relasi layanan
            $table->foreignId('layanan_id')
                ->constrained('layanans')
                ->cascadeOnDelete();

            // Relasi kategori/form
            $table->foreignId('category_id')
                ->constrained('form_categories')
                ->cascadeOnDelete();

            // Nomor pengajuan
            $table->string('nomor_pengajuan')->unique();

            // Status proses pengajuan
            $table->enum('status', [
                'diajukan',
                'review',   
                'diproses',
                'perlu_revisi', 
                'selesai',
                'ditolak'
            ])->default('diajukan');

            // Kolom untuk menyimpan alasan revisi dari admin
            $table->text('catatan_admin')->nullable();
            
            // --- KOLOM NOTIFIKASI ---
            $table->boolean('is_read_notification')->default(false);      // Flag untuk Admin
            $table->boolean('is_read_user_notification')->default(false); // Flag untuk Warga

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};