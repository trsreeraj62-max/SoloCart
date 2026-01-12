<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'user',
        ]);

        // Send OTP
        $otp = rand(100000, 999999);
        Cache::put('otp_' . $user->id, $otp, 600); // 10 minutes
        // In real app, send Email/SMS here. For now, we return it in response for testing.
        
        return response()->json([
            'message' => 'User registered. Please verify OTP.',
            'user_id' => $user->id,
            'otp_debug' => $otp 
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Admin bypass
        if ($user->role === 'admin') {
            $token = $user->createToken('auth_token')->plainTextToken;
            $user->update(['last_login_at' => now()]);
            return response()->json([
                'message' => 'Admin Login Successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role' => 'admin',
                'user' => $user
            ]);
        }

        // Check 2 months logic
        $twoMonthsAgo = Carbon::now()->subMonths(2);
        if (!$user->last_login_at || Carbon::parse($user->last_login_at)->lt($twoMonthsAgo)) {
             $otp = rand(100000, 999999);
             Cache::put('otp_' . $user->id, $otp, 600);
             return response()->json([
                 'message' => 'OTP Required',
                 'require_otp' => true,
                 'user_id' => $user->id,
                 'otp_debug' => $otp
             ]);
        }

        // Normal login
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->update(['last_login_at' => now()]);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => 'user',
            'user' => $user
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required'
        ]);

        $cachedOtp = Cache::get('otp_' . $request->user_id);

        if ($cachedOtp && $cachedOtp == $request->otp) {
            $user = User::find($request->user_id);
            Cache::forget('otp_' . $request->user_id);
            
            $token = $user->createToken('auth_token')->plainTextToken;
            $user->update(['last_login_at' => now(), 'email_verified_at' => now()]);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ]);
        }

        return response()->json(['message' => 'Invalid OTP'], 400);
    }
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function user(Request $request)
    {
        return $request->user();
    }
}
