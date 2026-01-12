@extends('layouts.app')

@section('content')
<div class="order-detail-page py-16 bg-slate-50 min-h-screen">
    <div class="container" style="max-width: 1100px; margin: 0 auto; padding: 0 1.5rem;">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 bg-primary/10 text-primary text-[10px] font-black rounded-full uppercase tracking-widest">Order Detail</span>
                    <span class="text-slate-300">/</span>
                    <span class="text-slate-400 font-bold text-sm">#{{ $order->id }}</span>
                </div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter">Package Information</h1>
            </div>
            
            <div class="flex items-center gap-4">
                 <div class="text-right hidden md:block">
                     <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Placement Date</p>
                     <p class="font-bold text-slate-700">{{ $order->created_at->format('d M, Y') }}</p>
                 </div>
                 @if($order->status == 'delivered')
                    <a href="{{ route('orders.invoice', $order->id) }}" class="p-4 bg-white text-slate-700 border border-slate-200 rounded-2xl hover:bg-slate-50 transition shadow-sm flex items-center gap-2 font-bold no-underline">
                        <i class="fas fa-file-invoice text-primary"></i> <span class="text-sm">Invoice</span>
                    </a>
                 @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mb-10 p-6 bg-green-500 text-white rounded-[2rem] shadow-xl shadow-green-500/20 flex items-center justify-between animate-pulse">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-lg m-0">Order Placed Successfully</h3>
                        <p class="text-green-100 text-sm m-0">Your package is now in our secure processing queue.</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid lg:grid-cols-12 gap-10 items-start">
            
            <!-- Items Column -->
            <div class="lg:col-span-8 space-y-8">
                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-slate-100 italic-summary">
                    <h3 class="text-xl font-black text-slate-800 mb-8 flex items-center gap-3">
                        <i class="fas fa-box-open text-primary"></i> Cart Overview
                    </h3>
                    <div class="space-y-8">
                        @foreach($order->items as $item)
                        <div class="flex items-center gap-6 group">
                             <div class="w-24 h-24 bg-slate-50 rounded-2xl overflow-hidden p-2 flex-shrink-0 group-hover:scale-105 transition duration-500">
                                  @if($item->product->images->first())
                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" class="w-full h-full object-contain">
                                    @else
                                        <img src="https://placehold.co/100x100?text=Item" class="w-full h-full object-contain opacity-20">
                                    @endif
                             </div>
                             <div class="flex-1">
                                 <h4 class="font-black text-slate-800 text-lg mb-1">{{ $item->product->name }}</h4>
                                 <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Qty: {{ $item->quantity }} • ${{ number_format($item->price, 2) }} unit</p>
                             </div>
                             <div class="text-right font-black text-slate-900 text-xl tracking-tighter">
                                 ${{ number_format($item->price * $item->quantity, 2) }}
                             </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="h-px bg-slate-50 w-full my-8"></div>
                    
                    <div class="flex justify-between items-end">
                        <div class="text-slate-400 font-bold text-sm">
                            Payment: <span class="bg-slate-100 px-3 py-1 rounded-full text-slate-600 font-black text-[10px] uppercase">{{ $order->payment_method }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Grand Total Paid</span>
                            <span class="text-4xl font-black text-primary tracking-tighter italic">${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-slate-100">
                    <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-3">
                        <i class="fas fa-map-marker-alt text-primary"></i> Shipping Destination
                    </h3>
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <p class="text-slate-600 font-bold leading-relaxed m-0">{{ $order->address }}</p>
                    </div>
                    <div class="mt-6 flex items-center gap-3 text-slate-400 text-xs font-bold uppercase">
                        <i class="fas fa-shield-check text-green-500"></i> Standard Express Delivery Enabled
                    </div>
                </div>
            </div>

            <!-- Status Column -->
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-white p-10 rounded-[2.5rem] shadow-xl border border-slate-100 relative overflow-hidden">
                     <div class="absolute top-0 right-0 p-8 opacity-5">
                         <i class="fas fa-shipping-fast text-8xl -rotate-12"></i>
                     </div>

                     <h3 class="text-xl font-black text-slate-800 mb-8 relative z-10">Tracking Status</h3>
                     
                     @php
                        $statuses = ['pending', 'approved', 'packed', 'shipped', 'out_for_delivery', 'delivered'];
                        $currentStatus = $order->status;
                        $isCancelled = $currentStatus === 'cancelled';
                        $isReturned = $currentStatus === 'returned';
                        
                        $currentIndex = array_search($currentStatus, $statuses);
                        if ($currentIndex === false) $currentIndex = 0;
                     @endphp
                     
                     @if($isCancelled)
                        <div class="mb-8 p-4 bg-red-50 border border-red-100 text-red-600 font-black text-center rounded-2xl uppercase text-[10px] tracking-widest">
                            Order Cancelled
                        </div>
                     @elseif($isReturned)
                         <div class="mb-8 p-4 bg-amber-50 border border-amber-100 text-amber-600 font-black text-center rounded-2xl uppercase text-[10px] tracking-widest">
                            Item Returned
                        </div>
                     @else
                         <!-- Tracking Map: Only if admin approved (Shipped or beyond) -->
                         @if($currentIndex >= 3)
                         <div class="mb-10 rounded-3xl overflow-hidden border border-slate-100 shadow-inner group">
                             <div class="relative h-40">
                                 <img src="https://media.wired.com/photos/59269cd37034dc5f91becd32/master/w_2560%2Cc_limit/GoogleMapTA.jpg" class="w-full h-full object-cover grayscale brightness-90 group-hover:grayscale-0 transition duration-1000">
                                 <div class="absolute inset-0 bg-blue-500/10 flex items-center justify-center">
                                     <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-blue-600 shadow-xl animate-bounce">
                                         <i class="fas fa-truck"></i>
                                     </div>
                                 </div>
                             </div>
                             <div class="p-3 bg-slate-900 text-white text-center text-[9px] font-black uppercase tracking-[0.2em]">En-route to Destination</div>
                         </div>
                         @endif

                         <div class="relative pl-8 border-l-2 border-slate-50 space-y-10">
                             @foreach($statuses as $index => $status)
                                @php 
                                    $isCompleted = $index <= $currentIndex;
                                    $isActive = $index == $currentIndex;
                                @endphp
                                <div class="relative">
                                    <div class="absolute -left-10 top-0 w-4 h-4 rounded-full border-4 border-white shadow-sm {{ $isCompleted ? 'bg-primary' : 'bg-slate-200' }} {{ $isActive ? 'ring-4 ring-primary/20 scale-125' : '' }} transition-all duration-500"></div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black uppercase tracking-widest {{ $isCompleted ? 'text-slate-800' : 'text-slate-300' }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span>
                                        @if($isActive)
                                            <span class="text-[9px] font-black text-primary p-0.5 mt-1 bg-primary/5 w-fit rounded animate-pulse">CURRENT PHASE</span>
                                        @endif
                                    </div>
                                </div>
                             @endforeach
                         </div>
                     @endif
                     
                     <div class="mt-12 pt-8 border-t border-slate-50 space-y-4">
                        @if(in_array($order->status, ['pending', 'approved', 'packed']))
                            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Revoke this order?')">
                                @csrf
                                <button type="submit" class="w-full py-4 border-2 border-slate-100 text-slate-400 font-black rounded-2xl hover:border-red-100 hover:text-red-500 transition uppercase tracking-widest text-[10px]">
                                    Cancel Order
                                </button>
                            </form>
                        @endif
                        @if($order->status == 'delivered')
                            <form action="{{ route('orders.return', $order->id) }}" method="POST" onsubmit="return confirm('Initiate return?')">
                                @csrf
                                <button type="submit" class="w-full py-4 border-2 border-slate-100 text-slate-400 font-black rounded-2xl hover:border-amber-100 hover:text-amber-500 transition uppercase tracking-widest text-[10px]">
                                    Report Return
                                </button>
                            </form>
                        @endif
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .order-detail-page h1, .order-detail-page h3, .order-detail-page h4 { margin: 0; }
    .italic-summary h3 { font-style: normal; }
</style>
@endsection
