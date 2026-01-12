@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 2rem;">
    <h1 class="section-title">Checkout</h1>

    <form action="{{ route('checkout.store') }}" method="POST">
        @csrf
        
        @if(isset($isSingle) && $isSingle)
            <input type="hidden" name="product_id" value="{{ $singleProductId }}">
            <input type="hidden" name="quantity" value="{{ $singleQuantity }}">
        @endif
        <input type="hidden" name="grand_total" value="{{ $grandTotal }}">

        <div class="grid layout-grid-checkout" style="gap: 2rem;">
            
            <!-- Details -->
            <div class="card p-8">
                <h3 class="font-bold text-xl mb-4">Shipping Address</h3>
                <textarea name="address" class="input-field mb-8" rows="4" placeholder="Enter your full address details here..." required style="resize: vertical;">{{ old('address') }}</textarea>

                <h3 class="font-bold text-xl mb-4">Payment Method</h3>
                <div class="payment-methods grid gap-4">
                    <label class="card p-4 flex items-center gap-2" style="cursor: pointer; border: 1px solid var(--border); transition: border-color 0.3s;" onclick="selectPayment(this)">
                        <input type="radio" name="payment_method" value="cod" style="accent-color: var(--primary);" checked>
                        <span>Cash on Delivery</span>
                    </label>
                    <label class="card p-4 flex items-center gap-2" style="cursor: pointer; border: 1px solid var(--border); transition: border-color 0.3s;" onclick="selectPayment(this)">
                        <input type="radio" name="payment_method" value="upi" style="accent-color: var(--secondary);">
                        <span>UPI</span>
                    </label>
                    <label class="card p-4 flex items-center gap-2" style="cursor: pointer; border: 1px solid var(--border); transition: border-color 0.3s;" onclick="selectPayment(this)">
                        <input type="radio" name="payment_method" value="card" style="accent-color: var(--accent);">
                         <span>Credit/Debit Card</span>
                    </label>
                </div>
            </div>

            <!-- Summary -->
            <div class="card" style="height: fit-content;">
                 <h3 class="font-bold text-xl mb-4">Order Summary</h3>
                 <div style="max-height: 300px; overflow-y: auto; margin-bottom: 1rem;">
                     @foreach($items as $item)
                     <div class="flex justify-between mb-4 text-sm" style="align-items: center;">
                         <div style="display: flex; gap: 0.5rem; align-items: center;">
                             <div style="font-weight: bold; background: #eee; width: 24px; height: 24px; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-size: 0.8rem;">{{ $item->quantity }}</div>
                             <span>{{ Str::limit($item->product->name, 20) }}</span>
                         </div>
                         <span>${{ number_format($item->price * $item->quantity, 2) }}</span>
                     </div>
                     @endforeach
                 </div>
                 
                 <hr style="border-top: 1px solid var(--border); margin: 1rem 0;">
                 <div class="flex justify-between mb-2 text-muted">
                    <span>Subtotal</span>
                    <span>${{ number_format($total, 2) }}</span>
                </div>
                <div class="flex justify-between mb-4 text-muted">
                    <span>Platform Fee</span>
                    <span>${{ number_format($platformFee, 2) }}</span>
                </div>
                
                <div class="flex justify-between mb-8">
                    <span class="font-bold text-lg">Total Payable</span>
                    <span class="font-bold text-xl text-primary">${{ number_format($grandTotal, 2) }}</span>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Continue to Payment</button>
            </div>

        </div>
    </form>
</div>

<style>
    .layout-grid-checkout { grid-template-columns: 2fr 1fr; }
    .payment-methods { grid-template-columns: repeat(3, 1fr); }
    
    @media (max-width: 768px) {
        .layout-grid-checkout { grid-template-columns: 1fr; }
        .payment-methods { grid-template-columns: 1fr; }
    }
</style>
<script>
    function selectPayment(el) {
        document.querySelectorAll('.payment-methods label').forEach(l => l.style.borderColor = 'var(--border)');
        el.style.borderColor = 'var(--primary)';
    }
</script>
@endsection
