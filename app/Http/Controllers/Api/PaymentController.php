<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RazorpayService;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $razorpay;

    public function __construct(RazorpayService $razorpay)
    {
        $this->razorpay = $razorpay;
    }

    // 1️⃣ Create Order (Internal Razorpay Order)
    public function createOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        $order = Order::findOrFail($request->order_id);
        $user = Auth::user();

        // Create Razorpay Order
        $razorpayOrder = $this->razorpay->createOrder($order->total);

        return response()->json([
            'order_id' => $razorpayOrder['id'],
            'amount' => $razorpayOrder['amount'],
            'currency' => 'INR',
            'key' => env('RAZORPAY_KEY'),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone
            ]
        ]);
    }

    // 2️⃣ Verify Payment & Update Local Order
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'local_order_id' => 'required|exists:orders,id',
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required'
        ]);

        $data = [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ];

        try {
            $this->razorpay->verifyPayment($data);

            // ✅ Payment successful - Update local order
            $order = Order::findOrFail($request->local_order_id);
            $order->update([
                'payment_status' => 'paid',
                'status' => 'approved' // Set to approved so admin can process
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Payment verified successfully',
                'order' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 400);
        }
    }
}
