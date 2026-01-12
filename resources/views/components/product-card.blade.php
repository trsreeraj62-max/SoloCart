<a href="{{ route('products.show', $product->slug ?? $product->id) }}" class="card block hover:shadow-xl transition-all duration-500 no-underline text-inherit group overflow-hidden bg-white" style="border-radius: 24px; border: 1px solid #f8fafc; height: 320px;">
    <div class="product-card-img relative bg-slate-50/30 overflow-hidden" style="height: 200px;">
        <div class="flex items-center justify-center h-full w-full p-6">
            @if($product->images && $product->images->first())
                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-110 transition duration-700">
            @else
                <img src="https://placehold.co/400x300?text=Premium+Item" alt="Placeholder" class="w-full h-full object-contain opacity-20">
            @endif
        </div>
        
        <div class="absolute inset-0 bg-gradient-to-t from-white/10 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>

        @if($product->discount_percent > 0)
            <span class="absolute top-4 left-4 bg-red-500 text-white text-[9px] font-black px-3 py-1 rounded-full shadow-lg tracking-widest uppercase">
                Save {{ $product->discount_percent }}%
            </span>
        @endif
        
        <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-500">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-xl text-primary">
                <i class="fas fa-plus"></i>
            </div>
        </div>
    </div>
    
    <div class="product-info p-6 text-center space-y-2">
        <h3 class="font-extrabold text-slate-800 text-base leading-tight group-hover:text-primary transition line-clamp-1">
            {{ $product->name }}
        </h3>
        
        <div class="flex flex-col items-center justify-center gap-0.5">
             <div class="text-xl font-black text-slate-900 tracking-tighter">
                ${{ number_format($product->price * (1 - ($product->discount_percent/100)), 2) }}
             </div>
             @if($product->discount_percent > 0)
                <span class="text-[10px] text-slate-300 line-through font-bold">${{ number_format($product->price, 2) }}</span>
             @endif
        </div>
    </div>
</a>
