<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user', // default role
        ]);
        
        event(new Registered($user));

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Register berhasil',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }


public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email belum diverifikasi'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $redirect = $user->role === 'admin' ? '/dashboardadmin' : '/dashboard';

        return response()->json([
            'message'  => 'Login berhasil.',
            'token'    => $token,
            'redirect' => $redirect,
            'role'     => $user->role,
            'user'     => [
                'id'       => $user->id,
                'name'     => $user->name,
                // 'username' => $user->username,
                'email'    => $user->email,
                'role'     => $user->role,
            ],
        ]);
        
    }

    /**
     * Logout user (revoke token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }
}


