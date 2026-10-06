<?php

namespace App\Models;

use Illuminate\Support\HtmlString; // Tambahkan import ini di atas
use Illuminate\Contracts\Auth\MustVerifyEmail; // Import diletakkan di atas sini
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Atribut yang dapat diisi (Mass Assignment).
     */
    // Tambahkan di dalam class User
    protected $fillable = [
        'name', 'username', 'email', 'phone', 'instansi', 
        'profile_photo', 'role', 'status', // <--- Tambahkan status
        'instansi_id', 'password', 'last_seen', 'email_verified_at',
    ];

    /**
     * Relasi ke Instansi (PENTING untuk Dashboard Admin Dinas)
     */
    public function agency()
    {
        return $this->belongsTo(\App\Models\Admin\Instansi::class, 'instansi_id');
    }

    /**
     * Atribut yang harus disembunyikan saat serialisasi.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus dikonversi (Casting).
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'created_at' => 'datetime', // TAMBAHKAN INI agar format() berjalan lancar
            'updated_at' => 'datetime', // TAMBAHKAN INI agar diffForHumans() berjalan lancar
            'password' => 'hashed',
            'last_seen' => 'datetime',
        ];
    }

    /**
     * Helper untuk mengecek apakah user adalah Super Admin (Bisa Semua)
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Helper untuk mengecek apakah user adalah Admin Dinas
     */
    public function isAdminDinas()
    {
        return $this->role === 'admin_dinas';
    }

    public function isOperator()
    {
        return $this->role === 'operator';
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class)->latest();
    }

    public function isOnline()
    {
        // Jika aktivitas terakhir kurang dari 5 menit yang lalu, anggap online
        return $this->last_seen && $this->last_seen->gt(now()->subMinutes(5));
    }

    /**
     * Relasi ke Pengaduan
     */
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new class($this->name) extends \Illuminate\Auth\Notifications\VerifyEmail {
            protected $name;
            public function __construct($name) { $this->name = $name; }

            public function toMail($notifiable) {
                $url = $this->verificationUrl($notifiable);

                return (new \Illuminate\Notifications\Messages\MailMessage)
                    ->subject('Otorisasi Aktivasi Akun - Sukabumi One Access')
                    
                    // Greeting dengan styling kustom
                    ->greeting(new HtmlString('
                        <div style="text-align: left;">
                            <span style="color: #64748b; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px;">Selamat Datang</span>
                            <h1 style="color: #0A1E33; margin-top: 5px; font-size: 24px; font-weight: 900;">Sampurasun, '.$this->name.'!</h1>
                        </div>
                    '))

                    // Baris pertama dengan aksen visual
                    ->line(new HtmlString('
                        <div style="border-left: 3px solid #E8A33D; padding-left: 15px; margin: 25px 0;">
                            <p style="color: #475569; font-size: 15px; line-height: 1.8; margin: 0;">
                                Permintaan aktivasi akun baru untuk <strong>Sukabumi One Access</strong> telah diterima. 
                                Demi menjaga keamanan data kependudukan, silakan melakukan otorisasi identitas digital Anda.
                            </p>
                        </div>
                    '))

                    // Tombol (Akan menggunakan desain button.blade.php yang kita buat tadi)
                    ->action('Otorisasi Akun Sekarang', $url)

                    // Baris penutup dengan gaya catatan teknis
                    ->line(new HtmlString('
                        <div style="background-color: #f8fafc; padding: 20px; border-radius: 12px; margin-top: 30px;">
                            <p style="color: #94a3b8; font-size: 11px; margin: 0; text-transform: uppercase; font-weight: bold; letter-spacing: 1px;">
                                Peringatan Keamanan:
                            </p>
                            <p style="color: #64748b; font-size: 12px; margin-top: 5px; line-height: 1.5;">
                                Tautan ini hanya berlaku selama <strong>60 menit</strong>. Jangan memberikan akses atau membagikan email ini kepada siapapun demi mencegah penyalahgunaan akun.
                            </p>
                        </div>
                    '))

                    ->salutation(new HtmlString('
                        <div style="margin-top: 30px;">
                            <p style="color: #0A1E33; font-weight: 800; font-size: 14px; margin-bottom: 0;">Salam Hormat,</p>
                            <p style="color: #E8A33D; font-weight: 700; font-size: 12px; margin-top: 0; letter-spacing: 1px;">ADMINISTRATOR SISTEM SOA</p>
                        </div>
                    '));
            }
        });
    }
}