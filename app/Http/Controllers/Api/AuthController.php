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
        try {
            // Normalize email to lowercase
            $request->merge(['email' => strtolower($request->email)]);

            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'phone' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if ($user && $user->email_verified_at) {
                // Return 422 with exact format expected by frontend validation errors
                throw \Illuminate\Validation\ValidationException::withMessages(['email' => 'The email has already been taken.']);
            }

            if ($user) {
                // User exists but is UNVERIFIED -> Update info
                $user->update([
                    'name' => $request->name,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'role' => 'user', 
                ]);
                \Illuminate\Support\Facades\Log::info("Existing unverified user re-registering: {$user->email}");
            } else {
                // New User
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'role' => 'user',
                ]);
                \Illuminate\Support\Facades\Log::info("New user registered: {$user->email}");
            }

            // Send OTP
            $otp = rand(100000, 999999);
            Cache::put('otp_' . $user->id, $otp, 600); // 10 minutes
            $mail_sent = false;
            $mail_error = null;
            $mailer_type = config('mail.default');

            \Illuminate\Support\Facades\Log::info("Attempting to send OTP to {$user->email} using mailer: {$mailer_type}");

            try {
                if ($mailer_type !== 'log') {
                    \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));
                    $mail_sent = true;
                    \Illuminate\Support\Facades\Log::info("OTP Email sent successfully to {$user->email}");
                } else {
                    $mail_error = "Server is in 'LOG' mode. No real email sent.";
                    \Illuminate\Support\Facades\Log::warning($mail_error);
                }
            } catch (\Exception $e) {
                $mail_error = $e->getMessage();
                \Illuminate\Support\Facades\Log::error('OTP Mail Error for ' . $user->email . ': ' . $mail_error);
            }
            
            $message = $mail_sent ? 'User registered. Please verify OTP sent to email.' : 'User registered but OTP mail failed.';
            if (!$mail_sent) {
                $message .= " Error: " . ($mail_error ?? 'Check server logs');
            }

            return $this->success([
                'user_id' => $user->id,
                'otp_sent' => $mail_sent,
                'otp_debug' => (!$mail_sent || config('app.env') !== 'production' || $request->has('debug')) ? $otp : null,
                'mail_driver' => $mailer_type,
                'mail_error' => $mail_error
            ], $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error("Validation failed", 422, $e->errors());
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Register Error: ' . $e->getMessage());
            return $this->error("Registration failed: " . $e->getMessage(), 500);
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
                'token' => $token,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'OTP Verified Successfully');
        }

        return $this->error('Invalid OTP', 400);
    }
    
    public function resendOtp(Request $request)
    {
        // Allow looking up by user_id OR email
        $request->validate([
            'user_id' => 'required_without:email|exists:users,id',
            'email' => 'required_without:user_id|exists:users,email'
        ]);

        if ($request->filled('user_id')) {
            $user = User::find($request->user_id);
        } else {
            $user = User::where('email', strtolower($request->email))->first();
        }

        $otp = rand(100000, 999999);
        Cache::put('otp_' . $user->id, $otp, 600);
        
        $mail_sent = false;
        $mail_error = null;
        $mailer_type = config('mail.default');

        try {
            if ($mailer_type !== 'log') {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));
                $mail_sent = true;
                \Illuminate\Support\Facades\Log::info("Resend OTP Email sent successfully to {$user->email}");
            } else {
                $mail_error = "Server is in 'LOG' mode. No real email sent.";
                \Illuminate\Support\Facades\Log::warning($mail_error);
            }
        } catch (\Exception $e) {
            $mail_error = $e->getMessage();
            \Illuminate\Support\Facades\Log::error('Resend OTP Mail Error: ' . $mail_error);
        }

        return $this->success([
            'otp_sent' => $mail_sent,
            'otp_debug' => (!$mail_sent || config('app.env') !== 'production' || $request->has('debug')) ? $otp : null,
            'mail_driver' => $mailer_type,
            'mail_error' => $mail_error
        ], $mail_sent ? 'New OTP sent to email.' : 'Failed to send OTP email: ' . ($mail_error ?? 'Unknown error'));
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
