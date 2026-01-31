<?php

namespace App\Services;

use Razorpay\Api\Api;

class RazorpayService
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(
            env('RAZORPAY_KEY'),
            env('RAZORPAY_SECRET')
        );
    }

    // Create Order
    public function createOrder($amount)
    {
        return $this->api->order->create([
            'receipt' => 'rcpt_' . time(),
            'amount' => $amount * 100, // rupees → paise
            'currency' => 'INR'
        ]);
    }

    // Verify Payment Signature
    public function verifyPayment($data)
    {
        $this->api->utility->verifyPaymentSignature($data);
    }
}
