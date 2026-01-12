<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
</head>
<body style="font-family: sans-serif; background-color: #f3f4f6; padding: 20px;">
    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 40px;">
            <h1 style="color: #6366f1;">SoloCart.</h1>
            <div style="text-align: right;">
                <h3 style="margin: 0;">INVOICE</h3>
                <p style="color: #64748b;">#{{ $order->id }}</p>
                <p style="color: #64748b;">{{ $order->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <div style="margin-bottom: 40px;">
            <p><strong>Billed To:</strong></p>
            <p>{{ $order->user->name }}</p>
            <p>{{ $order->address }}</p>
            <p>{{ $order->user->email }}</p>
        </div>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 40px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px; text-align: left;">Item</th>
                    <th style="padding: 12px; text-align: center;">Quantity</th>
                    <th style="padding: 12px; text-align: right;">Price</th>
                    <th style="padding: 12px; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 12px;">{{ $item->product->name }}</td>
                    <td style="padding: 12px; text-align: center;">{{ $item->quantity }}</td>
                    <td style="padding: 12px; text-align: right;">${{ number_format($item->price, 2) }}</td>
                    <td style="padding: 12px; text-align: right;">${{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="text-align: right;">
            <p style="font-size: 1.25rem; font-weight: bold;">Total: ${{ number_format($order->total, 2) }}</p>
        </div>
        
        <div style="margin-top: 40px; text-align: center; color: #64748b; font-size: 0.875rem;">
            <p>Thank you for shopping with SoloCart!</p>
        </div>
    </div>
</body>
</html>
