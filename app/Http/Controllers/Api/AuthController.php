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
    public function register(Request $request, \App\Services\BrevoService $brevoService)
    {
        try {
            // 1. Sanitize & Validate
            $request->merge(['email' => strtolower($request->email)]);
            
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'password' => 'required|min:6',
                'phone' => 'required|string|max:20',
            ]);

            // 2. Atomic Transaction
            \Illuminate\Support\Facades\DB::beginTransaction();

            $user = User::where('email', $request->email)->first();

            if ($user && $user->email_verified_at) {
                // Production: standard error response
                 throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => 'The email has already been taken.'
                ]);
            }

            if ($user) {
                // Existing unverified user -> Update
                $user->update([
                    'name' => $request->name,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'role' => 'user', 
                ]);
            } else {
                // Create New
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'role' => 'user',
                ]);
            }

            // 3. Generate & Securely Store OTP
            $otp = rand(100000, 999999);
            // Single source of truth: Cache. Expires in 10 mins.
            Cache::put('otp_' . $user->id, $otp, 600);

            // 4. Send Email via Brevo HTTP API
            $mailSent = $brevoService->sendOtp($user->email, $otp, $user->name);

            if (!$mailSent) {
                \Illuminate\Support\Facades\DB::rollBack();
                // Production Error: Generic message, no technical details
                return $this->error('Failed to send verification email. Please try again later.', 503);
            }

            \Illuminate\Support\Facades\DB::commit();

            // 5. Clean Response
            return $this->success([
                'user_id' => $user->id,
                'email' => $user->email
            ], 'User registered. Please check your email for the OTP.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return $this->error('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Registration System Error: ' . $e->getMessage());
            return $this->error('An unexpected error occurred during registration.', 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Normalize email to lowercase
            $email = strtolower($request->email);
            $user = User::where('email', $email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->error('Invalid credentials', 401);
            }

            // Admin Bypass: Login immediately regardless of verification
            if ($user->role === 'admin') {
                $user->update(['last_login_at' => now()]);
                $token = $user->createToken('admin_token')->plainTextToken;
                return $this->success([
                    'token' => $token,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'role' => 'admin',
                    'user' => $user
                ], 'Admin Login Successful');
            }

            if (!$user->email_verified_at) {
                return $this->error('Email not verified', 403);
            }

            // Check 2 months logic for API? 
            // For now, standard functionality implies just logging in if verified.
            // We update last_login_at.
            $user->update(['last_login_at' => now()]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->success([
                'token' => $token,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role' => 'user',
                'user' => $user
            ], 'Login successful');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error("Validation failed", 422, $e->errors());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Login Error: ' . $e->getMessage());
            return $this->error("Login failed: " . $e->getMessage(), 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        // Strict Validation: Require identifiers + OTP
        $request->validate([
            'user_id' => 'required_without:email|exists:users,id',
            'email' => 'required_without:user_id|email|exists:users,email',
            'otp' => 'required|digits:6'
        ]);

        try {
            // Resolve User
            if ($request->filled('user_id')) {
                $user = User::find($request->user_id);
            } else {
                $user = User::where('email', strtolower($request->email))->first();
            }

            // Retrieve OTP from secure cache
            $cachedOtp = Cache::get('otp_' . $user->id);

            // Validation Logic
            if (!$cachedOtp) {
                return $this->error('OTP has expired. Please request a new one.', 400);
            }

            if ($cachedOtp != $request->otp) {
                return $this->error('Invalid OTP provided.', 400); // 400 Bad Request
            }

            // Success Flow
            // 1. Invalidate OTP (Single Use Enforcement)
            Cache::forget('otp_' . $user->id);

            // 2. Verify User
            $user->update([
                'email_verified_at' => now(), 
                'last_login_at' => now()
            ]);

            // 3. Issue Token
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->success([
                'token' => $token,
                'user' => $user,
                'token_type' => 'Bearer',
            ], 'Email verified and logged in successfully.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('OTP Verify Error: ' . $e->getMessage());
            return $this->error('Verification failed.', 500);
        }
    }
    
    public function resendOtp(Request $request, \App\Services\BrevoService $brevoService)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        try {
            $user = User::where('email', strtolower($request->email))->first();

            // Rate Limiting Check
            if (Cache::has('otp_rate_' . $user->id)) {
                 return $this->error('Please wait before requesting another OTP.', 429);
            }

            // Generate New OTP
            $otp = rand(100000, 999999);
            Cache::put('otp_' . $user->id, $otp, 600);
            Cache::put('otp_rate_' . $user->id, true, 60); // 1 minute cooldown

            // Send via Brevo
            $mailSent = $brevoService->sendOtp($user->email, $otp, $user->name);

            if (!$mailSent) {
                return $this->error('Unable to send OTP at this time.', 503);
            }

            return $this->success([], 'A new verification code has been sent to your email.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Resend OTP Error: ' . $e->getMessage());
            return $this->error('Something went wrong.', 500);
        }
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
        try {
            $user = Auth::user();
            
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
                'phone' => 'sometimes|string|max:20',
                'address' => 'sometimes|string|max:500',
                'profile_image' => 'sometimes|image|max:5120' // 5MB max
            ]);

            // Handle profile image upload if provided
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if ($user->profile_photo && \Storage::disk('public')->exists($user->profile_photo)) {
                    \Storage::disk('public')->delete($user->profile_photo);
                }
                
                $path = $request->file('profile_image')->store('profiles', 'public');
                $validated['profile_photo'] = $path;
                unset($validated['profile_image']); // Remove temp key
            }

            // Update user data
            $user->update(array_filter($validated));
            
            // Reload to get updated profile_photo_url accessor
            $user->refresh();

            return $this->success($user, 'Profile updated successfully');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        } catch (\Exception $e) {
            \Log::error('Profile Update Error: ' . $e->getMessage());
            return $this->error('Profile update failed: ' . $e->getMessage(), 500);
        }
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
