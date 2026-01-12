<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class OrderController extends Controller
{
    // User Methods
    public function index()
    {
        try {
            $orders = Auth::user()->orders()->with('items.product')->latest()->paginate(10);
            return response()->json($orders);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch orders', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $order = Auth::user()->orders()->with('items.product')->findOrFail($id);
            return response()->json($order);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Order not found', 'message' => $e->getMessage()], 404);
        }
    }

    public function cancel($id)
    {
        try {
            $order = Auth::user()->orders()->findOrFail($id);
            
            if (in_array($order->status, ['pending', 'approved', 'packed'])) {
                $order->update(['status' => 'cancelled']);
                foreach($order->items as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
                return response()->json(['message' => 'Order cancelled successfully']);
            }
            
            return response()->json(['message' => 'Order cannot be cancelled at this stage'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to cancel order', 'message' => $e->getMessage()], 500);
        }
    }

    public function returnOrder($id)
    {
        try {
            $order = Auth::user()->orders()->findOrFail($id);
            
            if ($order->status == 'delivered') {
                $order->update(['status' => 'returned']);
                return response()->json(['message' => 'Return request initiated']);
            }
            
            return response()->json(['message' => 'Order cannot be returned'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to return order', 'message' => $e->getMessage()], 500);
        }
    }

    public function downloadInvoice($id)
    {
        try {
            $order = Auth::user()->orders()->with('items.product')->findOrFail($id);
            
            if ($order->status != 'delivered') {
                return response()->json(['message' => 'Invoice only available for delivered orders'], 400);
            }

            // For API, we might return the content or a URL. 
            // Returning content text for simplicity as per web controller.
            $content = "SOLOCART INVOICE\n";
            $content .= "Order ID: " . $order->id . "\n";
            $content .= "Date: " . $order->created_at . "\n";
            $content .= "Address: " . $order->address . "\n\n";
            $content .= "Items:\n";
            foreach($order->items as $item) {
                $content .= $item->product->name . " x " . $item->quantity . " - $" . ($item->price * $item->quantity) . "\n";
            }
            $content .= "\nTOTAL: $" . $order->total . "\n";

            return response()->json(['invoice_content' => $content]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate invoice', 'message' => $e->getMessage()], 500);
        }
    }

    // Admin Methods
    public function adminIndex()
    {
        try {
            $orders = Order::with('user', 'items')->latest()->paginate(20);
            return response()->json($orders);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch orders', 'message' => $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate(['status' => 'required|in:pending,approved,packed,shipped,out_for_delivery,delivered,cancelled,returned']);
            
            $order = Order::with('user', 'items.product')->findOrFail($id);
            $oldStatus = $order->status;
            $newStatus = $request->status;
            
            $order->update(['status' => $newStatus]);
            
            // If Delivered, Send Invoice Email
            if ($newStatus === 'delivered' && $oldStatus !== 'delivered') {
                Mail::to($order->user->email)->send(new InvoiceMail($order));
            }
            
            return response()->json(['message' => 'Order status updated', 'status' => $newStatus]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update status', 'message' => $e->getMessage()], 500);
        }
    }
}
