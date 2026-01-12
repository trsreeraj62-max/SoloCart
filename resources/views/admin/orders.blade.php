@extends('layouts.admin')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="font-bold text-2xl">Orders</h1>
</div>
<div class="card p-0" style="overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead style="background: #f8fafc; border-bottom: 1px solid var(--border);">
            <tr>
                <th class="p-4 text-left text-sm font-bold text-muted uppercase">Order ID</th>
                <th class="p-4 text-left text-sm font-bold text-muted uppercase">User</th>
                <th class="p-4 text-left text-sm font-bold text-muted uppercase">Status</th>
                <th class="p-4 text-right text-sm font-bold text-muted uppercase">Total</th>
                <th class="p-4 text-right text-sm font-bold text-muted uppercase">Date</th>
                <th class="p-4 text-right text-sm font-bold text-muted uppercase">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr style="border-bottom: 1px solid var(--border);">
                <td class="p-4 text-muted">#{{ $order->id }}</td>
                <td class="p-4">
                    <div class="font-bold">{{ $order->user->name }}</div>
                    <div class="text-xs text-muted">{{ $order->user->email }}</div>
                </td>
                <td class="p-4">
                    <span class="inline-block px-2 py-1 rounded text-xs font-bold 
                        {{ $order->status == 'delivered' ? 'bg-green-100 text-green-700' : 
                           ($order->status == 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                    </span>
                </td>
                <td class="p-4 text-right font-bold">${{ number_format($order->total, 2) }}</td>
                <td class="p-4 text-right text-sm text-muted">{{ $order->created_at->format('M d, Y') }}</td>
                <td class="p-4 text-right">
                    <button class="text-primary font-bold hover:underline" onclick="alert('Implement Order Details/Status Update')">Manage</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection
