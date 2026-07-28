<?php

use App\Http\Controllers\Auth\FirebaseSessionController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevicePairingController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ── Root ───────────────────────────────────────────────────────────────
Route::get('/', function () {
    return session('firebase_uid')
        ? redirect()->route('dashboard')
        : view('landing');
})->name('landing');

// ── Halaman publik (auth) ────────────────────────────────────────────────
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

Route::post('/auth/session', [FirebaseSessionController::class, 'store']); // dipanggil dari JS
Route::get('/logout-server', [FirebaseSessionController::class, 'destroy'])->name('logout');

// ── Verifikasi kode (register & login Google pertama kali) ───────────────
Route::get('/verify-email', [VerificationController::class, 'form'])->name('verify.email.form');
Route::post('/verify-email', [VerificationController::class, 'verify'])->name('verify.email.submit');
Route::post('/verify-email/resend', [VerificationController::class, 'resend'])->name('verify.email.resend');

// ── Lupa password (server-side, pakai kode OTP) ───────────────────────────
Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendCode'])->name('password.email');
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// ── Halaman yang butuh login ──────────────────────────────────────────────
Route::middleware('firebase.auth')->group(function () {

    Route::get('/device/pair', [DevicePairingController::class, 'form'])->name('device.pair.form');
    Route::post('/device/pair', [DevicePairingController::class, 'store'])->name('device.pair.store');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/live-data', [DashboardController::class, 'liveData'])->name('dashboard.live'); // buat AJAX polling

    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::delete('/history/{id}', [HistoryController::class, 'destroy'])->name('history.destroy');

    Route::get('/download', [DownloadController::class, 'instructions'])->name('download.instructions');
    Route::get('/download/widget', [DownloadController::class, 'widget'])->name('download.widget');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/name', [ProfileController::class, 'updateName'])->name('profile.name.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::post('/profile/email/request', [ProfileController::class, 'requestEmailChange'])->name('profile.email.request');
    Route::post('/profile/email/confirm', [ProfileController::class, 'confirmEmailChange'])->name('profile.email.confirm');
    Route::post('/profile/email/cancel', [ProfileController::class, 'cancelEmailChange'])->name('profile.email.cancel');
    Route::post('/profile/password/request', [ProfileController::class, 'requestPasswordChange'])->name('profile.password.request');
    Route::post('/profile/password/confirm', [ProfileController::class, 'confirmPasswordChange'])->name('profile.password.confirm');
});
