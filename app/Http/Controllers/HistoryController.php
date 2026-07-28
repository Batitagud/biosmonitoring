<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Database;

class HistoryController extends Controller
{
    public function __construct(private Database $firebase) {}

    public function index()
    {
        $uid = session('firebase_uid');
        $raw = $this->firebase->getReference("users/{$uid}/sessions")->getValue() ?: [];

        $sessions = collect($raw)
            ->map(function ($s, $key) {
                $s['id'] = $key;
                $summary = $s['label_summary'] ?? ['normal_pct' => 0, 'stres_pct' => 0, 'lelah_pct' => 0];
                $s['normal_pct'] = round($summary['normal_pct'] ?? 0, 1);
                $s['stres_pct']  = round($summary['stres_pct']  ?? 0, 1);
                $s['lelah_pct']  = round($summary['lelah_pct']  ?? 0, 1);
                return $s;
            })
            ->sortByDesc('started_at')
            ->values();

        return view('history', [
            'sessions' => $sessions,
            'name'     => session('firebase_name'),
        ]);
    }

    public function destroy(string $id)
    {
        $uid = session('firebase_uid');
        $this->firebase->getReference("users/{$uid}/sessions/{$id}")->remove();

        return back()->with('success', 'Riwayat berhasil dihapus.');
    }
}
