<?php

namespace App\Models\User;

use App\Models\User; // Import model User utama
use App\Models\Admin\FormCategory;
use App\Models\Admin\Layanan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    protected $table = 'pengajuans';

    protected $fillable = [
        'user_id',         // <--- TAMBAHKAN INI agar ID pemilik bisa disimpan
        'layanan_id',
        'category_id',
        'nomor_pengajuan',
        'status',
        'catatan_admin', 
        'is_read_notification',      // <--- TAMBAHKAN INI (Untuk Notif Admin)
        'is_read_user_notification', // <--- TAMBAHKAN INI (Untuk Notif User)
    ];

    /**
     * Relasi: Pengajuan dimiliki oleh satu User (Warga).
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Pengajuan dimiliki oleh satu layanan.
     */
    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    /**
     * Pengajuan dimiliki oleh satu kategori/form.
     */
    public function category()
    {
        return $this->belongsTo(FormCategory::class, 'category_id');
    }

    /**
     * Data/jawaban dari pengajuan.
     */
    public function data()
    {
        return $this->hasMany(PengajuanData::class, 'pengajuan_id');
    }

    /**
     * Dokumen dari pengajuan.
     */
    public function dokumen()
    {
        return $this->hasMany(PengajuanDokumen::class, 'pengajuan_id');
    }
}