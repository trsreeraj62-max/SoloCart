<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
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
     * Get user orders
     */
    public function index()
    {
        $orders = Auth::user()->orders()->with('items.product')->latest()->paginate(10);
        return $this->success($orders, "Orders retrieved successfully");
    }

    /**
     * Get order details
     */
    public function show($id)
    {
        $order = Auth::user()->orders()->with('items.product')->find($id);
        
        if (!$order) {
            return $this->error("Order not found", 404);
        }

        return $this->success($order, "Order details retrieved");
    }

    /**
     * Cancel order
     */
    public function cancel($id)
    {
        $order = Auth::user()->orders()->find($id);

        if (!$order) {
            return $this->error("Order not found", 404);
        }

        if (!in_array($order->status, ['pending', 'approved', 'packed'])) {
            return $this->error("Order cannot be cancelled at stage: " . $order->status);
        }

        $this->orderService->updateStatus($order, 'cancelled');

        return $this->success([], "Order cancelled successfully");
    }

    /**
     * Admin: Update status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,packed,shipped,out_for_delivery,delivered,cancelled,returned'
        ]);

        $order = Order::find($id);
        if (!$order) {
            return $this->error("Order not found", 404);
        }

        $this->orderService->updateStatus($order, $request->status);

        return $this->success($order, "Order status updated to " . $request->status);
    }
}
