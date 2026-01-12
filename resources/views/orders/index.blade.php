@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 2rem;">
    <h1 class="section-title">My Orders</h1>

    @if($orders->count() > 0)
        <div class="grid gap-4">
            @foreach($orders as $order)
            <div class="card p-6 flex justify-between items-center flex-wrap gap-4" style="flex-direction: row;">
                <div style="flex: 1; min-width: 200px;">
                    <h3 class="font-bold text-lg">Order #{{ $order->id }}</h3>
                    <p class="text-muted text-sm">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                    <p class="text-sm mt-1 text-muted">{{ $order->items->count() }} Items</p>
                </div>
                <div>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-bold" 
                          style="background: {{ $order->status == 'delivered' ? '#dcfce7' : ($order->status == 'cancelled' ? '#fee2e2' : '#dbeafe') }}; 
                                 color: {{ $order->status == 'delivered' ? '#166534' : ($order->status == 'cancelled' ? '#b91c1c' : '#1e40af') }};">
                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <div class="text-right" style="min-width: 100px;">
                    <span class="font-bold text-lg block">${{ number_format($order->total, 2) }}</span>
                    <small class="text-muted">{{ strtoupper($order->payment_method) }}</small>
                </div>
                <div class="flex items-center gap-2">
                    @if($order->status == 'delivered')
                        <a href="{{ route('orders.invoice', $order->id) }}" class="btn btn-primary" title="Download Invoice" style="padding: 0.5rem 1.25rem;">
                            <i class="fas fa-download"></i>
                        </a>
                    @endif
                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline" style="padding: 0.5rem 1.5rem;">View</a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-muted text-lg mb-4">You haven't placed any orders yet.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Start Shopping</a>
        </div>
    @endif
</div>
@endsection
