<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/home-data', function () {
    return response()->json([
        'status' => true,
        'products' => Product::with(['category', 'images'])->latest()->take(8)->get()
    ]);
});
