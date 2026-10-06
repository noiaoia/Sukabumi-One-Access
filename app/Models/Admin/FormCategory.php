<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormCategory extends Model
{
    use HasFactory;

    protected $table = 'form_categories';

    protected $fillable = [
        'layanan_id',
        'nama',
        'slug',
        'deskripsi',

        // Konfigurasi Form Builder
        'form_title',
        'form_description',
        'edit_limit',
        'structure',

        // Visual
        'icon',
        'primary_color',

        // Pengaturan
        'urutan',
        'status',
    ];

    protected $casts = [
        'structure' => 'array',
        'edit_limit' => 'integer',
    ];

    /**
     * Relasi ke Layanan
     * Satu Kategori dimiliki oleh satu Layanan.
     */
    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    // Tambahkan di dalam model FormCategory
    public function pengajuans()
    {
        return $this->hasMany(\App\Models\User\Pengajuan::class, 'category_id');
    }
}