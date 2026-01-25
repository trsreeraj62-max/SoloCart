<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\BrevoMailService;

class AuthController extends ApiController
{
    public function register(Request $request)
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
            Cache::put('otp_' . $user->email, $otp, 600); // Changed to email key to match verifyOtp refactor

            // 4. Send Email via Brevo HTTP API
            try {
                // Use Static Service
                BrevoMailService::sendOtp($user->email, $otp);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                \Illuminate\Support\Facades\Log::error("Brevo Mail Failed: " . $e->getMessage());
                // Production Error
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
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        // Key based on EMAIL not ID
        $storedOtp = Cache::get('otp_' . $request->email);

        if (!$storedOtp || $storedOtp != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP',
            ], 422);
        }

        Cache::forget('otp_' . $request->email);

        $user = User::where('email', $request->email)->firstOrFail();
        
        // Update verification status
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
        }
        $user->update(['last_login_at' => now()]);
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
            'token' => $token,
            'user' => $user,
        ], 200);
    }
    
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $otp = random_int(100000, 999999);

        // Store by EMAIL key
        Cache::put(
            'otp_' . $request->email,
            $otp,
            now()->addMinutes(5)
        );

        // Use the new BrevoMailService
        BrevoMailService::sendOtp($request->email, $otp);

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully to your email',
        ], 200);
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
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $user = User::where('email', $request->email)->first();
        
        // Security: Always return success to prevent email enumeration, unless debugging
        if (!$user) {
            return $this->success([], 'If an account exists with this email, a password reset code has been sent.');
        }

        $otp = rand(100000, 999999);
        Cache::put('reset_otp_' . $user->email, $otp, 600); // 10 mins

        try {
            BrevoMailService::sendPasswordResetOtp($user->email, $otp);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Forgot Password Email Error: " . $e->getMessage());
             return $this->error('Failed to send email. Please try again later.', 500);
        }

        return $this->success([], 'If an account exists with this email, a password reset code has been sent.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:6'
        ]);

        $cachedOtp = Cache::get('reset_otp_' . $request->email);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return $this->error('Invalid or expired OTP', 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
             return $this->error('User not found', 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Cache::forget('reset_otp_' . $request->email);

        return $this->success([], 'Password reset successfully. You can now login.');
    }
}
