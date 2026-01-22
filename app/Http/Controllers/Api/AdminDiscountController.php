<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminDiscountController extends ApiController
{
    /**
     * Unified Apply Discount (Handles both Category and Global)
     * Fixes 404 on /api/admin/discounts/apply
     */
    public function apply(Request $request)
    {
        if ($request->filled('category_id')) {
            return $this->applyToCategory($request);
        }
        return $this->applyToAll($request);
    }

    /**
     * Apply discount to a specific category
     */
    public function applyToCategory(Request $request)
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
                'discount_percent' => 'required|integer|min:0|max:100'
            ]);

            Product::where('category_id', $request->category_id)->update([
                'discount_percent' => $request->discount_percent,
                'discount_start_date' => now(),
                'discount_end_date' => null // Permanent until changed
            ]);

            return $this->success([], "Discount of {$request->discount_percent}% applied to category");
        } catch (\Exception $e) {
            Log::error('Apply Category Discount Error: ' . $e->getMessage());
            return $this->error("Failed to apply category discount", 500);
        }
    }

    /**
     * Apply discount to ALL products (All Categories)
     */
    public function applyToAll(Request $request)
    {
        try {
            $request->validate([
                'discount_percent' => 'required|integer|min:0|max:100'
            ]);

            Product::query()->update([
                'discount_percent' => $request->discount_percent,
                'discount_start_date' => now(),
                'discount_end_date' => null
            ]);

            return $this->success([], "Global discount of {$request->discount_percent}% applied to all products");
        } catch (\Exception $e) {
            Log::error('Apply Global Discount Error: ' . $e->getMessage());
            return $this->error("Failed to apply global discount", 500);
        }
    }

    /**
     * Remove discounts (Reset)
     */
    public function removeDiscount(Request $request)
    {
        try {
            $query = Product::query();

            // If category_id provided, only reset that category. Otherwise reset all.
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            $query->update([
                'discount_percent' => 0,
                'discount_start_date' => null,
                'discount_end_date' => null
            ]);

            return $this->success([], "Discounts removed successfully");
        } catch (\Exception $e) {
            Log::error('Remove Discount Error: ' . $e->getMessage());
            return $this->error("Failed to remove discounts", 500);
        }
    }
}
