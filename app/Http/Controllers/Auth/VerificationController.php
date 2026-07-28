<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Kreait\Firebase\Database;

class VerificationController extends Controller
{
    public function __construct(private Database $firebase, private OtpService $otp) {}

    public function form()
    {
        if (!session('pending_uid')) {
            return redirect()->route('login');
        }
        return view('auth.verify-email', ['email' => session('pending_email')]);
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);

        $uid = session('pending_uid');
        if (!$uid) {
            return redirect()->route('login');
        }

        $result = $this->otp->verify($uid, 'register', $request->code);
        if (!$result) {
            return back()->withErrors(['code' => 'Kode salah atau sudah kedaluwarsa.']);
        }

        $userRef = $this->firebase->getReference("users/{$uid}");
        $userRef->update(['email_verified' => true]);

        // Sekarang login penuh
        session([
            'firebase_uid'   => $uid,
            'firebase_email' => session('pending_email'),
            'firebase_name'  => session('pending_name'),
        ]);
        session()->forget(['pending_uid', 'pending_email', 'pending_name']);

        $deviceCode = $userRef->getChild('device_code')->getValue();
        return redirect($deviceCode ? route('dashboard') : route('device.pair.form'))
            ->with('success', 'Akun berhasil diverifikasi!');
    }

    public function resend()
    {
        $uid   = session('pending_uid');
        $email = session('pending_email');
        if (!$uid || !$email) {
            return redirect()->route('login');
        }
        $this->otp->send($uid, 'register', $email);
        return back()->with('success', 'Kode baru sudah dikirim ke email kamu.');
    }
}
