@extends('layouts.app')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen py-8">
    <div class="container container-max px-4">
        
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase italic">IDENTITY : <span class="text-[#2874f0]"> CORE PROTOCOL</span></h1>
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-[0.3em] mt-1">Manage your operative credentials and signal linkages</p>
            </div>

            @if(session('status'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 animate-in slide-in-from-top">
                    <p class="text-green-700 text-[10px] font-black uppercase tracking-widest m-0">{{ session('status') }}</p>
                </div>
            @endif

            <div class="row g-4">
                <!-- Left: Quick Info / Avatar -->
                <div class="col-lg-4">
                    <div class="bg-white rounded-sm shadow-sm border border-slate-100 p-8 text-center sticky top-20">
                        <div class="relative inline-block mb-6">
                            <div class="w-24 h-24 rounded-full bg-[#2874f0] text-white flex items-center justify-center text-4xl font-black border-4 border-slate-50 shadow-lg overflow-hidden">
                                <img src="{{ $user->profile_photo_url }}" class="w-full h-full object-cover">
                            </div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 bg-white rounded-full border border-slate-100 flex items-center justify-center text-[#2874f0] shadow-sm">
                                <i class="fas fa-camera text-xs"></i>
                            </div>
                        </div>
                        <h3 class="text-lg font-black text-slate-800 uppercase tracking-tight m-0">{{ $user->name }}</h3>
                        <p class="text-[10px] font-black text-slate-400 mt-1 uppercase tracking-widest">{{ $user->email }}</p>
                        
                        <div class="mt-8 pt-6 border-t border-slate-50 space-y-3">
                            <div class="flex items-center justify-between text-left">
                                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Protocol Type</span>
                                <span class="text-[10px] font-black text-[#2874f0] uppercase bg-blue-50 px-2 py-0.5 rounded">{{ $user->role }}</span>
                            </div>
                            <div class="flex items-center justify-between text-left">
                                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Signal Active Since</span>
                                <span class="text-[10px] font-black text-slate-800">{{ $user->created_at->format('M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="bg-white rounded-sm shadow-sm border border-slate-100 p-8 md:p-10">
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                            @csrf
                            
                            <div class="space-y-6">
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-[0.4em] mb-6">Core Manifest</h4>
                                
                                <div class="row g-4">
                                    {{-- Avatar Upload Field --}}
                                    <div class="col-12 mb-4">
                                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Profile Photo Protocol</label>
                                        <div class="flex items-center gap-6">
                                            <img src="{{ $user->profile_photo_url }}" class="w-20 h-20 rounded-full object-cover border-2 border-slate-100">
                                            <input type="file" name="profile_photo" class="text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-blue-50 file:text-[#2874f0] hover:file:bg-blue-100 italic">
                                        </div>
                                        @error('profile_photo') <p class="text-rose-600 text-[10px] font-black uppercase mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Operative Identity (Name)</label>
                                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                                               class="w-full bg-slate-50 border border-slate-200 py-3 px-4 text-sm font-bold text-slate-800 focus:outline-none focus:border-[#2874f0] focus:bg-white transition-all rounded-sm">
                                        @error('name') <p class="text-rose-600 text-[10px] font-black uppercase mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Fixed Signal (Email)</label>
                                        <div class="w-full bg-slate-100 border border-slate-200 py-3 px-4 text-sm font-bold text-slate-400 rounded-sm cursor-not-allowed">
                                            {{ $user->email }}
                                        </div>
                                        <p class="text-[8px] font-bold text-slate-300 uppercase tracking-widest mt-1">Signal locking prevents unauthorized masking</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Mobile Terminal (Phone)</label>
                                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                                               class="w-full bg-slate-50 border border-slate-200 py-3 px-4 text-sm font-bold text-slate-800 focus:outline-none focus:border-[#2874f0] focus:bg-white transition-all rounded-sm" 
                                               placeholder="10 digit number">
                                    </div>
                                    <div class="col-12">
                                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Geographic Vector (Default Address)</label>
                                        <textarea name="address" rows="3" 
                                                  class="w-full bg-slate-50 border border-slate-200 py-3 px-4 text-sm font-bold text-slate-800 focus:outline-none focus:border-[#2874f0] focus:bg-white transition-all rounded-sm" 
                                                  placeholder="Address for rapid dispatch">{{ old('address', $user->address) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Security Notice --}}
                            <div class="p-6 bg-slate-50 rounded-sm border border-slate-100 flex items-start gap-4">
                                <i class="fas fa-shield-alt text-[#2874f0] mt-1"></i>
                                <div>
                                    <h5 class="text-[11px] font-black text-slate-900 uppercase tracking-widest mb-1">Security Paradigm</h5>
                                    <p class="text-[10px] text-slate-500 font-medium leading-relaxed m-0 italic">Credential modification is logged in the central archive. Password reset protocols require multi-factor verification through your fixed email signal.</p>
                                </div>
                            </div>

                            <button type="submit" class="bg-[#2874f0] text-white px-10 py-4 rounded-sm text-sm font-black uppercase tracking-[0.2em] shadow-lg shadow-blue-100 hover:bg-[#1266ec] transition-all w-full md:w-auto">
                                UPDATE ACCOUNT
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
