@extends('layouts.app')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-4xl w-full bg-white rounded-sm shadow-2xl flex flex-col md:flex-row overflow-hidden border border-slate-100">
        
        <!-- Left Side: Graphic -->
        <div class="md:w-1/2 bg-[#2874f0] p-12 text-white flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-4xl font-black italic tracking-tighter mb-4">SoloCart<span class="text-[#ffe500]">Plus</span></h1>
                <p class="text-lg font-medium opacity-80 leading-relaxed mb-8">Join the prime circle of elite shoppers. Access exclusive manifestations and rapid dispatch protocols.</p>
                
                <ul class="space-y-6 m-0 p-0 list-none">
                    <li class="flex items-center gap-4 group">
                        <div class="w-10 h-10 bg-white/10 rounded flex items-center justify-center group-hover:bg-white/20 transition-all"><i class="fas fa-check text-[#ffe500]"></i></div>
                        <span class="text-sm font-bold tracking-wide">Premium Product Verification</span>
                    </li>
                    <li class="flex items-center gap-4 group">
                        <div class="w-10 h-10 bg-white/10 rounded flex items-center justify-center group-hover:bg-white/20 transition-all"><i class="fas fa-check text-[#ffe500]"></i></div>
                        <span class="text-sm font-bold tracking-wide">Priority Dispatch Line</span>
                    </li>
                    <li class="flex items-center gap-4 group">
                        <div class="w-10 h-10 bg-white/10 rounded flex items-center justify-center group-hover:bg-white/20 transition-all"><i class="fas fa-check text-[#ffe500]"></i></div>
                        <span class="text-sm font-bold tracking-wide">Elite Member Support</span>
                    </li>
                </ul>
            </div>

            <!-- Background Decorations -->
            <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
        </div>

        <!-- Right Side: Form -->
        <div class="md:w-1/2 p-12 bg-white">
            <div class="mb-8">
                <h2 class="text-2xl font-black text-slate-900 tracking-tighter">Initialize Identity</h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Join the Acquisition Network</p>
            </div>

            @if($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 mb-6">
                    @foreach($errors->all() as $error)
                        <p class="text-rose-600 text-[10px] font-black uppercase tracking-tight m-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Operative Name</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full bg-slate-50 border border-slate-200 py-3.5 pl-12 pr-4 text-sm font-bold text-slate-800 focus:outline-none focus:border-[#2874f0] focus:bg-white transition-all rounded-sm cursor-text" placeholder="John Doe">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Communication Signal (Email)</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-slate-50 border border-slate-200 py-3.5 pl-12 pr-4 text-sm font-bold text-slate-800 focus:outline-none focus:border-[#2874f0] focus:bg-white transition-all rounded-sm cursor-text" placeholder="ops@solocart.com">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Security Credential (Password)</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
                        <input type="password" name="password" required class="w-full bg-slate-50 border border-slate-200 py-3.5 pl-12 pr-4 text-sm font-bold text-slate-800 focus:outline-none focus:border-[#2874f0] focus:bg-white transition-all rounded-sm cursor-text" placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-[#2874f0] text-white py-4 rounded-sm text-sm font-black uppercase tracking-widest hover:bg-[#1266ec] shadow-lg shadow-blue-100 transition-all flex items-center justify-center gap-2">
                        Confirm Registration <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>

                <div class="text-center pt-6">
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">
                        Already Registered? <a href="{{ route('login') }}" class="text-[#2874f0] no-underline hover:underline ml-2">Authorize Login</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
