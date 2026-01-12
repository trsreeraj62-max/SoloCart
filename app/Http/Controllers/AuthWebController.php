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
             
             // Store user_id in session to verify OTP
             session(['otp_user_id' => $user->id]);
             return redirect()->route('otp.verify')->with('info', 'OTP sent to your email. (Debug: ' . $otp . ')');
        }

        // Normal Login
        Auth::login($user);
        $user->update(['last_login_at' => now()]);
        return redirect()->route('home');
    }
    
    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone' => 'required'
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'user'
        ]);
        
        $otp = rand(100000, 999999);
        Cache::put('otp_' . $user->id, $otp, 600);
        
        session(['otp_user_id' => $user->id]);
        return redirect()->route('otp.verify')->with('info', 'Please verify OTP. (Debug: ' . $otp . ')');
    }
    
    public function verifyOtp(Request $request) {
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
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('home');
    }
}
