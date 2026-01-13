<div class="group bg-white rounded-sm overflow-hidden transition-all duration-300 hover:shadow-xl relative flex flex-col h-full cursor-pointer border border-transparent hover:border-slate-100">
    <!-- Clickable Container -->
    <a href="{{ route('products.show', $product->slug ?? $product->id) }}" class="no-underline text-inherit flex flex-col h-full p-4">
        
        <!-- Image Area -->
        <div class="relative w-full aspect-square mb-4 overflow-hidden flex items-center justify-center p-2">
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full object-contain transform transition-transform duration-700 group-hover:scale-105">
            @else
                <div class="w-full h-full bg-slate-50 flex items-center justify-center text-slate-200">
                    <i class="fas fa-image text-4xl"></i>
                </div>
            @endif
            
            @if($product->discount_percent > 0)
                <div class="absolute top-0 right-0">
                    <span class="text-[10px] font-black text-white bg-green-500 px-2 py-1 rounded-bl-lg uppercase tracking-widest">{{ $product->discount_percent }}% OFF</span>
                </div>
            @endif
        </div>

        <!-- Meta Info -->
        <div class="flex-grow">
            <h3 class="text-sm font-bold text-slate-800 line-clamp-2 mb-1 group-hover:text-[#2874f0] transition-colors min-h-[40px]">
                {{ $product->name }}
            </h3>

            @if(isset($product->rating) || true)
                <div class="flex items-center gap-2 mb-2">
                    <div class="bg-green-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded flex items-center gap-1">
                        {{ $product->rating ?? '4.2' }} <i class="fas fa-star text-[8px]"></i>
                    </div>
                    <span class="text-slate-400 text-xs font-semibold">({{ rand(100, 5000) }})</span>
                </div>
            @endif

            <div class="flex items-center gap-3">
                <span class="text-lg font-bold text-slate-900">₹{{ number_format($product->price) }}</span>
                @if($product->discount_percent > 0)
                    <span class="text-xs text-slate-400 line-through">₹{{ number_format($product->price * 1.2) }}</span>
                    <span class="text-[11px] font-bold text-green-600">{{ $product->discount_percent }}% off</span>
                @endif
            </div>

            <!-- Trust Badge Removed -->
        </div>
    </a>
</div>
