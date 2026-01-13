use App\Models\Product;

Route::get('/home-data', function () {
    return response()->json([
        'status' => true,
        'products' => Product::latest()->take(8)->get()
    ]);
});
