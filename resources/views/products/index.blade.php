@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 2rem;">
    <div class="grid layout-grid" style="gap: 2rem; align-items: start;">
        
        <!-- Sidebar -->
        <div class="card p-4 sidebar-filters" style="min-width: 250px;">
            <h3 class="font-bold mb-4">Categories</h3>
            <ul class="mb-8">
                <li><a href="{{ route('products.index') }}" class="text-muted {{ !request('category_id') ? 'text-primary font-bold' : '' }}">All Products</a></li>
                @foreach($categories as $cat)
                <li style="margin-top: 0.5rem;">
                    <a href="{{ route('products.index', array_merge(request()->all(), ['category_id' => $cat->id])) }}" class="text-muted {{ request('category_id') == $cat->id ? 'text-primary font-bold' : '' }}">
                        {{ $cat->name }}
                    </a>
                </li>
                @endforeach
            </ul>
            
            <h3 class="font-bold mb-4">Price Range</h3>
            <form action="{{ route('products.index') }}" method="GET">
                @if(request('category_id'))
                    <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                @endif
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <div class="flex gap-2 mb-2">
                    <input type="number" name="min_price" placeholder="Min" class="input-field" style="padding: 0.5rem;" value="{{ request('min_price') }}">
                    <input type="number" name="max_price" placeholder="Max" class="input-field" style="padding: 0.5rem;" value="{{ request('max_price') }}">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 0.8rem;">Filter</button>
            </form>
        </div>

        <!-- Products -->
        <div style="flex: 1;">
            @if(request('search'))
                <h2 class="mb-4 text-xl">Search results for "{{ request('search') }}"</h2>
            @endif

            @if($products->count() > 0)
                <div class="product-grid">
                    @foreach($products as $product)
                         @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
                
                <div class="mt-8 flex justify-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-8 card">
                    <p class="text-muted text-lg">No products found matching your criteria.</p>
                </div>
            @endif
        </div>

    </div>
    
    <style>
        .layout-grid { display: flex; }
        @media (max-width: 768px) {
            .layout-grid { flex-direction: column; }
            .sidebar-filters { width: 100%; }
        }
    </style>
</div>
@endsection
