@extends('layouts.app')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-sm shadow-2xl p-8 border border-slate-100">
        
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 bg-[#2874f0]/10 text-[#2874f0] rounded-full items-center justify-center mb-4">
                <i class="fas fa-key text-2xl"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tighter uppercase">Security Protocol</h2>
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] mt-1">Verification Code Required</p>
        </div>

        @if(session('info'))
            <div class="bg-blue-50 border-l-4 border-[#2874f0] p-4 mb-6">
                <p class="text-[#2874f0] text-[10px] font-black uppercase m-0">{{ session('info') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-6">
                @foreach($errors->all() as $error)
                    <p class="text-rose-600 text-[10px] font-black uppercase tracking-tight m-0">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('otp.verify') }}" class="space-y-6">
            @csrf

            <div>
                <label for="otp" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3 block text-center">Enter 6-Digit Transmission Code</label>
                <input 
                    id="otp"
                    type="number" 
                    name="otp" 
                    class="w-full bg-slate-50 border-b-4 border-slate-200 py-4 text-center text-3xl font-black text-slate-800 focus:outline-none focus:border-[#2874f0] focus:bg-white transition-all tracking-[0.5em]"
                    placeholder="000000"
                    required>
            </div>

            <button type="submit" class="w-full bg-[#2874f0] text-white py-4 rounded-sm text-sm font-black uppercase tracking-widest hover:bg-[#1266ec] shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-2">
                Authorize Access <i class="fas fa-shield-check text-xs"></i>
            </button>

            <div class="text-center pt-6">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                    No Signal? <a href="#" class="text-[#2874f0] no-underline hover:underline ml-2">Request New Code</a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection
