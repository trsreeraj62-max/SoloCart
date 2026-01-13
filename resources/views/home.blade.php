@extends('layouts.app')

@section('content')

{{-- =======================
    FULL-WIDTH BANNER SLIDER
======================= --}}
<div id="flipkartCarousel" class="carousel slide bg-white shadow-sm mb-6" data-bs-ride="carousel" data-bs-interval="3000">
    <div class="carousel-inner">
        @forelse($banners as $index => $banner)
            <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                <img src="{{ $banner->image_url }}" 
                     class="d-block w-full h-[280px] md:h-[350px] object-cover" alt="Banner">
                @if($banner->title)
                <div class="carousel-caption d-none d-md-block bg-black/20 backdrop-blur-sm rounded-lg p-4 mb-10 max-w-lg mx-auto">
                    <h5 class="text-3xl font-black italic tracking-tighter">{{ $banner->title }}</h5>
                    <p class="font-bold text-sm tracking-widest uppercase text-white/80">{{ $banner->subtitle }}</p>
                </div>
                @endif
            </div>
        @empty
            <!-- Fallback Static Banner -->
            <div class="carousel-item active">
                <img src="https://rukminim1.flixcart.com/fk-p-flap/1600/270/image/35349f783226317b.jpg?q=20" class="d-block w-full h-[280px] md:h-[350px] object-cover" alt="Default Banner">
            </div>
            <div class="carousel-item">
                <img src="https://rukminim1.flixcart.com/fk-p-flap/1600/270/image/edeb643194511d54.jpg?q=20" class="d-block w-full h-[280px] md:h-[350px] object-cover" alt="Default Banner">
            </div>
        @endforelse
    </div>
    
    <!-- Navigation Buttons -->
    <button class="carousel-control-prev w-[5%] bg-white/10 hover:bg-white/30" type="button" data-bs-target="#flipkartCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon bg-slate-900 rounded-sm p-3"></span>
    </button>
    <button class="carousel-control-next w-[5%] bg-white/10 hover:bg-white/30" type="button" data-bs-target="#flipkartCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon bg-slate-900 rounded-sm p-3"></span>
    </button>
</div>

{{-- Main Page Wrap --}}
<div class="container container-max px-4">

    {{-- =======================
           CATEGORIES ROW
    ======================= --}}
    <div class="bg-white rounded-sm shadow-sm p-4 mb-6 flex justify-around items-center overflow-x-auto gap-10 border border-slate-100 no-scrollbar">
        @foreach ($categories as $cat)
        <a href="{{ url('/products?category='.$cat->id) }}" 
           class="flex flex-col items-center gap-2 no-underline group flex-shrink-0 transition-transform active:scale-95">
            <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center p-2 group-hover:bg-[#2874f0]/5 transition-colors overflow-hidden">
                <img src="{{ $cat->image_url }}" 
                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($cat->name) }}&background=f1f3f6&color=2874f0&bold=true'"
                     class="h-full w-full object-contain group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[11px] font-bold text-slate-700 group-hover:text-[#2874f0] tracking-wide m-0 text-center">{{ $cat->name }}</p>
        </a>
        @endforeach
        @if($categories->isEmpty())
            <div class="py-2 text-center w-full">
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest m-0">No active categories detected in system archive</p>
            </div>
        @endif
    </div>

    {{-- =======================
           FEATURED PRODUCTS
    ======================= --}}
    <div class="mb-8">
        <div class="bg-white p-4 flex items-center justify-between border-b border-slate-100 rounded-t-sm shadow-sm">
            <h3 class="text-xl font-bold text-slate-900">Featured Acquisitions</h3>
            <a href="{{ route('products.index') }}" class="bg-[#2874f0] text-white px-4 py-2 rounded-sm text-xs font-bold no-underline hover:bg-[#1266ec] shadow-sm">VIEW ALL</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 bg-white p-2 rounded-b-sm border-x border-b border-slate-100 shadow-sm">
            @foreach ($featuredProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>

    {{-- =======================
           LATEST ARRIVALS
    ======================= --}}
    <div class="mb-12">
        <div class="bg-white p-4 flex items-center justify-between border-b border-slate-100 rounded-t-sm shadow-sm">
            <h3 class="text-xl font-bold text-slate-900">Latest Series Drops</h3>
            <a href="{{ route('products.index') }}" class="bg-[#2874f0] text-white px-4 py-2 rounded-sm text-xs font-bold no-underline hover:bg-[#1266ec] shadow-sm">VIEW ALL</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 bg-white p-2 rounded-b-sm border-x border-b border-slate-100 shadow-sm">
            @foreach ($latestProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>

</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

@endsection
