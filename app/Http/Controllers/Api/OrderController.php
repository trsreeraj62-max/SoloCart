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
     * Return order
     */
    public function returnOrder($id)
    {
        $order = Auth::user()->orders()->find($id);
        if (!$order) return $this->error("Order not found", 404);
        if ($order->status != 'delivered') return $this->error("Only delivered orders can be returned");

        $this->orderService->updateStatus($order, 'returned');
        return $this->success([], "Return initiated successfully");
    }

    /**
     * Download invoice
     */
    public function downloadInvoice($id)
    {
        $order = Auth::user()->orders()->with(['items.product', 'user'])->find($id);
        if (!$order) return $this->error("Order not found", 404);
        if ($order->status != 'delivered') return $this->error("Invoice available for delivered orders only");

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('order'));
        return $pdf->download('invoice_'.$order->id.'.pdf');
    }

    /**
     * Admin: List all orders
     */
    public function adminIndex(Request $request)
    {
        try {
            $query = Order::with(['user', 'items.product'])->latest();
            if ($request->filled('status')) $query->where('status', $request->status);
            
            $orders = $query->paginate(20);
            return $this->success($orders, "Admin orders retrieved");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin Orders Error: ' . $e->getMessage());
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
                'status' => 'required|in:pending,approved,packed,shipped,out_for_delivery,delivered,cancelled,returned'
            ]);

            $order = Order::find($id);
            if (!$order) {
                return $this->error("Order not found", 404);
            }

            $this->orderService->updateStatus($order, $request->status);

            return $this->success($order, "Order status updated to " . $request->status);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Update Order Status Error: ' . $e->getMessage());
            return $this->error("Failed to update order status", 500);
        }
    }
}
