@extends('layouts.app')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen py-8">
    <div class="container container-max px-4">
        
        <!-- Step Indicator -->
        <div class="flex items-center justify-center mb-8 gap-4 md:gap-10">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center font-bold shadow-sm"><i class="fas fa-check text-[10px]"></i></span>
                <span class="text-xs font-black uppercase tracking-widest text-slate-400">Summary</span>
            </div>
            <div class="w-12 md:w-20 h-0.5 bg-green-200"></div>
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-[#2874f0] text-white flex items-center justify-center font-bold shadow-lg">2</span>
                <span class="text-xs font-black uppercase tracking-widest text-slate-800">Payment</span>
            </div>
            <div class="w-12 md:w-20 h-0.5 bg-slate-200"></div>
            <div class="flex items-center gap-2 opacity-30">
                <span class="w-8 h-8 rounded-full bg-slate-300 text-white flex items-center justify-center font-bold">3</span>
                <span class="text-xs font-black uppercase tracking-widest text-slate-400">Finish</span>
            </div>
        </div>

        <form action="{{ route('checkout.success.post') }}" method="POST">
            @csrf
            <div class="row g-4 justify-center">
                
                <div class="col-lg-8">
                    <div class="space-y-4">
                        
                        <!-- Header -->
                        <div class="bg-white rounded-sm shadow-sm border border-slate-100 p-6 flex flex-col md:flex-row items-center justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-black text-slate-900 tracking-tighter m-0">PAYMENT GATEWAY</h1>
                                <p class="text-[10px] font-black uppercase text-slate-400 tracking-[0.3em] mt-1">Select your preferred transaction protocol</p>
                            </div>
                            <div class="bg-slate-50 border border-slate-100 rounded-sm px-6 py-3 text-center">
                                <p class="text-[9px] font-black text-slate-400 uppercase mb-1">DUE AMOUNT</p>
                                <p class="text-xl font-black text-[#2874f0] m-0">₹{{ number_format($totalPrice) }}</p>
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        <div class="bg-white rounded-sm shadow-sm border border-slate-100 overflow-hidden">
                            <div class="divide-y divide-slate-100">
                                
                                {{-- PhonePe/UPI --}}
                                <label class="p-6 flex items-start gap-4 cursor-pointer hover:bg-slate-50 transition-colors group">
                                    <input type="radio" name="method" value="upi" checked class="mt-1.5 w-4 h-4 text-[#2874f0] border-slate-300 focus:ring-[#2874f0]">
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-bold text-slate-800 uppercase tracking-wide">UPI Protocol (Instant)</span>
                                            <span class="bg-green-100 text-green-700 text-[9px] font-black px-2 mt-0.5 rounded uppercase">Optimized</span>
                                        </div>
                                        <p class="text-xs text-slate-400 mt-1">PhonePe, Google Pay, BHIM, etc. Highly recommended.</p>
                                    </div>
                                    <div class="flex gap-2 opacity-40 group-hover:opacity-100 transition-opacity">
                                        <i class="fab fa-google-pay text-2xl"></i>
                                        <i class="fas fa-university text-xl"></i>
                                    </div>
                                </label>

                                {{-- Card --}}
                                <label class="p-6 flex items-start gap-4 cursor-pointer hover:bg-slate-50 transition-colors group opacity-60 grayscale">
                                    <input type="radio" name="method" value="card" disabled class="mt-1.5 w-4 h-4 text-[#2874f0] border-slate-300 focus:ring-[#2874f0]">
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-bold text-slate-800 uppercase tracking-wide">Credit / Debit Cards</span>
                                            <span class="bg-slate-100 text-slate-500 text-[9px] font-black px-2 mt-0.5 rounded uppercase italic">Maintenance</span>
                                        </div>
                                        <p class="text-xs text-slate-400 mt-1">Visa, Mastercard, RuPay & More. Currently unavailable.</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <i class="fab fa-cc-visa text-2xl"></i>
                                        <i class="fab fa-cc-mastercard text-2xl"></i>
                                    </div>
                                </label>

                                {{-- COD --}}
                                <label class="p-6 flex items-start gap-4 cursor-pointer hover:bg-slate-50 transition-colors group">
                                    <input type="radio" name="method" value="cod" class="mt-1.5 w-4 h-4 text-[#2874f0] border-slate-300 focus:ring-[#2874f0]">
                                    <div class="flex-grow">
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-bold text-slate-800 uppercase tracking-wide">Collection on Arrival (COD)</span>
                                        </div>
                                        <p class="text-xs text-slate-400 mt-1">Cash or QR payment at your location. +₹40 handling may apply.</p>
                                    </div>
                                    <i class="fas fa-hand-holding-usd text-2xl text-slate-200"></i>
                                </label>

                            </div>
                        </div>

                        <!-- Confirmation Button -->
                        <div class="bg-white rounded-sm shadow-xl border border-slate-100 p-8 flex flex-col items-center">
                            <div class="text-center mb-6 max-w-sm">
                                <i class="fas fa-shield-alt text-[#2874f0] text-3xl mb-4"></i>
                                <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest leading-loose">Secure Terminal Confirmation</h4>
                                <p class="text-[11px] text-slate-400 leading-relaxed italic">By clicking below, you authorize the generation of this order manifest and agree to our Transactional Protocols.</p>
                            </div>
                            <button type="submit" class="bg-[#fb641b] text-white px-16 py-4 rounded-sm text-sm font-black uppercase tracking-[0.3em] shadow-2xl shadow-orange-200 hover:bg-[#ff4500] hover:scale-105 transition-all">
                                CONFIRM & PAY ₹{{ number_format($totalPrice) }}
                            </button>
                            <p class="text-[9px] font-bold text-slate-300 mt-6 uppercase tracking-widest"><i class="fas fa-lock mr-2"></i> End-to-End Cryptographic Protocol</p>
                        </div>

                    </div>
                </div>

            </div>
        </form>

    </div>
</div>
@endsection
