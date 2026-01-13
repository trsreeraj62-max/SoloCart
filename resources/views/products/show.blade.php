@extends('layouts.app')

@section('content')
<div class="bg-[#f1f3f6] min-h-screen py-6">
    <div class="container container-max px-4">
        
        <div class="bg-white rounded-sm shadow-sm border border-slate-100 p-4 mb-4">
            <nav class="text-xs text-slate-400 font-bold uppercase tracking-widest flex items-center gap-2">
                <a href="{{ route('home') }}" class="hover:text-[#2874f0] no-underline transition-colors">Home</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <a href="{{ route('products.index') }}" class="hover:text-[#2874f0] no-underline transition-colors">Shop</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span class="text-slate-900">{{ $product->name }}</span>
            </nav>
        </div>

        <div class="row g-4">
            <!-- Left Side: Images -->
            <div class="col-lg-5">
                <div class="sticky top-20">
                    <div class="bg-white p-4 border border-slate-100 rounded-sm shadow-sm">
                        <div class="aspect-square w-full relative overflow-hidden flex items-center justify-center p-4">
                            <img id="mainImage" src="{{ $product->image_url }}" class="h-full object-contain hover:scale-110 transition-transform duration-500 cursor-zoom-in" alt="{{ $product->name }}">
                        </div>
                    </div>
                    
                    {{-- Action Buttons --}}
                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <form id="addToCartForm" action="{{ url('/cart/add/'.$product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-[#ff9f00] text-white py-4 font-black uppercase tracking-widest text-sm flex items-center justify-center gap-2 shadow-lg shadow-orange-100 hover:bg-[#fb9200] transition-colors">
                                <i class="fas fa-shopping-cart text-lg"></i> Add to Cart
                            </button>
                        </form>
                        <form action="{{ route('checkout.index') }}" method="GET">
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-[#fb641b] text-white py-4 font-black uppercase tracking-widest text-sm flex items-center justify-center gap-2 shadow-lg shadow-orange-100 hover:bg-[#ff4500] transition-colors">
                                <i class="fas fa-bolt text-lg"></i> Buy Now
                            </button>
                        </form>
                    </div>

                    <script>
                    document.getElementById('addToCartForm')?.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const form = this;
                        const formData = new FormData(form);
                        
                        fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success || data.message) {
                                window.showToast(data.message || "Added to Cart Successfully", 'success');
                            } else {
                                window.showToast("Failed to add to cart", 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            window.showToast("An error occurred", 'error');
                        });
                    });
                    </script>
                </div>
            </div>

            <!-- Right Side: Details -->
            <div class="col-lg-7">
                <div class="bg-white p-8 border border-slate-100 rounded-sm shadow-sm space-y-6">
                    
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 leading-tight mb-2">{{ $product->name }}</h1>
                        <div class="flex items-center gap-3">
                            <div class="bg-green-600 text-white text-xs font-bold px-2 py-0.5 rounded flex items-center gap-1">
                                4.4 <i class="fas fa-star text-[8px]"></i>
                            </div>
                            <span class="text-slate-400 text-sm font-semibold">2,458 Ratings & 451 Reviews</span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-baseline gap-3">
                            <span class="text-3xl font-black text-slate-900">₹{{ number_format($product->price) }}</span>
                            @if($product->discount_percent > 0)
                                <span class="text-slate-400 text-lg line-through">₹{{ number_format($product->price * 1.3) }}</span>
                                <span class="text-green-600 font-bold">{{ $product->discount_percent }}% off</span>
                            @endif
                        </div>
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">+ ₹69 Platform Handling Fee</p>
                    </div>

                    {{-- Bank Offers --}}
                    <div class="space-y-3 pt-4 border-t border-slate-50">
                        <p class="text-sm font-bold text-slate-800">Available Offers</p>
                        <div class="space-y-2">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-tag text-green-600 mt-1 text-xs"></i>
                                <p class="text-[13px] text-slate-600"><span class="font-bold text-slate-800">Bank Offer</span> 10% Instant Discount on SBI Credit Card Transactions, up to ₹1,500 on orders of ₹5,000 and above <span class="text-[#2874f0] font-bold cursor-pointer">T&C</span></p>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-tag text-green-600 mt-1 text-xs"></i>
                                <p class="text-[13px] text-slate-600"><span class="font-bold text-slate-800">Combo Offer</span> Buy 3 items, get 5% Off; Buy 5 or more get 10% Off <span class="text-[#2874f0] font-bold cursor-pointer">T&C</span></p>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-tag text-green-600 mt-1 text-xs"></i>
                                <p class="text-[13px] text-slate-600"><span class="font-bold text-slate-800">Special Price</span> Get extra ₹1000 off (price inclusive of cashback/coupon) <span class="text-[#2874f0] font-bold cursor-pointer">T&C</span></p>
                            </div>
                        </div>
                    </div>

                    {{-- Delivery Details --}}
                    <div class="row pt-6 border-t border-slate-50">
                        <div class="col-md-4">
                            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-1">Delivery Protocol</p>
                            <div class="flex items-center gap-2 border-b-2 border-slate-100 pb-2 focus-within:border-[#2874f0] transition-all">
                                <i class="fas fa-map-marker-alt text-slate-400"></i>
                                <input type="text" value="560001" class="w-full text-sm font-bold text-slate-800 bg-transparent focus:outline-none" placeholder="Enter Pincode">
                                <span class="text-[#2874f0] text-[10px] font-black uppercase cursor-pointer">Check</span>
                            </div>
                            <p class="text-[11px] font-bold text-slate-900 mt-2">Delivery by Friday, 15 Jan | <span class="text-green-600">Free</span> <span class="text-slate-300 line-through">₹40</span></p>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="pt-8 border-t border-slate-50">
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3">Manifest Description</p>
                        <p class="text-sm text-slate-600 leading-loose">
                            {{ $product->description }}
                        </p>
                    </div>

                    {{-- Specifications --}}
                    @if($product->specifications)
                    <div class="pt-8 border-t border-slate-50">
                        <p class="text-10px font-black uppercase text-slate-400 tracking-widest mb-4">Functional Specifications</p>
                        <div class="border border-slate-100 rounded-sm">
                            <table class="w-full text-sm">
                                <tbody class="divide-y divide-slate-100">
                                    @foreach(json_decode($product->specifications, true) as $key => $value)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="w-1/3 p-4 text-slate-400 font-medium">{{ $key }}</td>
                                        <td class="p-4 text-slate-800 font-bold uppercase tracking-tight">{{ $value }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        {{-- Similar Products --}}
        @if(isset($similarProducts) && $similarProducts->count() > 0)
        <div class="mt-12">
            <div class="bg-white p-4 border-b border-slate-100 flex items-center justify-between rounded-t-sm">
                <h3 class="text-xl font-bold text-slate-900 tracking-tight">Similar Product Manifests</h3>
                <a href="{{ route('products.index') }}" class="text-[#2874f0] text-xs font-black uppercase tracking-widest no-underline hover:underline">View All</a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-2 bg-white p-2 border border-slate-100 rounded-b-sm shadow-sm">
                @foreach($similarProducts as $p)
                    @include('components.product-card', ['product' => $p])
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
