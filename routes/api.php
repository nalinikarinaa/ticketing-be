<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

Route::get('/test', function () {
    return response()->json([
        'message' => 'API OK',
    ]);
});

Route::post('/register', [AuthController::class, 'register']);
