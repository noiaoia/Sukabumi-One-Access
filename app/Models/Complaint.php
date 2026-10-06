<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    // Tentukan kolom mana saja yang boleh diisi
    protected $fillable = [
        'user_id',
        'instansi_id', // <--- WAJIB TAMBAH INI agar bisa difilter per dinas
        'ticket_id',
        'title',
        'category',
        'description',
        'attachment',
        'latitude',
        'longitude',
        'location_address',
        'incident_date',
        'is_anonymous',
        'contact_phone',
        'contact_email',
        'status',
        'response_message',
    ];

    /**
     * Fungsi Otomatis saat data dibuat
     */
    protected static function booted()
    {
        static::creating(function ($complaint) {
            // Membuat Format: ADU-YYYYMMDD-001
            $date = now()->format('Ymd');
            
            // Cari jumlah laporan yang masuk hari ini untuk nomor urut
            $count = self::whereDate('created_at', now())->count() + 1;
            
            // Gabungkan jadi Ticket ID
            $complaint->ticket_id = "ADU-{$date}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
            
            // Set status default jika belum ada
            if (!$complaint->status) {
                $complaint->status = 'pending';
            }
        });
    }

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}