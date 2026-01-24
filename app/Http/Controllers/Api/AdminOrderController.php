<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminOrderController extends ApiController
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Admin: List all orders with complete product details
     */
    public function index(Request $request)
    {
        try {
            // Load orders with complete relationships
            $query = Order::with([
                'user:id,name,email,phone', // Buyer details
                'items.product.images', // Product with images
                'items.product' // Eager load product details
            ])->latest();
            
            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // Search by order ID or user details
            if ($request->filled('search')) {
                $query->where(function($q) use ($request) {
                    $q->where('id', $request->search)
                      ->orWhereHas('user', function($userQuery) use ($request) {
                          $userQuery->where('name', 'like', '%'.$request->search.'%')
                                   ->orWhere('email', 'like', '%'.$request->search.'%');
                      });
                });
            }

            $orders = $query->get();
            
            // Transform data to include calculated fields
            $orders->transform(function ($order) {
                $order->buyer_name = $order->user->name ?? 'N/A';
                $order->buyer_email = $order->user->email ?? 'N/A';
                
                // Add line total for each item
                $order->items->transform(function ($item) {
                    $item->line_total = $item->price * $item->quantity;
                    $item->product_name = $item->product->name ?? 'N/A';
                    $item->product_image = $item->product->image_url ?? null;
                    return $item;
                });
                
                return $order;
            });
            
            Log::info('Admin orders retrieved', [
                'count' => $orders->count()
            ]);
            
            return $this->success($orders, "Admin orders retrieved");
            
        } catch (\Exception $e) {
            Log::error("Admin Orders Error: " . $e->getMessage());
            return $this->error("Failed to retrieve orders: " . $e->getMessage(), 500);
        }
    }

    /**
     * Admin: Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            // Validate status
            $validated = $request->validate([
                'status' => 'required|in:' . implode(',', [
                    Order::STATUS_PENDING, 
                    Order::STATUS_APPROVED, 
                    Order::STATUS_PROCESSING, 
                    Order::STATUS_SHIPPED, 
                    Order::STATUS_DELIVERED, 
                    Order::STATUS_CANCELLED
                ])
            ]);

            // Find order
            $order = Order::with(['user', 'items.product.images'])->find($id);
            
            if (!$order) {
                return $this->error("Order not found", 404);
            }

            // Update status via service (handles stock restoration, notifications, timestamps)
            $this->orderService->updateStatus($order, $validated['status']);
            
            // Reload order with all relationships
            $order->load(['user', 'items.product.images']);
            
            // Add computed fields
            $order->buyer_name = $order->user->name ?? 'N/A';
            $order->buyer_email = $order->user->email ?? 'N/A';
            
            $order->items->transform(function ($item) {
                $item->line_total = $item->price * $item->quantity;
                $item->product_name = $item->product->name ?? 'N/A';
                $item->product_image = $item->product->image_url ?? null;
                return $item;
            });

            Log::info('Order status updated', [
                'order_id' => $order->id,
                'new_status' => $validated['status']
            ]);

            return $this->success($order, "Order status updated to {$validated['status']}");
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error($e->errors(), 422);
        } catch (\Exception $e) {
            // Catch strict transition errors from Service
            Log::error('Order status update error', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);
            return $this->error($e->getMessage(), 422);
        }
    }
}
