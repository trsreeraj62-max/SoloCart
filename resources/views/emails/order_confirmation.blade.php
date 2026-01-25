<x-mail::message>
# Order Confirmation

Thank you for your order, {{ $order->user->name }}!

Your order **#{{ $order->id }}** has been successfully placed. We are currently processing it for shipment.

**Order Details:**
<x-mail::table>
| Product | Quantity | Price |
| :--- | :---: | :--- |
@foreach ($order->items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | ${{ number_format($item->price * $item->quantity, 2) }} |
@endforeach
| **Total** | | **${{ number_format($order->total, 2) }}** |
</x-mail::table>

**Delivery Address:**
{{ $order->address }}

<x-mail::button :url="config('app.frontend_url') . '/orders.html'">
View Your Order
</x-mail::button>

If you have any questions, feel free to contact our support.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
