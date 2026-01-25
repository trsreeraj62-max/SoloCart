<x-mail::message>
# Order Status Update

Hello {{ $order->user->name }},

The status of your order **#{{ $order->id }}** has been updated to: **{{ ucwords(str_replace('_', ' ', $status)) }}**.

**Items in this order:**
<x-mail::table>
| Product | Qty | Price |
| :--- | :---: | :--- |
@foreach ($order->items as $item)
| {{ $item->product->name }} | {{ $item->quantity }} | ${{ number_format($item->price, 2) }} |
@endforeach
</x-mail::table>

<x-mail::panel>
Current Status: **{{ ucwords(str_replace('_', ' ', $status)) }}**
</x-mail::panel>

<x-mail::button :url="config('app.frontend_url') . '/orders.html'">
View Order Details
</x-mail::button>

If you have any questions relative to your order, please contact our support team.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
