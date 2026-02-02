<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\RefreshToken;
use App\Services\BrevoMailService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

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
                
                [$token, $cookie] = $this->issueTokens($user);

                return $this->success([
                    'token' => $token,
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'role' => 'admin',
                    'user' => $user
                ], 'Admin Login Successful')->withCookie($cookie);
            }

            if (!$user->email_verified_at) {
                return $this->error('Email not verified', 403);
            }

            // Check 2 months logic for API? 
            // For now, standard functionality implies just logging in if verified.
            // We update last_login_at.
            $user->update(['last_login_at' => now()]);

            [$token, $cookie] = $this->issueTokens($user);

            return $this->success([
                'token' => $token,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'role' => 'user',
                'user' => $user
            ], 'Login successful')->withCookie($cookie);
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
        
        [$token, $cookie] = $this->issueTokens($user);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully',
            'token' => $token,
            'user' => $user,
        ], 200)->withCookie($cookie);
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
        $user = $request->user();
        if ($user) {
            $user->currentAccessToken()->delete();
            
            // Revoke refresh token from cookie
            $refreshToken = $request->cookie('refresh_token');
            if ($refreshToken) {
                RefreshToken::where('token', hash('sha256', $refreshToken))->update(['revoked' => true]);
            }
        }

        return $this->success([], 'Logged out')->withCookie(Cookie::forget('refresh_token'));
    }

    public function refresh(Request $request)
    {
        $refreshTokenStr = $request->cookie('refresh_token');

        if (!$refreshTokenStr) {
            return $this->error('Refresh token missing', 401);
        }

        $tokenRecord = RefreshToken::where('token', hash('sha256', $refreshTokenStr))
            ->where('revoked', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$tokenRecord) {
            return $this->error('Invalid or expired refresh token', 401)->withCookie(Cookie::forget('refresh_token'));
        }

        $user = $tokenRecord->user;
        
        // Revoke old refresh token (Token Rotation)
        $tokenRecord->update(['revoked' => true]);

        // Issue new tokens
        [$token, $cookie] = $this->issueTokens($user);

        return $this->success([
            'token' => $token,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 'Token refreshed')->withCookie($cookie);
    }

    private function issueTokens(User $user)
    {
        $tokenName = $user->role === 'admin' ? 'admin_token' : 'auth_token';
        $accessToken = $user->createToken($tokenName)->plainTextToken;

        $refreshTokenStr = Str::random(64);
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $refreshTokenStr),
            'expires_at' => now()->addDays(7),
        ]);

        $cookie = cookie(
            'refresh_token',
            $refreshTokenStr,
            7 * 24 * 60, // 7 days in minutes
            '/',
            null,
            true, // secure
            true, // httpOnly
            false,
            'None' // Using None for cross-site if needed, but 'Lax' or 'Strict' is better for stateful. 
                   // However, standard API usage with cookies often needs 'None' if domains differ or it's a separate frontend on Render.
                   // Actually, if it's solocart-frontend.onrender.com vs solocart-backend.onrender.com, 'None' + Secure is required.
        );

        return [$accessToken, $cookie];
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
        
        $email = strtolower($request->email);
        $user = User::where('email', $email)->first();
        
        // Security: Always return success to prevent email enumeration
        if (!$user) {
            return $this->success([], 'If an account exists with this email, a password reset link has been sent.');
        }

        // Generate Secure Token
        $token = \Illuminate\Support\Str::random(64);
        
        // Store in DB (Standard Laravel Table)
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $email)->delete();
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => Hash::make($token), // Hash token for security
            'created_at' => now()
        ]);

        // Create Link
        // Force the correct Render URL as requested, ignoring potential misconfigured env vars
        $frontendUrl = 'https://solocart-frontend.onrender.com';
        // Assume frontend handles /reset-password?token=XYZ
        $link = "{$frontendUrl}/reset-password.html?token={$token}&email=" . urlencode($email); 
        // Note: Email is often needed payload for reset, but user prompt said payload is only token, password, confirmation.
        // If payload relies ONLY on token, the backend must find email by token.
        // Standard Laravel password_resets stores hashed token, so we can't look up by token easily unless we iterate or store plain (Laravel default stores plain in v10-, hashed in v11? wait).
        // Update: Standard Laravel `password_reset_tokens` has `token` column. 
        // If we hash it, we can't find the row by token alone. exist? 
        // PROMPT REQUIREMENTS: 
        // Payload: { "token": "...", "password": "...", "password_confirmation": "..." }
        // This implies the lookup MUST be done via Token.
        // If we hash the token in DB, we cannot look it up by token from Request.
        // So we must store it PLAIN TEXT in DB if we want to lookup by token, OR the frontend must send Email + Token.
        // PROMPT says: Payload: { "token": "...", "password": "..." }. NO EMAIL.
        // So I must receive the token and find the user.
        // Thus, I will store the token as PLAIN TEXT or (if security demands) I'm stuck because I can't look it up.
        // However, Laravel default PasswordBroker uses email + token. 
        // Since I am implementing custom flow:
        // I will store the token (Str::random(64)) directly in `token` column.
        // Wait, `password_reset_tokens` usually has (email, token, created_at).
        // If I can't send email in payload, I can't use composite key.
        // I will rely on `token` being unique enough? No, `password_reset_tokens` doesn't enforce unique token usually.
        // But for this custom flow, I will create a unique token.
        // Let's assume I can store it.
        
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $email)->delete();
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token, // Store plain to allow lookup by token
            'created_at' => now()
        ]);

        try {
            BrevoMailService::sendPasswordResetLink($email, $link);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Forgot Password Email Error: " . $e->getMessage());
             return $this->error('Failed to send email. Please try again later.', 500);
        }

        return $this->success([], 'If an account exists with this email, a password reset link has been sent.');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        // Find token in DB
        // Since we don't have email, we look for the token.
        // CAUTION: Laravel default table has (email, token). Email is primary? No, usually index.
        // schema: string('email')->primary();
        // matches `0001_01_01_000000_create_users_table.php`: $table->string('email')->primary();
        // So email is primary key. One token per email.
        // We can query `DB::table('...')->where('token', $request->token)->first()`
        
        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
                    ->where('token', $request->token)
                    ->first();

        if (!$record) {
            return $this->error('Invalid or expired reset token.', 400);
        }

        // Check Expiry (15 mins)
        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $record->email)->delete();
            return $this->error('Reset link has expired.', 400);
        }

        $user = User::where('email', $record->email)->first();
        if (!$user) {
             return $this->error('User not found.', 404);
        }

        // Update Password
        $user->password = Hash::make($request->password);
        $user->save();

        // Invalidate Token
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->where('email', $record->email)->delete();

        return $this->success([], 'Password updated successfully. You can now login with your new password.');
    }
}
