<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AuthWebController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            $user = User::where('email', $request->email)->first();
    
            if (! $user || ! Hash::check($request->password, $user->password)) {
                 return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
            }
    
            // Admin bypass OTP
            if ($user->role === 'admin') {
                Auth::login($user);
                $user->update(['last_login_at' => now()]);
                return redirect()->route('admin.dashboard');
            }
    
            // Check 2 months logic
            $twoMonthsAgo = Carbon::now()->subMonths(2);
            if (!$user->last_login_at || Carbon::parse($user->last_login_at)->lt($twoMonthsAgo)) {
                 $otp = rand(100000, 999999);
                 Cache::put('otp_' . $user->id, $otp, 600);
                 // Send OTP (email)
                 \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));
                 
                 // Store user_id in session to verify OTP
                 session(['otp_user_id' => $user->id]);
                 return redirect()->route('otp.verify')->with('info', 'OTP sent to your email.');
            }
    
            // Normal Login
            Auth::login($user);
            $user->update(['last_login_at' => now()]);
            return redirect()->route('home');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Login Error: ' . $e->getMessage());
            return back()->with('error', 'Login failed. Please try again.')->withInput();
        }
    }
    
    public function register(Request $request) {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'phone' => 'nullable'
            ]);
            
            \Illuminate\Support\Facades\DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => 'user'
            ]);
            
            $otp = rand(100000, 999999);
            Cache::put('otp_' . $user->id, $otp, 600);
            
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));
            
            \Illuminate\Support\Facades\DB::commit();

            session(['otp_user_id' => $user->id]);
            return redirect()->route('otp.verify')->with('info', 'Please check your email for the verification code.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Register Error: ' . $e->getMessage());
            
            $msg = 'Registration failed. ';
            if (str_contains($e->getMessage(), 'Connection timed out')) {
                $msg .= 'Email server timeout. Please try again later.';
            } else {
                $msg .= 'Please try again.';
            }
            
            return back()->with('error', $msg)->withInput();
        }
    }
    
    public function verifyOtp(Request $request) {
        try {
            $userId = session('otp_user_id');
            if(!$userId) return redirect()->route('login');
            
            $cachedOtp = Cache::get('otp_' . $userId);
            
            if ($request->otp == $cachedOtp) {
                $user = User::find($userId);
                Auth::login($user);
                Cache::forget('otp_' . $userId);
                session()->forget('otp_user_id');
                $user->update(['last_login_at' => now()]);
                
                return redirect()->route('home');
            }
            
            return back()->withErrors(['otp' => 'Invalid OTP']);
        } catch (\Exception $e) {
             \Illuminate\Support\Facades\Log::error('OTP Verify Error: ' . $e->getMessage());
            return back()->with('error', 'Verification failed.');
        }
    }

    public function logout() {
        try {
            Auth::logout();
            return redirect()->route('home');
        } catch (\Exception $e) {
             \Illuminate\Support\Facades\Log::error('Logout Error: ' . $e->getMessage());
            return redirect()->route('home');
        }
    }
}
