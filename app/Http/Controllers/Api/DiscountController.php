<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class DiscountController extends ApiController
{
    /**
     * Apply global discount
     */
    public function applyGlobal(Request $request)
    {
        $request->validate(['discount_percent' => 'required|integer|min:0|max:100']);

        Product::query()->update([
            'discount_percent' => $request->discount_percent,
            'start_at' => now(),
            'end_at' => now()->addYears(1)
        ]);

        return $this->success([], "Global discount applied to all products");
    }

    /**
     * Apply category discount
     */
    public function applyCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'discount_percent' => 'required|integer|min:0|max:100'
        ]);

        Product::where('category_id', $request->category_id)->update([
            'discount_percent' => $request->discount_percent,
            'start_at' => now(),
            'end_at' => now()->addYears(1)
        ]);

        return $this->success([], "Category discount applied");
    }
}
