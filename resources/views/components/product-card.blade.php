<div class="card">
    <div class="product-card-img">
        <a href="{{ route('products.show', $product->slug ?? $product->id) }}">
            @if($product->images && $product->images->first())
                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}">
            @else
                <img src="https://placehold.co/400x300?text=No+Image" alt="Placeholder">
            @endif
        </a>
        @if($product->discount_percent > 0)
            <span class="discount-badge">-{{ $product->discount_percent }}%</span>
        @endif
    </div>
    <div class="product-info">
        <h3 class="font-bold text-lg mb-2" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            <a href="{{ route('products.show', $product->slug ?? $product->id) }}">{{ $product->name }}</a>
        </h3>
        <p class="text-muted text-sm mb-2">{{ Str::limit($product->description, 50) }}</p>
        <div class="product-meta">
            <span class="product-price">${{ number_format($product->price - ($product->price * ($product->discount_percent / 100)), 2) }}</span>
            @if($product->discount_percent > 0)
                <span class="text-muted" style="text-decoration: line-through; font-size: 0.9rem; margin-left: 5px;">${{ number_format($product->price, 2) }}</span>
            @endif
            
            <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Add
                </button>
            </form>
        </div>
    </div>
</div>
