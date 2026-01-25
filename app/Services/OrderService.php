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
        Log::info('OrderService: Starting order creation', [
            'user_id' => $user->id,
            'data' => $data,
            'items_count' => count($items)
        ]);

        // DB::transaction removed for debugging persistence
        
        $fees = $this->calculateFees($data['subtotal']);
        
        Log::info('OrderService: Fees calculated', $fees);
        Log::info('OrderService: Creating order for user', ['user_id' => $user->id, 'email' => $user->email]);
        
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total' => $fees['grand_total'],
            'address' => $data['address'],
            'payment_method' => $data['payment_method'] ?? 'pending',
            'payment_status' => 'unpaid',
        ]);

        Log::info('OrderService: Order created', [
            'order_id' => $order->id, 
            'user_id_in_db' => $order->user_id
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
            
            Log::info('OrderService: Item added', [
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity']
            ]);
        }

        // If created from cart, clear it
        if (isset($data['clear_cart']) && $data['clear_cart']) {
            $user->cart?->items()->delete();
            Log::info('OrderService: Cart cleared', ['user_id' => $user->id]);
        }

        if ($order->payment_method === 'cod') {
            $order->update(['status' => 'approved']);
            $this->sendNotification($order, 'confirmation');
            Log::info('OrderService: COD order auto-approved', ['order_id' => $order->id]);
        }

        Log::info('OrderService: Order creation completed', ['order_id' => $order->id]);
        
        return $order;

    }

    /**
     * Update order status and trigger notifications
     */
    public function updateStatus(Order $order, $status)
    {
        $oldStatus = $order->status;

        // Prevent update if already final
        if (in_array($oldStatus, [Order::STATUS_DELIVERED, Order::STATUS_CANCELLED])) {
             throw new \Exception("Order is already {$oldStatus} and cannot be changed.");
        }

        // Strict transition rules
        if ($status !== Order::STATUS_CANCELLED) {
            $allowed = match($oldStatus) {
                Order::STATUS_PENDING => [Order::STATUS_APPROVED],
                Order::STATUS_APPROVED => [Order::STATUS_PROCESSING, Order::STATUS_SHIPPED], // Allow skipping processing
                Order::STATUS_PROCESSING => [Order::STATUS_SHIPPED],
                Order::STATUS_SHIPPED => [Order::STATUS_DELIVERED],
                Order::STATUS_DELIVERED => [Order::STATUS_RETURNED],
                default => []
            };

            if (!in_array($status, $allowed)) {
                throw new \Exception("Invalid status transition from {$oldStatus} to {$status}");
            }
        }

        $updateData = ['status' => $status];
        
        if ($status === Order::STATUS_CANCELLED) {
            $updateData['cancelled_at'] = now();
        }
        if ($status === Order::STATUS_DELIVERED) {
            $updateData['delivered_at'] = now();
        }

        $order->update($updateData);

        // Notifications
        $this->sendNotification($order, 'status_update');

        // Restore stock if cancelled (and wasn't already)
        if ($status === Order::STATUS_CANCELLED) {
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        // Invoice is sent with status_update when Delivered (handled in sendNotification or we can verify logic)
        // Original logic checked if Delivered. sendNotification 'status_update' uses OrderStatusMail.
        // We need to ensure OrderStatusMail attaches invoice if status is delivered, OR we explicitly send InvoiceMail.
        // Requirement 5: "Attach invoice PDF in 'Order Delivered' email".
        // So OrderStatusMail should probably handle it or we trigger a special mail.
        // existing logic: 
        // if ($status === 'delivered') $this->sendNotification($order, 'invoice');
        // Let's optimize. sendNotification handles 'status_update'. 
        // If delivered, we might want a specific 'Order Delivered' email which includes invoice.
        
        return $order;
    }

    /**
     * Centralized notification sender (UPGRADED for Brevo API on Render)
     */
    protected function sendNotification(Order $order, $type)
    {
        try {
            $user = $order->user;
            if (!$user) return;

            switch ($type) {
                case 'confirmation':
                    BrevoMailService::sendOrderConfirmation($user, $order);
                    break;

                case 'status_update':
                    if ($order->status === Order::STATUS_DELIVERED) {
                        try {
                            // Generate PDF for delivery email
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['order' => $order]);
                            $pdfContent = base64_encode($pdf->output());
                            BrevoMailService::sendOrderDelivered($user, $order, $pdfContent);
                        } catch (\Exception $pdfErr) {
                            Log::error("Invoice Generation Failed for Order #{$order->id}: " . $pdfErr->getMessage());
                            // Fallback to simple status update if PDF fails
                            BrevoMailService::sendOrderStatusUpdate($user, $order);
                        }
                    } else {
                        BrevoMailService::sendOrderStatusUpdate($user, $order);
                    }
                    break;

                case 'invoice':
                    // This case might be redundant now if 'delivered' status handles it, 
                    // but we keep it for manual triggers if needed.
                    try {
                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', ['order' => $order]);
                        $attachment = [
                            'content' => base64_encode($pdf->output()),
                            'name' => "invoice_{$order->order_number}.pdf"
                        ];
                        BrevoMailService::sendMail(
                            $user->email, 
                            "Invoice for Order #{$order->order_number}", 
                            "Please find attached your invoice.", 
                            $user->name, 
                            $attachment
                        );
                    } catch (\Exception $e) {
                        Log::error("Manual Invoice Send Failed: " . $e->getMessage());
                    }
                    break;
            }
        } catch (\Exception $e) {
            Log::error("Brevo Mail Error ($type) for Order #{$order->id}: " . $e->getMessage());
        }
    }
}
