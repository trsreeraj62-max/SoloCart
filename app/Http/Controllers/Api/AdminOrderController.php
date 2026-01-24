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
     * Admin: List all orders
     */
    public function index(Request $request)
    {
        try {
            $query = Order::with(['user', 'items.product'])->latest();
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('search')) {
                $query->where('id', $request->search)
                      ->orWhereHas('user', function($q) use ($request) {
                          $q->where('name', 'like', '%'.$request->search.'%')
                            ->orWhere('email', 'like', '%'.$request->search.'%');
                      });
            }

            $orders = $query->paginate(20);
            return $this->success($orders, "Admin orders retrieved");
        } catch (\Exception $e) {
            Log::error("Admin Orders Error: " . $e->getMessage());
            return $this->error("Failed to retrieve orders", 500);
        }
    }

    /**
     * Admin: Update status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:' . implode(',', [
                    Order::STATUS_PENDING, 
                    Order::STATUS_APPROVED, 
                    Order::STATUS_PROCESSING, 
                    Order::STATUS_SHIPPED, 
                    Order::STATUS_DELIVERED, 
                    Order::STATUS_CANCELLED
                ])
            ]);

            $order = Order::find($id);
            if (!$order) {
                return $this->error("Order not found", 404);
            }

            $this->orderService->updateStatus($order, $request->status);

            return $this->success($order, "Order status updated to " . $request->status);
        } catch (\Exception $e) {
             // Catch strict transition errors from Service
            return $this->error($e->getMessage(), 422);
        }
    }
}
