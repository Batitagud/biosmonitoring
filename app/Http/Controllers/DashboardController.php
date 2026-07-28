<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Database;

class DashboardController extends Controller
{
    public function __construct(private Database $firebase) {}

    public function index()
    {
        $uid  = session('firebase_uid');
        $code = $this->firebase->getReference("users/{$uid}/device_code")->getValue();

        // Belum pairing device -> paksa ke halaman pairing dulu
        if (!$code) {
            return redirect()->route('device.pair.form')
                ->with('info', 'Masukkan kode perangkat dulu sebelum bisa melihat dashboard.');
        }

        return view('dashboard', ['deviceCode' => $code]);
    }

    // Dipanggil dashboard.blade.php via fetch() tiap beberapa detik (polling)
    public function liveData()
    {
        $uid  = session('firebase_uid');
        $code = $this->firebase->getReference("users/{$uid}/device_code")->getValue();

        if (!$code) {
            return response()->json(['error' => 'Belum pairing device'], 404);
        }

        $latest = $this->firebase
            ->getReference("device_data/{$code}/sensor_data/latest")
            ->getValue();

        if ($latest && isset($latest['ts'])) {
            // Latency = selisih waktu server Laravel sekarang dengan timestamp
            // yang ditulis ESP32 (via NTP) saat data itu dikirim. Asumsi jam
            // server & ESP32 sama-sama akurat (NTP-synced).
            $latest['latency_sec'] = max(0, now()->timestamp - (int) $latest['ts']);
        }

        return response()->json($latest ?: []);
    }
}
