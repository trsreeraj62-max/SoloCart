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
                    $isCancelled = $currentStatus === 'cancelled';
                    $isReturned = $currentStatus === 'returned';
                    
                    if ($isCancelled) {
                        echo '<div style="background: #fee2e2; color: #b91c1c; font-weight: bold; text-align: center; border: 1px solid #fecaca; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">Current Status: Cancelled</div>';
                    } elseif ($isReturned) {
                         echo '<div style="background: #ffedd5; color: #c2410c; font-weight: bold; text-align: center; border: 1px solid #fed7aa; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">Current Status: Returned</div>';
                    } else {
                        $currentIndex = array_search($currentStatus, $statuses);
                        if ($currentIndex === false) $currentIndex = 0;
                        if ($currentStatus == 'return_requested') $currentIndex = 5; // Treat as delivered visually + message
                 @endphp
                 
                 <!-- Tracking Map Placeholder -->
                 @if(!$isCancelled && !$isReturned && $currentIndex >= 3)
                 <div class="mb-4" style="border-radius: 16px; overflow: hidden; border: 1px solid var(--border);">
                     <img src="https://media.wired.com/photos/59269cd37034dc5f91becd32/master/w_2560%2Cc_limit/GoogleMapTA.jpg" alt="Tracking Map" style="width: 100%; height: 200px; object-fit: cover;">
                     <div class="p-2 bg-light text-center text-sm text-muted">Live Tracking (Demo)</div>
                 </div>
                 @endif

                 <div class="timeline">
                     @foreach($statuses as $index => $status)
                        <div class="timeline-item {{ (!$isCancelled && !$isReturned && $index <= $currentIndex) ? 'completed' : '' }}">
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
                
                <!-- Order Actions -->
               <div class="mt-6 pt-4 border-t flex flex-col gap-2">
                   @if(in_array($order->status, ['pending', 'approved', 'packed']))
                       <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                           @csrf
                           <button type="submit" class="btn btn-outline text-danger w-100 justify-center" style="border-color: #ef4444; color: #ef4444; width: 100%;">Cancel Order</button>
                       </form>
                   @endif
                   
                   @if($order->status == 'delivered')
                       <a href="{{ route('orders.invoice', $order->id) }}" class="btn btn-primary w-100 justify-center" style="width: 100%; text-align: center;">Download Invoice</a>
                       
                       <form action="{{ route('orders.return', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to return this order?')">
                           @csrf
                           <button type="submit" class="btn btn-outline w-100 justify-center mt-2" style="width: 100%;">Return Order</button>
                       </form>
                   @endif
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
