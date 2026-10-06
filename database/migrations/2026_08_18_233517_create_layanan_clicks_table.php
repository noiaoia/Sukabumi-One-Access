<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan_clicks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('layanan_id')
                ->constrained('layanans')
                ->cascadeOnDelete();

            // Nullable: warga yang belum login tetap tercatat kliknya
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index(['layanan_id', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_clicks');
    }
};