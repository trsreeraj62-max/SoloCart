@extends('layouts.admin')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="font-bold text-2xl">Products</h1>
    <button class="btn btn-primary" onclick="alert('Implement Add Product Modal')">Add New Product</button>
</div>
<div class="card p-0" style="overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8fafc; border-bottom: 1px solid var(--border);">
            <tr>
                <th class="p-4 text-left text-sm font-bold text-muted uppercase">ID</th>
                <th class="p-4 text-left text-sm font-bold text-muted uppercase">Name</th>
                <th class="p-4 text-left text-sm font-bold text-muted uppercase">Category</th>
                <th class="p-4 text-right text-sm font-bold text-muted uppercase">Price</th>
                <th class="p-4 text-right text-sm font-bold text-muted uppercase">Stock</th>
                <th class="p-4 text-right text-sm font-bold text-muted uppercase">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr style="border-bottom: 1px solid var(--border);">
                <td class="p-4 text-muted">#{{ $product->id }}</td>
                <td class="p-4 font-bold">{{ $product->name }}</td>
                <td class="p-4"><span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $product->category->name }}</span></td>
                <td class="p-4 text-right font-bold">${{ number_format($product->price, 2) }}</td>
                <td class="p-4 text-right {{ $product->stock < 10 ? 'text-red-500 font-bold' : '' }}">{{ $product->stock }}</td>
                <td class="p-4 text-right">
                    <button class="text-primary font-bold hover:underline">Edit</button>
                    <button class="text-red-500 font-bold hover:underline ml-2">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">
        {{ $products->links() }}
    </div>
</div>
@endsection
