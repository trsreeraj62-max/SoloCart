@extends('layouts.app')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen py-8">
    <div class="container container-max px-4">
        
        <!-- Step Indicator -->
        <div class="flex items-center justify-center mb-8 gap-4 md:gap-10">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-[#2874f0] text-white flex items-center justify-center font-bold shadow-lg">1</span>
                <span class="text-xs font-black uppercase tracking-widest text-slate-800">Summary</span>
            </div>
            <div class="w-12 md:w-20 h-0.5 bg-slate-200"></div>
            <div class="flex items-center gap-2 opacity-30">
                <span class="w-8 h-8 rounded-full bg-slate-300 text-white flex items-center justify-center font-bold">2</span>
                <span class="text-xs font-black uppercase tracking-widest text-slate-400">Payment</span>
            </div>
            <div class="w-12 md:w-20 h-0.5 bg-slate-200"></div>
            <div class="flex items-center gap-2 opacity-30">
                <span class="w-8 h-8 rounded-full bg-slate-300 text-white flex items-center justify-center font-bold">3</span>
                <span class="text-xs font-black uppercase tracking-widest text-slate-400">Finish</span>
            </div>
        </div>

        <form action="{{ route('checkout.payment') }}" method="GET">
            <div class="row g-4">
                <!-- Left Side: Address form -->
                <div class="col-lg-8">
                    <div class="space-y-4">
                        
                        <!-- User Identity (Readonly) -->
                        <div class="bg-white rounded-sm shadow-sm border border-slate-100">
                            <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                                <span class="bg-[#2874f0] text-white w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black">1</span>
                                <h4 class="text-sm font-black uppercase tracking-widest m-0 text-slate-400">LOGIN PROTOCOL</h4>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center gap-4">
                                    <h5 class="text-sm font-bold text-slate-900 m-0">{{ $user->name }}</h5>
                                    <span class="text-xs text-slate-400 font-medium">{{ $user->email }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Address -->
                        <div class="bg-white rounded-sm shadow-sm border border-slate-100">
                            <div class="p-4 bg-[#2874f0] border-b border-slate-100 flex items-center gap-3">
                                <span class="bg-white text-[#2874f0] w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-black">2</span>
                                <h4 class="text-sm font-black uppercase tracking-widest m-0 text-white">DELIVERY DESTINATION</h4>
                            </div>
                            <div class="p-8 space-y-6">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Full Name</label>
                                        <input type="text" name="name" value="{{ $user->name }}" required class="w-full bg-slate-50 border border-slate-200 py-3 px-4 text-sm font-bold focus:outline-none focus:border-[#2874f0] transition-colors rounded-sm">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Mobile Terminal</label>
                                        <input type="text" name="phone" value="{{ $user->phone }}" required class="w-full bg-slate-50 border border-slate-200 py-3 px-4 text-sm font-bold focus:outline-none focus:border-[#2874f0] transition-colors rounded-sm" placeholder="10-digit mobile number">
                                    </div>
                                    <div class="col-12">
                                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2 block">Vector Address (Detailed)</label>
                                        <textarea name="address" rows="3" required class="w-full bg-slate-50 border border-slate-200 py-3 px-4 text-sm font-bold focus:outline-none focus:border-[#2874f0] transition-colors rounded-sm" placeholder="House No, Building, Street, Area">{{ $user->address }}</textarea>
                                    </div>
                                </div>
                                
                                <button type="submit" class="bg-[#fb641b] text-white px-12 py-4 rounded-sm text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-orange-100 hover:bg-[#ff4500] transition-all w-full md:w-auto mt-4">
                                    DELIVER TO THIS ADDRESS
                                </button>
                            </div>
                        </div>

                        <!-- Order Summary View -->
                        <div class="bg-white rounded-sm shadow-sm border border-slate-100 hidden md:block">
                            <div class="p-4 bg-slate-50 border-b border-slate-100">
                                <h4 class="text-sm font-black uppercase tracking-widest m-0 text-slate-400">CARGO MANIFEST ({{ count($items) }} Items)</h4>
                            </div>
                            <div class="p-0 divide-y divide-slate-50">
                                @foreach($items as $item)
                                <div class="p-4 flex gap-4 items-center">
                                    <div class="w-16 h-16 bg-slate-50 p-1 flex-shrink-0 border border-slate-100">
                                        <img src="{{ $item->product->image_url }}" class="w-full h-full object-contain">
                                    </div>
                                    <div class="flex-grow">
                                        <h5 class="text-xs font-bold text-slate-800 line-clamp-1 m-0">{{ $item->product->name }}</h5>
                                        <p class="text-[10px] text-slate-400 mt-1 uppercase font-black tracking-widest">Qty: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-black text-slate-900">₹{{ number_format($item->price * $item->quantity) }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right Side: Price Details -->
                <div class="col-lg-4">
                    <div class="sticky top-20 bg-white rounded-sm shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-4 border-b border-slate-100 bg-slate-50">
                            <h4 class="text-sm font-black uppercase tracking-widest m-0 text-slate-400">FINANCIAL BREAKDOWN</h4>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 font-medium">Acquisition Subtotal</span>
                                <span class="text-slate-900 font-bold">₹{{ number_format($subtotal) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 font-medium">Logistic Dispatch Fee</span>
                                <span class="text-green-600 font-black uppercase italic">{{ $shipping_fee > 0 ? '₹'.number_format($shipping_fee) : 'FREE' }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 font-medium">Digital Processing Protocol</span>
                                <span class="text-slate-900 font-bold">₹{{ number_format($platform_fee) }}</span>
                            </div>
                            
                            <div class="border-t border-dashed border-slate-200 pt-4 mt-4 flex justify-between items-center">
                                <span class="text-lg font-black text-slate-900 uppercase tracking-tighter">Net Total</span>
                                <span class="text-2xl font-black text-slate-900">₹{{ number_format($grand_total) }}</span>
                            </div>
                        </div>
                        <div class="bg-green-50 p-4 border-t border-green-100 text-center">
                            <p class="text-green-700 text-[10px] font-black uppercase tracking-[0.2em] m-0">Protocol Optimized: Minimal Charges Applied</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
@endsection
