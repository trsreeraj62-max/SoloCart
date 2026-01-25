<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\User;

class DebugOrders extends Command
{
    protected $signature = 'debug:orders {user_id?}';
    protected $description = 'Debug orders in database';

    public function handle()
    {
        $userId = $this->argument('user_id');
        
        $this->info('=== ORDER DEBUG INFO ===');
        $this->info('Total Orders in DB: ' . Order::count());
        $this->info('Total Users in DB: ' . User::count());
        $this->info('');
        
        if ($userId) {
            $this->info("Orders for User ID: $userId");
            $user = User::find($userId);
            if ($user) {
                $this->info("User: {$user->email}");
                $orders = Order::where('user_id', $userId)->with('items.product')->latest()->get();
                $this->info("Found {$orders->count()} orders");
                
                foreach ($orders as $order) {
                    $this->line("  Order #{$order->id} - Total: ₹{$order->total} - Status: {$order->status} - Items: {$order->items->count()} - Created: {$order->created_at}");
                    foreach ($order->items as $item) {
                        $this->line("    - {$item->product->name} x{$item->quantity}");
                    }
                }
            } else {
                $this->error("User not found!");
            }
        } else {
            $this->info("Recent Orders (All Users):");
            $orders = Order::with(['user', 'items.product'])->latest()->take(10)->get();
            
            foreach ($orders as $order) {
                $userEmail = $order->user ? $order->user->email : 'NO USER';
                $this->line("Order #{$order->id} - User: {$userEmail} (ID: {$order->user_id}) - Total: ₹{$order->total} - Status: {$order->status} - Created: {$order->created_at}");
                foreach ($order->items as $item) {
                    $this->line("  - {$item->product->name} x{$item->quantity}");
                }
            }
            
            $this->info('');
            $this->info('Users with orders:');
            $usersWithOrders = User::has('orders')->withCount('orders')->get();
            foreach ($usersWithOrders as $user) {
                $this->line("  {$user->email} - {$user->orders_count} orders");
            }
        }
    }
}
