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
            $cartItems = collect();
            $totalPrice = 0;
            
            if (Auth::check()) {
                $cart = Cart::where('user_id', Auth::id())->first();
                if ($cart) {
                    $cartItems = CartItem::where('cart_id', $cart->id)->with('product.images')->get();
                    $totalPrice = $cartItems->sum(function($item) {
                        return $item->quantity * $item->product->price;
                    });
                }
            }
            
            return view('cart.index', [
                'cart' => $cartItems,
                'totalPrice' => $totalPrice
            ]);
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

    public function addByUrl($product_id)
    {
        try {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login to add items to cart.');
            }

            $product = Product::findOrFail($product_id);
            $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
            
            $cartItem = CartItem::where('cart_id', $cart->id)->where('product_id', $product->id)->first();
            if ($cartItem) {
                $cartItem->increment('quantity');
            } else {
                CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 1]);
            }
            return redirect()->back()->with('success', 'Product added to cart!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add item.');
        }
    }

    public function updateQuantity(Request $request, $id)
    {
        try {
            $item = CartItem::findOrFail($id);
            $item->update(['quantity' => $request->quantity]);
            return redirect()->back()->with('success', 'Quantity updated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update.');
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

    public function removeById($id)
    {
        try {
            CartItem::destroy($id);
            return redirect()->back()->with('success', 'Item removed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to remove.');
        }
    }
}
