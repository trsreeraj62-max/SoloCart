@extends('layouts.app')

@section('content')
<div class="cart-page py-16 bg-slate-50 min-h-screen">
    <div class="container" style="max-width: 1100px; margin: 0 auto; padding: 0 1.5rem;">
        
        <div class="mb-12">
            <span class="text-[10px] font-black text-primary uppercase tracking-[0.3em] block mb-2 cursor-default">Your Selection</span>
            <h1 class="text-5xl font-black text-slate-900 tracking-tighter italic">Shopping Cart</h1>
        </div>

        @if($cart && $cart->items->count() > 0)
            <div class="grid lg:grid-cols-12 gap-12 items-start">
                
                <!-- Items Column -->
                <div class="lg:col-span-8 space-y-8">
                    @foreach($cart->items as $item)
                    <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-slate-100 flex md:flex-row flex-col gap-10 items-center relative group overflow-hidden">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-slate-100 group-hover:bg-primary transition-all duration-500"></div>
                        
                        <!-- Product Image -->
                        <div class="w-40 h-40 bg-slate-50 rounded-3xl overflow-hidden p-6 flex-shrink-0 group-hover:scale-105 transition duration-500">
                             @if($item->product->images->first())
                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" class="w-full h-full object-contain">
                            @else
                                <img src="https://placehold.co/100x100?text=Item" class="w-full h-full object-contain opacity-20">
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1 space-y-2">
                            <h3 class="text-2xl font-black text-slate-800 tracking-tight">
                                <a href="{{ route('products.show', $item->product->slug ?? $item->product->id) }}" class="hover:text-primary transition no-underline block">
                                    {{ $item->product->name }}
                                </a>
                            </h3>
                            <div class="flex items-center gap-4 text-xs font-bold text-slate-400 uppercase tracking-widest">
                                <span>{{ $item->product->category->name ?? 'Premium' }}</span>
                                <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                <span>ID: #{{ $item->product->id }}</span>
                            </div>
                            
                            <div class="pt-6 flex items-center gap-10">
                                <div>
                                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Quantity</p>
                                    <div class="flex items-center bg-slate-100 rounded-xl px-4 py-2 font-black text-slate-700">
                                         {{ $item->quantity }}
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Subtotal</p>
                                    <div class="text-2xl font-black text-slate-900 tracking-tighter italic">
                                        ${{ number_format($item->quantity * $item->product->price, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Row Actions -->
                        <div class="flex flex-col gap-4 min-w-[160px] relative z-10">
                            <a href="{{ route('checkout.index') }}?buy_item={{ $item->id }}" class="bg-primary text-white text-[10px] font-black py-4 px-6 rounded-2xl text-center shadow-lg shadow-primary/20 hover:shadow-primary/40 transition no-underline uppercase tracking-widest hover:-translate-y-1">
                                Buy Item
                            </a>
                            
                            <form action="{{ route('cart.remove') }}" method="POST" class="absolute -top-12 -right-4 md:static">
                                 @csrf
                                 @method('DELETE')
                                 <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                 <button type="submit" class="w-full md:bg-slate-50 md:text-slate-300 md:hover:text-red-500 transition py-3 rounded-xl border-none cursor-pointer flex items-center justify-center gap-2 group/btn">
                                     <i class="fas fa-trash-alt text-sm"></i>
                                     <span class="text-[9px] font-black uppercase md:hidden lg:inline">Remove</span>
                                 </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Summary Column -->
                <div class="lg:col-span-4 lg:sticky lg:top-32">
                    <div class="bg-white p-12 rounded-[3.5rem] shadow-xl border border-slate-100 relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
                        
                        <h3 class="text-2xl font-black mb-10 text-slate-900 relative z-10">Order Summary</h3>
                        
                        <div class="space-y-6 mb-10">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-400 font-bold uppercase tracking-widest">Base Value</span>
                                <span class="text-slate-700 font-black tracking-tighter text-lg italic">${{ number_format($cart->items->sum(fn($i) => $i->quantity * $i->product->price), 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-emerald-500 text-sm">
                                <span class="font-bold uppercase tracking-widest">Premium Shipping</span>
                                <span class="font-black italic">COMPLIMENTARY</span>
                            </div>
                        </div>

                        <div class="h-px bg-slate-50 w-full mb-10"></div>

                        <div class="flex justify-between items-end mb-12">
                            <div>
                                <span class="text-slate-300 uppercase text-[9px] font-black tracking-widest block mb-1">Final Amount</span>
                                <div class="text-5xl font-black text-primary tracking-tighter italic leading-none">${{ number_format($cart->items->sum(fn($i) => $i->quantity * $i->product->price), 2) }}</div>
                            </div>
                        </div>

                        <a href="{{ route('checkout.index') }}" class="w-full py-6 bg-slate-900 text-white font-black rounded-3xl shadow-2xl hover:shadow-slate-900/40 transition-all flex items-center justify-center gap-4 no-underline text-xs uppercase tracking-[0.2em] transform hover:-translate-y-1">
                            Checkout All <i class="fas fa-chevron-right text-[10px]"></i>
                        </a>
                        
                        <div class="mt-8 pt-8 border-t border-slate-50 flex items-center justify-center gap-6 text-slate-200">
                            <i class="fab fa-cc-visa text-3xl hover:text-slate-300 transition"></i>
                            <i class="fab fa-cc-mastercard text-3xl hover:text-slate-300 transition"></i>
                            <i class="fab fa-apple-pay text-3xl hover:text-slate-300 transition"></i>
                            <i class="fab fa-google-pay text-3xl hover:text-slate-300 transition"></i>
                        </div>
                    </div>
                </div>

            </div>
        @else
            <div class="text-center py-32 bg-white rounded-[4rem] shadow-sm border border-slate-100 flex flex-col items-center">
                <div class="w-32 h-32 bg-slate-50 text-slate-100 rounded-full flex items-center justify-center mb-10">
                    <i class="fas fa-shopping-bag text-5xl opacity-40"></i>
                </div>
                <h3 class="text-3xl font-black text-slate-800 tracking-tighter mb-4 italic">Empty Reserve</h3>
                <p class="text-slate-400 font-medium mb-12 max-w-sm mx-auto leading-relaxed">Your curated collection awaits. Start exploring our latest signature drops.</p>
                <a href="{{ route('products.index') }}" class="inline-flex py-5 px-14 bg-slate-900 text-white font-black rounded-2xl shadow-2xl hover:shadow-slate-900/30 transition no-underline uppercase tracking-[0.2em] text-[10px]">
                    Enter Boutique
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
