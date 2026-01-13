@extends('layouts.app')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen py-8">
    <div class="container container-max px-4">
        
        <div class="mb-8">
            <h1 class="text-3xl font-black text-slate-900 tracking-tighter italic">Archive : <span class="text-[#2874f0]">Order History</span></h1>
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-[0.3em] mt-1">Review your past acquisition manifestations</p>
        </div>

        @if($orders->count() > 0)
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white rounded-sm shadow-sm border border-slate-100 hover:shadow-md transition-shadow overflow-hidden group">
                        <div class="p-6 md:p-8 flex flex-col md:flex-row items-center gap-6">
                            
                            <!-- Product Image (First item) -->
                            <div class="w-24 h-24 bg-slate-50 border border-slate-100 p-2 flex-shrink-0">
                                <img src="{{ $order->items->first()->product->image_url }}" class="w-full h-full object-contain">
                            </div>

                            <!-- Order Info -->
                            <div class="flex-grow space-y-2 text-center md:text-left">
                                <h4 class="text-sm font-black text-slate-800 uppercase tracking-wide">Order #{{ $order->id }}</h4>
                                <p class="text-[11px] text-slate-400 font-bold uppercase tracking-widest">Manifested on {{ $order->created_at->format('d M, Y') }}</p>
                                <div class="pt-2">
                                    <span class="text-xl font-black text-slate-900">₹{{ number_format($order->total) }}</span>
                                    <span class="ml-2 text-[10px] font-black text-slate-300 uppercase italic">via {{ strtoupper($order->payment_method) }}</span>
                                </div>
                            </div>

                            <!-- Timeline Status -->
                            <div class="flex-shrink-0 flex flex-col items-center md:items-end gap-3 w-full md:w-auto">
                                <div class="flex items-center gap-2">
                                    @php
                                        $statusClass = match($order->status) {
                                            'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            'approved' => 'bg-blue-100 text-[#2874f0] border-blue-200',
                                            'delivered' => 'bg-green-100 text-green-700 border-green-200',
                                            'cancelled', 'returned' => 'bg-rose-100 text-rose-700 border-rose-200',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200'
                                        };
                                    @endphp
                                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClass }}">
                                        {{ $order->status }}
                                    </span>
                                </div>
                                <a href="{{ route('orders.show', $order->id) }}" class="text-[#2874f0] text-xs font-black uppercase tracking-widest no-underline border-b-2 border-transparent hover:border-[#2874f0] transition-all">
                                    Details <i class="fas fa-chevron-right text-[8px] ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-sm shadow-sm border border-slate-100 p-20 text-center">
                <i class="fas fa-box-open text-slate-100 text-8xl mb-6"></i>
                <h3 class="text-2xl font-black text-slate-800 tracking-tighter uppercase leading-none">Manifest Null</h3>
                <p class="text-slate-400 text-sm italic mt-2">No transaction history detected in the archive</p>
                <a href="{{ route('products.index') }}" class="inline-block mt-8 bg-[#2874f0] text-white px-10 py-3 rounded-sm text-xs font-black uppercase tracking-widest hover:bg-[#1266ec] transition-all">Start Acquisition</a>
            </div>
        @endif

    </div>
</div>
@endsection
