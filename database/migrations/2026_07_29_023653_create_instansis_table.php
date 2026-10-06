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
        Schema::create('instansis', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Informasi Dasar
            |--------------------------------------------------------------------------
            */
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('kategori')->nullable();

            $table->string('slogan')->nullable();
            $table->text('deskripsi_singkat')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Visual Identity
            |--------------------------------------------------------------------------
            */
            $table->string('logo')->nullable();
            $table->string('cover')->nullable();

            $table->string('icon')->default('bi-building');

            $table->string('icon_background_color', 20)
                  ->default('#F8FAFC');

            $table->string('primary_color', 20)
                  ->default('#123457');

            $table->string('secondary_color', 20)
                  ->default('#E8A33D');

            /*
            |--------------------------------------------------------------------------
            | Company Profile
            |--------------------------------------------------------------------------
            */
            $table->string('about_title')->nullable();
            $table->longText('deskripsi')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Kontak
            |--------------------------------------------------------------------------
            */
            $table->text('alamat')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->text('maps_embed')->nullable();
            $table->string('jam_operasional')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Pengaturan Tampilan
            |--------------------------------------------------------------------------
            */
            $table->integer('urutan')->default(0);

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */
            $table->enum('status', [
                'aktif',
                'draft'
            ])->default('draft');

            $table->timestamps();
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Quick Statistics
            |--------------------------------------------------------------------------
            */

            $table->string('stat1')->nullable();
            $table->string('stat1_label')->nullable();

            $table->string('stat2')->nullable();
            $table->string('stat2_label')->nullable();

            $table->string('stat3')->nullable();
            $table->string('stat3_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instansis');
    }
};