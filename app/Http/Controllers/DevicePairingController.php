<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Database;

class DevicePairingController extends Controller
{
    public function __construct(private Database $firebase) {}

    public function form()
    {
        $uid = session('firebase_uid');
        $existingCode = $this->firebase->getReference("users/{$uid}/device_code")->getValue();

        if ($existingCode) {
            return redirect()->route('dashboard');
        }

        return view('device.pair');
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_code' => ['required', 'regex:/^[A-Z0-9]{6}$/i'],
        ]);

        $uid   = session('firebase_uid');
        $email = session('firebase_email');
        $code  = strtoupper($request->device_code);

        $codeRef = $this->firebase->getReference("device_codes/{$code}");
        $current = $codeRef->getValue();

        // Kode dianggap "tidak aktif" cuma kalau memang di-nonaktifkan manual
        // (active === false). Selain itu, kode boleh dipakai banyak akun sekaligus.
        if ($current && array_key_exists('active', $current) && $current['active'] === false) {
            return back()->withErrors(['device_code' => 'Kode ini sedang dinonaktifkan.']);
        }

        // Tambahkan/perbarui entri user ini di bawah device_codes/{code}/users/{uid}
        $codeRef->update([
            'active' => true,
            "users/{$uid}" => [
                'email'     => $email,
                'paired_at' => now()->timestamp,
            ],
        ]);

        $this->firebase->getReference("users/{$uid}/device_code")->set($code);

        return redirect()->route('dashboard')->with('success', "Perangkat {$code} berhasil dipasangkan.");
    }
}
