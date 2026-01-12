@extends('layouts.app')

@section('content')
<div class="product-details-page py-10">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem;">
        
        <nav class="flex text-sm font-medium text-slate-400 mb-8 items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-primary transition">Home</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <a href="{{ route('products.index') }}" class="hover:text-primary transition">Shop</a>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-slate-800">{{ $product->name }}</span>
        </nav>

        <div class="grid lg:grid-cols-12 gap-12">
            <!-- Left: Images -->
            <div class="lg:col-span-6 space-y-4">
                <div class="bg-white rounded-[2.5rem] p-12 border border-slate-100 shadow-sm relative group overflow-hidden">
                    @if($product->images && $product->images->first())
                        <img id="mainImage" src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="w-full aspect-square object-contain transition duration-500 group-hover:scale-105">
                    @else
                        <img src="https://placehold.co/800x800?text=No+Image" alt="Placeholder" class="w-full aspect-square object-contain">
                    @endif

                    @if($product->discount_percent > 0)
                        <div class="absolute top-8 left-8 bg-red-500 text-white font-black px-4 py-1.5 rounded-full shadow-lg">
                            {{ $product->discount_percent }}% OFF
                        </div>
                    @endif
                </div>

                <!-- Thumbnails -->
                @if($product->images && $product->images->count() > 1)
                <div class="flex gap-4">
                    @foreach($product->images as $img)
                        <div class="w-24 h-24 bg-white rounded-2xl border border-slate-100 p-2 cursor-pointer hover:border-primary transition" onclick="document.getElementById('mainImage').src = '{{ asset('storage/' . $img->image_path) }}'">
                            <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-contain">
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Right: Details -->
            <div class="lg:col-span-6 space-y-8">
                <div>
                    <div class="text-primary font-bold text-sm mb-2 px-3 py-1 bg-primary/10 rounded-full w-fit">{{ $product->category->name }}</div>
                    <h1 class="text-4xl font-black text-slate-900 mb-4 leading-tight">{{ $product->name }}</h1>
                    
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex text-amber-400">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="text-slate-400 font-bold text-sm">(120 Reviews)</span>
                        <div class="w-1.5 h-1.5 bg-slate-200 rounded-full"></div>
                        <span class="text-green-600 font-bold text-sm">In Stock ({{ $product->stock }})</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="text-4xl font-black text-slate-900">${{ number_format($product->price * (1 - ($product->discount_percent/100)), 2) }}</span>
                        @if($product->discount_percent > 0)
                            <span class="text-xl text-slate-400 line-through font-bold">${{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>
                </div>

                <div class="h-px bg-slate-100 w-full"></div>

                <!-- Available Offers -->
                <div class="space-y-4">
                    <h3 class="font-black text-slate-800 flex items-center gap-2"><i class="fas fa-bolt text-amber-500"></i> Available Offers</h3>
                    <div class="space-y-3">
                        <div class="flex items-start gap-3 p-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
                            <i class="fas fa-tag text-primary mt-1"></i>
                            <div>
                                <span class="font-bold">Bank Offer:</span> 10% instant discount on State Bank Credit Cards up to $150.
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm">
                            <i class="fas fa-tag text-primary mt-1"></i>
                            <div>
                                <span class="font-bold">Partner Offer:</span> Get free shipping on orders above $500 using GPay.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Details -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-white border border-slate-100 rounded-2xl shadow-sm flex items-center gap-4">
                        <div class="bg-blue-50 p-3 rounded-xl text-blue-600"><i class="fas fa-truck-moving"></i></div>
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase">Est. Delivery</div>
                            <div class="text-sm font-bold">2-4 Business Days</div>
                        </div>
                    </div>
                    <div class="p-4 bg-white border border-slate-100 rounded-2xl shadow-sm flex items-center gap-4">
                        <div class="bg-purple-50 p-3 rounded-xl text-purple-600"><i class="fas fa-undo"></i></div>
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase">Return Policy</div>
                            <div class="text-sm font-bold">30 Days Easy Return</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex md:flex-row flex-col gap-4">
                    <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="w-full py-5 bg-slate-900 text-white font-black rounded-2xl shadow-xl hover:shadow-slate-900/20 transition flex items-center justify-center gap-2">
                            <i class="fas fa-shopping-cart"></i> ADD TO CART
                        </button>
                    </form>
                    <a href="{{ route('checkout.index') }}?product_id={{ $product->id }}" class="flex-1 py-5 bg-primary text-white font-black rounded-2xl shadow-xl hover:shadow-primary/30 transition flex items-center justify-center gap-2 text-center no-underline">
                        <i class="fas fa-bolt"></i> BUY NOW
                    </a>
                </div>

                <!-- Tabs -->
                <div class="pt-8">
                    <div class="flex border-b border-slate-200 mb-6">
                        <button class="px-6 py-3 border-b-2 border-primary font-bold text-primary">Description</button>
                        <button class="px-6 py-3 text-slate-400 font-bold hover:text-slate-600 transition">Specifications</button>
                    </div>
                    <div class="text-slate-600 leading-relaxed">
                        <p class="mb-6 font-bold text-slate-800">Product Overview</p>
                        <p>{{ $product->description }}</p>
                        
                        @if($product->specifications)
                            <div class="mt-8">
                                <p class="mb-4 font-bold text-slate-800">Technical Details</p>
                                <div class="bg-slate-50 rounded-2xl p-6 border border-slate-200">
                                    <pre class="font-sans whitespace-pre-wrap text-sm text-slate-600">{{ $product->specifications }}</pre>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .product-details-page h1, .product-details-page h2, .product-details-page h3 { margin: 0; }
</style>
@endsection
