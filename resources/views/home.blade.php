@extends('layouts.app')

@section('content')
<div class="container">
    <div class="home-layout">
        <!-- Sidebar -->
        <aside class="home-sidebar">
            <div class="filter-group">
                <div class="filter-title">Categories</div>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="{{ route('products.index') }}" class="category-link" style="font-weight: 600;">All Products</a>
                    @foreach($categories as $category)
                        <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="category-link">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="filter-group">
                <div class="filter-title">Filters</div>
                <form action="{{ route('products.index') }}" method="GET">
                    <input type="text" name="search" class="search-input mb-4" style="width: 100%; border: 1px solid var(--border); padding: 0.5rem; border-radius: 6px;" placeholder="Search products...">
                </form>
                
                <div class="p-4 bg-light rounded text-center text-muted text-sm">
                    Price filters available on Shop page
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main>
             <!-- Hero Banner -->
             <div class="hero mb-8" style="height: 300px;">
                 @if($banners->where('type', 'hero')->first())
                     <img src="{{ asset('storage/' . $banners->where('type', 'hero')->first()->image_path) }}" alt="Hero">
                     <div class="hero-content">
                         <h1>{{ $banners->where('type', 'hero')->first()->title ?? 'Welcome to SoloCart' }}</h1>
                         <a href="{{ $banners->where('type', 'hero')->first()->link ?? route('products.index') }}" class="btn btn-primary">Shop Now</a>
                     </div>
                 @else
                     <div style="background: linear-gradient(135deg, #6366f1, #a855f7); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: white; border-radius: var(--radius);">
                         <div class="text-center p-8">
                             <h1 style="font-weight: 800; font-size: 2.5rem; margin-bottom: 1rem;">Super Sale Starts Now</h1>
                             <p class="mb-4 text-lg opacity-90">Up to 50% off on all electronics.</p>
                             <a href="{{ route('products.index') }}" class="btn btn-light" style="background: white; color: #6366f1; border: none;">Shop Now</a>
                         </div>
                     </div>
                 @endif
             </div>
        
             <!-- Featured Products -->
             <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="section-title" style="margin-bottom: 0;">Featured Products</h2>
                    <a href="{{ route('products.index') }}" class="text-primary font-bold text-sm">View All &rarr;</a>
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
        </main>
    </div>
</div>
@endsection
