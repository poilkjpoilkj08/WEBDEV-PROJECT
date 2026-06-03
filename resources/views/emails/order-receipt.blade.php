<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { color: #2c3e50; }
        h2 { color: #34495e; font-size: 16px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th, table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        table th { background-color: #f5f5f5; font-weight: bold; }
        .summary-table { margin-top: 20px; }
        .summary-table td { padding: 8px; }
        .summary-table tr.total { font-weight: bold; font-size: 16px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Receipt</h1>
        
        <p>Hello {{ $user->name }},</p>
        <p>Thank you for your purchase! Here's your order receipt.</p>

        <h2>Order Details</h2>
        <p>
            <strong>Invoice Number:</strong> {{ $order->invoice_number }}<br>
            <strong>Order Date:</strong> {{ $order->created_at->format('M d, Y H:i A') }}<br>
            <strong>Status:</strong> {{ ucfirst($order->status) }}
        </p>

        <h2>Items Ordered</h2>
        <table>
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orderDetails as $detail)
                <tr>
                    <td>{{ $detail->book->title }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>RM {{ number_format($detail->book->price / 100, 2) }}</td>
                    <td>RM {{ number_format($detail->subtotal / 100, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <h2>Order Summary</h2>
        <table class="summary-table">
            <tr>
                <td><strong>Subtotal</strong></td>
                <td>RM {{ number_format($order->total_price / 100, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Shipping Cost</strong></td>
                <td>RM {{ number_format($order->shipping_cost / 100, 2) }}</td>
            </tr>
            <tr class="total">
                <td>Total Amount</td>
                <td>RM {{ number_format(($order->total_price + $order->shipping_cost) / 100, 2) }}</td>
            </tr>
        </table>

        <h2>Shipping Information</h2>
        <p>
            <strong>Delivery Address:</strong><br>
            {{ $order->shipping_address }}
        </p>
        <p>
            <strong>Shipping Status:</strong> {{ ucfirst($order->shipping_status) }}
        </p>

        <div class="footer">
            <p>Thank you for shopping with us!</p>
            <p><a href="{{ route('orders.show', $order) }}" class="button">View Order Details</a></p>
        </div>
    </div>
</body>
</html>
