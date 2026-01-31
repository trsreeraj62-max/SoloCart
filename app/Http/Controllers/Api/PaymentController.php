<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RazorpayService;

class PaymentController extends Controller
{
    protected $razorpay;

    public function __construct(RazorpayService $razorpay)
    {
        $this->razorpay = $razorpay;
    }

    // 1️⃣ Create Order
    public function createOrder(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $order = $this->razorpay->createOrder($request->amount);

        return response()->json([
            'order_id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => 'INR',
            'key' => env('RAZORPAY_KEY')
        ]);
    }

    // 2️⃣ Verify Payment
    public function verifyPayment(Request $request)
    {
        $data = [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature
        ];

        try {
            $this->razorpay->verifyPayment($data);

            // ✅ Payment successful
            return response()->json([
                'status' => 'success',
                'message' => 'Payment verified successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Payment verification failed'
            ], 400);
        }
    }
}
