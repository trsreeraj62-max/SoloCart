<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CartWebController extends Controller
{
    public function index()
    {
        try {
            $cart = null;
            if (Auth::check()) {
                $cart = Cart::where('user_id', Auth::id())->with('items.product.images')->first();
            }
            
            return view('cart.index', compact('cart'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Cart Index Error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load cart.');
        }
    }

    public function add(Request $request)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login to add items to cart.');
            }
    
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'nullable|integer|min:1'
            ]);
    
            $product = Product::findOrFail($request->product_id);
            $quantity = $request->quantity ?? 1;
    
            $cart = Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => session()->getId()]
            );
    
            $cartItem = CartItem::where('cart_id', $cart->id)->where('product_id', $product->id)->first();
    
            if ($cartItem) {
                $cartItem->increment('quantity', $quantity);
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity
                ]);
            }
    
            return redirect()->back()->with('success', 'Product added to cart!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Cart Add Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add item to cart.');
        }
    }

    public function remove(Request $request)
    {
         try {
            $request->validate(['cart_item_id' => 'required|exists:cart_items,id']);
            CartItem::destroy($request->cart_item_id);
            return redirect()->back()->with('success', 'Item removed.');
         } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Cart Remove Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to remove item.');
         }
    }
}
