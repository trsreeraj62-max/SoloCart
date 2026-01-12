@extends('layouts.app')

@section('content')
<div class="checkout-page py-12 bg-slate-50 min-h-screen">
    <div class="container" style="max-width: 1100px; margin: 0 auto; padding: 0 1.5rem;">
        
        <div class="flex items-center gap-4 mb-10">
            <h1 class="text-3xl font-black text-slate-800 m-0">Order Summary</h1>
            <div class="flex-1 h-px bg-slate-200"></div>
            <div class="flex items-center gap-2 text-slate-400 font-bold text-sm">
                <span class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-[10px]">1</span>
                Summary
                <span class="w-6 h-6 rounded-full bg-white border border-slate-200 text-slate-400 flex items-center justify-center text-[10px]">2</span>
                Payment
            </div>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            
            @if(isset($isSingle) && $isSingle)
                <input type="hidden" name="product_id" value="{{ $singleProductId }}">
                <input type="hidden" name="quantity" value="{{ $singleQuantity }}">
            @endif
            <input type="hidden" name="grand_total" value="{{ $grandTotal }}">
            {{-- Default hidden payment method to move to next step --}}
            <input type="hidden" name="payment_method" value="pending">

            <div class="grid lg:grid-cols-12 gap-10 items-start">
                
                <!-- Left: Product Details & Address -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Products -->
                    <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
                        <h3 class="text-xl font-black mb-6 flex items-center gap-3">
                            <i class="fas fa-shopping-bag text-primary"></i> 
                            Review Items ({{ count($items) }})
                        </h3>
                        <div class="divide-y divide-slate-50">
                            @foreach($items as $item)
                            <div class="py-6 flex gap-6 items-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-2xl p-2 flex-shrink-0">
                                    @if($item->product->images->first())
                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" class="w-full h-full object-contain">
                                    @else
                                        <img src="https://placehold.co/100x100" class="w-full h-full object-contain opacity-20">
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-slate-800 text-lg mb-1">{{ $item->product->name }}</h4>
                                    <p class="text-sm text-slate-400 font-medium">Quantity: <span class="text-slate-700">{{ $item->quantity }}</span></p>
                                </div>
                                <div class="text-right">
                                    <div class="font-black text-slate-900">${{ number_format($item->price * $item->quantity, 2) }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold">${{ number_format($item->price, 2) }} each</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="bg-white rounded-[2rem] p-10 shadow-sm border border-slate-100">
                        <h3 class="text-xl font-black mb-6 flex items-center gap-3">
                            <i class="fas fa-map-marker-alt text-primary"></i> 
                            Delivery Address
                        </h3>
                        <div class="relative">
                            <textarea name="address" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-5 focus:ring-2 focus:ring-primary outline-none transition text-slate-700 font-medium h-32" placeholder="Street name, House No, Landmark, City, State, ZIP..." required>{{ old('address', $user->address ?? '') }}</textarea>
                        </div>
                        <p class="mt-4 text-xs text-slate-400 font-bold italic">Safe delivery ensured by SoloCart Express.</p>
                    </div>

                </div>

                <!-- Right: Price Details -->
                <div class="lg:col-span-4 lg:sticky lg:top-24">
                    <div class="bg-white p-10 rounded-[2.5rem] shadow-xl border border-slate-100">
                        <h3 class="text-2xl font-black mb-8 text-slate-900 uppercase tracking-tighter italic">Price Details</h3>
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 font-bold">Price ({{ count($items) }} items)</span>
                                <span class="text-slate-700 font-black">${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-slate-500">
                                <span class="font-bold">Delivery Charges</span>
                                <span class="font-black {{ $shipping_fee == 0 ? 'text-green-600' : 'text-slate-700' }}">
                                    {{ $shipping_fee == 0 ? 'FREE' : '$' . number_format($shipping_fee, 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 font-bold">Platform Fee</span>
                                <span class="text-slate-700 font-black">${{ number_format($platform_fee, 2) }}</span>
                            </div>
                        </div>

                        <div class="h-px bg-slate-100 w-full mb-6"></div>

                        <div class="flex justify-between items-end mb-10">
                            <div>
                                <span class="text-slate-400 uppercase text-[10px] font-black tracking-widest">Amount Payable</span>
                                <div class="text-3xl font-black text-slate-900">${{ number_format($grand_total, 2) }}</div>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-5 bg-primary text-white font-black rounded-2xl shadow-xl hover:shadow-primary/30 transition flex items-center justify-center gap-3 uppercase tracking-widest text-sm">
                            Continue <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    
                    <div class="mt-6 flex items-center gap-3 justify-center text-slate-400 text-xs font-bold uppercase">
                        <i class="fas fa-shield-check text-green-500 text-lg"></i> 100% SECURE CHECKOUT
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection
