@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 2rem;">
    <div class="flex justify-between items-center mb-8">
        <h1 class="font-bold text-2xl">Order #{{ $order->id }}</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-outline">Back to My Orders</a>
    </div>

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; text-align: center;">
            <h3 class="font-bold text-lg">🎉 {{ session('success') }}</h3>
            <p>Your order has been placed successfully.</p>
        </div>
    @endif

    <div class="grid layout-grid-order" style="gap: 2rem; align-items: start;">
        
        <div class="card p-8">
            <h3 class="font-bold text-xl mb-4">Items Ordered</h3>
            @foreach($order->items as $item)
            <div class="flex gap-4 mb-4 border-b pb-4" style="border-color: var(--border);">
                 <div style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden; background: #f1f5f9;">
                      @if($item->product->images->first())
                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <img src="https://placehold.co/80x80" style="width: 100%; height: 100%; object-fit: cover;">
                        @endif
                 </div>
                 <div>
                     <h4 class="font-bold">{{ $item->product->name }}</h4>
                     <p class="text-muted text-sm">Qty: {{ $item->quantity }} x ${{ number_format($item->price, 2) }}</p>
                 </div>
                 <div style="margin-left: auto; font-weight: bold;">
                     ${{ number_format($item->price * $item->quantity, 2) }}
                 </div>
            </div>
            @endforeach
            
            <div class="text-right mt-4">
                <span class="text-muted">Total Paid:</span>
                <span class="font-bold text-xl text-primary ml-2">${{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <div>
            <!-- Timeline -->
            <div class="card p-8 mb-4">
                 <h3 class="font-bold text-xl mb-6">Order Status</h3>
                 
                 @php
                    $statuses = ['pending', 'approved', 'packed', 'shipped', 'out_for_delivery', 'delivered'];
                    $currentStatus = $order->status;
                    if ($currentStatus == 'cancelled') {
                        echo '<div style="color: red; font-weight: bold; text-align: center; border: 2px solid red; padding: 1rem; border-radius: 8px;">Order Cancelled</div>';
                    } elseif ($currentStatus == 'returned') {
                        echo '<div style="color: orange; font-weight: bold; text-align: center; border: 2px solid orange; padding: 1rem; border-radius: 8px;">Order Returned</div>';
                    } else {
                        $currentIndex = array_search($currentStatus, $statuses);
                        if ($currentIndex === false) $currentIndex = -1;
                 @endphp
                 
                 <div class="timeline">
                     @foreach($statuses as $index => $status)
                        <div class="timeline-item {{ $index <= $currentIndex ? 'completed' : '' }}">
                            <div class="timeline-dot"></div>
                            <div class="timeline-text">{{ ucwords(str_replace('_', ' ', $status)) }}</div>
                            @if($index == $currentIndex)
                                <small class="text-primary font-bold">Current Status</small>
                            @endif
                        </div>
                     @endforeach
                 </div>
                 
                 @php } @endphp
            </div>

            <!-- Address -->
            <div class="card p-8">
                <h3 class="font-bold text-xl mb-2">Shipping Address</h3>
                <p class="text-muted">{{ $order->address }}</p>
                <div class="mt-4 pt-4 border-t text-sm text-muted">
                    Payment Method: <span class="font-bold">{{ strtoupper($order->payment_method) }}</span>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .layout-grid-order { grid-template-columns: 2fr 1fr; }
    @media (max-width: 768px) { .layout-grid-order { grid-template-columns: 1fr; } }
    
    .timeline { position: relative; padding-left: 1rem; border-left: 2px solid #e2e8f0; margin-left: 0.5rem; }
    .timeline-item { position: relative; margin-bottom: 1.5rem; padding-left: 1.5rem; }
    .timeline-dot { 
        position: absolute; left: -1.35rem; top: 0.25rem; 
        width: 0.8rem; height: 0.8rem; border-radius: 50%; 
        background: white; border: 2px solid #cbd5e1; 
        transition: all 0.3s;
    }
    .timeline-item.completed .timeline-dot { background: var(--primary); border-color: var(--primary); transform: scale(1.2); }
    .timeline-text { color: #94a3b8; font-weight: 500; }
    .timeline-item.completed .timeline-text { color: var(--text-main); font-weight: bold; }
</style>
@endsection
