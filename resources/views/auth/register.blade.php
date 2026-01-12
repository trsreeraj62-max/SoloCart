@extends('layouts.app')

@section('content')
<div class="container flex justify-center items-center" style="min-height: 80vh;">
    <div class="card p-8" style="width: 100%; max-width: 400px;">
        <h2 class="text-center font-bold text-xl mb-4">Register</h2>
        
        @if($errors->any())
            <div style="background: #fee2e2; color: #b91c1c; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 font-bold text-sm">Full Name</label>
                <input type="text" name="name" class="input-field" required value="{{ old('name') }}">
            </div>

            <div class="mb-4">
                <label class="block mb-2 font-bold text-sm">Email Address</label>
                <input type="email" name="email" class="input-field" required value="{{ old('email') }}">
            </div>
            
            <div class="mb-4">
                 <label class="block mb-2 font-bold text-sm">Phone Number</label>
                 <input type="text" name="phone" class="input-field" required value="{{ old('phone') }}">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2 font-bold text-sm">Password (min 6 chars)</label>
                <input type="password" name="password" class="input-field" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Sign Up</button>
        </form>
        
        <p class="text-center mt-4 text-sm">
            Already have an account? <a href="{{ route('login') }}" class="text-primary font-bold">Login</a>
        </p>
    </div>
</div>
@endsection
