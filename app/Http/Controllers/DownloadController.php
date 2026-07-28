<?php

namespace App\Http\Controllers;

use ZipArchive;

class DownloadController extends Controller
{
    private array $files = [
        'BiosensorMonitor.exe',
        'best_stress_model.pkl',
        'scaler.pkl',
        'encoder.pkl',
        'features.pkl',
        'favicon.ico',
    ];

    // Halaman instruksi sebelum download
    public function instructions()
    {
        return view('download.instructions');
    }

    // Endpoint yang beneran nge-generate & kirim file zip
    public function widget()
    {
        $sourceDir = storage_path('app/releases');
        $zipPath   = storage_path('app/releases/BiosensorMonitor.zip');

        if (file_exists($zipPath)) {
            unlink($zipPath);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            abort(500, 'Gagal membuat file zip.');
        }

        foreach ($this->files as $file) {
            $fullPath = $sourceDir . DIRECTORY_SEPARATOR . $file;
            if (!file_exists($fullPath)) {
                $zip->close();
                abort(500, "File hilang: {$file}. Pastikan sudah ditaruh di storage/app/releases/");
            }
            $zip->addFile($fullPath, $file);
        }
        $zip->close();

        return response()->download($zipPath, 'BiosensorMonitor.zip')->deleteFileAfterSend(false);
    }
}
