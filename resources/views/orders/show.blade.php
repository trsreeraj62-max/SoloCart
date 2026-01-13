@extends('layouts.app')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen py-8">
    <div class="container container-max px-4">
        
        <!-- Navigation / Header -->
        <div class="bg-white rounded-sm shadow-sm border border-slate-100 p-6 flex flex-col md:flex-row justify-between items-center gap-4 mb-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('orders.index') }}" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-[#2874f0] hover:bg-slate-100 transition-all">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tighter uppercase m-0">Blueprint: #{{ $order->id }}</h1>
                    <p class="text-[9px] font-black uppercase text-slate-300 tracking-[0.2em]">Transaction Timeline Interface</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                @if($order->status == 'delivered')
                    <a href="{{ route('orders.invoice', $order->id) }}" class="bg-white text-[#2874f0] border border-[#2874f0] px-6 py-2 rounded-sm text-[10px] font-black uppercase tracking-widest hover:bg-[#2874f0] hover:text-white transition-all">
                        Download Manifest
                    </a>
                @endif
                @if(in_array($order->status, ['pending', 'approved', 'packed']))
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-rose-50 text-rose-500 border border-rose-100 px-6 py-2 rounded-sm text-[10px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all">
                            ABORT PROTOCOL
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Side: Timeline & Products -->
            <div class="col-lg-8">
                <div class="space-y-4">
                    
                    <!-- Timeline Display -->
                    <div class="bg-white rounded-sm shadow-sm border border-slate-100 p-8">
                        <div class="flex items-center justify-between gap-2 max-w-2xl mx-auto relative px-10">
                            <!-- Track line -->
                            <div class="absolute left-10 right-10 top-5 h-1 bg-slate-100 z-0"></div>
                            
                            @php
                                $statuses = ['pending', 'approved', 'packed', 'shipped', 'delivered'];
                                $currentIndex = array_search($order->status, $statuses);
                            @endphp

                            @foreach($statuses as $index => $step)
                                <div class="flex flex-col items-center gap-2 relative z-10">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center border-2 
                                        {{ $index <= $currentIndex ? 'bg-[#2874f0] border-[#2874f0] text-white shadow-lg shadow-blue-100' : 'bg-white border-slate-200 text-slate-300' }}">
                                        @if($index < $currentIndex)
                                            <i class="fas fa-check text-xs"></i>
                                        @else
                                            <i class="fas fa-{{ match($step) {
                                                'pending' => 'file-invoice',
                                                'approved' => 'check-double',
                                                'packed' => 'box',
                                                'shipped' => 'truck',
                                                'delivered' => 'home'
                                            } }} text-xs"></i>
                                        @endif
                                    </div>
                                    <span class="text-[9px] font-black uppercase tracking-widest {{ $index <= $currentIndex ? 'text-slate-800' : 'text-slate-300' }}">{{ $step }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Products List -->
                    <div class="bg-white rounded-sm shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-4 bg-slate-50 border-b border-slate-100">
                            <h4 class="text-xs font-black uppercase tracking-widest m-0 text-slate-400">MANIFEST CONTENT</h4>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach($order->items as $item)
                            <div class="p-6 flex items-center gap-6 group">
                                <div class="w-20 h-20 bg-slate-50 border border-slate-100 p-2 flex-shrink-0">
                                    <img src="{{ $item->product->image_url }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform">
                                </div>
                                <div class="flex-grow">
                                    <h5 class="text-sm font-bold text-slate-800 m-0"><a href="{{ route('products.show', $item->product->slug) }}" class="no-underline text-inherit hover:text-[#2874f0] transition-colors">{{ $item->product->name }}</a></h5>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Acquired for ₹{{ number_format($item->price) }} × {{ $item->quantity }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-black text-slate-900 leading-none">₹{{ number_format($item->price * $item->quantity) }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Side: Details -->
            <div class="col-lg-4">
                <div class="space-y-4 sticky top-20">
                    
                    <!-- Destination -->
                    <div class="bg-white rounded-sm shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-[#2874f0] text-xs"></i>
                            <h4 class="text-[10px] font-black uppercase tracking-widest m-0 text-slate-400">VECTOR DESTINATION</h4>
                        </div>
                        <div class="p-6">
                            <p class="text-sm font-bold text-slate-900 mb-1 italic">{{ $order->user->name }}</p>
                            <p class="text-xs text-slate-500 leading-relaxed font-medium uppercase tracking-tight">{{ $order->address }}</p>
                            <div class="mt-4 pt-4 border-t border-slate-50">
                                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Signal Locked To</p>
                                <p class="text-xs font-bold text-slate-800">{{ $order->user->phone ?? 'Terminal Unspecified' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary -->
                    <div class="bg-white rounded-sm shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center gap-2">
                            <i class="fas fa-receipt text-[#2874f0] text-xs"></i>
                            <h4 class="text-[10px] font-black uppercase tracking-widest m-0 text-slate-400">FINANCIAL AUDIT</h4>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center text-xs font-medium uppercase tracking-wide">
                                <span class="text-slate-400">Manifest Total</span>
                                <span class="text-slate-900 font-black">₹{{ number_format($order->total) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs font-medium uppercase tracking-wide">
                                <span class="text-slate-400">Billing Protocol</span>
                                <span class="text-[#2874f0] font-black">{{ strtoupper($order->payment_method) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs font-medium uppercase tracking-wide">
                                <span class="text-slate-400">Payment Status</span>
                                <span class="{{ $order->payment_status == 'paid' ? 'text-green-600' : 'text-amber-500' }} font-black">{{ strtoupper($order->payment_status) }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
