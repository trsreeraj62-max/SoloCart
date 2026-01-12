@extends('layouts.app')

@section('content')
<div class="container py-10">
    <div class="home-layout flex flex-col lg:flex-row gap-10">
        <!-- Sidebar -->
        <aside class="home-sidebar w-full lg:w-[260px] flex-shrink-0">
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 italic">
                <div class="filter-group mb-10">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Discover</h4>
                    <form action="{{ route('products.index') }}" method="GET">
                        <div class="relative group">
                            <input type="text" name="search" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-5 py-3 outline-none focus:ring-2 focus:ring-primary/20 focus:bg-white transition-all font-bold text-sm text-slate-700 placeholder:text-slate-300" placeholder="Quick find...">
                            <button type="submit" class="absolute right-4 top-3.5 text-slate-300 group-focus-within:text-primary transition">
                                <i class="fas fa-search text-xs"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="filter-group">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Collections</h4>
                    <div class="flex flex-col gap-1">
                        <a href="{{ route('products.index') }}" class="category-link flex items-center gap-3 py-3 px-4 rounded-xl no-underline transition-all {{ !request('category_id') ? 'bg-primary/5 text-primary font-black scale-105 shadow-sm' : 'text-slate-500 font-bold hover:bg-slate-50' }}">
                            <i class="fas fa-th-large text-[10px] opacity-70"></i> 
                            <span class="text-sm">All Items</span>
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('products.index', ['category_id' => $category->id]) }}" 
                               class="category-link flex items-center gap-3 py-3 px-4 rounded-xl no-underline transition-all {{ request('category_id') == $category->id ? 'bg-primary/5 text-primary font-black scale-105 shadow-sm' : 'text-slate-500 font-bold hover:bg-slate-50' }}">
                                <i class="fas fa-chevron-right text-[8px] opacity-30"></i> 
                                <span class="text-sm">{{ $category->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-w-0">
             <!-- Hero Banner -->
             <div class="hero-banner-main mb-12 relative h-[280px] rounded-[3rem] overflow-hidden shadow-2xl group border border-slate-100">
                 @if($banners->where('type', 'hero')->first())
                     <img src="{{ asset('storage/' . $banners->where('type', 'hero')->first()->image_path) }}" 
                          class="w-full h-full object-cover group-hover:scale-105 transition duration-[2s]" 
                          alt="Hero Banner">
                     <div class="absolute inset-0 bg-gradient-to-r from-black/40 to-transparent flex items-center px-12">
                         <div class="text-white max-w-sm">
                             <h2 class="text-5xl font-black tracking-tighter italic m-0">Season Drop.</h2>
                             <p class="text-white/80 font-bold text-sm mt-2 uppercase tracking-widest">Premium Curations Only</p>
                         </div>
                     </div>
                 @else
                     <div class="w-full h-full bg-slate-900 flex items-center justify-center relative overflow-hidden">
                         <div class="absolute -top-10 -left-10 w-60 h-60 bg-primary/20 rounded-full blur-3xl animate-pulse"></div>
                         <div class="absolute -bottom-10 -right-10 w-60 h-60 bg-secondary/20 rounded-full blur-3xl animate-pulse delay-700"></div>
                         <div class="text-center relative z-10">
                             <h2 class="text-6xl font-black text-white italic tracking-tighter opacity-10">SOLOCART</h2>
                             <p class="text-slate-400 font-black tracking-[0.5em] text-[10px] uppercase mt-4">Elevating Minimalism</p>
                         </div>
                     </div>
                 @endif
             </div>
        
             <!-- Featured Products -->
             <div class="mb-16">
                <div class="flex items-end justify-between mb-10">
                    <div>
                        <span class="text-[10px] font-black text-primary uppercase tracking-[0.3em] block mb-2">Editor's Choice</span>
                        <h2 class="text-3xl font-black text-slate-900 tracking-tighter m-0">Signature Pieces</h2>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-[10px] font-black text-slate-400 hover:text-primary transition no-underline uppercase tracking-widest flex items-center gap-2 pb-1 border-b-2 border-transparent hover:border-primary">
                        Browse all <i class="fas fa-arrow-right text-[8px]"></i>
                    </a>
                </div>
                <!-- INCREASED MINMAX to 260px for bigger cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    @foreach($featuredProducts as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        
            <!-- Recommended -->
            @if($recommendedProducts->count() > 0)
            <div class="mb-16">
                <div class="mb-10">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] block mb-2">Selected for you</span>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tighter m-0">Recommended</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    @foreach($recommendedProducts as $product)
                        @include('components.product-card', ['product' => $product])
                    @endforeach
                </div>
            </div>
            @endif
        </main>
    </div>
</div>

<style>
    .category-link:hover { transform: translateX(5px); }
</style>
@endsection
