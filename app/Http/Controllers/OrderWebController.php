<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\CartItem;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderWebController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        try {
            $orders = Auth::user()->orders()->with('items.product')->latest()->get();
            return view('orders.index', compact('orders'));
        } catch (\Exception $e) {
            Log::error('Order Index Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load orders.');
        }
    }

    public function show($id)
    {
        try {
            $order = Auth::user()->orders()->with(['items.product', 'user'])->findOrFail($id);
            return view('orders.show', compact('order'));
        } catch (\Exception $e) {
            Log::error('Order Show Error: ' . $e->getMessage());
            return redirect()->route('orders.index')->with('error', 'Order not found.');
        }
    }

    public function checkout(Request $request)
    {
        try {
            $user = Auth::user();
            $items = [];
            $subtotal = 0;
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
                $subtotal = $price * $qty;
                $isSingle = true;
                $singleProductId = $product->id;
                $singleQuantity = $qty;
            } elseif ($request->has('buy_item')) {
                $cartItem = CartItem::with('product')->findOrFail($request->buy_item);
                $price = $cartItem->product->price - ($cartItem->product->price * ($cartItem->product->discount_percent / 100));
                $items[] = (object) [
                    'product' => $cartItem->product,
                    'quantity' => $cartItem->quantity,
                    'price' => $price
                ];
                $subtotal = $price * $cartItem->quantity;
                $isSingle = true;
                $singleProductId = $cartItem->product_id;
                $singleQuantity = $cartItem->quantity;
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
                    $subtotal += $price * $item->quantity;
                }
            }
            
            $fees = $this->orderService->calculateFees($subtotal);
            
            return view('checkout.index', array_merge($fees, [
                'items' => $items,
                'cart' => (object)$items, // Compatibility with user's foreach($cart as $item)
                'user' => $user,
                'totalPrice' => $fees['grand_total'],
                'isSingle' => $isSingle,
                'singleProductId' => $singleProductId,
                'singleQuantity' => $singleQuantity
            ]));

        } catch (\Exception $e) {
            Log::error('Checkout Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load checkout page.');
        }
    }
    
    public function store(Request $request) 
    {
        $request->validate([
             'address' => 'required',
             'payment_method' => 'required|in:cod,upi,card,pending'
        ]);
        
        try {
            $user = Auth::user();
            $itemsData = [];
            $subtotal = 0;
            $clearCart = false;

            if ($request->has('product_id') && $request->product_id) {
                $product = Product::findOrFail($request->product_id);
                $price = $product->price - ($product->price * ($product->discount_percent / 100));
                $qty = $request->quantity ?? 1;
                $itemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price
                ];
                $subtotal = $price * $qty;
            } else {
                $cart = $user->cart;
                foreach($cart->items as $item) {
                    $price = $item->product->price - ($item->product->price * ($item->product->discount_percent / 100));
                    $itemsData[] = [
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $price
                    ];
                    $subtotal += $price * $item->quantity;
                }
                $clearCart = true;
            }
            
            $order = $this->orderService->createOrder($user, [
                'subtotal' => $subtotal,
                'address' => $request->address,
                'payment_method' => $request->payment_method,
                'clear_cart' => $clearCart
            ], $itemsData);
            
            if ($order->payment_method == 'cod') {
                return redirect()->route('orders.show', $order->id)->with('success', 'Order Placed Successfully!');
            }
            
            return redirect()->route('checkout.pay', $order->id);
            
        } catch (\Exception $e) {
            Log::error('Order Store Error: ' . $e->getMessage());
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    public function paymentPage(Request $request) {
        try {
            $user = Auth::user();
            $cart = $user->cart()->with('items.product')->first();
            if (!$cart || $cart->items->count() == 0) return redirect()->route('cart.index');
            
            $subtotal = 0;
            foreach($cart->items as $item) {
                $subtotal += $item->product->price * $item->quantity;
            }
            $fees = $this->orderService->calculateFees($subtotal);
            
            // Store address info in session from GET params
            session(['checkout_address' => $request->only(['name', 'address', 'phone'])]);

            return view('checkout.payment', [
                'order' => null, // Blade expects $order, but user template doesn't use it much or we can Mock it
                'totalPrice' => $fees['grand_total']
            ]);
        } catch (\Exception $e) {
             Log::error('Payment Page Error: ' . $e->getMessage());
             return redirect()->route('orders.index')->with('error', 'Unable to load payment page.');
        }
    }

    public function paymentPageLegacy($id) {
        try {
            $order = Auth::user()->orders()->findOrFail($id);
            if($order->payment_status === 'paid') return redirect()->route('orders.show', $order->id);
            
            return view('checkout.payment', compact('order'));
        } catch (\Exception $e) {
             Log::error('Payment Page Error: ' . $e->getMessage());
             return redirect()->route('orders.index')->with('error', 'Unable to load payment page.');
        }
    }

    public function confirmSimplifiedOrder(Request $request)
    {
        try {
            $user = Auth::user();
            $addressData = session('checkout_address', []);
            $cart = $user->cart()->with('items.product')->first();
            
            if (!$cart || $cart->items->count() == 0) return redirect()->route('home');

            $itemsData = [];
            $subtotal = 0;
            foreach($cart->items as $item) {
                $itemsData[] = [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price
                ];
                $subtotal += $item->product->price * $item->quantity;
            }

            $order = $this->orderService->createOrder($user, [
                'subtotal' => $subtotal,
                'address' => $addressData['address'] ?? $user->address ?? 'No Address Provided',
                'payment_method' => $request->input('method', 'cod'),
                'clear_cart' => true
            ], $itemsData);

            if ($request->input('method') != 'cod') {
                $order->update(['payment_status' => 'paid']); // Mock payment success
            }

            return view('checkout.success', compact('order'));
        } catch (\Exception $e) {
            Log::error('Simplified Order Error: ' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Order failed.');
        }
    }

    public function confirmPayment(Request $request, $id) {
        try {
            $order = Auth::user()->orders()->with('items.product')->findOrFail($id);
            
            // In a real app, verify transaction with gateway here
            // Verify transaction and update
            $this->orderService->updateStatus($order, 'approved');
            $order->update(['payment_status' => 'paid', 'payment_method' => $request->input('payment_method', 'upi')]);
            
            \App\Models\Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total,
                'method' => $request->input('payment_method', 'upi'),
                'status' => 'success',
                'transaction_id' => 'TXN' . rand(100000, 999999)
            ]);

            // Notification is handled in a more centralized way or here if specialized
            return view('checkout.success', compact('order'));
        } catch (\Exception $e) {
            Log::error('Payment Confirm Error: ' . $e->getMessage());
            return back()->with('error', 'Payment confirmation failed.');
        }
    }

    public function cancel($id)
    {
        try {
            $order = Auth::user()->orders()->findOrFail($id);
            if (in_array($order->status, ['pending', 'approved', 'packed'])) {
                $this->orderService->updateStatus($order, 'cancelled');
                return back()->with('success', 'Order has been cancelled.');
            }
            return back()->with('error', 'Order cannot be cancelled at this stage.');
        } catch (\Exception $e) {
            Log::error('Order Cancel Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel order.');
        }
    }

    public function returnOrder($id)
    {
        try {
            $order = Auth::user()->orders()->findOrFail($id);
            if ($order->status == 'delivered') {
                $this->orderService->updateStatus($order, 'returned');
                return back()->with('success', 'Return request initiated.');
            }
            return back()->with('error', 'Order cannot be returned.');
        } catch (\Exception $e) {
            Log::error('Order Return Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit return request.');
        }
    }

    public function downloadInvoice($id)
    {
        try {
            $order = Auth::user()->orders()->with(['items.product', 'user'])->findOrFail($id);
            
            if ($order->status != 'delivered') {
                 return back()->with('error', 'Invoice allowed only for delivered orders.');
            }
            
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('order'));
            
            return $pdf->download('invoice_'.$order->id.'.pdf');
            
        } catch (\Exception $e) {
            Log::error('Invoice Download Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate invoice.');
        }
    }
}
