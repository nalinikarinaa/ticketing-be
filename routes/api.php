<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TicketController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\User;


Route::get('/test', function () {
    return response()->json([
        'message' => 'API OK',
    ]);
});

Route::prefix('v1')->group(function () {

    // Auth API
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        // Kirim ulang email verifikasi (harus login)
        Route::post('/email/verification-notification', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Verification link sent!'
            ]);
        })->middleware(['auth:sanctum', 'throttle:6,1']);
    });

    // ✅ Link verifikasi email (diklik dari email)
   Route::get('/email/verify/{id}/{hash}', function ($id, $hash, Request $request) {
    $user = \App\Models\User::findOrFail($id);

    if (! hash_equals(
        (string) $hash,
        sha1($user->getEmailForVerification())
    )) {
        abort(403, 'Invalid verification link.');
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    // ✅ redirect ke FE
    return redirect(config('app.frontend_url') . '/');
    })->middleware(['signed', 'throttle:6,1'])->name('api.verification.verify');

    // Dashboard API (role based)
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/dashboardadmin', fn() => response()->json(['message' => 'Halaman Admin']));
    });

    Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
        Route::get('/dashboard', fn() => response()->json(['message' => 'Halaman User']));
    });

    // ngambil data untuk manajemen user
    Route::get('/users', [UserController::class, 'index']); //list semua
    Route::get('/users/{id}', [UserController::class, 'show']);   // detail
    Route::put('/users/{id}', [UserController::class, 'update']); // edit
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // delete

    // ADD TIKET
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/addticket', [TicketController::class, 'store']);
    });



});

