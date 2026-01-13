@extends('layouts.app')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-4xl w-full bg-white rounded-sm shadow-2xl flex flex-col md:flex-row overflow-hidden border border-slate-100">
        
        <!-- Left Side: Graphic -->
        <div class="md:w-1/2 bg-[#2874f0] p-12 text-white flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-4xl font-black italic tracking-tighter mb-4">SoloCart<span class="text-[#ffe500]">Plus</span></h1>
                <p class="text-lg font-medium opacity-80 leading-relaxed mb-8">Reconnect with the elite acquisition protocol. Access your personalized manifest and order blueprints.</p>
                
                <div class="space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center border border-white/20">
                            <i class="fas fa-shield-alt text-[#ffe500]"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black uppercase tracking-widest">Secure Gateway</p>
                            <p class="text-[10px] opacity-60 font-bold">Encrypted Data Transmission</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Background Decorations -->
            <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
        </div>

        <!-- Right Side: Form -->
        <div class="md:w-1/2 p-12 bg-white">
            <div class="mb-8">
                <h2 class="text-2xl font-black text-slate-900 tracking-tighter uppercase">Authorize Login</h2>
                <div class="h-1 bg-[#2874f0] w-12 mt-2"></div>
            </div>

            @if($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-6">
                    @foreach($errors->all() as $error)
                        <p class="text-rose-600 text-xs font-bold uppercase tracking-tight m-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-6">
                    <p class="text-rose-600 text-xs font-bold uppercase tracking-tight m-0">{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Communication Signal (Email)</label>
                    <div class="relative group">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#2874f0] transition-colors"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required 
                               class="w-full bg-slate-50 border border-slate-200 py-4 pl-12 pr-4 text-sm font-bold text-slate-800 focus:outline-none focus:border-[#2874f0] focus:bg-white transition-all rounded-sm" 
                               placeholder="ops@solocart.com">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest block m-0">Security Credential</label>
                        <a href="#" class="text-[9px] font-black uppercase text-[#2874f0] hover:underline">Lost Access?</a>
                    </div>
                    <div class="relative group">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-[#2874f0] transition-colors"></i>
                        <input type="password" name="password" required 
                               class="w-full bg-slate-50 border border-slate-200 py-4 pl-12 pr-4 text-sm font-bold text-slate-800 focus:outline-none focus:border-[#2874f0] focus:bg-white transition-all rounded-sm" 
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-[#2874f0] text-white py-4 rounded-sm text-sm font-black uppercase tracking-widest hover:bg-[#1266ec] shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-2">
                        Verify Identity <i class="fas fa-sign-in-alt text-xs"></i>
                    </button>
                </div>

                <div class="text-center pt-8">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">
                        New Operative? <a href="{{ route('register') }}" class="text-[#2874f0] no-underline hover:underline ml-2">Initialize Identity</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
