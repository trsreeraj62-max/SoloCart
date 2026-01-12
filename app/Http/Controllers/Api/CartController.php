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
        $cart = Auth::user()->cart()->with('items.product')->first();
        
        if (!$cart) {
            $cart = Auth::user()->cart()->create();
        }

        return $this->success($cart, "Cart retrieved");
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

        $user = Auth::user();
        $cart = $user->cart ?: $user->cart()->create();
        
        $item = $cart->items()->where('product_id', $request->product_id)->first();

        if ($item) {
            $item->increment('quantity', $request->quantity);
        } else {
            $cart->items()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }

        return $this->success($cart->load('items.product'), "Item added to cart");
    }

    /**
     * Update item quantity
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Auth::user()->cart;
        if (!$cart) return $this->error("Cart not found", 404);

        $item = $cart->items()->where('product_id', $request->product_id)->first();
        if (!$item) return $this->error("Item not in cart", 404);

        $item->update(['quantity' => $request->quantity]);

        return $this->success($cart->load('items.product'), "Cart updated");
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required']);

        $cart = Auth::user()->cart;
        if ($cart) {
            $cart->items()->where('product_id', $request->product_id)->delete();
        }

        return $this->success($cart ? $cart->load('items.product') : [], "Item removed");
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
