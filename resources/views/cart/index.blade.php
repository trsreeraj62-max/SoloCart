@extends('layouts.app')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen py-8">
    <div class="container container-max px-4">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic uppercase">Acquisition : <span class="text-[#2874f0]">Cart Manifest</span></h1>
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-[0.3em] mt-1">Review and synchronize your selected items</p>
            </div>
            <div class="text-right hidden md:block">
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Total Cart Value</p>
                <p class="text-2xl font-black text-[#2874f0] m-0">₹{{ number_format($totalPrice) }}</p>
            </div>
        </div>

        @if ($cart->isEmpty())
            <div class="bg-white rounded-sm shadow-sm border border-slate-100 p-20 text-center">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shopping-basket text-slate-200 text-4xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-widest">MANIFEST NULL</h3>
                <p class="text-slate-400 text-sm mt-2 italic font-medium">Your digital acquisition container is currently unpopulated</p>
                <a href="{{ route('products.index') }}" class="inline-block mt-8 bg-[#2874f0] text-white px-10 py-3 rounded-sm text-xs font-black uppercase tracking-widest hover:bg-[#1266ec] transition-all no-underline shadow-lg shadow-blue-100">
                    Browse Manifests
                </a>
            </div>
        @else
            <div class="row g-4">
                {{-- Products List --}}
                <div class="col-lg-8">
                    <div class="space-y-4">
                        @foreach ($cart as $item)
                        <div class="bg-white rounded-sm shadow-sm border border-slate-100 p-6 flex flex-col md:flex-row items-center gap-6 group transition-all hover:shadow-md">
                            
                            {{-- Product Image --}}
                            <div class="w-32 h-32 bg-slate-50 border border-slate-100 p-2 flex-shrink-0">
                                <img src="{{ $item->product->image_url }}" 
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($item->product->name) }}&background=f1f3f6&color=2874f0&bold=true'"
                                     class="w-full h-full object-contain group-hover:scale-110 transition-transform">
                            </div>

                            {{-- Info --}}
                            <div class="flex-grow space-y-2 text-center md:text-left">
                                <h4 class="text-lg font-black text-slate-800 tracking-tight m-0 line-clamp-1">
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="no-underline text-inherit hover:text-[#2874f0] transition-colors">
                                        {{ $item->product->name }}
                                    </a>
                                </h4>
                                <div class="flex items-center justify-center md:justify-start gap-4">
                                    <span class="text-2xl font-black text-slate-900">₹{{ number_format($item->product->price) }}</span>
                                    @if($item->product->discount_percent > 0)
                                        <span class="bg-green-100 text-green-700 text-[9px] font-black px-2 py-0.5 rounded uppercase">{{ $item->product->discount_percent }}% Protocol Applied</span>
                                    @endif
                                </div>
                                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Seller: SoloCart Prime Fulfillment</p>
                            </div>

                            {{-- Quantity Toggle / Actions --}}
                            <div class="flex-shrink-0 flex flex-col items-center md:items-end gap-4 w-full md:w-auto">
                                <div class="flex items-center gap-2 border border-slate-200 rounded-sm p-1">
                                    <form action="{{ url('/cart/update/' . $item->id) }}" method="POST" class="m-0 flex items-center">
                                        @csrf
                                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" 
                                               class="w-12 text-center border-0 focus:ring-0 text-sm font-black text-slate-800 p-0" 
                                               onchange="this.form.submit()">
                                        <button type="submit" class="p-1 px-2 hover:text-[#2874f0] transition-colors">
                                            <i class="fas fa-sync-alt text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                                
                                <div class="flex gap-2">
                                    {{-- Process Single Item (Buy Now) --}}
                                    <a href="{{ route('checkout.index', ['product_id' => $item->product_id, 'quantity' => $item->quantity]) }}" 
                                       title="Instant Process Protocol"
                                       class="w-10 h-10 bg-[#fb641b]/10 text-[#fb641b] rounded-full flex items-center justify-center hover:bg-[#fb641b] hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-bolt text-xs"></i>
                                    </a>

                                    {{-- Remove Icon --}}
                                    <form action="{{ url('/cart/remove/' . $item->id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Abort Acquisition"
                                                class="w-10 h-10 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Price Summary --}}
                <div class="col-lg-4">
                    <div class="bg-white rounded-sm shadow-sm border border-slate-100 overflow-hidden sticky top-20">
                        <div class="p-4 border-b border-slate-100 bg-slate-50">
                            <h4 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 m-0">FINANCIAL MANIFEST</h4>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center text-sm font-medium">
                                <span class="text-slate-500">Items Count (Acquisitions)</span>
                                <span class="text-slate-900">{{ $cart->sum('quantity') }} Units</span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-medium">
                                <span class="text-slate-500">Gross Subtotal</span>
                                <span class="text-slate-900 font-bold">₹{{ number_format($totalPrice) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-medium">
                                <span class="text-slate-500">Logistics Protocol</span>
                                <span class="text-green-600 font-black uppercase italic">FREE</span>
                            </div>

                            <div class="border-t border-dashed border-slate-200 pt-4 mt-4 flex justify-between items-center">
                                <span class="text-lg font-black text-slate-900 uppercase italic opacity-40">Net Value</span>
                                <span class="text-2xl font-black text-slate-900 tracking-tighter">₹{{ number_format($totalPrice) }}</span>
                            </div>
                        </div>

                        <div class="p-4 bg-green-50 border-t border-green-100">
                            <p class="text-[9px] font-black text-green-700 text-center uppercase tracking-widest m-0">Protocol Optimized: You saved ₹450 on this manifest</p>
                        </div>

                        <div class="p-6 pt-2">
                            <a href="{{ route('checkout.index') }}" class="block text-center bg-[#fb641b] text-white py-4 rounded-sm text-sm font-black uppercase tracking-[0.2em] shadow-xl shadow-orange-100 hover:bg-[#ff4500] transition-all no-underline">
                                SYNCHRONIZE & CHECKOUT
                            </a>
                            <p class="text-[8px] font-black text-slate-300 text-center uppercase tracking-[0.2em] mt-4 italic">
                                <i class="fas fa-shield-alt mr-1"></i> End-to-End Encryption Enabled
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
