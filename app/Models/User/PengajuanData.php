<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanData extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_data';

    protected $fillable = [
        'pengajuan_id',
        'parameter',
        'value',
    ];

    /**
     * Data ini dimiliki oleh satu pengajuan.
     */
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }
}