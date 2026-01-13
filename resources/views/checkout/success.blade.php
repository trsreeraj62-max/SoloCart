@extends('layouts.app')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen py-16 flex items-center justify-center">
    <div class="container container-max px-4">
        
        <div class="max-w-xl mx-auto bg-white rounded-sm shadow-2xl overflow-hidden border border-slate-100 text-center">
            <!-- Success Banner -->
            <div class="bg-green-500 p-12 text-white relative">
                <div class="relative z-10 animate-in zoom-in duration-500">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                        <i class="fas fa-check text-green-500 text-4xl"></i>
                    </div>
                    <h1 class="text-3xl font-black tracking-tighter uppercase m-0">TRANSACTION SECURED</h1>
                    <p class="text-[10px] font-black uppercase tracking-[0.4em] opacity-80 mt-2">Manifest #{{ $order->id }} confirmed</p>
                </div>
                <!-- Abstract patterns -->
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <i class="fas fa-shield-alt absolute -top-10 -right-10 text-[15rem] rotate-12"></i>
                </div>
            </div>

            <!-- Content -->
            <div class="p-10 space-y-8">
                <div class="space-y-4">
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-widest">Protocol Success</h3>
                    <p class="text-sm text-slate-500 font-medium leading-relaxed">
                        Your acquisition has been officially logged in our central archive. Our logistics team has been signaled for rapid dispatch.
                    </p>
                </div>

                <div class="bg-slate-50 rounded-sm p-6 border border-slate-100 space-y-4">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 font-bold uppercase tracking-widest">Acquisition Value</span>
                        <span class="text-slate-900 font-black">₹{{ number_format($order->total) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 font-bold uppercase tracking-widest">Destination Locked</span>
                        <span class="text-slate-900 font-black truncate max-w-[200px]">{{ $order->address }}</span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4">
                    <a href="{{ route('orders.show', $order->id) }}" class="flex-1 bg-[#2874f0] text-white py-4 rounded-sm text-xs font-black uppercase tracking-widest hover:bg-[#1266ec] transition-all no-underline shadow-lg shadow-blue-100">
                        View Manifest
                    </a>
                    <a href="{{ route('home') }}" class="flex-1 bg-white text-slate-900 border border-slate-200 py-4 rounded-sm text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-all no-underline">
                        Return to Hub
                    </a>
                </div>

                <p class="text-[9px] font-black text-slate-300 uppercase tracking-[0.2em] pt-4">Estimated Dispatch: Within 24 Hours</p>
            </div>
        </div>

    </div>
</div>
@endsection
