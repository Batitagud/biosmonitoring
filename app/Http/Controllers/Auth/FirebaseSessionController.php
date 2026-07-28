<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Database;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class FirebaseSessionController extends Controller
{
    public function __construct(
        private FirebaseAuth $firebaseAuth,
        private Database $firebase,
        private OtpService $otp,
    ) {}

    // Dipanggil dari fetch('/auth/session') setelah register/login/google berhasil di client
    public function store(Request $request)
    {
        $request->validate(['idToken' => 'required|string']);

        try {
            $verifiedToken = $this->firebaseAuth->verifyIdToken($request->idToken);
        } catch (FailedToVerifyToken $e) {
            return response()->json(['message' => 'Token tidak valid'], 401);
        }

        $claims = $verifiedToken->claims();
        $uid    = $claims->get('sub');
        $email  = $claims->get('email');
        $name   = $claims->get('name', $email);

        $userRef  = $this->firebase->getReference("users/{$uid}");
        $existing = $userRef->getValue();

        if (!$existing) {
            // User baru (baik dari register email/password ATAU pertama kali Google sign-in)
            $userRef->set([
                'name'           => $name,
                'email'          => $email,
                'created_at'     => now()->timestamp,
                'email_verified' => false,
            ]);
            $existing = ['email_verified' => false];
        }

        if (empty($existing['email_verified'])) {
            // Belum verifikasi -> jangan login penuh dulu, kirim kode & gantung di session sementara
            session(['pending_uid' => $uid, 'pending_email' => $email, 'pending_name' => $name]);
            $this->otp->send($uid, 'register', $email);

            return response()->json(['redirect' => route('verify.email.form')]);
        }

        // Sudah terverifikasi -> login penuh seperti biasa
        session([
            'firebase_uid'   => $uid,
            'firebase_email' => $email,
            'firebase_name'  => $existing['name'] ?? $name,
        ]);

        $deviceCode = $userRef->getChild('device_code')->getValue();

        return response()->json([
            'redirect' => $deviceCode ? route('dashboard') : route('device.pair.form'),
        ]);
    }

    public function destroy(Request $request)
    {
        $request->session()->forget(['firebase_uid', 'firebase_email', 'firebase_name']);
        $request->session()->invalidate();
        return redirect()->route('login');
    }
}
