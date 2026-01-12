@extends('layouts.app')

@section('content')
<div class="container flex justify-center items-center" style="min-height: 80vh;">
    <div class="card p-8 text-center" style="max-width: 500px; width: 100%;">
        <h2 class="font-bold text-2xl mb-4">Complete Your Payment</h2>
        <p class="text-muted mb-8">You chose <strong>{{ strtoupper($order->payment_method) }}</strong>. Please confirm payment of <br><strong class="text-xl text-primary">${{ number_format($order->total, 2) }}</strong>.</p>
        
        <div style="background: #f8fafc; padding: 2rem; border-radius: 16px; margin-bottom: 2rem; border: 1px dashed var(--border);">
            <div class="mb-4 text-4xl">💳</div>
            <p>Simulating Secure Gateway...</p>
        </div>

        <form action="{{ route('checkout.confirm', $order->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">Confirm & Pay</button>
        </form>
    </div>
</div>
@endsection
