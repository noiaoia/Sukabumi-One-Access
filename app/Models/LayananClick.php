<?php

namespace App\Models;

use App\Models\Admin\Layanan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LayananClick extends Model
{
    protected $fillable = [
        'layanan_id',
        'user_id',
        'ip_address',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Catat 1 klik untuk layanan ini — KECUALI ini cuma refresh/reload halaman
     * portal.show atau form.show yang sama dalam sesi berjalan (dedup 30 menit).
     * Dipanggil dari PortalUserController@show dan FormController@show.
     */
    public static function recordIfNew(Layanan $layanan): void
    {
        $sessionKey = 'layanan_click_' . $layanan->id;
        $lastClick  = session($sessionKey);

        if ($lastClick && now()->diffInMinutes($lastClick) < 30) {
            return; // dianggap refresh, bukan klik baru
        }

        static::create([
            'layanan_id' => $layanan->id,
            'user_id'    => Auth::check() ? Auth::id() : null,
            'ip_address' => request()->ip(),
        ]);

        session([$sessionKey => now()]);
    }
}