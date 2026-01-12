@extends('layouts.app')

@section('content')
<div class="container flex justify-center items-center" style="min-height: 80vh;">
    <div class="card p-8" style="width: 100%; max-width: 400px;">
        <h2 class="text-center font-bold text-xl mb-4">Login</h2>
        
        @if(session('error'))
            <div style="background: #fee2e2; color: #b91c1c; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 font-bold text-sm">Email Address</label>
                <input type="email" name="email" class="input-field" required value="{{ old('email') }}">
                @error('email') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block mb-2 font-bold text-sm">Password</label>
                <input type="password" name="password" class="input-field" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Login</button>
        </form>
        
        <p class="text-center mt-4 text-sm">
            Don't have an account? <a href="{{ route('register') }}" class="text-primary font-bold">Sign Up</a>
        </p>
    </div>
</div>
@endsection
