<?php

namespace App\Http\Controllers;

use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Database;

class ProfileController extends Controller
{
    public function __construct(
        private Database $firebase,
        private FirebaseAuth $firebaseAuth,
        private OtpService $otp,
    ) {}

    public function edit()
    {
        $uid = session('firebase_uid');
        $profile = $this->firebase->getReference("users/{$uid}")->getValue() ?: [];

        return view('profile.edit', [
            'name'            => $profile['name'] ?? session('firebase_name'),
            'email'           => $profile['email'] ?? session('firebase_email'),
            'photoUrl'        => $profile['photo_url'] ?? null,
            'deviceCode'      => $profile['device_code'] ?? null,
            'pendingNewEmail' => session('pending_new_email'), // kalau lagi nunggu konfirmasi kode email baru
            'passwordCodeSent' => session('password_otp_sent', false),
        ]);
    }

    // ── NAMA ──────────────────────────────────────────────────────────────
    public function updateName(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $uid = session('firebase_uid');

        $this->firebase->getReference("users/{$uid}/name")->set($request->name);
        $this->firebaseAuth->updateUser($uid, ['displayName' => $request->name]);
        session(['firebase_name' => $request->name]);

        return back()->with('success', 'Nama berhasil diubah.');
    }

    // ── FOTO PROFIL ───────────────────────────────────────────────────────
    public function updatePhoto(Request $request)
    {
        $request->validate(['photo' => 'required|image|max:2048']); // maks 2MB
        $uid = session('firebase_uid');

        $path = $request->file('photo')->storeAs('profile-photos', "{$uid}.jpg", 'public');
        $url  = Storage::disk('public')->url($path);

        $this->firebase->getReference("users/{$uid}/photo_url")->set($url);

        return back()->with('success', 'Foto profil berhasil diubah.');
    }

    // ── GANTI EMAIL — Langkah 1: minta kode ke email BARU ─────────────────
    public function requestEmailChange(Request $request)
    {
        $request->validate(['new_email' => 'required|email']);
        $uid = session('firebase_uid');

        if ($request->new_email === session('firebase_email')) {
            return back()->withErrors(['new_email' => 'Email baru sama dengan email sekarang.']);
        }

        // Cek email itu belum dipakai akun lain
        try {
            $this->firebaseAuth->getUserByEmail($request->new_email);
            return back()->withErrors(['new_email' => 'Email ini sudah dipakai akun lain.']);
        } catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
            // bagus, berarti belum dipakai siapa-siapa
        }

        $this->otp->send($uid, 'change_email', $request->new_email, ['new_email' => $request->new_email]);
        session(['pending_new_email' => $request->new_email]);

        return back()->with('success', "Kode verifikasi dikirim ke {$request->new_email}.");
    }

    // ── GANTI EMAIL — Langkah 2: konfirmasi kode ──────────────────────────
    public function confirmEmailChange(Request $request)
    {
        $request->validate(['code' => 'required|digits:6']);
        $uid = session('firebase_uid');

        $result = $this->otp->verify($uid, 'change_email', $request->code);
        if (!$result) {
            return back()->withErrors(['code' => 'Kode salah atau sudah kedaluwarsa.']);
        }

        $newEmail = $result['new_email'];

        $this->firebaseAuth->changeUserEmail($uid, $newEmail);
        $this->firebase->getReference("users/{$uid}/email")->set($newEmail);

        // Update juga email di device_codes/{code}/users/{uid} kalau device-nya udah di-pairing
        $deviceCode = $this->firebase->getReference("users/{$uid}/device_code")->getValue();
        if ($deviceCode) {
            $this->firebase->getReference("device_codes/{$deviceCode}/users/{$uid}/email")->set($newEmail);
        }

        session(['firebase_email' => $newEmail]);
        session()->forget('pending_new_email');

        return back()->with('success', 'Email berhasil diganti. Silakan login ulang lain kali pakai email baru.');
    }

    public function cancelEmailChange()
    {
        $uid = session('firebase_uid');
        $this->otp->clear($uid, 'change_email');
        session()->forget('pending_new_email');
        return back()->with('success', 'Perubahan email dibatalkan.');
    }

    // ── GANTI PASSWORD — Langkah 1: minta kode ke email SEKARANG ──────────
    public function requestPasswordChange()
    {
        $uid   = session('firebase_uid');
        $email = session('firebase_email');

        $this->otp->send($uid, 'change_password', $email);
        session(['password_otp_sent' => true]);

        return back()->with('success', "Kode verifikasi dikirim ke {$email}.");
    }

    // ── GANTI PASSWORD — Langkah 2: konfirmasi kode + password baru ───────
    public function confirmPasswordChange(Request $request)
    {
        $request->validate([
            'code'     => 'required|digits:6',
            'password' => 'required|min:6|confirmed',
        ]);
        $uid = session('firebase_uid');

        $result = $this->otp->verify($uid, 'change_password', $request->code);
        if (!$result) {
            return back()->withErrors(['code' => 'Kode salah atau sudah kedaluwarsa.']);
        }

        $this->firebaseAuth->changeUserPassword($uid, $request->password);
        session()->forget('password_otp_sent');

        return back()->with('success', 'Password berhasil diganti.');
    }
}
