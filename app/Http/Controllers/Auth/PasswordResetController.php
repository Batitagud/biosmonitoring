<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\UserNotFound;

class PasswordResetController extends Controller
{
    public function __construct(private FirebaseAuth $firebaseAuth, private OtpService $otp) {}

    public function sendCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            $user = $this->firebaseAuth->getUserByEmail($request->email);
        } catch (UserNotFound $e) {
            // Sengaja tetap bilang "terkirim" walau email nggak ada, biar orang
            // nggak bisa dipakai buat ngecek email siapa aja yang terdaftar.
            return back()->with('success', 'Kalau email itu terdaftar, kode sudah dikirim.');
        }

        session(['reset_email' => $request->email]);
        $this->otp->send($user->uid, 'reset_password', $request->email);

        return redirect()->route('password.reset.form')
            ->with('success', 'Kode sudah dikirim ke email kamu.');
    }

    public function showResetForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password', ['email' => session('reset_email')]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'code'     => 'required|digits:6',
            'password' => 'required|min:6|confirmed',
        ]);

        $email = session('reset_email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        $user   = $this->firebaseAuth->getUserByEmail($email);
        $result = $this->otp->verify($user->uid, 'reset_password', $request->code);

        if (!$result) {
            return back()->withErrors(['code' => 'Kode salah atau sudah kedaluwarsa.']);
        }

        $this->firebaseAuth->changeUserPassword($user->uid, $request->password);
        session()->forget('reset_email');

        return redirect()->route('login')->with('success', 'Password berhasil diganti, silakan login.');
    }
}
