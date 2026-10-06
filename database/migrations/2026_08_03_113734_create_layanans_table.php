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
        Schema::create('layanans', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Relasi Instansi
            |--------------------------------------------------------------------------
            */
            $table->foreignId('instansi_id')
                  ->constrained('instansis')
                  ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Informasi Dasar
            |--------------------------------------------------------------------------
            */
            $table->string('nama');
            $table->string('slug')->unique();

            $table->text('deskripsi')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Jenis Integrasi
            |--------------------------------------------------------------------------
            */
            $table->enum('jenis', [
                'iframe',
                'form'
            ]);

            /*
            |--------------------------------------------------------------------------
            | Visual Card
            |--------------------------------------------------------------------------
            */
            $table->string('icon')->default('bi-grid-1x2-fill');

            $table->string('cover')->nullable();

            $table->string('primary_color', 20)
                  ->default('#123457');

            $table->string('secondary_color', 20)
                  ->default('#E8A33D');

            /*
            |--------------------------------------------------------------------------
            | Konfigurasi
            |--------------------------------------------------------------------------
            */
            $table->text('iframe_url')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Pengaturan
            |--------------------------------------------------------------------------
            */
            $table->integer('urutan')->default(0);

            $table->enum('status', [
                'aktif',
                'draft'
            ])->default('draft');

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanans');
    }
};