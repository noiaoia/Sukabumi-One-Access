<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Layanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'instansi_id',

        // Informasi Dasar
        'nama',
        'slug',
        'deskripsi',

        // Jenis Integrasi
        'jenis',

        // Visual
        'icon',
        'cover',
        'primary_color',
        'secondary_color',

        // Konfigurasi
        'iframe_url',

        // Pengaturan
        'urutan',
        'status',
    ];

    /**
     * Relasi ke Instansi
     */
    public function instansi()
    {
        return $this->belongsTo(Instansi::class);
    }

    /**
     * Relasi ke Kategori (Satu Layanan bisa punya banyak Kategori)
     */
    public function categories()
    {
        return $this->hasMany(FormCategory::class, 'layanan_id')->orderBy('urutan');
    }

    // Tambahkan di dalam model Layanan
    public function pengajuans()
    {
        // Mengarah ke model Pengajuan di namespace User
        return $this->hasMany(\App\Models\User\Pengajuan::class, 'layanan_id');
    }

    /**
     * Relasi ke klik "Buka Layanan" (untuk dashboard realtime)
     */
    public function clicks()
    {
        return $this->hasMany(\App\Models\LayananClick::class, 'layanan_id');
    }
}