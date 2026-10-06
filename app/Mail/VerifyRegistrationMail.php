<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    // WAJIB: Tambahkan dua variabel ini agar bisa dikirim ke view email
    public $url;
    public $name;

    public function __construct($url, $name)
    {
        $this->url = $url;
        $this->name = $name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Otorisasi Akun - Sukabumi One Access',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-registration', 
        );
    }

    public function attachments(): array
    {
        return [];
    }
}