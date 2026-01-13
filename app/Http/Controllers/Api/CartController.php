<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends ApiController
{
    /**
     * Get user cart
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $cart = $user->cart()->with('items.product.images')->first();
            
            if (!$cart) {
                $cart = $user->cart()->create();
            }

            return $this->success($cart, "Cart retrieved");
        } catch (\Exception $e) {
            return $this->error("Failed to retrieve cart: " . $e->getMessage());
        }
    }

    /**
     * Add item to cart
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            
            if ($product->stock < $request->quantity) {
                return $this->error("Only " . $product->stock . " items in stock", 422);
            }

            $user = Auth::user();
            $cart = $user->cart ?: $user->cart()->create();
            
            $item = $cart->items()->where('product_id', $request->product_id)->first();

            if ($item) {
                if ($product->stock < ($item->quantity + $request->quantity)) {
                    return $this->error("Cannot add more. Only " . $product->stock . " items in stock total.", 422);
                }
                $item->increment('quantity', $request->quantity);
            } else {
                $cart->items()->create([
                    'product_id' => $request->product_id,
                    'quantity' => $request->quantity
                ]);
            }

            return $this->success($cart->load('items.product.images'), "Item added to cart");
        } catch (\Exception $e) {
            return $this->error("Failed to add item: " . $e->getMessage());
        }
    }

    /**
     * Update item quantity
     */
    public function update(Request $request, $id = null)
    {
        $productId = $id ?? $request->product_id;
        
        if (!$productId) {
            return $this->error("Product ID is required", 422);
        }

        try {
            $product = Product::findOrFail($productId);
            if ($product->stock < $request->quantity) {
                return $this->error("Only " . $product->stock . " items in stock", 422);
            }

            $cart = Auth::user()->cart;
            if (!$cart) return $this->error("Cart not found", 404);

            $item = $cart->items()->where('product_id', $request->product_id)->first();
            if (!$item) return $this->error("Item not in cart", 404);

            $item->update(['quantity' => $request->quantity]);

            return $this->success($cart->load('items.product.images'), "Cart updated");
        } catch (\Exception $e) {
            return $this->error("Update failed: " . $e->getMessage());
        }
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request, $id = null)
    {
        $productId = $id ?? $request->product_id;

        if (!$productId) {
            return $this->error("Product ID is required", 422);
        }

        try {
            $cart = Auth::user()->cart;
            if ($cart) {
                $cart->items()->where('product_id', $productId)->delete();
            }

            return $this->success($cart ? $cart->load('items.product.images') : [], "Item removed from cart");
        } catch (\Exception $e) {
            return $this->error("Removal failed: " . $e->getMessage());
        }
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        $cart = Auth::user()->cart;
        if ($cart) {
            $cart->items()->delete();
        }

        return $this->success([], "Cart cleared");
    }
}
