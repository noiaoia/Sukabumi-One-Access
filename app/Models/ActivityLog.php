<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 
        'activity_name', 
        'description', 
        'ip_address', 
        'user_agent'
    ];

    /**
     * TAMBAHKAN RELASI INI:
     * Menghubungkan log ke data User (Operator)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}