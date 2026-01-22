<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sanctum-test', function () {
    return 'Sanctum OK';
});

Route::get('/email/verify/{id}/{hash}', function ($id, $hash, Request $request) {
    $user = User::findOrFail($id);

    // Cek apakah hash di URL cocok dengan email user
    if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        abort(403, 'Invalid verification link.');
    }

    // Kalau belum terverifikasi, verifikasi sekarang
    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    // Redirect ke halaman login FE setelah verifikasi
    return redirect('http://localhost:5173/'); // sesuaikan URL FE-mu
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
