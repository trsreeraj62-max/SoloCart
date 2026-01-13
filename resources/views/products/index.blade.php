@extends('layouts.app')

@section('content')
<div class="shop-page bg-[#f1f3f6] min-h-screen py-6">
    <div class="container container-max">
        
        <div class="row g-4">
            <!-- Left Sidebar -->
            <div class="col-lg-3">
                @include('components.sidebar-filters', ['categories' => $categories])
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-9">
                <!-- Top Breadcrumbs / Summary -->
                <div class="bg-white rounded-sm shadow-sm p-4 mb-4 flex items-center justify-between border border-slate-100">
                    <nav class="text-xs text-slate-400 font-bold uppercase tracking-widest flex items-center gap-2">
                        <a href="{{ route('home') }}" class="hover:text-[#2874f0] no-underline transition-colors">Home</a>
                        <i class="fas fa-chevron-right text-[8px]"></i>
                        <span class="text-slate-900">Shop</span>
                    </nav>
                    <div class="text-[10px] font-black uppercase text-slate-300 tracking-widest bg-slate-50 px-3 py-1.5 rounded">
                        Scanning {{ $products->total() }} pieces
                    </div>
                </div>

                <!-- Product Grid -->
                <div id="product-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 transition-all duration-500">
                    @forelse($products as $product)
                        @include('components.product-card', ['product' => $product])
                    @empty
                        <div class="col-span-full py-20 bg-white rounded-sm shadow-sm border border-slate-100 text-center">
                            <img src="https://static-assets-web.flixcart.com/fk-p-linchpin-web/fk-cp-zion/img/error-no-search-results_2353c5.png" class="mx-auto mb-6 h-40" alt="No results">
                            <h3 class="text-xl font-bold text-slate-800">Sorry, no results found!</h3>
                            <p class="text-slate-400 text-sm italic">Check your spelling or try different keywords</p>
                        </div>
                    @endforelse
                </div>

                <!-- Sentinel for Infinite Scroll -->
                <div id="infinite-scroll-marker" class="mt-8 flex justify-center h-20">
                    @if($products->hasMorePages())
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-8 h-8 border-4 border-slate-100 border-t-[#2874f0] rounded-full animate-spin"></div>
                            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-300">Synchronizing Data...</span>
                        </div>
                    @else
                        <div class="flex flex-col items-center gap-3 opacity-20">
                            <div class="h-px bg-slate-200 w-40"></div>
                            <span class="text-[8px] font-black uppercase tracking-[0.4em] text-slate-400">End of Protocol Catalogue</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    let nextPageUrl = '{{ $products->nextPageUrl() }}';
    let loading = false;
    const grid = document.getElementById('product-grid');
    const marker = document.getElementById('infinite-scroll-marker');

    // Threshold for infinite scroll
    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && nextPageUrl && !loading) {
            loadMore();
        }
    }, { threshold: 0.1, rootMargin: '200px' });

    if (marker) {
        observer.observe(marker);
    }

    async function loadMore() {
        loading = true;
        try {
            const response = await fetch(nextPageUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            
            if(data.html) {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;
                
                // Animate items insertion
                const children = [...tempDiv.children];
                children.forEach((child, index) => {
                    child.style.opacity = '0';
                    child.style.transform = 'translateY(20px)';
                    grid.appendChild(child);
                    
                    setTimeout(() => {
                        child.style.transition = 'all 0.5s cubic-bezier(0.16, 1, 0.3, 1)';
                        child.style.opacity = '1';
                        child.style.transform = 'translateY(0)';
                    }, index * 50);
                });

                nextPageUrl = data.next_page;
                
                if(!nextPageUrl) {
                    marker.innerHTML = `
                        <div class="flex flex-col items-center gap-3 opacity-20">
                            <div class="h-px bg-slate-200 w-40"></div>
                            <span class="text-[8px] font-black uppercase tracking-[0.4em] text-slate-400">End of Protocol Catalogue</span>
                        </div>
                    `;
                    observer.disconnect();
                }
            }
        } catch (error) {
            console.error('Error loading more products:', error);
        } finally {
            loading = false;
        }
    }
</script>
@endpush
@endsection
