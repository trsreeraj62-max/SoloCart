<aside class="w-full bg-white rounded-sm shadow-sm border border-slate-100 divide-y divide-slate-100 sticky top-20">
    
    <!-- Filter Header -->
    <div class="p-4 bg-slate-50">
        <h4 class="text-lg font-bold text-slate-900 m-0">Filters</h4>
    </div>

    <!-- Search Section -->
    <div class="p-4">
        <label class="text-[10px] uppercase font-black tracking-widest text-slate-400 mb-2 block">Keywords</label>
        <form action="{{ route('products.index') }}" method="GET" class="relative">
            <input type="text" name="search" value="{{ request('search') }}" 
                   class="w-full bg-slate-50 border-b-2 border-slate-200 py-2.5 px-3 text-sm focus:outline-none focus:border-[#2874f0] transition-colors font-medium"
                   placeholder="Search products...">
            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#2874f0]">
                <i class="fas fa-search text-xs"></i>
            </button>
        </form>
    </div>

    <!-- Categories Section -->
    <div class="p-4">
        <label class="text-[10px] uppercase font-black tracking-widest text-slate-400 mb-3 block">Categories</label>
        <div class="flex flex-col gap-1 max-h-[400px] overflow-y-auto custom-scrollbar">
            <a href="{{ route('products.index', request()->except(['category', 'category_id', 'page'])) }}" 
               class="flex items-center justify-between px-3 py-2 no-underline rounded-sm transition-all group {{ !request('category') && !request('category_id') ? 'bg-[#2874f0] text-white' : 'text-slate-600 hover:bg-slate-50' }}">
                <span class="text-xs font-bold uppercase tracking-wide">All Products</span>
                <i class="fas fa-chevron-right text-[8px] opacity-30 group-hover:opacity-100"></i>
            </a>
            
            @if(isset($categories))
                @foreach($categories as $cat)
                    @php 
                        $isActive = request('category') == $cat->id || request('category_id') == $cat->id;
                    @endphp
                    <a href="{{ route('products.index', array_merge(request()->all(), ['category' => $cat->id])) }}" 
                       class="flex items-center justify-between px-3 py-2 no-underline rounded-sm transition-all group {{ $isActive ? 'bg-[#2874f0]/10 text-[#2874f0] border-l-4 border-[#2874f0]' : 'text-slate-600 hover:bg-slate-50' }}">
                        <span class="text-xs font-bold">{{ $cat->name }}</span>
                        <i class="fas fa-chevron-right text-[8px] opacity-30 group-hover:opacity-100"></i>
                    </a>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Price Section -->
    <div class="p-4">
        <label class="text-[10px] uppercase font-black tracking-widest text-slate-400 mb-4 block">Price Range</label>
        <form action="{{ route('products.index') }}" method="GET" class="space-y-4">
            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
            @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
            
            <div class="flex items-center gap-2">
                <input type="number" name="min" value="{{ request('min') }}" placeholder="Min" 
                       class="w-1/2 bg-slate-50 border border-slate-200 py-1.5 px-3 text-xs focus:outline-none focus:border-[#2874f0]">
                <span class="text-slate-300">to</span>
                <input type="number" name="max" value="{{ request('max') }}" placeholder="Max" 
                       class="w-1/2 bg-slate-50 border border-slate-200 py-1.5 px-3 text-xs focus:outline-none focus:border-[#2874f0]">
            </div>
            <button type="submit" class="w-full bg-white text-[#2874f0] border border-[#2874f0] py-2 rounded-sm text-xs font-black uppercase tracking-widest hover:bg-[#2874f0] hover:text-white transition-all">
                Apply Filters
            </button>
        </form>
    </div>

    @if(request()->anyFilled(['search', 'category', 'min', 'max']))
    <div class="p-4">
        <a href="{{ route('products.index') }}" class="text-[10px] font-black uppercase text-rose-500 hover:underline no-underline tracking-widest">
            <i class="fas fa-times mr-1"></i> Clear All Filters
        </a>
    </div>
    @endif
</aside>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
