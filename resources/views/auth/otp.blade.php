@extends('layouts.app')

@section('content')
<div class="container flex justify-center items-center" style="min-height: 80vh;">
    <div class="card p-8" style="width: 100%; max-width: 400px;">
        <h2 class="text-center font-bold text-xl mb-4">Verify OTP</h2>
        
        @if(session('info'))
            <div style="background: #dbeafe; color: #1e40af; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
                {{ session('info') }}
            </div>
        @endif
        
        @if($errors->any())
            <div style="background: #fee2e2; color: #b91c1c; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('otp.verify') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 font-bold text-sm">Enter OTP sent to your email</label>
                <input type="text" name="otp" class="input-field" placeholder="xxxxxx" required style="text-align: center; font-size: 1.5rem; letter-spacing: 0.5rem;">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Verify OTP</button>
        </form>
    </div>
</div>
@endsection
