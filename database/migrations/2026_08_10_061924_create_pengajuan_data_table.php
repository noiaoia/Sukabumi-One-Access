<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengajuan_data', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengajuan_id')
                ->constrained('pengajuans')
                ->cascadeOnDelete();

            $table->string('parameter', 100);
            $table->text('value')->nullable();

            $table->timestamps();

            $table->index(['pengajuan_id', 'parameter']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_data');
    }
};