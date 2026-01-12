<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends ApiController
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
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));
        } catch (\Exception $e) {
            // Silently log mail error but continue registration in dev/local
            \Illuminate\Support\Facades\Log::error('OTP Mail Error: ' . $e->getMessage());
        }
        
        return $this->success([
            'user_id' => $user->id,
            'otp_debug' => config('app.env') !== 'production' ? $otp : null
        ], 'User registered. Please verify OTP sent to email.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials', 401);
        }

        // Admin bypass
        if ($user->role === 'admin') {
            $token = $user->createToken('auth_token')->plainTextToken;
            $user->update(['last_login_at' => now()]);
            return $this->success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role' => 'admin',
                'user' => $user
            ], 'Admin Login Successful');
        }

        // Check 2 months logic (for security)
        $twoMonthsAgo = Carbon::now()->subMonths(2);
        if (!$user->last_login_at || Carbon::parse($user->last_login_at)->lt($twoMonthsAgo)) {
             $otp = rand(100000, 999999);
             Cache::put('otp_' . $user->id, $otp, 600);
             try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));
             } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Login OTP Mail Error: ' . $e->getMessage());
             }
             
             return $this->success([
                 'require_otp' => true,
                 'user_id' => $user->id
             ], 'OTP Required. Sent to email.');
        }

        // Normal login
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->update(['last_login_at' => now()]);

        return $this->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => 'user',
            'user' => $user
        ], 'Login Successful');
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

            return $this->success([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'OTP Verified Successfully');
        }

        return $this->error('Invalid OTP', 400);
    }
    
    public function resendOtp(Request $request)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);
        $user = User::find($request->user_id);

        $otp = rand(100000, 999999);
        Cache::put('otp_' . $user->id, $otp, 600);
        
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Resend OTP Mail Error: ' . $e->getMessage());
        }

        return $this->success([
            'otp_debug' => config('app.env') !== 'production' ? $otp : null
        ], 'New OTP sent to email.');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success([], 'Logged out');
    }

    public function user(Request $request)
    {
        return $this->success($request->user(), 'User profile retrieved');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'sometimes|string',
            'phone' => 'sometimes',
            'address' => 'sometimes',
        ]);

        $user->update($request->only(['name', 'phone', 'address']));

        return $this->success($user, 'Profile updated');
    }

    public function uploadProfilePhoto(Request $request)
    {
        $request->validate(['photo' => 'required|image']);
        $user = Auth::user();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profiles', 'public');
            $user->update(['profile_photo' => $path]);
            return $this->success(['photo_url' => asset('storage/' . $path)], 'Photo uploaded');
        }

        return $this->error('Upload failed');
    }
}
