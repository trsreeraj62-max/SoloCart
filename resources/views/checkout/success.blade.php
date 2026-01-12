@extends('layouts.app')

@section('content')
<div class="success-page py-20 bg-white min-h-screen flex items-center justify-center">
    <div class="container text-center" style="max-width: 600px;">
        
        <div class="relative inline-block mb-10">
            <div class="w-32 h-32 bg-green-100 rounded-full flex items-center justify-center mx-auto text-green-600 animate-bounce">
                <i class="fas fa-check text-5xl"></i>
            </div>
            <!-- Confetti-like dots -->
            <div class="absolute top-0 -right-4 w-4 h-4 bg-blue-400 rounded-full"></div>
            <div class="absolute bottom-0 -left-6 w-3 h-3 bg-red-400 rounded-full"></div>
            <div class="absolute -top-6 left-10 w-2 h-2 bg-yellow-400 rounded-full"></div>
        </div>

        <h1 class="text-5xl font-black text-slate-900 mb-4 tracking-tighter">Payment Successful!</h1>
        <p class="text-slate-500 text-lg mb-10 leading-relaxed font-medium">
            Thank you for your order. We've received your payment and our team is already preparing your package for delivery.
        </p>

        <div class="bg-slate-50 rounded-[2.5rem] p-8 border border-slate-100 mb-10 text-left">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Order ID</div>
                    <div class="text-xl font-black text-slate-800">#{{ $order->id }}</div>
                </div>
                <div class="text-right">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Paid</div>
                    <div class="text-xl font-black text-primary">${{ number_format($order->total, 2) }}</div>
                </div>
            </div>
            
            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-xl p-1 border border-slate-200">
                        <img src="{{ asset('storage/' . ($item->product->images->first()->image_path ?? '')) }}" class="w-full h-full object-contain">
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-bold text-slate-700">{{ $item->product->name }}</div>
                        <div class="text-[10px] text-slate-400 font-bold italic">Qty: {{ $item->quantity }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('orders.index') }}" class="flex-1 py-5 bg-slate-900 text-white font-black rounded-2xl shadow-xl hover:shadow-slate-900/30 transition no-underline uppercase tracking-widest text-xs">
                View My Orders
            </a>
            <a href="{{ route('home') }}" class="flex-1 py-5 bg-primary text-white font-black rounded-2xl shadow-xl hover:shadow-primary/30 transition no-underline uppercase tracking-widest text-xs">
                Continue Shopping
            </a>
        </div>

    </div>
</div>
@endsection
