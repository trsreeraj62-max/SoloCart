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
                'discount_percent' => 'required|integer|min:0|max:100',
                'discount_start_date' => 'nullable|date',
                'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date'
            ]);

            Product::where('category_id', $request->category_id)->update([
                'discount_percent' => $request->discount_percent,
                'discount_start_date' => $request->discount_start_date ?? now(),
                'discount_end_date' => $request->discount_end_date
            ]);

            // Track discount on the category model as well
            \App\Models\Category::where('id', $request->category_id)->update([
                'discount_percent' => $request->discount_percent,
                'discount_start_date' => $request->discount_start_date ?? now(),
                'discount_end_date' => $request->discount_end_date
            ]);

            \Illuminate\Support\Facades\Cache::forget('home_data');

            return $this->success([], "Discount applied to category successfully with timing.");
        } catch (\Exception $e) {
            Log::error('Apply Category Discount Error: ' . $e->getMessage());
            return $this->error("Failed to apply category discount: " . $e->getMessage(), 500);
        }
    }

    /**
     * Apply discount to ALL products (All Categories)
     */
    public function applyToAll(Request $request)
    {
        try {
            $request->validate([
                'discount_percent' => 'required|integer|min:0|max:100',
                'discount_start_date' => 'nullable|date',
                'discount_end_date' => 'nullable|date|after_or_equal:discount_start_date'
            ]);

            Product::query()->update([
                'discount_percent' => $request->discount_percent,
                'discount_start_date' => $request->discount_start_date ?? now(),
                'discount_end_date' => $request->discount_end_date
            ]);

            \Illuminate\Support\Facades\Cache::forget('home_data');

            return $this->success([], "Global discount applied successfully with timing.");
        } catch (\Exception $e) {
            Log::error('Apply Global Discount Error: ' . $e->getMessage());
            return $this->error("Failed to apply global discount: " . $e->getMessage(), 500);
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

            if ($request->filled('category_id')) {
                \App\Models\Category::where('id', $request->category_id)->update([
                    'discount_percent' => 0,
                    'discount_start_date' => null,
                    'discount_end_date' => null
                ]);
            } else {
                \App\Models\Category::query()->update([
                    'discount_percent' => 0,
                    'discount_start_date' => null,
                    'discount_end_date' => null
                ]);
            }

            \Illuminate\Support\Facades\Cache::forget('home_data');

            return $this->success([], "Discounts removed successfully");
        } catch (\Exception $e) {
            Log::error('Remove Discount Error: ' . $e->getMessage());
            return $this->error("Failed to remove discounts", 500);
        }
    }
}
