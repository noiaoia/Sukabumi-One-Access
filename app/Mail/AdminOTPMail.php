<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class AdminOTPMail extends Mailable
{
    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this
            ->subject('Kode OTP Administrator - Sukabumi One Access')
            ->view('emails.admin-otp');
    }
}