@extends('layouts.app')

@section('content')
<div class="container py-16">
    <div class="shop-layout flex flex-col lg:flex-row gap-12 items-start">
        
        <!-- Sidebar -->
        <aside class="sidebar-filters w-full lg:w-[280px] flex-shrink-0">
            <div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100 flex flex-col gap-12">
                <!-- Search -->
                <div class="filter-group">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Precision Search</h4>
                    <form action="{{ route('products.index') }}" method="GET">
                        @if(request('category_id')) <input type="hidden" name="category_id" value="{{ request('category_id') }}"> @endif
                        <div class="relative group">
                            <input type="text" name="search" value="{{ request('search') }}" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-3 outline-none focus:ring-2 focus:ring-primary/20 transition-all font-bold text-sm text-slate-700 placeholder:text-slate-300" placeholder="Keywords...">
                            <button type="submit" class="absolute right-4 top-3.5 text-slate-300 hover:text-primary transition">
                                <i class="fas fa-search text-xs"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Categories -->
                <div class="filter-group">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Categories</h4>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('products.index', request()->except('category_id')) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-black no-underline transition-all border {{ !request('category_id') ? 'bg-slate-900 border-slate-900 text-white shadow-xl' : 'bg-white border-slate-100 text-slate-400 hover:border-slate-200 hover:text-slate-600' }}">
                            All
                        </a>
                        @foreach($categories as $cat)
                        <a href="{{ route('products.index', array_merge(request()->all(), ['category_id' => $cat->id])) }}" 
                           class="px-4 py-2 rounded-xl text-xs font-black no-underline transition-all border {{ request('category_id') == $cat->id ? 'bg-slate-900 border-slate-900 text-white shadow-xl' : 'bg-white border-slate-100 text-slate-400 hover:border-slate-200 hover:text-slate-600' }}">
                            {{ $cat->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- Price Range -->
                <div class="filter-group">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Price Ceiling</h4>
                    <form action="{{ route('products.index') }}" method="GET">
                        @if(request('category_id')) <input type="hidden" name="category_id" value="{{ request('category_id') }}"> @endif
                        @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-slate-50 rounded-2xl p-3 border border-slate-100">
                                    <span class="text-[8px] font-bold text-slate-300 uppercase block mb-1">Min $</span>
                                    <input type="number" name="min_price" class="bg-transparent border-none outline-none w-full font-black text-slate-700 text-sm" value="{{ request('min_price') }}">
                                </div>
                                <div class="flex-1 bg-slate-50 rounded-2xl p-3 border border-slate-100">
                                    <span class="text-[8px] font-bold text-slate-300 uppercase block mb-1">Max $</span>
                                    <input type="number" name="max_price" class="bg-transparent border-none outline-none w-full font-black text-slate-700 text-sm" value="{{ request('max_price') }}">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="w-full py-4 bg-primary text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-all hover:-translate-y-1">
                            Refine Grid
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Products Area -->
        <main class="flex-1 min-w-0">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
                <div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] block mb-2">Shopping Grid</span>
                    <h2 class="text-4xl font-black text-slate-900 tracking-tighter m-0 italic">
                        @if(request('search'))
                            Search: "{{ request('search') }}"
                        @elseif(request('category_id'))
                            {{ $categories->find(request('category_id'))->name }}
                        @else
                            The Collection
                        @endif
                    </h2>
                </div>
                <div class="text-[10px] font-black text-slate-300 uppercase tracking-widest bg-slate-50 px-4 py-2 rounded-full cursor-default">
                    Results {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} • Total {{ $products->total() }}
                </div>
            </div>

            @if($products->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    @foreach($products as $product)
                         @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
                
                <div class="mt-20">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-32 bg-white rounded-[3rem] border border-slate-100">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-8 text-slate-200">
                        <i class="fas fa-search-minus text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tighter mb-2">No items discovered.</h3>
                    <p class="text-slate-400 font-medium mb-10">Your parameters didn't match any of our premium pieces.</p>
                    <a href="{{ route('products.index') }}" class="py-4 px-10 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest no-underline shadow-xl hover:shadow-slate-900/30 transition">
                        Clear Parameters
                    </a>
                </div>
            @endif
        </main>

    </div>
</div>
@endsection
