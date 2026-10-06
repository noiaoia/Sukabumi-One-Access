<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingRegistration extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'username',
        'password',
        'token',
        'expired_at',
    ];

    /**
     * Konversi otomatis tipe data.
     */
    protected $casts = [
        'expired_at' => 'datetime',
    ];
}