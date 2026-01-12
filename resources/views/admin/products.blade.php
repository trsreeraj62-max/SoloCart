@extends('layouts.admin')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="font-bold text-2xl text-slate-800">Products</h1>
        <p class="text-slate-500">Manage your store inventory</p>
    </div>
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow transition flex items-center gap-2" onclick="openAddModal()">
        <i class="fas fa-plus"></i> Add Product
    </button>
</div>

@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
    <p>{{ session('success') }}</p>
</div>
@endif
@if(session('error'))
<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
    <p>{{ session('error') }}</p>
</div>
@endif

<div class="bg-white rounded-xl shadow-sm overflow-hidden border border-slate-200">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Image</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Name</th>
                    <th class="p-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Category</th>
                    <th class="p-4 text-xs text-right font-bold text-slate-500 uppercase tracking-wider">Price</th>
                    <th class="p-4 text-xs text-right font-bold text-slate-500 uppercase tracking-wider">Stock</th>
                    <th class="p-4 text-xs text-center font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="p-4 text-xs text-right font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($products as $product)
                <tr class="hover:bg-slate-50 transition">
                    <td class="p-4">
                        @if($product->images->first())
                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="w-12 h-12 rounded object-cover border border-slate-200">
                        @else
                            <div class="w-12 h-12 rounded bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </td>
                    <td class="p-4 font-bold text-slate-700">{{ $product->name }}</td>
                    <td class="p-4">
                        <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2 py-1 rounded-full border border-blue-100">
                            {{ $product->category->name }}
                        </span>
                    </td>
                    <td class="p-4 text-right font-bold text-slate-700">
                        @if($product->discount_percent > 0)
                            <span class="text-xs text-red-500 line-through mr-1">${{ number_format($product->price, 2) }}</span>
                            ${{ number_format($product->price * (1 - $product->discount_percent/100), 2) }}
                        @else
                            ${{ number_format($product->price, 2) }}
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        @if($product->stock < 10)
                            <span class="text-red-600 font-bold flex items-center justify-end gap-1"><i class="fas fa-exclamation-circle"></i> {{ $product->stock }}</span>
                        @else
                            <span class="text-green-600 font-bold">{{ $product->stock }}</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        @if($product->discount_percent > 0)
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded font-bold">On Sale ({{ $product->discount_percent }}%)</span>
                        @else
                            <span class="text-slate-400 text-xs">-</span>
                        @endif
                    </td>
                    <td class="p-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                             <!-- Simple JS Edit (Pass ID only for now, ideally populate details) -->
                            <button class="text-slate-400 hover:text-blue-600 transition" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-600 transition" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-200 bg-slate-50">
        {{ $products->links() }}
    </div>
</div>

<!-- Add Product Modal -->
<div id="addProductModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto m-4">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center sticky top-0 bg-white z-10">
            <h3 class="text-xl font-bold text-slate-800">Add New Product</h3>
            <button onclick="closeAddModal()" class="text-slate-400 hover:text-slate-600 text-2xl">&times;</button>
        </div>
        
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Basic Info -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Product Name</label>
                        <input type="text" name="name" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Category</label>
                        <select name="category_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Price ($)</label>
                            <input type="number" step="0.01" name="price" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Stock</label>
                            <input type="number" name="stock" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
                        </div>
                    </div>
                </div>

                <!-- Image Upload & Preview -->
                <div>
                     <label class="block text-sm font-bold text-slate-700 mb-1">Product Image</label>
                     <div class="border-2 border-dashed border-slate-300 rounded-lg p-4 text-center hover:bg-slate-50 transition cursor-pointer relative" style="min-height: 200px; display: flex; align-items: center; justify-content: center;">
                         <input type="file" name="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(event)" required>
                         <div id="placeholderText">
                             <i class="fas fa-cloud-upload-alt text-3xl text-slate-400 mb-2"></i>
                             <p class="text-sm text-slate-500">Click to upload image</p>
                         </div>
                         <img id="imagePreview" src="#" alt="Preview" class="hidden absolute inset-0 w-full h-full object-contain bg-white rounded-lg p-2">
                     </div>
                </div>
            </div>

            <!-- Description & Specs -->
            <div class="mb-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Specifications (JSON or Text)</label>
                    <textarea name="specifications" rows="3" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Box Content: Charger, Manual..."></textarea>
                </div>
            </div>

            <!-- Discount Section -->
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                <h4 class="font-bold text-blue-800 mb-3 flex items-center gap-2"><i class="fas fa-tags"></i> Discount Settings (Optional)</h4>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-blue-700 mb-1">Percentage (%)</label>
                        <input type="number" name="discount_percent" min="0" max="100" class="w-full border border-blue-200 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-blue-700 mb-1">Start Date</label>
                        <input type="date" name="discount_start_date" class="w-full border border-blue-200 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-blue-700 mb-1">End Date</label>
                        <input type="date" name="discount_end_date" class="w-full border border-blue-200 rounded px-2 py-1 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="closeAddModal()" class="px-6 py-2 text-slate-600 hover:bg-slate-100 rounded-lg font-bold transition">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow transition">Save Product</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('addProductModal').classList.remove('hidden');
    }
    function closeAddModal() {
        document.getElementById('addProductModal').classList.add('hidden');
    }
    function previewImage(event) {
        var output = document.getElementById('imagePreview');
        var placeholder = document.getElementById('placeholderText');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.classList.remove('hidden');
        placeholder.classList.add('hidden');
    }
</script>

@endsection
