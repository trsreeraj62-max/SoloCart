<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderStatusMail;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Calculate fees for the order
     */
    public function calculateFees($subtotal)
    {
        $shippingFee = $subtotal > 500 ? 0 : 60;
        $platformFee = 10;
        $grandTotal = $subtotal + $shippingFee + $platformFee;

        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'platform_fee' => $platformFee,
            'grand_total' => $grandTotal
        ];
    }

    /**
     * Create an order from cart or single product
     */
    public function createOrder($user, array $data, $items = [])
    {
        return DB::transaction(function () use ($user, $data, $items) {
            $fees = $this->calculateFees($data['subtotal']);
            
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total' => $fees['grand_total'],
                'address' => $data['address'],
                'payment_method' => $data['payment_method'] ?? 'pending',
                'payment_status' => 'unpaid',
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Decrement stock
                Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
            }

            // If created from cart, clear it
            if (isset($data['clear_cart']) && $data['clear_cart']) {
                $user->cart?->items()->delete();
            }

            if ($order->payment_method === 'cod') {
                $order->update(['status' => 'approved']);
                $this->sendNotification($order, 'confirmation');
            }

            return $order;
        });
    }

    /**
     * Update order status and trigger notifications
     */
    public function updateStatus(Order $order, $status)
    {
        $oldStatus = $order->status;
        $order->update(['status' => $status]);

        $this->sendNotification($order, 'status_update');

        // Restore stock if cancelled or returned
        if (in_array($status, ['cancelled', 'returned']) && !in_array($oldStatus, ['cancelled', 'returned'])) {
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        if ($status === 'delivered' && $oldStatus !== 'delivered') {
            $this->sendNotification($order, 'invoice');
        }

        return $order;
    }

    /**
     * Centralized notification sender
     */
    protected function sendNotification(Order $order, $type)
    {
        try {
            switch ($type) {
                case 'confirmation':
                    Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
                    break;
                case 'status_update':
                    Mail::to($order->user->email)->send(new OrderStatusMail($order, $order->status));
                    break;
                case 'invoice':
                    Mail::to($order->user->email)->send(new InvoiceMail($order));
                    break;
            }
        } catch (\Exception $e) {
            Log::error("Mail Error ($type) for Order #{$order->id}: " . $e->getMessage());
        }
    }
}
