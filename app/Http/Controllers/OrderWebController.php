<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderWebController extends Controller
{
    public function index()
    {
        try {
            $orders = Auth::user()->orders()->latest()->get();
            return view('orders.index', compact('orders'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order Index Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load orders.');
        }
    }

    public function show($id)
    {
        try {
            $order = Auth::user()->orders()->with('items.product')->findOrFail($id);
            return view('orders.show', compact('order'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order Show Error: ' . $e->getMessage());
            return redirect()->route('orders.index')->with('error', 'Order not found.');
        }
    }

    public function checkout(Request $request)
    {
        try {
            $user = Auth::user();
            $items = [];
            $total = 0;
            $isSingle = false;
            $singleProductId = null;
            $singleQuantity = 1;
    
            if ($request->has('product_id')) {
                $product = Product::findOrFail($request->product_id);
                $qty = $request->input('quantity', 1);
                $price = $product->price - ($product->price * ($product->discount_percent / 100));
                
                $items[] = (object) [
                    'product' => $product,
                    'quantity' => $qty,
                    'price' => $price
                ];
                $total = $price * $qty;
                $isSingle = true;
                $singleProductId = $product->id;
                $singleQuantity = $qty;
            } else {
                $cart = $user->cart()->with('items.product')->first();
                if (!$cart || $cart->items->count() == 0) {
                    return redirect()->route('cart.index')->with('error', 'Cart is empty');
                }
                foreach ($cart->items as $item) {
                    $price = $item->product->price - ($item->product->price * ($item->product->discount_percent / 100));
                    $items[] = (object) [
                        'product' => $item->product,
                        'quantity' => $item->quantity,
                        'price' => $price
                    ];
                    $total += $price * $item->quantity;
                }
            }
            
            $platformFee = 10;
            $grandTotal = $total + $platformFee;
    
            return view('checkout.index', compact('items', 'total', 'platformFee', 'grandTotal', 'user', 'isSingle', 'singleProductId', 'singleQuantity'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Checkout Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load checkout page.');
        }
    }
    
    public function store(Request $request) {
        $request->validate([
             'address' => 'required',
             'payment_method' => 'required|in:cod,upi,card'
        ]);
        
        DB::beginTransaction();
        try {
            $total = $request->grand_total; // trusting frontend for now, in prod recalculate
            
            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'pending',
                'address' => $request->address,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'total' => $total
            ]);
            
            // Items
            if ($request->has('product_id') && $request->product_id) {
                 $product = Product::findOrFail($request->product_id);
                 $price = $product->price - ($product->price * ($product->discount_percent / 100));
                 OrderItem::create([
                     'order_id' => $order->id,
                     'product_id' => $product->id,
                     'quantity' => $request->quantity ?? 1,
                     'price' => $price
                 ]);
                 // Decrease stock
                 $product->decrement('stock', $request->quantity ?? 1);
            } else {
                $cart = Auth::user()->cart;
                foreach($cart->items as $item) {
                     $price = $item->product->price - ($item->product->price * ($item->product->discount_percent / 100));
                     OrderItem::create([
                         'order_id' => $order->id,
                         'product_id' => $item->product_id,
                         'quantity' => $item->quantity,
                         'price' => $price
                     ]);
                     $item->product->decrement('stock', $item->quantity);
                }
                // Clear cart
                $cart->items()->delete();
            }
            
            DB::commit();
            
            if ($request->payment_method == 'cod') {
                $order->update(['status' => 'approved']); // Auto approve COD for now
                return redirect()->route('orders.show', $order->id)->with('success', 'Order Placed Successfully!');
            }
            
            return redirect()->route('checkout.pay', $order->id);
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    public function paymentPage($id) {
        try {
            $order = Auth::user()->orders()->findOrFail($id);
            if($order->payment_status === 'paid') return redirect()->route('orders.show', $order->id);
            
            return view('checkout.payment', compact('order'));
        } catch (\Exception $e) {
             \Illuminate\Support\Facades\Log::error('Payment Page Error: ' . $e->getMessage());
             return redirect()->route('orders.index')->with('error', 'Unable to load payment page.');
        }
    }

    public function confirmPayment(Request $request, $id) {
        try {
            $order = Auth::user()->orders()->findOrFail($id);
            $order->update(['status' => 'approved', 'payment_status' => 'paid']);
            // Create Payment Record
            \App\Models\Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total,
                'method' => $order->payment_method,
                'status' => 'success',
                'transaction_id' => 'TXN' . rand(100000, 999999)
            ]);
            
            return redirect()->route('orders.show', $id)->with('success', 'Payment Successful! Order Placed.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment Confirm Error: ' . $e->getMessage());
            return back()->with('error', 'Payment confirmation failed.');
        }
    }

    public function cancel($id)
    {
        try {
            $order = Auth::user()->orders()->findOrFail($id);
            // Allow cancel if not yet shipped
            if (in_array($order->status, ['pending', 'approved', 'packed'])) {
                $order->update(['status' => 'cancelled']);
                // Restore stock logic could go here
                foreach($order->items as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
                return back()->with('success', 'Order has been cancelled.');
            }
            return back()->with('error', 'Order cannot be cancelled at this stage. It might be shipped or delivered.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order Cancel Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel order.');
        }
    }

    public function returnOrder($id)
    {
        try {
            $order = Auth::user()->orders()->findOrFail($id);
            if ($order->status == 'delivered') {
                $order->update(['status' => 'returned']);
                 return back()->with('success', 'Return request initiated.');
            }
            return back()->with('error', 'Order cannot be returned.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order Return Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit return request.');
        }
    }

    public function downloadInvoice($id)
    {
        try {
            $order = Auth::user()->orders()->findOrFail($id);
            if ($order->status != 'delivered') {
                 return back()->with('error', 'Invoice allowed only for delivered orders.');
            }
            
            // Generate a simple text-based invoice for now
            $content = "SOLOCART INVOICE\n";
            $content .= "Order ID: " . $order->id . "\n";
            $content .= "Date: " . $order->created_at . "\n";
            $content .= "Address: " . $order->address . "\n\n";
            $content .= "Items:\n";
            foreach($order->items as $item) {
                $content .= $item->product->name . " x " . $item->quantity . " - $" . ($item->price * $item->quantity) . "\n";
            }
            $content .= "\nTOTAL: $" . $order->total . "\n";
            
            return response($content, 200, [
                'Content-Type' => 'text/plain',
                'Content-Disposition' => 'attachment; filename="invoice_'.$order->id.'.txt"',
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Invoice Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate invoice.');
        }
    }
}
