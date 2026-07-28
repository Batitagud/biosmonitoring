<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $purposeLabel, // "Verifikasi Akun", "Reset Password", dll — buat teks di email
    ) {}

    public function build()
    {
        return $this->subject("Kode Verifikasi: {$this->code}")
            ->view('emails.otp');
    }
}
