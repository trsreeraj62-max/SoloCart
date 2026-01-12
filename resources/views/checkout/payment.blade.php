@extends('layouts.app')

@section('content')
<div class="payment-page py-12 bg-slate-50 min-h-screen">
    <div class="container" style="max-width: 900px; margin: 0 auto; padding: 0 1.5rem;">
        
        <div class="flex items-center gap-4 mb-10">
            <h1 class="text-3xl font-black text-slate-800 m-0">Payment Options</h1>
            <div class="flex-1 h-px bg-slate-200"></div>
            <div class="flex items-center gap-2 text-slate-400 font-bold text-sm">
                <span class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px]"><i class="fas fa-check"></i></span>
                Summary
                <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-[10px]">2</span>
                Payment
            </div>
        </div>

        <div class="grid lg:grid-cols-12 gap-8">
            <!-- Left: Options -->
            <div class="lg:col-span-8 space-y-4">
                <form id="paymentForm" action="{{ route('checkout.confirm', $order->id) }}" method="POST">
                    @csrf
                    
                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                        
                        <!-- UPI -->
                        <label class="payment-option p-8 flex items-start gap-6 cursor-pointer hover:bg-slate-50 transition border-b border-slate-50">
                            <input type="radio" name="payment_method" value="upi" class="mt-1.5 w-5 h-5 accent-primary" checked>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-black text-slate-800 text-lg uppercase tracking-tight">UPI (Google Pay / PhonePe)</span>
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/UPI-Logo.png/600px-UPI-Logo.png" class="h-4">
                                </div>
                                <p class="text-slate-400 text-sm font-medium">Fast & Secure payment using any UPI App.</p>
                                
                                <div class="mt-6 vpa-input scale-y-0 origin-top h-0 transition-all opacity-0">
                                    <input type="text" placeholder="Enter UPI ID (e.g. user@okaxis)" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary text-sm font-bold">
                                    <button type="button" class="mt-3 text-primary font-black text-xs uppercase tracking-widest">Verify ID</button>
                                </div>
                            </div>
                        </label>

                        <!-- Cards -->
                        <label class="payment-option p-8 flex items-start gap-6 cursor-pointer hover:bg-slate-50 transition border-b border-slate-50">
                            <input type="radio" name="payment_method" value="card" class="mt-1.5 w-5 h-5 accent-primary">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-black text-slate-800 text-lg uppercase tracking-tight">Credit / Debit Cards</span>
                                    <div class="flex gap-2">
                                        <i class="fab fa-cc-visa text-slate-400 text-xl"></i>
                                        <i class="fab fa-cc-mastercard text-slate-400 text-xl"></i>
                                    </div>
                                </div>
                                <p class="text-slate-400 text-sm font-medium">Visa, Mastercard, RuPay & more.</p>
                            </div>
                        </label>

                        <!-- Netbanking -->
                        <label class="payment-option p-8 flex items-start gap-6 cursor-pointer hover:bg-slate-50 transition border-b border-slate-50">
                            <input type="radio" name="payment_method" value="netbanking" class="mt-1.5 w-5 h-5 accent-primary">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-black text-slate-800 text-lg uppercase tracking-tight">Net Banking</span>
                                    <i class="fas fa-university text-slate-400 text-xl"></i>
                                </div>
                                <p class="text-slate-400 text-sm font-medium">Pay via your preferred bank account.</p>
                            </div>
                        </label>

                        <!-- COD -->
                        <label class="payment-option p-8 flex items-start gap-6 cursor-pointer hover:bg-slate-50 transition">
                            <input type="radio" name="payment_method" value="cod" class="mt-1.5 w-5 h-5 accent-primary">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-black text-slate-800 text-lg uppercase tracking-tight">Cash on Delivery</span>
                                    <i class="fas fa-money-bill-wave text-slate-400 text-xl"></i>
                                </div>
                                <p class="text-slate-400 text-sm font-medium">Pay when your order arrives.</p>
                            </div>
                        </label>

                    </div>

                    <button type="submit" class="w-full mt-8 py-5 bg-primary text-white font-black rounded-[2rem] shadow-2xl hover:shadow-primary/40 transition-all flex items-center justify-center gap-3 uppercase tracking-[0.2em] text-sm transform hover:-translate-y-1">
                        Finish Payment <i class="fas fa-lock"></i>
                    </button>
                </form>
            </div>

            <!-- Right: Tiny Summary -->
            <div class="lg:col-span-4">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                    <h4 class="text-slate-400 uppercase text-[10px] font-black tracking-widest mb-4">You're Paying</h4>
                    <div class="text-4xl font-black text-slate-900 mb-2 tracking-tighter italic">${{ number_format($order->total, 2) }}</div>
                    <p class="text-xs text-slate-400 font-bold mb-6">Order ID: #{{ $order->id }}</p>
                    
                    <div class="h-px bg-slate-50 w-full mb-6"></div>
                    
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                        <div class="flex justify-between text-xs font-bold text-slate-600">
                            <span>{{ Str::limit($item->product->name, 20) }}</span>
                            <span>x{{ $item->quantity }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .payment-option input:checked + div .vpa-input {
        scale-y: 1;
        height: auto;
        opacity: 1;
        margin-top: 1.5rem;
    }
</style>
@endsection
