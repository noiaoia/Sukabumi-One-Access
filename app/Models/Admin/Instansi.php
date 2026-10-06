<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Instansi extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel
     */
    protected $table = 'instansis';

    /**
     * Mass Assignment
     */
    protected $fillable = [
        'nama',
        'slug',
        'kategori',
        'slogan',
        'deskripsi_singkat',

        'logo',
        'cover',
        'icon',
        'icon_background_color',
        'primary_color',
        'secondary_color',

        'about_title',
        'deskripsi',

        // Quick Statistics
        'stat1',
        'stat1_label',
        'stat2',
        'stat2_label',
        'stat3',
        'stat3_label',

        'alamat',
        'telepon',
        'email',
        'website',
        'maps_embed',
        'jam_operasional',

        'urutan',
        'status',
    ];

    /**
     * Attribute Casting
     */
    protected $casts = [
        'urutan' => 'integer',
    ];

    /**
     * 1. URL BINDING MENGGUNAKAN SLUG
     * Agar route otomatis mencari berdasarkan slug (portal/diskominfo)
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * 2. SCOPE ACTIVE
     * Memudahkan query: Instansi::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * 3. ACCESSOR LOGO URL
     * Gunakan di Blade: <img src="{{ $instansi->logo_url }}">
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return Storage::url($this->logo);
        }
        return asset('assets/images/default-logo.png'); // Pastikan path default benar
    }

    /**
     * 4. ACCESSOR COVER URL
     * Gunakan di Blade: style="background-image: url('{{ $instansi->cover_url }}')"
     */
    public function getCoverUrlAttribute()
    {
        if ($this->cover && Storage::disk('public')->exists($this->cover)) {
            return Storage::url($this->cover);
        }
        return asset('assets/images/default-cover.jpg');
    }

    /**
     * 5. HELPER UNTUK DASHBOARD
     * Menghitung total layanan aktif milik instansi ini
     */
    public function getTotalLayananAttribute()
    {
        return $this->layanans()->count();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS (RELASI)
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke Layanan (One to Many)
     */
    public function layanans()
    {
        return $this->hasMany(Layanan::class, 'instansi_id');
    }

    /**
     * Relasi ke User (One to Many)
     * Admin Dinas yang mengelola instansi ini
     */
    public function users()
    {
        return $this->hasMany(\App\Models\User::class, 'instansi_id');
    }

    /**
     * Relasi ke Pengaduan (One to Many)
     * Laporan masyarakat yang masuk ke dinas ini
     */
    public function complaints()
    {
        return $this->hasMany(\App\Models\Complaint::class, 'instansi_id');
    }

    /**
     * Relasi ke Pengajuan Berkas (Has Many Through)
     * Instansi -> Layanan -> Pengajuan
     * Digunakan agar Admin Dinas bisa melihat statistik berkas yang masuk ke layanannya
     */
    public function pengajuans()
    {
        return $this->hasManyThrough(
            \App\Models\User\Pengajuan::class, 
            \App\Models\Admin\Layanan::class,
            'instansi_id', // Foreign key di tabel layanans
            'layanan_id',  // Foreign key di tabel pengajuans
            'id',          // Local key di tabel instansis
            'id'           // Local key di tabel layanans
        );
    }
}