<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\CartItem;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends ApiController
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Checkout from a single product (Buy Now)
     */
    public function singleProductCheckout(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'address' => 'required',
            'payment_method' => 'required|in:cod,upi,card'
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            $price = $product->price - ($product->price * ($product->discount_percent / 100));
            
            $itemsData = [[
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $price
            ]];

            $order = $this->orderService->createOrder(Auth::user(), [
                'subtotal' => $price * $request->quantity,
                'address' => $request->address,
                'payment_method' => $request->payment_method
            ], $itemsData);

            return $this->success($order, "Order placed successfully via single product checkout");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Single Checkout Error: ' . $e->getMessage());
            return $this->error("Failed to place order: " . $e->getMessage());
        }
    }

    /**
     * Checkout from cart contents
     */
    public function cartCheckout(Request $request)
    {
        $request->validate([
            'address' => 'required',
            'payment_method' => 'required|in:cod,upi,card'
        ]);

        try {
            $user = Auth::user();
            $cart = $user->cart;

            if (!$cart || $cart->items->count() === 0) {
                return $this->error("Your cart is empty");
            }

            $itemsData = [];
            $subtotal = 0;

            foreach ($cart->items as $item) {
                $price = $item->product->price - ($item->product->price * ($item->product->discount_percent / 100));
                $itemsData[] = [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $price
                ];
                $subtotal += $price * $item->quantity;
            }

            $order = $this->orderService->createOrder($user, [
                'subtotal' => $subtotal,
                'address' => $request->address,
                'payment_method' => $request->payment_method,
                'clear_cart' => true
            ], $itemsData);

            return $this->success($order, "Order placed successfully from cart");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Cart Checkout Error: ' . $e->getMessage());
            return $this->error("Failed to place order from cart: " . $e->getMessage());
        }
    }
}
