<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Product; // Added Product model
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends ApiController
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Place a new order
     */
    public function store(Request $request)
    {
        $user = $request->user(); 
        
        \Illuminate\Support\Facades\Log::info('Order Creation Started', [
            'request_user_id' => $user ? $user->id : 'null', 
            'request_data' => $request->all()
        ]);

        if (!$user) {
            return $this->error('Unauthorized', 401);
        }

        $request->validate([
            'address' => 'required|string',
            'payment_method' => 'required|in:cod,upi,card,netbanking',
            'source' => 'required|in:cart,direct',
            'items' => 'required_if:source,direct|array',
            'items.*.product_id' => 'exists:products,id',
            'items.*.quantity' => 'integer|min:1',
        ]);

        try {
            $itemsData = [];
            $subtotal = 0;
            $clearCart = false;

            if ($request->source === 'cart') {
                $cart = $user->cart;
                if (!$cart || $cart->items->count() === 0) {
                    return $this->error("Cart is empty", 400);
                }
                foreach ($cart->items as $item) {
                     // Check stock
                    if ($item->product->stock < $item->quantity) {
                         return $this->error("Product {$item->product->name} is out of stock (Available: {$item->product->stock})", 400);
                    }

                    $price = $item->product->price - ($item->product->price * ($item->product->discount_percent / 100));
                    $itemsData[] = [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $price
                    ];
                    $subtotal += $price * $item->quantity;
                    
                    \Illuminate\Support\Facades\Log::info('Cart Item Being Ordered', [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'quantity' => $item->quantity,
                        'price' => $price
                    ]);
                }
                $clearCart = true;
            } else {
                // Direct buy
                foreach ($request->items as $itemRequest) {
                    $product = Product::find($itemRequest['product_id']);
                    if (!$product) {
                        return $this->error("Product not found", 404);
                    }
                     // Check stock
                    if ($product->stock < $itemRequest['quantity']) {
                         return $this->error("Product {$product->name} is out of stock (Available: {$product->stock})", 400);
                    }
                    
                    $price = $product->price - ($product->price * ($product->discount_percent / 100));
                    $itemsData[] = [
                        'product_id' => $product->id,
                        'quantity' => $itemRequest['quantity'],
                        'price' => $price
                    ];
                    $subtotal += $price * $itemRequest['quantity'];
                    
                    \Illuminate\Support\Facades\Log::info('Direct Purchase Item', [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $itemRequest['quantity'],
                        'price' => $price
                    ]);
                }
            }

            \Illuminate\Support\Facades\Log::info('Passing to OrderService', [
                'user_id' => $user->id, 
                'items_count' => count($itemsData)
            ]);

            // Pass user explicitly
            $order = $this->orderService->createOrder($user, [
                'subtotal' => $subtotal,
                'address' => $request->address,
                'customer_name' => $request->full_name ?? $request->name,
                'customer_email' => $request->email,
                'customer_phone' => $request->phone,
                'payment_method' => $request->payment_method,
                'clear_cart' => $clearCart
            ], $itemsData);

            \Illuminate\Support\Facades\Log::info('Order Created Result', [
                'order_id' => $order->id,
                'order_user_id' => $order->user_id,
                'current_user_id' => $user->id
            ]);

            // Reload order with relationships
            $order->load(['items.product', 'user']);

            return $this->success($order, "Order placed successfully", 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error($e->errors(), 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Place Order Error: ' . $e->getMessage());
            return $this->error("Failed to place order: " . $e->getMessage(), 500);
        }
    }

    /**
     * Get user orders
     */
    public function index(Request $request)
    {
        try {
            $userId = $request->user()->id;
            $userRole = $request->user()->role;
            
            // Debugging log
            \Illuminate\Support\Facades\Log::info('Order Debug', [
                'auth_id' => $userId, 
                'role' => $userRole,
                'total_orders_in_db' => \App\Models\Order::count(),
                'user_orders_count' => \App\Models\Order::where('user_id', $userId)->count()
            ]);

            // Explicitly use request user ID to filter orders
            $orders = Order::with('items.product')
                ->where('user_id', $userId)
                ->latest()
                ->get();
            
            $msg = "Orders retrieved. Found: " . $orders->count() . " for User ID: " . $userId;
            
            return $this->success($orders, $msg);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Get Orders Error: ' . $e->getMessage());
            return $this->error("Failed to retrieve orders", 500);
        }
    }

    /**
     * Get order details
     */
    public function show(Request $request, $id)
    {
        $order = Order::with(['items.product', 'user'])
            ->where('user_id', $request->user()->id)
            ->find($id);
        
        if (!$order) {
            return $this->error("Order not found", 404);
        }

        return $this->success($order, "Order details retrieved");
    }

    /**
     * Cancel order
     */
    /**
     * Cancel order
     */
    public function cancel($id)
    {
        $order = Auth::user()->orders()->find($id);

        if (!$order) {
            return $this->error("Order not found", 404);
        }

        // User permission check: Only Pending or Approved
        if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_APPROVED])) {
            return $this->error("Order cannot be cancelled at this stage. Please contact support.");
        }

        try {
            $this->orderService->updateStatus($order, Order::STATUS_CANCELLED);
            return $this->success([], "Order cancelled successfully");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    /**
     * Return order
     */
    public function returnOrder(Request $request, $id)
    {
        $order = Auth::user()->orders()->find($id);
        if (!$order) return $this->error("Order not found", 404);
        
        if ($order->status != Order::STATUS_DELIVERED) {
            return $this->error("Only delivered orders can be returned");
        }

        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            \Illuminate\Support\Facades\Log::info("Order Return Request: #{$id}", [
                'user_id' => Auth::id()
            ]);
            \Illuminate\Support\Facades\Log::info("Order Return Request: {$order->id} - Reason: {$request->reason}");

            $order->update(['return_reason' => $request->reason]);
            
            $this->orderService->updateStatus($order, Order::STATUS_RETURNED);
            return $this->success([], "Return request processed successfully. Status updated to Returned.");
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    /**
     * Download invoice
     */
    public function downloadInvoice($id)
    {
        $order = Auth::user()->orders()->with(['items.product', 'user'])->find($id);
        if (!$order) return $this->error("Order not found", 404);
        
        if ($order->status != Order::STATUS_DELIVERED) {
            return $this->error("Invoice available for delivered orders only");
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('order'));
        return $pdf->download('invoice_'.$order->id.'.pdf');
    }
}
