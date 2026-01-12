@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h1 class="font-bold text-2xl text-slate-800">Promotions & Categories</h1>
    <p class="text-slate-500">Manage discounts and store categories</p>
</div>

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm role='alert'">
    <p>{{ session('success') }}</p>
</div>
@endif
@if(session('error'))
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm role='alert'">
    <p>{{ session('error') }}</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Create Category -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3 mb-4 text-purple-600">
            <i class="fas fa-folder-plus text-xl"></i>
            <h3 class="font-bold text-lg text-slate-800">Create Category</h3>
        </div>
        <p class="text-slate-500 text-sm mb-4">Add a new product category to the store.</p>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold text-slate-700 mb-1">Category Name</label>
                <input type="text" name="name" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 outline-none" required>
            </div>
            <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg font-bold shadow transition">Add Category</button>
        </form>
        
        <div class="mt-6 pt-6 border-t border-slate-100">
            <h4 class="font-bold text-sm text-slate-600 mb-3">Existing Categories</h4>
            <div class="flex flex-wrap gap-2">
                @foreach($categories as $category)
                    <span class="bg-slate-100 text-slate-600 text-xs px-2 py-1 rounded border border-slate-200">{{ $category->name }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Category Discount -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3 mb-4 text-blue-600">
            <i class="fas fa-tags text-xl"></i>
            <h3 class="font-bold text-lg text-slate-800">Category Discount</h3>
        </div>
        <p class="text-slate-500 text-sm mb-4">Apply discount to a specific category.</p>
        <form action="{{ route('admin.discounts.category') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-bold text-slate-700 mb-1">Select Category</label>
                <select name="category_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold text-slate-700 mb-1">Discount (%)</label>
                <input type="number" name="discount_percent" min="0" max="100" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-bold shadow transition">Apply Discount</button>
        </form>
    </div>

    <!-- Global Discount -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3 mb-4 text-orange-500">
            <i class="fas fa-percent text-xl"></i>
            <h3 class="font-bold text-lg text-slate-800">Global Discount</h3>
        </div>
        <p class="text-slate-500 text-sm mb-4">Apply discount to ALL products.</p>
        <form action="{{ route('admin.discounts.global') }}" method="POST">
            @csrf
            <div class="mb-4">
                 <label class="block text-sm font-bold text-slate-700 mb-1">Discount (%)</label>
                 <input type="number" name="discount_percent" min="0" max="100" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 outline-none" required>
            </div>
             <div class="p-3 bg-orange-50 text-orange-800 text-xs rounded border border-orange-100 mb-4">
                 <strong>Warning:</strong> This will overwrite all individual product discounts.
             </div>
            <button class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg font-bold shadow transition">Apply to All</button>
        </form>
    </div>
</div>
@endsection
