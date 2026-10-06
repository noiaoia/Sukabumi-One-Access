<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanDokumen extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_dokumen';

    protected $fillable = [
        'pengajuan_id',
        'requirement_id',
        'nama_dokumen',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];

    /**
     * Dokumen ini dimiliki oleh satu pengajuan.
     */
    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class, 'pengajuan_id');
    }
}