<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Kreait\Firebase\Database;

class OtpService
{
    public function __construct(private Database $firebase) {}

    private array $labels = [
        'register'        => 'Kode verifikasi buat aktivasi akun kamu.',
        'google_first'     => 'Kode verifikasi buat aktivasi akun Google kamu.',
        'reset_password'   => 'Kode buat reset password akun kamu.',
        'change_email'     => 'Kode verifikasi buat konfirmasi email baru kamu.',
        'change_password'  => 'Kode verifikasi buat konfirmasi ganti password.',
    ];

    // Generate kode 6 digit, simpan ke Firebase, kirim email
    public function send(string $uidOrEmailKey, string $purpose, string $sendToEmail, array $extra = []): void
    {
        $code = (string) random_int(100000, 999999);

        $this->firebase->getReference("otp_codes/{$uidOrEmailKey}/{$purpose}")->set(array_merge([
            'code'       => $code,
            'expires_at' => now()->addMinutes(10)->timestamp,
        ], $extra));

        Mail::to($sendToEmail)->send(new OtpMail($code, $this->labels[$purpose] ?? 'Kode verifikasi.'));
    }

    // Cek kode benar & belum expired. Return data OTP (termasuk $extra) kalau valid, null kalau tidak.
    public function verify(string $uidOrEmailKey, string $purpose, string $inputCode): ?array
    {
        $ref  = $this->firebase->getReference("otp_codes/{$uidOrEmailKey}/{$purpose}");
        $data = $ref->getValue();

        if (!$data) return null;
        if ($data['code'] !== $inputCode) return null;
        if (now()->timestamp > $data['expires_at']) return null;

        $ref->remove(); // sekali pakai, langsung hapus setelah valid
        return $data;
    }

    public function clear(string $uidOrEmailKey, string $purpose): void
    {
        $this->firebase->getReference("otp_codes/{$uidOrEmailKey}/{$purpose}")->remove();
    }
}
