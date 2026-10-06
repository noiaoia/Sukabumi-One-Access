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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('ticket_id')->unique(); // Contoh: #SOA-20230808-001
            $table->string('title');
            $table->string('category');
            $table->text('description');
            $table->string('attachment')->nullable();

            // --- TAMBAHKAN KOLOM BERIKUT ---
            
            // Lokasi Presisi (Koordinat GPS)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Alamat tekstual hasil deteksi peta atau input manual
            $table->text('location_address')->nullable(); 

            // Tanggal kejadian yang dipilih user
            $table->date('incident_date')->nullable();

            // Fitur Anonim
            $table->boolean('is_anonymous')->default(false);

            // Kontak Balasan Khusus Laporan Ini (Jika diisi di Section 4)
            $table->string('contact_phone', 15)->nullable();
            $table->string('contact_email')->nullable();

            // --- AKHIR KOLOM TAMBAHAN ---

            $table->enum('status', [
                'pending',
                'review',
                'process',
                'done',
                'rejected'
            ])->default('pending');

            $table->boolean('is_read_notification')->default(false);
            $table->boolean('is_read_user_notification')->default(false);

            $table->text('response_message')->nullable();

            // TAMBAHKAN KOLOM INI agar sinkron dengan Controller
            $table->foreignId('instansi_id')
                ->nullable()
                ->constrained('instansis') // Pastikan tabel 'instansis' sudah dibuat sebelumnya
                ->onDelete('set null'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
