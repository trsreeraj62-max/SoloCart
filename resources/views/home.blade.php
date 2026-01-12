@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 2rem;">
    <!-- Hero Banner -->
    <div class="hero mb-8">
        @if($banners->where('type', 'hero')->first())
            <img src="{{ asset('storage/' . $banners->where('type', 'hero')->first()->image_path) }}" alt="Hero">
            <div class="hero-content">
                <h1>{{ $banners->where('type', 'hero')->first()->title ?? 'Welcome to SoloCart' }}</h1>
                <a href="{{ $banners->where('type', 'hero')->first()->link ?? route('products.index') }}" class="btn btn-primary">Shop Now</a>
            </div>
        @else
            <div style="background: linear-gradient(135deg, #1e293b, #0f172a); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: white;">
                <div class="text-center">
                    <h1>Spring Collection 2026</h1>
                    <p class="mb-4 text-lg">Discover the latest trends in our premium collection.</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Shop Now</a>
                </div>
            </div>
        @endif
    </div>

    <!-- Categories -->
    <div class="mb-8">
        <h2 class="section-title">Shop by Category</h2>
        <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
            @foreach($categories as $category)
            <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="card p-4 text-center" style="display: block; text-decoration: none;">
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" style="width: 50px; height: 50px; margin: 0 auto 1rem; object-fit: contain;">
                @else
                   <div style="width: 50px; height: 50px; background: #e0e7ff; color: var(--primary); border-radius: 50%; margin: 0 auto 0.5rem; display: flex; align-items: center; justify-content: center;">
                       <span style="font-size: 1.5rem;">●</span>
                   </div>
                @endif
                <h3 class="font-bold text-sm">{{ $category->name }}</h3>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Featured Products -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="section-title" style="margin-bottom: 0;">Featured Products</h2>
            <a href="{{ route('products.index') }}" class="text-primary font-bold">View All &rarr;</a>
        </div>
        <div class="product-grid mt-4">
            @foreach($featuredProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>

    <!-- Recommended / You May Like -->
    @if($recommendedProducts->count() > 0)
    <div class="mb-8">
        <h2 class="section-title">You May Like</h2>
        <div class="product-grid mt-4">
            @foreach($recommendedProducts as $product)
                @include('components.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
