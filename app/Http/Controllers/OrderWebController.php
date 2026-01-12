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
        $orders = Auth::user()->orders()->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Auth::user()->orders()->with('items.product')->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    public function checkout(Request $request)
    {
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
        $order = Auth::user()->orders()->findOrFail($id);
        if($order->payment_status === 'paid') return redirect()->route('orders.show', $order->id);
        
        return view('checkout.payment', compact('order'));
    }

    public function confirmPayment(Request $request, $id) {
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
    }
}
