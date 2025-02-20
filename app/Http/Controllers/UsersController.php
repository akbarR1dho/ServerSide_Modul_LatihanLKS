<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class UsersController extends Controller
{
    // Login function
    public function login(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'email' => 'required|email',
            'password' => 'required|min:5'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validate->errors()
            ], 422);
        }

        $user = User::where('email', $req->email)->first();

        if ($user && $user->password == $req->password) {
            // Login user
            Auth::login($user);

            // Hapus token sebelumnya
            $user->tokens()->delete();

            // Kirim response dengan token
            return response()->json([
                'message' => 'Login success',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'accessToken' => $user->createToken('auth-token')->plainTextToken
                ],
            ], 200);
        }

        return response()->json([
            'message' => 'Email or password incorrect'
        ], 401);
    }

    // Logout function
    public function logout()
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout success',
        ], 200);
    }
}
