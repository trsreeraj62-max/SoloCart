@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 2rem;">
    <div class="card p-8 mb-8">
        <div class="grid layout-grid-product" style="gap: 3rem; align-items: start;">
            <!-- Images -->
            <div>
                <div style="border-radius: 16px; overflow: hidden; margin-bottom: 1rem; border: 1px solid var(--border);">
                     @if($product->images->count() > 0)
                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" style="width: 100%; height: auto; object-fit: cover;">
                    @else
                        <img src="https://placehold.co/600x600" alt="Placeholder" style="width: 100%;">
                    @endif
                </div>
            </div>

            <!-- Details -->
            <div>
                <h1 class="font-bold text-3xl mb-2">{{ $product->name }}</h1>
                <div class="flex items-center gap-4 mb-4">
                    <span class="text-2xl font-bold text-primary">${{ number_format($product->price - ($product->price * ($product->discount_percent / 100)), 2) }}</span>
                    @if($product->discount_percent > 0)
                        <span class="text-muted" style="text-decoration: line-through; font-size: 1.2rem;">${{ number_format($product->price, 2) }}</span>
                        <span class="discount-badge" style="position: static;">-{{ $product->discount_percent }}% OFF</span>
                    @endif
                </div>
                
                <p class="text-muted mb-6 leading-relaxed">{{ $product->description ?? 'No description available for this product.' }}</p>

                <div class="flex flex-wrap gap-4 mb-6">
                    <form action="{{ route('cart.add') }}" method="POST" class="flex gap-2 items-center">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="flex items-center gap-2">
                             <label class="text-sm font-bold">Qty:</label>
                             <input type="number" name="quantity" id="quantityInput" value="1" min="1" class="input-field" style="width: 70px; padding: 0.5rem;">
                        </div>
                        <button type="submit" class="btn btn-outline">Add to Cart</button>
                    </form>
                    
                     <!-- Buy Now Form -->
                    <form action="{{ route('checkout.single') }}" method="GET" style="flex: 1;" onsubmit="this.quantity.value = document.getElementById('quantityInput').value">
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Buy Now</button>
                    </form>
                </div>
                
                <div class="text-sm text-muted">
                    <p class="mb-1">Category: <a href="{{ route('products.index', ['category_id' => $product->category_id]) }}" class="text-primary font-bold">{{ $product->category->name }}</a></p>
                    <p>Stock: <span class="{{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }} font-bold">{{ $product->stock > 0 ? 'In Stock (' . $product->stock . ')' : 'Out of Stock' }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Products -->
    @if($similarProducts->count() > 0)
    <div class="mb-8">
        <h2 class="section-title">Similar Products</h2>
        <div class="product-grid mt-4">
            @foreach($similarProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
    .layout-grid-product { display: grid; grid-template-columns: 1fr 1fr; }
    @media (max-width: 768px) {
        .layout-grid-product { grid-template-columns: 1fr; }
    }
</style>
@endsection
