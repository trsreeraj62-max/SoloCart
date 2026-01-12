@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 2rem;">
    <h1 class="section-title">Your Shopping Cart</h1>

    @if($cart && $cart->items->count() > 0)
        <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Cart Items -->
            <div class="card p-0" style="padding: 0; overflow: hidden; align-self: start;">
                @foreach($cart->items as $item)
                <div class="cart-item" style="display: grid; grid-template-columns: 100px 2fr 100px 50px; gap: 1rem; align-items: center; padding: 1.5rem; border-bottom: 1px solid var(--border);">
                    <!-- Image -->
                    <div style="width: 80px; height: 80px; border-radius: 8px; overflow: hidden;">
                         @if($item->product->images->first())
                            <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <img src="https://placehold.co/80x80" style="width: 100%; height: 100%; object-fit: cover;">
                        @endif
                    </div>
                    
                    <!-- Details -->
                    <div>
                        <h3 class="font-bold"><a href="{{ route('products.show', $item->product->slug ?? $item->product->id) }}">{{ $item->product->name }}</a></h3>
                        <p class="text-muted text-sm">${{ number_format($item->product->price, 2) }}</p>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <input type="number" value="{{ $item->quantity }}" min="1" class="input-field" style="width: 70px; padding: 0.5rem;" readonly>
                    </div>

                    <!-- Actions -->
                    <div>
                         <form action="{{ route('cart.remove') }}" method="POST">
                             @csrf
                             @method('DELETE')
                             <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                             <button type="submit" class="text-muted" style="background:none; border:none; cursor:pointer; font-size: 1.5rem; line-height: 1;">&times;</button>
                         </form>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Summary -->
            <div class="card" style="height: fit-content; position: sticky; top: 100px;">
                <h3 class="font-bold text-xl mb-4">Summary</h3>
                <div class="flex justify-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span class="font-bold">${{ number_format($cart->items->sum(fn($i) => $i->quantity * $i->product->price), 2) }}</span>
                </div>
                <div class="flex justify-between mb-4">
                    <span class="text-muted">Discount</span>
                    <span class="font-bold text-primary">$0.00</span>
                </div>
                <hr style="border-top: 1px solid var(--border); margin: 1rem 0;">
                <div class="flex justify-between mb-8">
                    <span class="font-bold text-lg">Total</span>
                    <span class="font-bold text-xl">${{ number_format($cart->items->sum(fn($i) => $i->quantity * $i->product->price), 2) }}</span>
                </div>
                
                <a href="{{ route('checkout') }}" class="btn btn-primary" style="width: 100%; justify-content: center;">Proceed to Checkout</a>
            </div>
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-muted text-lg mb-4">Your cart is empty.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Start Shopping</a>
        </div>
    @endif
</div>
@endsection
