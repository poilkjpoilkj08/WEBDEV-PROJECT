@component('mail::message')
# Order Receipt

Hello {{ $user->name }},

Thank you for your purchase! Here's your order receipt.

## Order Details
**Invoice Number:** {{ $order->invoice_number }}
**Order Date:** {{ $order->created_at->format('M d, Y H:i A') }}
**Status:** {{ ucfirst($order->status) }}

## Items Ordered

@component('mail::table')
| Book | Quantity | Unit Price | Subtotal |
|------|----------|-----------|----------|
@foreach($orderDetails as $detail)
| {{ $detail->book->title }} | {{ $detail->quantity }} | RM {{ number_format($detail->book->price / 100, 2) }} | RM {{ number_format($detail->subtotal / 100, 2) }} |
@endforeach
@endcomponent

## Order Summary

| | |
|------|---------|
| **Subtotal** | RM {{ number_format($order->total_price / 100, 2) }} |
| **Shipping Cost** | RM {{ number_format($order->shipping_cost / 100, 2) }} |
| **Total Amount** | **RM {{ number_format(($order->total_price + $order->shipping_cost) / 100, 2) }}** |

## Shipping Information

**Delivery Address:**
{{ $order->shipping_address }}

**Shipping Status:** {{ ucfirst($order->shipping_status) }}

---

Thank you for shopping with us!

@component('mail::button', ['url' => route('orders.show', $order)])
View Order Details
@endcomponent

@endcomponent
