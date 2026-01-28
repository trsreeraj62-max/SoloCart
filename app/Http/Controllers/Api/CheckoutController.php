<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\CartItem;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckoutController extends ApiController
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Preview checkout (calculate totals)
     */
    public function preview(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $subtotal = 0;
        $itemsResponse = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $price = $product->price - ($product->price * ($product->discount_percent / 100));
            $lineTotal = $price * $item['quantity'];
            
            $subtotal += $lineTotal;
            
            $itemsResponse[] = [
                'product' => $product, // Includes name, images, etc.
                'quantity' => $item['quantity'],
                'price' => $price,
                'total' => $lineTotal
            ];
        }

        $fees = $this->orderService->calculateFees($subtotal);

        return $this->success([
            'items' => $itemsResponse,
            'summary' => $fees
        ], "Checkout preview calculated");
    }

    /**
     * Checkout from a single product (Buy Now)
     */
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
            $user = Auth::user();
            Log::info('Checkout: Single Product', [
                'user_id' => $user->id,
                'email' => $user->email,
                'product_id' => $request->product_id,
                'qty' => $request->quantity
            ]);

            $product = Product::findOrFail($request->product_id);
            $price = $product->price - ($product->price * ($product->discount_percent / 100));
            
            $itemsData = [[
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $price
            ]];

            $order = $this->orderService->createOrder($user, [
                'subtotal' => $price * $request->quantity,
                'address' => $request->address,
                'payment_method' => $request->payment_method
            ], $itemsData);

            Log::info('Checkout: Order Created', ['order_id' => $order->id]);

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
            $cart = $user->cart()->with('items.product')->first();

            \Illuminate\Support\Facades\Log::info('CheckoutController: Cart Checkout Started', [
                'user_id' => $user->id,
                'email' => $user->email,
                'cart_exists' => !!$cart,
                'item_count' => $cart ? $cart->items->count() : 0
            ]);
            
            if (!$cart || $cart->items->count() === 0) {
                return $this->error("Your cart is empty");
            }

            $itemsData = [];
            $subtotal = 0;

            foreach ($cart->items as $item) {
                if (!$item->product) {
                    \Illuminate\Support\Facades\Log::warning("CheckoutController: Item missing product", ['item_id' => $item->id]);
                    continue;
                }

                $price = (float) $item->product->current_price;
                $itemsData[] = [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $price
                ];
                $subtotal += $price * $item->quantity;
                
                \Illuminate\Support\Facades\Log::info('CheckoutController: Processing Cart Item', [
                    'product_id' => $item->product_id,
                    'name' => $item->product->name,
                    'qty' => $item->quantity,
                    'price' => $price
                ]);
            }

            if (empty($itemsData)) {
                return $this->error("Your cart items are invalid or unavailable.");
            }

            $order = $this->orderService->createOrder($user, [
                'subtotal' => $subtotal,
                'address' => $request->address,
                'payment_method' => $request->payment_method,
                'clear_cart' => true
            ], $itemsData);

            \Illuminate\Support\Facades\Log::info('CheckoutController: Cart Order Created', [
                'order_id' => $order->id,
                'user_id' => $order->user_id
            ]);

            return $this->success($order, "Order placed successfully from cart");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Cart Checkout Error: ' . $e->getMessage());
            return $this->error("Failed to place order from cart: " . $e->getMessage());
        }
    }
}
