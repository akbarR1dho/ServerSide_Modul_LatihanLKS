<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

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

        try {
            if (!$token = JWTAuth::attempt($req->only('email', 'password'))) {
                return response()->json([
                    'message' => 'Email or password incorrect'
                ], 401);
            }

            $user = auth()->user();

            return response()->json([
                'message' => 'Login success',
                'user' => $user,
                'accessToken' => $token
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Logout function
    public function logout(Request $req)
    {
        try {
            // Invalidate token
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'status' => 'success',
                'message' => 'Logout success',
            ]);
        } catch (JWTException $e) {
            // Jika terjadi error saat invalidate token
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to logout, please try again.',
            ], 500);
        }
    }

    // Register function
    public function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => bcrypt($req->password)
        ]);

        return response()->json([
            'message' => 'Register success',
            'user' => $user
        ], 200);
    }
}
